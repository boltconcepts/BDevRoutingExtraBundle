<?php
namespace BDev\Bundle\RoutingExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();
        $root = $tb->root('bdev_routing_extra');

        $root
            ->children()
                ->scalarNode('resource')->cannotBeEmpty()->defaultValue('%kernel.root_dir%/config/routing.ext.yml')->end()
                ->booleanNode('twig')->defaultTrue()->end()

                ->arrayNode('context')
                    ->info('Route context configuration')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('items')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('label', 'summary'))
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('breadcrumb')
                    ->info('Breadcrumb configuration')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('label_attribute')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('breadcrumb'))
                            ->beforeNormalization()
                                ->ifTrue(function($v) { return !is_array($v); })
                                ->then(function($v) { return array($v); })
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('knp_menu')
                    ->info('KnpMenuBundle configuration')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('menus')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('label')->end()
                                ->variableNode('attributes')->end()
                                ->variableNode('link_attributes')->end()
                                ->variableNode('children_attributes')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $tb;
    }
}
