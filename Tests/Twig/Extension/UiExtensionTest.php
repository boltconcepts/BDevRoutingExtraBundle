<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\Twig\Extension;

use BDev\Bundle\RoutingExtraBundle\Route;
use BDev\Bundle\RoutingExtraBundle\RouteCollection;
use BDev\Bundle\RoutingExtraBundle\Twig\Extension\UiExtension;
use Symfony\Component\DependencyInjection\Container;

class UiExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\BDev\Bundle\RoutingExtraBundle\RouteCollectionInterface
     */
    protected $collection;

    /**
     * @var UiExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->collection = $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteCollectionInterface');
        $this->container = new Container();
        $this->extension = new UiExtension();

        $provider = $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteProviderInterface');
        $provider->expects($this->any())->method('getCollection')->will($this->returnValue($this->collection));
        $this->container->set('bdev_routing_extra.provider', $provider);

        $this->extension->setContainer($this->container);
    }

    public function tearDown()
    {
        $this->extension = null;
        $this->container = null;
        $this->collection = null;
    }

    public function testGetParent()
    {
        $parentRoute = new Route('my_parent');
        $this->collection->expects($this->once())->method('getParent')->with('my_child')->will($this->returnValue($parentRoute));

        $this->assertEquals('my_parent', $this->extension->getParent('my_child'));
    }

    public function testGetParentCurrentRoute()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->any())->method('get')->with('_route')->will($this->returnValue('current_route'));
        $this->container->set('request', $request);

        $parentRoute = new Route('current_parent');
        $this->collection->expects($this->once())->method('getParent')->with('current_route')->will($this->returnValue($parentRoute));

        $this->assertEquals('current_parent', $this->extension->getParent());
    }

    public function testGetOption()
    {
        $route = new Route('my_route', array('label' => 'my label'));
        $this->collection->expects($this->exactly(2))->method('getRoute')->with('my_route')->will($this->returnValue($route));

        $this->assertEquals('my label', $this->extension->getOption('label', null, 'my_route'));
        $this->assertEquals('default', $this->extension->getOption('i dont exist', 'default', 'my_route'));
    }

    public function testGetOptionCurrentRoute()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->any())->method('get')->with('_route')->will($this->returnValue('current_route'));
        $this->container->set('request', $request);

        $route = new Route('current_route', array('label' => 'my label'));
        $this->collection->expects($this->exactly(2))->method('getRoute')->with('current_route')->will($this->returnValue($route));

        $this->assertEquals('my label', $this->extension->getOption('label'));
        $this->assertEquals('default', $this->extension->getOption('i dont exist', 'default'));
    }
}