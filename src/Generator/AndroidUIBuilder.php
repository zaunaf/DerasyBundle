<?php


namespace Derasy\DerasyBundle\Generator;


use Psr\Log\LoggerInterface;

class AndroidUIBuilder extends AbstractModelBuilder {

    /**
     * @var \SimpleXMLElement
     */
    private $namespace;

    public function __construct($config, $propelConfig, LoggerInterface $logger)
    {
        // Make sure everything is loaded
        parent::__construct($config, $propelConfig, $logger);

        if (!isset($config["android_project_folder"])) {
            throw new \Exception("Derasy configuration not found: android_project_folder");
        }

        $androidProjectfolder = $config["project_dir"] .'/'. $this->config["android_project_folder"];
        if (!is_dir($androidProjectfolder)) {
            throw new \Exception("Derasy configuration error: $androidProjectfolder is not a folder");
        }

        $androidProjectfolder = realpath($androidProjectfolder);
        echo "Working on Android Project on $androidProjectfolder\n";

        // Checking manifest
        $manifestFile = $androidProjectfolder."/app/src/main/AndroidManifest.xml";

        if (!is_file($manifestFile)) {
            throw new \Exception("Derasy configuration error: AndroidManifest not found in $manifestFile. This folder probably is not an Android Project Folder");
        }

        $xmldata = simplexml_load_file($manifestFile);
        $namespace = $xmldata->attributes()->package;

        echo "Finding android namespace: $namespace\n";

        $namespaceFolder = str_replace(".", "/", $namespace);

        // Creating model folder if not created yet
        $javaSourceFolder = realpath($androidProjectfolder."/app/src/main/java/".$namespaceFolder);

        echo "Finding $javaSourceFolder\n";
        $modelFolder = $javaSourceFolder."/model";

        if (!is_dir($modelFolder)) {
            echo "Creating model folder: $modelFolder\n";
            mkdir($modelFolder);
        }

        // Prepare
        $this->namespace = $namespace;

        // Generate in var
        $this->outputPath = realpath($config["project_dir"]."/var/android/model");
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, null, true);
        }
        echo "Generating files in: {$this->outputPath}\n";
        $this->generate();

        // To be deployed in
        $this->deployPath = realpath($modelFolder);
        if (!is_dir($this->deployPath )) {
            mkdir($this->deployPath, null, true);
        }
        echo "Copying files to: {$this->deployPath}\n";
        $this->deploy();

    }


    public function generate() {

        $this->updateType();

        // Loop it
        foreach ($this->getModelTree() as $schemaKey => $tables) {

            foreach ($tables as $table) {
                // Attach namespace
                $table["namespace"] = $this->namespace;
                
                $filePath = $this->outputPath."/".$table["tableCcName"].".java";
                $templateFileName = "android_entity.php.twig";

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

        // Always overwrite
        // $overwrite = isset($this->config["overwrite"]);
        $overwrite = true;

        foreach ($this->getModelTree() as $schemaKey => $tables) {
            foreach ($tables as $table) {
                $sourcePath = $this->outputPath."/".$table["tableCcName"].".java";
                $targetPath = $this->deployPath."/".$table["tableCcName"].".java";
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
                    $modelTree[$schemaKey][$tableKey]["columns"][$columnKey]["javaType"] = JavaType::getDoctrineType($column["type"]);
                }
            }
        }

        $this->updateModelTree($modelTree);

    }
}