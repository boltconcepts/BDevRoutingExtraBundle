<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\DependencyInjection\Compiler;

use BDev\Bundle\RoutingExtraBundle\DependencyInjection\Compiler\AddCacheWarmerPass;
use Symfony\Component\DependencyInjection\Definition;

class AddCacheWarmerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testNoWarmer()
    {
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false));

        $resolverPass = new AddCacheWarmerPass();
        $resolverPass->process($builder);
    }

    public function testWarmer()
    {
        $services = array(
            'my_warmer' => array(),
            'my_warmer_service' => array(array('priority' => 10)),
        );

        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));

        // fake the getDefinition()
        $definition = new Definition('Symfony\Component\Config\Loader\LoaderResolver', array(array()));
        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $resolverPass = new AddCacheWarmerPass();
        $resolverPass->process($builder);

        $argument = $definition->getArgument(0);
        $this->assertCount(2, $argument);
        $this->assertEquals('my_warmer_service', (string)$argument[0]);
        $this->assertEquals('my_warmer', (string)$argument[1]);
    }
}