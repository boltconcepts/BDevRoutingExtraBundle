<?php
namespace BDev\Bundle\RoutingExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BDevRoutingExtraExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        $container->setParameter('bdev_routing_extra.resource', $config['resource']);

        if ($config['twig']) {
            $loader->load('services_twig.yml');

            if ($config['breadcrumb']['enabled']) {
                $loader->load('services_breadcrumb.yml');

                $container->setParameter('bdev_routing_extra.breadcrumb.options', array(
                    'label_attribute' => $config['breadcrumb']['label_attribute']
                ));
            }
        }

        if ($config['knp_menu']['enabled']) {
            $loader->load('services_knpmenu.yml');
            $container->setParameter('bdev_routing_extra.knp_menu.options.menus', $config['knp_menu']['menus']);
        }
    }

    public function getAlias()
    {
        return 'bdev_routing_extra';
    }
}
