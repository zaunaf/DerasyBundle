<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 10/10/2018
 * Time: 14.16
 */

namespace Derasy\DerasyBundle\Generator;

use Psr\Log\LoggerInterface;

class OrmModelBuilder extends AbstractModelBuilder {

    public function __construct($config, $propelConfig, LoggerInterface $logger)
    {
        // Make sure everything is loaded
        parent::__construct($config, $propelConfig, $logger);

        // Generate in var
        $this->outputPath = $config["project_dir"]."/var/orm/";
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath);
        }
        $this->generate();

        // Generate in var
        $this->deployPath = $config["project_dir"]."/src/Entity";
        if (!is_dir($this->deployPath )) {
            mkdir($this->deployPath );
        }
        $this->deploy();

    }


    public function generate() {

        $this->updateType();

        if (!isset($this->config["auth"]["roles"])) {
            throw new \Exception("Derasy configuration not found: roles");
        }
        if (!isset($this->config["auth"]['default_role'])) {
            throw new \Exception("Derasy configuration not found: default_role");
        }

        // Loop it
        foreach ($this->getModelTree() as $schemaKey => $tables) {

            foreach ($tables as $table) {

                if ($table["tableName"] == $this->config["auth"]['user_table']) {
                    $table['roles'] = $this->config['auth']['roles'];
                    $table['default_role'] = $this->config['auth']['default_role'];
                }

                $filePath = $this->outputPath."/".$table["tableCcName"].".php";
                $templateFileName = "";

                // Different entity type, different template
                // Avoid twig clusterfuck
                //if ($table["tableType"] == "reference") {
                //    $templateFileName = "reference.php.twig";
                //} else if ($table["compositePk"]) {
                //    $templateFileName = "composite.php.twig";
                //} else if ($table["isUserTable"]) {
                //    $templateFileName = "user.php.twig";
                //} else {
                //    $templateFileName = "entity.php.twig";
                //}
                $templateFileName = "doctrine_entity.php.twig";

                try {
                    $content = $this->render($templateFileName, $table);
                    $this->saveToFile($filePath, $content);
                    $this->addOutput("With $templateFileName, file $filePath is written.");
                } catch (\Exception $e) {
                    $this->addOutput("With $templateFileName, file $filePath failed to be written: ".$e->getMessage());
                }
            }
        }

    }

    public function deploy() {

        // Loop it
        $overwrite = $this->config["overwrite"];

        foreach ($this->getModelTree() as $schemaKey => $tables) {
            foreach ($tables as $table) {
                $sourcePath = $this->outputPath."/".$table["tableCcName"].".php";
                $targetPath = $this->deployPath."/".$table["tableCcName"].".php";
                try {
                    $this->copy($sourcePath, $targetPath, $overwrite);
                    $this->addOutput("File $targetPath is deployed");
                } catch (\Exception $e) {
                    $this->addOutput("File $targetPath failed to be deployed: ".$e->getMessage());
                }
            }
        }

    }

    /**
     * Returns the native PHP type which corresponds to the
     * mapping type provided. Use in the base object class generation.
     *
     * @param  string $mappingType
     * @return string
     */
    public static function getDoctrineType($mappingType)
    {
        return OrmType::getDoctrineType($mappingType);
    }

    /**
     * Update column type for doctrine
     */
    public function updateType() {

        $modelTree = $this->getModelTree();

        foreach ($modelTree as $schemaKey => $tables)
        {
            foreach ($tables as $tableKey => $table)
            {
                foreach ($table["columns"] as $columnKey => $column)
                {
                    $modelTree[$schemaKey][$tableKey]["columns"][$columnKey]["ormType"] = OrmType::getDoctrineType($column["type"]);
                }
            }
        }

        $this->updateModelTree($modelTree);

    }
}