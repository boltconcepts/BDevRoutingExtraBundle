<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\DependencyInjection;

use BDev\Bundle\RoutingExtraBundle\DependencyInjection\BDevRoutingExtraExtension;
use BDev\Bundle\RoutingExtraBundle\DependencyInjection\Compiler\ResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class FixtureBDevRoutingExtraExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDefaults()
    {
        $container = $this->getContainer('empty');

        $this->assertTrue($container->hasDefinition('bdev_routing_extra.provider'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.loader'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.compiler'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.cache_warmer'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.resolver'));

        $this->assertFalse($container->hasDefinition('bdev_routing_extra.loader.yml'));

        $this->assertFalse($container->hasDefinition('bdev_routing_extra.breadcrumb.twig.extension'));
        $this->assertFalse($container->hasDefinition('bdev_routing_extra.knp_menu.provider'));
    }

    public function testLoadWithTwig()
    {
        $container = $this->getContainer('full');
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.twig.extension'));

        $container = $this->getContainer('twig_disabled');
        $this->assertFalse($container->hasDefinition('bdev_routing_extra.twig.extension'));
    }

    public function testLoadWithBreadcrumb()
    {
        $container = $this->getContainer('full');

        $this->assertTrue($container->hasDefinition('bdev_routing_extra.breadcrumb.twig.extension'));
    }

    public function testLoadWithKnpMenu()
    {
        $container = $this->getContainer('full');

        $this->assertTrue($container->hasDefinition('bdev_routing_extra.knp_menu.loader'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.knp_menu.compiler'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.knp_menu.provider'));
        $this->assertTrue($container->hasDefinition('bdev_routing_extra.knp_menu.configure_listener'));
    }

    protected function getContainer($fixture)
    {
        $extension = new BDevRoutingExtraExtension();

        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug' => true,
            'kernel.root_dir' => __DIR__,
            'kernel.cache_dir' => __DIR__,
        )));

        $container->registerExtension($extension);

        $container->addDefinitions(array('file_locator' => new Definition('Symfony\Component\Config\FileLocator')));
        $container->addDefinitions(array('router' => new Definition('Symfony\Component\Routing\Router')));

        $container->loadFromExtension($extension->getAlias());

        $this->loadFixture($container, $fixture);

        $container->addCompilerPass(new ResolverPass());
        $container->compile();

        return $container;
    }

    abstract protected function loadFixture(ContainerBuilder $container, $fixture);
}