<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\Extension\KnpMenu;

use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\MenuCompiler;
use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenu;

class MenuCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MenuCompiler
     */
    protected $compiler;

    public function setUp()
    {
        $this->compiler = new MenuCompiler();
    }

    public function tearDown()
    {
        $this->compiler = null;
    }

    public function testCompile()
    {
        $finalMenu = array(
            'attributes' => array(),
            'linkAttributes' => array(),
            'childrenAttributes' => array(),
            'name' => 'hello',
            'children' => array(
                'my_route' =>
                array(
                    'name' => 'world',
                    'route' => 'my_route',
                    'children' => array(
                        array(
                            'name' => 'person 1',
                            'route' => 'my_child_route_1',
                        ),
                        array(
                            'name' => 'person 2',
                            'route' => 'my_child_route_2',
                        )
                    )
                ),
                'my_ufo_route' =>
                array(
                    'name' => 'ufo',
                    'route' => 'my_ufo_route',
                    'children' => array(
                        array(
                            'name' => 'mars',
                            'route' => 'my_ufo_mars',
                        )
                    )
                )
            )
        );

        $rawMenu = new RawMenu('hello');
        $rawMenu->addItem(array('name' => 'world', 'route' => 'my_route'));
        $rawMenu->addItem(array('name' => 'person 1', 'route' => 'my_child_route_1', 'parent' => 'my_route'));
        $rawMenu->addItem(array('name' => 'person 2', 'route' => 'my_child_route_2', 'parent' => 'my_route'));
        $rawMenu->addItem(array('name' => 'ufo', 'route' => 'my_ufo_route'));
        $rawMenu->addItem(array('name' => 'mars', 'route' => 'my_ufo_mars', 'parent' => 'my_ufo_route'));

        $menu = $this->compiler->compile($rawMenu);

        $this->assertEquals($finalMenu, $menu);
    }
}