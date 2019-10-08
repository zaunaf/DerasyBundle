<?php

namespace Derasy\DerasyBundle\Tests\Controller;

use Derasy\DerasyBundle\DerasyBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class DerasyApiControllerTest extends TestCase
{
    public function testIndex()
    {
        $kernel = new DerasyApiControllerKernel();

        $client = new Client($kernel);
        $client->request('GET', '/derasy/api/');

        var_dump($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}

class DerasyApiControllerKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        // TODO: Implement registerBundles() method.
        return [
            new DerasyBundle(),
            new FrameworkBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        // TODO: Implement configureContainer() method.
        $c->loadFromExtension('framework', [
            'secret' => 'F00',
        ]);
    }

    protected function configureRoutes(\Symfony\Component\Routing\RouteCollectionBuilder $routes)
    {
        // TODO: Implement configureRoutes() method.
        $routes->import(__DIR__.'/../../src/Resources/config/routes.xml', '/derasy/api');
    }

    public function getCacheDir()
    {
        return __DIR__.'/../cache/'.spl_object_hash($this);
    }

}