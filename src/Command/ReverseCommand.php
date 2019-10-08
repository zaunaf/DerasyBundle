<?php
/**
 * This file is part of the Derasy package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Derasy\DerasyBundle\Command;

use Psr\Log\LoggerInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Derasy\DerasyBundle\Config\SchemaFileManager;
use Derasy\DerasyBundle\Common\System;




/**
 * @author Donny Fauzan <donny@nufaza.com>
 */
class ReverseCommand extends AbstractCommand
{
    public function __construct(array $config, array $propelConfig, LoggerInterface $logger)
    {
        parent::__construct($config, $propelConfig, $logger);
    }

    public function configure()
    {
        $this
            ->setName('derasy:reverse')
            ->setDescription('Reverse engineer from existing database')
            ->setHelp('This command allows you to reverse engineer a base extended Entity Config from existing model')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '[Derasy] Reverse Engineer is starting ... ',
            '',
        ]);

        // print_r($this->propelConfig["database"]["connections"]);die;
        try {
            foreach ($this->propelConfig["database"]["connections"] as $key => $val) {

                $output->writeln([
                    'Reverse engineering database: '. $key,
                ]);

                if (System::getOs() == System::OS_WINDOWS) {
                    $command = 'vendor\bin\propel';
                } else {
                    $command = 'vendor/bin/propel';
                }

                $process = new Process(array(
                    $command,
                    'reverse',
                    '--config-dir',
                    'config/packages',
                    '--output-dir',
                    $this->propelConfig["paths"]["schemaDir"],
                    '--schema-name',
                    $key . '.schema',
                    '--namespace',
                    '\\' . str_replace('_', '', ucwords($key, '_')),
                    $key
                ));

                $process->run();

                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                $output->writeln([
                    $process->getOutput(),
                    '',
                ]);
                // $schemaFileManager->synchronizeSchema();

            }

            $output->writeln([
                '[Derasy] Updating schema with criteria configured:',
                '- schemas to skip: ' . implode(",", $this->config["skip_schema"]),
                '- tables to skip: ' . implode(",", $this->config["skip_table"]),
                '- columns to skip: ' . implode(",", $this->config["skip_column"]),
                ' ',
            ]);

            $schemaFileManager = new SchemaFileManager($this->config, $this->propelConfig, $this->logger);

            $output->writeln([
                $schemaFileManager->getOutput(),
                '',
            ]);

        } catch (\Exception $e) {

            $output->writeln([
                'Error - '. $e->getMessage(),
                '',
            ]);

        }

    }


}