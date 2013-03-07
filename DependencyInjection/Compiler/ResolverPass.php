<?php
namespace BDev\Bundle\RoutingExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('bdev_routing_extra.resolver')) {
            return;
        }

        $definition = $container->getDefinition('bdev_routing_extra.resolver');

        foreach ($container->findTaggedServiceIds('bdev_routing_extra.loader') as $id => $attributes) {
            $definition->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}