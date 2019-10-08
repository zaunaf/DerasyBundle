<?php

/**
 * This file is part of the Derasy package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Derasy\DerasyBundle\Config;

use Derasy\DerasyBundle\Common\Util;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use Derasy\DerasyBundle\Common;

/**
 * @author Donny Fauzan <donny@nufaza.com>
 */
class SchemaFileManager {

    private $config;
    private $propelConfig;
    private $logger;

    private $schemaFilePaths;
    private $outputStr;

    /**
     * SchemaFileManager constructor.
     * @param $config
     */
    public function __construct($config, $propelConfig, LoggerInterface $logger)
    {
        // Load config and logger
        $this->config = $config;
        $this->propelConfig = $propelConfig;
        $this->logger = $logger;

        // Unpack folder path from setting
        $projectDir = $this->config["project_dir"];
        $schemaDir = $this->propelConfig["paths"]["schemaDir"];
        $schemaPath = $projectDir . DIRECTORY_SEPARATOR . $schemaDir;

        // Detect how many files there is
        $this->schemaFilePaths = $this->getSchemas($schemaPath, false);

        // Process each files to schemaObjects
        foreach ($this->schemaFilePaths as $schemaFilePath)
        {
            $this->synchronizeSchema($schemaFilePath);
        }

    }

    /**
     * Synchronize Schema with Configuration
     * @return string
     */
    public function synchronizeSchema($schemaFilePath)
    {
        try {

            // Prepare
            $skipSchemas = $this->config["skip_schema"];
            $skipTables = $this->config["skip_table"];
            $skipColumns = $this->config["skip_column"];

            // Track
            $skippedTableCount = 0;
            $skippedColumnCount = 0;

            // Check the file
            if (!is_file($schemaFilePath)) {
                $errMsg = "File $schemaFilePath not found";
                $this->logger->error($errMsg);
                throw new \Exception($errMsg);
            }

            // Load to Object
            $schemaObj = simplexml_load_file($schemaFilePath);

            if (!$schemaObj) {
                throw new \Exception("File $schemaFilePath failed to be load with SimpleXML");
            }

            $tablesToRemove = array();
            $columnsToRemove = array();

            foreach ($schemaObj->table as $t) {

                // print_r($t['schema']);
                $schemaName = $t['schema'] == "" ? "dbo" : $t['schema'];
                $tableName = $t['name'];

                if (Util::contains($schemaName, $skipSchemas)) {
                    $this->addOutput("Skipping schema $schemaName");
                    $tablesToRemove[] = $t;
                    continue;
                }

                if (Util::contains($tableName, $skipTables)) {
                    $this->addOutput("Skipping table $tableName");
                    $tablesToRemove[] = $t;
                    continue;
                }

                // Clean up tables with space in it
                if (strpos($tableName, " ")) {
                    $this->addOutput("Skipping table $tableName");
                    $tablesToRemove[] = $t;
                    continue;
                }

                foreach ($t->column as $c) {

                    $columnName = $c['name'];

                    if (Util::contains($columnName, $skipColumns)) {
                        $this->addOutput("Skipping column $columnName");
                        $columnsToRemove[] = $c;
                        continue;
                    }
                }

            }

            if (is_array($tablesToRemove)) {
                foreach ($tablesToRemove as $t) {
                    unset ($t[0]);
                    $skippedTableCount++;
                }
            }

            if (is_array($columnsToRemove)) {
                foreach ($columnsToRemove as $c) {
                    unset ($c[0]);
                    $skippedColumnCount++;
                }
            }

            $schemaDesignStr = $schemaObj->asXml();
            //$schemaDesignStr = preg_replace('/[\n\r]+/', '', $schemaDesignStr);
            //$schemaDesignStr = str_replace(array("\n", "\r"), '', $schemaDesignStr);

            // Effort to clean up lines
            $xmlObj = simplexml_load_string($schemaDesignStr);
            $schemaDesignStr = $xmlObj->asXml();

            // Quick hack. Should be in somekind of config
            // $schemaDesignStr = str_replace("defaultValue=\"('now()')\"", "", $schemaDesignStr);
            // $schemaDesignStr = str_replace("defaultValue=\"('1901-01-01')\"", "", $schemaDesignStr);
            // $schemaDesignStr = str_replace("defaultValue=\"('2014-10-10')\"", "", $schemaDesignStr);

            if (isset($config["clean_schema_str"])) {
                if (!is_array($config["clean_schema_str"])) {
                    $strToBeCleanArr = explode(",", $config["clean_schema_str"]);
                } else {
                    $strToBeCleanArr = $config["clean_schema_str"];
                }
                foreach ($strToBeCleanArr as $strToBeClean) {
                    $schemaDesignStr = str_replace($strToBeClean, "", $schemaDesignStr);
                }
            }

            $target = $schemaFilePath;

            $fp = fopen($target, 'w');
            if (!$fp) {
                throw new \Exception("File $target failed to be written.");
            }
            if (!fwrite($fp, $schemaDesignStr)) {
                throw new \Exception("File $target failed to be written.");
            }

            fclose($fp);

            $this->addOutput("File $target written.");

        } catch (\Exception $e) {

            $errMsg = $e->getMessage();
            $this->addOutput($errMsg);

        }

    }

    /**
     * Find every schema files.
     *
     * @param string|array $directory Path to the input directory
     * @param bool         $recursive Search for file inside the input directory and all subdirectories
     *
     * @return array List of schema files
     */
    protected function getSchemas($directory, $recursive = false)
    {
        $finder = new Finder();
        $finder
            ->name('*schema.xml')
            ->sortByName()
            ->in($directory);
        if (!$recursive) {
            $finder->depth(0);
        }

        $schemas = iterator_to_array($finder->files());
        $schemaPaths = array();

        foreach ($schemas as $schema) {
            // $schema = new SplFileInfo();
            $schemaPaths[] = $schema->getPathname();
        }

        return $schemaPaths;
    }

    /**
     * Add log for command output
     * @param $msg
     */
    protected function addOutput($msg) {
        $this->outputStr .= $msg . "\r\n";
    }

    /**
     * Return the log to command
     * @return mixed
     */
    public function getOutput(){
        return $this->outputStr;
    }
}