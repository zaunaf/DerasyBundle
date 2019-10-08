<?php
/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 16.11
 */
namespace  Derasy\DerasyBundle\Command;

use Derasy\DerasyBundle\Generator\OrmModelBuilder;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Yaml\Yaml;

class BuildOrmCommand extends AbstractCommand
{
    public function __construct(array $config, array $propelConfig, LoggerInterface $logger)
    {
        parent::__construct($config, $propelConfig, $logger);
    }

    public function configure()
    {
        $this
            ->setName('derasy:build:orm')
            ->setDescription('Build entity doctrine orm model from existing model')
            ->setHelp('This command allows you to build doctrine orm model from existing model')
            ->addOption(
                'reset',
                null,
                InputOption::VALUE_NONE,
                'Rewrite the base model (derasy.base_model.yaml)'
            )
            ->addOption(
                'recalculate',
                null,
                InputOption::VALUE_NONE,
                'Recalculate the table rows for info on (derasy.base_model.yaml).'
            )
            ->addOption(
                'overwrite',
                null,
                InputOption::VALUE_NONE,
                'Overwrite all ORM Entity generated.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '[Derasy] Building ORM ...',
            '',
        ]);

        // Inject parameters
        $this->config["reset"] = $input->getOption('reset');
        $this->config["recalculate"] = $input->getOption('recalculate');
        $this->config["overwrite"] = $input->getOption('overwrite');

        $modelBuilder = new OrmModelBuilder($this->config, $this->propelConfig, $this->logger);
        $output->writeln([
            $modelBuilder->getOutput()
        ]);

        //
        // $arrModel = $modelBuilder->loadModel();
        // $modelTree = $modelBuilder->getModelTree();
        // print_r($modelTree); die;
        // $yaml = Yaml::dump($modelTree, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
        //$output->writeln([
        //    $yaml,
        //    '',
        //]);

    }
}