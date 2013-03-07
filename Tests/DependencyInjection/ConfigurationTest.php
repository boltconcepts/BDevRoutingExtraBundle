<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\DependencyInjection;

use BDev\Bundle\RoutingExtraBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array(array()));

        $this->assertEquals(
            self::getBundleDefaultConfig(),
            $config
        );
    }

    protected static function getBundleDefaultConfig()
    {
        return array(
            'resource' => '%kernel.root_dir%/config/routing.ext.yml',
            'twig' => true,
            'context' => array(
                'enabled' => false,
                'items' => array('label', 'summary')
            ),
            'breadcrumb' => array(
                'enabled' => false,
                'label_attribute' => array('breadcrumb')
            ),
            'knp_menu' => array(
                'enabled' => false,
                'menus' => array()
            )
        );
    }
}