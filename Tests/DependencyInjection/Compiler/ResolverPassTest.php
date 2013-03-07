<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\DependencyInjection\Compiler;

use BDev\Bundle\RoutingExtraBundle\DependencyInjection\Compiler\ResolverPass;
use Symfony\Component\DependencyInjection\Definition;

class ResolverPassTest extends \PHPUnit_Framework_TestCase
{
    public function testNoResolver()
    {
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false));

        $resolverPass = new ResolverPass();
        $resolverPass->process($builder);
    }

    public function testResolver()
    {
        $services = array(
            'my_resolver_service' => array(),
        );

        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));

        // fake the getDefinition()
        $definition = new Definition('Symfony\Component\Config\Loader\LoaderResolver');
        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));

        $resolverPass = new ResolverPass();
        $resolverPass->process($builder);

        // grab the method calls off of the "profiler" definition
        $methodCalls = $definition->getMethodCalls();
        $this->assertCount(1, $methodCalls);
        $this->assertEquals('addLoader', $methodCalls[0][0]); // grab the method part of the first call
    }
}