<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 16.11
 */
namespace  Derasy\DerasyBundle\Command;

use Derasy\DerasyBundle\Common\Util;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Derasy\DerasyBundle\Common\System;



class BuildModelCommand extends AbstractCommand
{
    public function __construct(array $config, array $propelConfig, LoggerInterface $logger)
    {
        parent::__construct($config, $propelConfig, $logger);
    }

    public function configure()
    {
        $this
            ->setName('derasy:build:model')
            ->setDescription('Build entity propel model from existing model')
            ->setHelp('This command allows you to build extended entity propel model from existing model')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '[Derasy] Building model ...',
            '',
        ]);

        // print_r($this->propelConfig["database"]["connections"]);die;
        $databases = array();

        try {
            foreach ($this->propelConfig["database"]["connections"] as $key => $val) {

                $databases[] = '"model/'.Util::phpNamize($key).'"';

                $output->writeln([
                    'Building database: '. $key,
                ]);

                if (System::getOs() == System::OS_WINDOWS) {
                    $command = 'vendor\bin\propel';
                } else {
                    $command = 'vendor/bin/propel';
                }

                $process = new Process(array(
                    $command,
                    'build',
                    '--config-dir',
                    'config/packages',
                    '--output-dir',
                    'model',
                    '-v',
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

            }

            $composerAutoloadStr = implode(", ", $databases);
            $output->writeln([
                "Model build finished. Please insert this line manually under autoload in composer.json:",
                '"classmap": [ '. $composerAutoloadStr .' ]',
                '',
                'Then run:',
                'composer update nothing',
                '',
                'Please do this each time there is any addition/removal of entities/tables.'
            ]);

        } catch (\Exception $e) {

            $output->writeln([
                'Error - '. $e->getMessage(),
                '',
            ]);

        }

    }
}