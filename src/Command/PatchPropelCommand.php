<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 16.11
 */

namespace  Derasy\DerasyBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Derasy\DerasyBundle\Common\System;


class PatchPropelCommand extends AbstractCommand
{
    public function __construct(array $config, array $propelConfig, LoggerInterface $logger)
    {
        parent::__construct($config, $propelConfig, $logger);
    }

    public function configure()
    {
        $this
            ->setName('derasy:patch:propel')
            ->setDescription('Patch propel bug on MSSQLSchemaParser')
            ->setHelp('This command allows you to patch the Propel 2\'s bug on MSSQLSchemaParser')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '[Derasy] Patching propel ...',
            '',
        ]);

        $projectDir = $this->config["project_dir"];

        $targetFilePath = $projectDir."/vendor/propel/propel/src/Propel/Generator/Reverse/MssqlSchemaParser.php";
        $patchFilePath = __DIR__."/../Resources/patch/MssqlSchemaParser.php";
        $patchingResultArr = $this->patch($targetFilePath, $patchFilePath);

        $output->writeln([
            $patchingResultArr["message"],
            '',
        ]);

        $targetFilePath = $projectDir."/vendor/propel/propel/src/Propel/Runtime/Connection/ConnectionFactory.php";
        $patchFilePath = __DIR__."/../Resources/patch/ConnectionFactory.php";
        $patchingResultArr = $this->patch($targetFilePath, $patchFilePath);

        $output->writeln([
            $patchingResultArr["message"],
            '',
        ]);
    }

    protected function patch($targetFilePath, $patchFilePath)
    {
        try {

            $success = copy($patchFilePath, $targetFilePath);

            if (!$success) {
                throw new \Exception("File can not be copied");
            }

            return array(
                "success" => true,
                "message" => 'Patching ' . basename($targetFilePath) . ' success.'
            );

        } catch (\Exception $e) {

            return array(
                "success" => false,
                "message" => 'Patching ' . basename($targetFilePath) . ' failed.'
            );

        }

    }
}