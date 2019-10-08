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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;


class GeneratePasswordCommand extends Command
{
    private $encoder;
    public function __construct(array $config, UserPasswordEncoder $encoder, LoggerInterface $logger)
    {
        $this->encoder = $encoder;
        parent::__construct();

    }

    public function configure()
    {
        $this
            ->setName('derasy:generate:password')
            ->setDescription('Generate password from parameter')
            ->setHelp('This command allows you generate password from parameter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '[Derasy] Please use: bin/console security:encode-password',
            '',
        ]);

    }
}