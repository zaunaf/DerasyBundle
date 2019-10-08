<?php

/**
 * This file is part of the Derasy package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace  Derasy\DerasyBundle\Command;

use Propel\Generator\Config\GeneratorConfig;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * @author Donny Fauzan <donny@nufaza.com>
 */
abstract class AbstractCommand extends Command {

    protected $propelConfig;
    protected $config;
    protected $logger;

    /**
     * AbstractCommand constructor.
     *
     * @param array $propelConfig
     * @param array $config
     */
    public function __construct(array $config, array $propelConfig, LoggerInterface $logger)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->config = $config;
        $this->propelConfig = $propelConfig;
        $this->logger = $logger;

        parent::__construct();
    }


}