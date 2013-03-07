<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\Breadcrumb\Twig\Extension;

use BDev\Bundle\RoutingExtraBundle\Plugin\Page\Generator\PageGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_Template;

class BreadcrumbExtension extends Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $options;

    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge(
            array(
                'label_attribute' => array('breadcrumb', 'label'),
                'showSingle' => true,
                'listClass' => 'breadcrumb',
                'separator' => '>',
                'separatorClass' => 'divider',
                'template' => 'BDevRoutingExtraBundle::bdev_breadcrumb.html.twig'
            ),
            $options
        );
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'route_breadcrumb' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
        );
    }

    public function render(array $options)
    {
        $options = array_merge($this->options, $options);

        $router = $this->getRouter();
        $collection = $this->getRouteCollection();

        $route = $collection->getRoute($this->getRequest()->get('_route'));
        $currentRouteParams = $this->getRequest()->get('_route_params');

        $breadcrumbs = array();
        while ($route !== null) {
            $label = $route->getRoute();
            foreach ($options['label_attribute'] as $option) {
                if ($route->hasOption($option)) {
                    $label = $route->getOption($option);
                    break;
                }
            }

            $routeParams = array_intersect_key($currentRouteParams, array_flip($route->getRouteRequirements()));

            $breadcrumbs[$label] = $router->generate($route->getRoute(), $routeParams);

            $route = $collection->getParent($route->getRoute());
        }
        $breadcrumbs = array_reverse($breadcrumbs, true);

        return $this->environment
            ->loadTemplate($options['template'])
            ->render(array('breadcrumbs' => $breadcrumbs, 'options' => $options));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'bdev_routing_extra_breadcrumb';
    }

    /**
     * @return \BDev\Bundle\RoutingExtraBundle\RouteCollection
     */
    protected function getRouteCollection()
    {
        return $this->container->get('bdev_routing_extra.provider')->getCollection();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    protected function getRouter()
    {
        return $this->container->get('router');
    }
}