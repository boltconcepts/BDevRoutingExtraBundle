<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\Loader;

use BDev\Bundle\RoutingExtraBundle\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;

class YamlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupport()
    {
        $loader = new YamlFileLoader($this->getMock('Symfony\Component\Config\FileLocator'));

        $this->assertTrue($loader->supports('foo.yml'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');

        $this->assertTrue($loader->supports('foo.yml', 'yaml'), '->supports() checks the resource type if specified');
        $this->assertFalse($loader->supports('foo.yml', 'foo'), '->supports() checks the resource type if specified');
    }

    public function testLoadDoesNothingIfEmpty()
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/Fixtures')));
        $collection = $loader->load('empty.yml');

        $this->assertEquals(array(), $collection->all());
        $this->assertEquals(array(new FileResource(realpath(__DIR__.'/Fixtures/empty.yml'))), $collection->getResources());
    }

    public function testLoadWithResource()
    {
        $loader = new YamlFileLoader(new FileLocator(array(__DIR__.'/Fixtures')));
        $routeCollection = $loader->load('validresource.yml');
        $routes = $routeCollection->all();

        $this->assertCount(2, $routes, 'Two routes are loaded');
        $this->assertContainsOnly('BDev\Bundle\RoutingExtraBundle\RawRoute', $routes);

        $this->assertEquals('blog_show', $routes['blog_show']->getRoute());
        $this->assertEquals(array('extra' => 'extra'), $routes['blog_show']->getOptions());

        $this->assertEquals('blog_show_legacy', $routes['blog_show_legacy']->getRoute());
        $this->assertEquals(array('parent' => 'blog_show', 'menu' => array('main' => array('attributes' => array('class' => 'home')))), $routes['blog_show_legacy']->getOptions());
    }
}