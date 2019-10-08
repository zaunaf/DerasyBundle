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


class BuildUiExt6Command extends AbstractCommand
{
    public function __construct(array $config, array $propelConfig, LoggerInterface $logger)
    {
        parent::__construct($config, $propelConfig, $logger);
    }

    public function configure()
    {
        $this
            ->setName('derasy:build_ui:ext6')
            ->setDescription('Build UI scripts for ExtJS 6 from existing model')
            ->setHelp('This command allows you to build UI scripts for ExtJS 6 from existing model')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Build UI Ext6',
            '============',
            '',
        ]);

        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword
        //$output->writeln($this->someMethod());

        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');

        // outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('build UI for Ext 6.');
    }
}