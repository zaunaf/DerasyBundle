<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 10/10/2018
 * Time: 14.10
 */

namespace Derasy\DerasyBundle\Generator;

use Propel\Generator\Model\PropelTypes;
//use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Propel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
//use Propel\Runtime\Map\ColumnMap;
use Propel\Runtime\Map\RelationMap;
use Derasy\DerasyBundle\Common\System;
use Derasy\DerasyBundle\Common\Util;
use Derasy\DerasyBundle\Derasy;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractModelBuilder {

    protected $propelConfig;
    protected $config;
    protected $logger;

    protected $projectDir;
    protected $packageDir;
    protected $derasyConfigDir;
    protected $modelPath;
    protected $modelConfig;
    protected $derasyBaseModelFilename;
    protected $derasyModelFilename;

    protected $databases;
    protected $databasesArr;
    protected $modelTree;

    protected $outputStr;

    protected $outputPath;
    protected $deployPath;
    
    public function __construct($config, $propelConfig, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->propelConfig = $propelConfig;
        $this->logger = $logger;

        $this->projectDir = $this->config["project_dir"];
        $modelDir = "model";    // temporer. nanti masuk config aja
        $this->modelPath = $this->projectDir . DIRECTORY_SEPARATOR . $modelDir;

        // Config Dirs
        $this->packageDir = $this->projectDir."/config/packages/";
        $this->derasyConfigDir = $this->projectDir."/config/derasy/";

        // File Model
        $this->derasyBaseModelFilename = "derasy.base_model.yaml";
        $this->derasyModelFilename = "derasy.model.yaml";

        // Base Model File Name
        $this->baseModelFileName = $this->derasyConfigDir."/".$this->derasyBaseModelFilename;

        // Check if config dir exists
        $this->checkConfigDir();

        // Check if there is command to reset.
        if (isset($this->config['reset'])) {
            $this->resetConfigTree();
        }

        // Check if derasy_base_model.yaml exists. If not, create it.
        $this->checkBaseModelTree();

        // Check if derasy_model.yaml exists. If not, create it.
        $this->checkModelTree();

        // Load model to
        $this->modelTree = $this->loadModel();
    }

    private function resetConfigTree()
    {
        $fileSystem = new Filesystem();
        try {
            if ($fileSystem->exists($this->baseModelFileName)) {
                $fileSystem->remove($this->baseModelFileName);
            }
            $this->addOutput("Existing derasy base model is deleted.");
        } catch (IOExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    private function checkConfigDir()
    {
        $fileSystem = new Filesystem();
        try {
            if (!$fileSystem->exists($this->derasyConfigDir)) {
                $fileSystem->mkdir($this->derasyConfigDir);
            }
        } catch (IOExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    private function checkBaseModelTree()
    {
        $fileSystem = new Filesystem();

        try {
            // echo "Checking if {($this->baseModelFileName} exists\n";
            // echo (($fileSystem->exists($this->baseModelFileName)) ? "It exists" : "It's not"); 

            if (!$fileSystem->exists($this->baseModelFileName)) {
                $modelTree = $this->generateModelTree();
                $yamlStr = Yaml::dump($modelTree, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
                $fileSystem->dumpFile($this->baseModelFileName, $yamlStr);
                $this->addOutput("Derasy base model written.");
            }
        } catch (IOExceptionInterface $exception) {
            throw new \Exception($exception->getMessage(). " Make sure you have done composer udpate nothing.");
        }
    }

    private function checkModelTree()
    {
        $fileSystem = new Filesystem();

        try {
            if (!$fileSystem->exists($this->baseModelFileName)) {
                $modelTree = $this->generateModelTree();
                $simpleModelTree = array();
                foreach ($modelTree as $schemaKey => $tables)
                {
                    foreach ($tables as $tableKey => $table)
                    {
                        foreach ($table["columns"] as $columnKey => $column)
                        {
                            // $simpleModelTree[$schemaKey][$tableKey]["columns"]["columnHeader"] = $modelTree[$schemaKey][$tableKey]["columns"]["columnHeader"]
                        }
                    }
                }
                $yamlStr = Yaml::dump($simpleModelTree, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
                $fileSystem->dumpFile($this->baseModelFileName, $yamlStr);
                $this->addOutput("Derasy override model written.");
            }
        } catch (IOExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    private function generateModelTree()
    {
        // Find databases in the folder
        $nameOfDatabases = System::getDirsIn($this->modelPath, true);
        $pathOfDatabases = System::getDirsIn($this->modelPath, false);

        // Record
        $this->databases = $nameOfDatabases;
        $this->databasesPath = $pathOfDatabases;

        // Loop Through Namespace's Map
        $classNames = array();
        $this->modelTree = array();

        foreach ($pathOfDatabases as $name => $path) {

            $mapFolder = $path."/Map/";
            $pathOfMaps = System::getFilesIn($mapFolder, false);

            foreach ($pathOfMaps as $fileName => $filePath)
            {
                $classMap = str_replace(".php", "", $fileName);
                $classMap = "\\".$name."\\Map\\".$classMap;
                $classMaps[] = $classMap;

                $classArr = $this->unpack($classMap);
                $this->modelTree[$name][$classArr["tableName"]] = $classArr;
            }
        }

        return $this->modelTree;
    }

    public function loadModel() {
        return Yaml::parseFile($this->baseModelFileName);
    }

    public function getModelTree() {
        return $this->modelTree;
    }

    protected function getTableNames($classMapTableName){

        $tableNameArr = explode(".", $classMapTableName);
        if (sizeof($tableNameArr) > 1) {
            list($schemaName, $tableName) = $tableNameArr;
        } else {
            $schemaName = "dbo";
            $tableName = $classMapTableName;
        }
        return array($schemaName, $tableName);
    }

    protected function unpack($classMap) {

        // Get SchemaName and TableName
        list($schemaName, $tableName) = $this->getTableNames($classMap::TABLE_NAME);
        //echo $tableName."|".$schemaName."\n";

        // Reading
        // $this->addOutput("Reading $tableName");
        // echo "Reading $tableName\n";

        $name = $classMap::TABLE_NAME;
        // echo "\nclassMap = ".$classMap;
        $dbMap = Propel::getServiceContainer()->getDatabaseMap($classMap::DATABASE_NAME);

        // Get CC (CamelCase) version
        list ($databaseCcName, $tableCcName) = explode(".", $classMap::CLASS_DEFAULT);

        // What type is the table, data, reference or report
        $referenceSchemas = $this->config["reference_schema"];
        $reportSchemas = $this->config["report_schema"];

        if (Util::contains($schemaName, $referenceSchemas)) {
            $tableType = Derasy::TABLE_TYPE_REFERENCE;
        } else if (Util::contains($schemaName, $reportSchemas)) {
            $tableType = Derasy::TABLE_TYPE_REPORT;
        } else {
            $tableType = Derasy::TABLE_TYPE_DATA;
        }

        // Get PK columns
        $classMapObj = new $classMap($name, $dbMap);
        $pkColumns = $classMapObj->getPrimaryKeys();
        $pkColumnsArr = array();

        if (sizeof($pkColumns) > 1) {
            foreach ($pkColumns  as $pkc) {
                $pkColumnsArr[] = $pkc->getName();
            }
        }

        // Get columns
        $columns = $classMapObj->getColumns();
        $colArr = array();

        // [ PENDING ]

        // Get relations
        $rOneToMany = $rManyToOne = $rOneToOne = array();

        $relations = $classMapObj->getRelations();

        if (sizeof($relations) > 0) {

            foreach($relations as $r) {

                // $r = new RelationMap();

                switch ($r->getType()) {
                    case RelationMap::ONE_TO_MANY:
                        // $rOneToMany[] = $r->getRightTable()->getName();
                        $relatedTableName = $r->getRightTable()->getName();
                        $arr = explode("\\", $r->getRightTable()->getClassName());
                        $targetEntity = $arr[sizeof($arr)-1];

                        list($relatedSchemaName, $varEntity) = $this->getTableNames($relatedTableName);
                        $rOneToMany[$varEntity] = array(
                            "targetEntity" => $targetEntity,
                            "mappedBy" => $tableName,
                            "varEntity" => $varEntity,
                            "accessible" => true,
                        );
                        break;
                    case RelationMap::MANY_TO_ONE:
                        //$rManyToOne[] = $r->getRightTable()->getName();
                        $relatedTableName = $r->getRightTable()->getName();
                        $arr = explode("\\", $r->getRightTable()->getClassName());
                        $targetEntity = $arr[sizeof($arr)-1];

                        list($relatedSchemaName, $varEntity) = $this->getTableNames($relatedTableName);
                        $rManyToOne[$varEntity] = array(
                            "targetEntity" => $targetEntity,
                            "mappedBy" => $tableName,
                            "varEntity" => $varEntity
                        );
                        break;
                    case RelationMap::ONE_TO_ONE:
                        //$rOneToOne[] = $r->getRightTable()->getName();
                        break;
                }
            }
        }

        $displayField = null;

        foreach ($columns as $c) {

            //$c = new ColumnMap("Anu", new TableMap());
            $r = $c->getRelation();
            // $r = new RelationMap();

            $relatedTableName = $relatedTableFullName = $relatedTableCcName = $relatedColumnName = $relatedColumnCcName = $relatedType = $relatedTableHasCompositePrimaryKey = $relatedColumnIsPrimaryKey = null;

            if (!is_null($r)) {

                $relatedTable = $r->getRightTable();
                $classPathArr = explode("\\", $relatedTable->getClassName());
                $relatedTableCcName = $classPathArr[sizeof($classPathArr) - 1];

                $relatedTableFullName = $relatedTable->getName();
                $relatedTableNameArr = explode(".", $relatedTableFullName);
                $relatedTableName =  sizeof($relatedTableNameArr) > 1 ? $relatedTableNameArr[1] : $relatedTableFullName;

                $relatedTablePrimaryKeys = $relatedTable->getPrimaryKeys();
                $relatedTablePrimaryKeysCount = sizeof($relatedTablePrimaryKeys);
                $relatedTableHasCompositePrimaryKey = $relatedTablePrimaryKeysCount > 1 ? true : false;

                switch ($r->getType()) {
                    case RelationMap::ONE_TO_MANY:
                        $relatedType = "one-to-many";
                        break;
                    case RelationMap::MANY_TO_ONE:
                        // print_r($r->getRightTable()); die;
                        $relatedType = "many-to-one";
                        break;
                    case RelationMap::ONE_TO_ONE:
                        $relatedType = "one-to-one";
                        break;
                }

                // Check how many PKs the related table has
                $relatedColumnName = ($c->isForeignKey()) ? $c->getRelatedColumnName(): null;
                $relatedColumnCcName = $c->getRelatedColumn()->getPhpName();
                $relatedColumnIsPrimaryKey = $c->getRelatedColumn()->isPrimaryKey() ? true : false;
            }



            // Check uniqueidentifier
            $isUuid = false;
            if (($c->isPrimaryKey()  || $c->isForeignKey()) && $c->getType() == 'CHAR' && $c->getSize() == 16) {
                $isUuid = true;
            }

            // Check is text
            $isLongText = ($c->getType() == 'LONGVARCHAR') ? true : false;

            $nativeType = PropelTypes::getPhpNative($c->getType());
            $type = $c->getType();

            // Is Text
            $isText = $c->isText();

            // UUID
            $nativeType = ($isUuid) ? 'uuid' : $nativeType;
            $type = ($isUuid) ? 'UUID' : $type;
            if ($isUuid) {
                $isText = false;
            }

            // Find displayField
            if (($displayField == null) && ($c->isText()) && (!$isLongText) && (!$c->isPrimaryKey()) && (!$c->isForeignKey()))
            {
                $displayField = $c->getName();
            }

            // Usercolumn check
            if (!isset($this->config["auth"])) {
                throw new \Exception("Derasy configuration not found: auth");
            }
            $isUsernameColumn = false;
            if ($this->config["auth"]["username"] == $c->getName()) {
                $isUsernameColumn = true;
            }
            $isPasswordColumn = false;
            if ($this->config["auth"]["password"] == $c->getName()) {
                $isPasswordColumn = true;
            }

            $columnTitles = trim(str_replace("Id", "", Util::humanize($c->getPhpName())));

            // Collect column
            $colArr[$c->getName()] = array (
                "columnName" => $c->getName(),
                "columnCcName" => $c->getPhpName(),
                "columnHumanName" => $columnTitles,
                "columnHeader" => $columnTitles,
                "fieldLabel" => $columnTitles,
                "nativeType" => $nativeType,
                "type" => $type,
                "length" => $c->getSize(),
                "isUuid" => $isUuid,
                "isPrimaryKey" => $c->isPrimaryKey(),
                "isForeignKey" => $c->isForeignKey(),
                "isPrimaryString" => $c->isPrimaryString(),
                "isNotNull" => $c->isNotNull(),
                "isText" => $isText,
                "isLongText" => $isLongText,
                "isNumeric" => $c->isNumeric(),
                "isUsernameColumn" => $isUsernameColumn,
                "isPasswordColumn" => $isPasswordColumn,
                "valueSet" => (sizeof($c->getValueSet()) > 0) ? implode(",", $c->getValueSet()) : null,
                "relatedTableName" => $relatedTableName,
                "relatedTableCcName" => $relatedTableCcName,
                "relatedTableFullName" => $relatedTableFullName,
                "relatedColumnName" => $relatedColumnName,
                "relatedColumnCcName" => $relatedColumnCcName,
                "relatedColumnIsPrimaryKey" => $relatedColumnIsPrimaryKey,
                "relatedTableHasCompositePrimaryKey" => $relatedTableHasCompositePrimaryKey,
                "relatedType" => $relatedType,
            );
        }

        // User Table
        $isUserTable = ($this->config["auth"]["user_table"] == $tableName) ? true : false;

        $data = array(
            "databaseName" => $classMap::DATABASE_NAME,
            "databaseCcName" => $databaseCcName,
            "schemaName" => $schemaName,
            "tableName" => $tableName,
            "tableCcName" => $tableCcName,
            "tableFullName" => $name,
            "tableType" => $tableType,
            "omClass" => $classMap::OM_CLASS,
            "rowCount" => $this->getRowCount($classMap::OM_CLASS),
            "compositePk" => (sizeof($pkColumns) > 1) ? true : false,
            "pkColumns" => (sizeof($pkColumnsArr) > 0) ? $pkColumnsArr : null,
            "displayField" => $displayField,
            "createStaticCombo" => true,
            "createDynamicCombo" => true,
            "createGrid" => true,
            "createForm" => true,
            "isUserTable" => $isUserTable,
            "relationOneToMany" => $rOneToMany,
            "relationManyToOne" => $rManyToOne,
            "relationOneToOne" => $rOneToOne,
            "columns" => $colArr,
        );

        // $outputStr = $this->render("model.yaml.twig", $data);
        // $this->addOutput($outputStr);

        return $data;
    }

    private function getRowCount($omClass)
    {
        // Prepare
        $queryClassName = $omClass."Query";
        $cacheTag = str_replace("\\", "", $queryClassName."Count");

        // Setup caching
        $cache = new FilesystemAdapter("derasy.calculate_rows");
        $cacheItem = $cache->getItem($cacheTag);

        // Empty the cache if commanded
        if (isset($this->config["recalculate"])) {
            $cache->clear();
        }

        if (!$cacheItem->isHit()) {
            // echo($queryClassName);
            try {
                $count = $queryClassName::create()->count();
            } catch (\Exception $e) {
                echo("Error!!");
                $count = 0;
            }
            $cacheItem->set($count);
            $cache->save($cacheItem);
            // echo "Cache $cacheTag saved.\n";
        } else {
            $count = $cacheItem->get();
            //echo "Cache $cacheTag hit. Value: $count\n";
        }

        return $count;
    }
    /**
     * Add log for command output
     * @param $msg
     */
    public function addOutput($msg) {
        $this->outputStr .= $msg . "\r\n";
    }

    /**
     * Return the log to command
     * @return mixed
     */
    public function getOutput(){
        return $this->outputStr;
    }

    public function render($templateFile, $data){

        $loader = new \Twig_Loader_Filesystem(__DIR__."/templates/");
        $twig = new \Twig_Environment($loader, array(
            "debug" => true
        ));
        $twig->addExtension(new \Twig_Extension_Debug());

        $outStr = $twig->render($templateFile, $data);

        return $outStr;
    }

    public function saveToFile($filePath, $content) {

        $fileSystem = new Filesystem();
        try {
            $fileSystem->dumpFile($filePath, $content);
        } catch (IOExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function copy($sourcePath, $targetPath, $overwrite=false) {

        $fileSystem = new Filesystem();
        try {
            if (
                !$fileSystem->exists($targetPath) ||
                ($fileSystem->exists($targetPath) && $overwrite)
            ) {
                $fileSystem->copy($sourcePath, $targetPath);
            }
        } catch (IOExceptionInterface $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
    
    public function updateModelTree($modelTree) {
        $this->modelTree = $modelTree;
    }
}