<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlBDevRoutingExtraExtensionTest extends FixtureBDevRoutingExtraExtensionTest
{
    protected function loadFixture(ContainerBuilder $container, $fixture)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/yml'));
        $loader->load($fixture.'.yml');
    }
}