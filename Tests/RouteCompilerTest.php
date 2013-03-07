<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests;

use BDev\Bundle\RoutingExtraBundle\RawRoute;
use BDev\Bundle\RoutingExtraBundle\RouteCompiler;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouteCollection
     */
    protected $routerCollection;

    /**
     * @var RouteCompiler
     */
    protected $compiler;

    public function setUp()
    {
        $this->routerCollection = new RouteCollection();

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->any())->method('getRouteCollection')->will($this->returnValue($this->routerCollection));

        $this->compiler = new RouteCompiler($router);
    }

    public function tearDown()
    {
        $this->routerCollection = null;
        $this->compiler = null;
    }

    public function testCompile()
    {
        $this->routerCollection->add('my_route', new Route('/'));
        $this->routerCollection->add('full_route', new Route('/{foo}', array(), array('foo' => '[0-9]+')));

        $route = $this->compiler->compile(new RawRoute('my_route'));

        $this->assertInstanceOf('BDev\Bundle\RoutingExtraBundle\Route', $route);
        $this->assertEquals('my_route', $route->getRoute());
        $this->assertNull($route->getParent());
        $this->assertCount(0, $route->getOptions());
        $this->assertCount(0, $route->getRouteRequirements());

        $route = $this->compiler->compile(new RawRoute('full_route', array('full' => 'yeah'), 'my_route'));

        $this->assertInstanceOf('BDev\Bundle\RoutingExtraBundle\Route', $route);
        $this->assertEquals('full_route', $route->getRoute());
        $this->assertEquals('my_route', $route->getParent());
        $this->assertEquals(array('full' => 'yeah'), $route->getOptions());
        $this->assertEquals(array('foo'), $route->getRouteRequirements());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCompileInvalidRoute()
    {
        $this->compiler->compile(new RawRoute('non_existent_route'));
    }
}