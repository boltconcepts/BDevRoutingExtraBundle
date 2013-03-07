<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\Extension\Breadcrumb\Twig\Extension;

use BDev\Bundle\RoutingExtraBundle\Extension\Breadcrumb\Twig\Extension\BreadcrumbExtension;
use Symfony\Component\DependencyInjection\Container;

class BreadcrumbExtensionTest extends \PHPUnit_Framework_TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    protected $environment;

    /**
     * @var BreadcrumbExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->collection = $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteCollectionInterface');
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->environment = $this->getMock('Twig_Environment');

        $this->container = new Container();
        $this->extension = new BreadcrumbExtension();

        $provider = $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteProviderInterface');
        $provider->expects($this->any())->method('getCollection')->will($this->returnValue($this->collection));

        $this->container->set('bdev_routing_extra.provider', $provider);
        $this->container->set('request', $this->request);
        $this->container->set('router', $this->router);

        $this->extension->setContainer($this->container);
        $this->extension->initRuntime($this->environment);
    }

    public function tearDown()
    {
        $this->container = null;
        $this->extension = null;
        $this->collection = null;
        $this->request = null;
        $this->router = null;
        $this->environment = null;
    }

    public function testRenderEmpty()
    {
        $template = $this->getMock('\Twig_TemplateInterface');
        $template->expects($this->once())->method('render')->with(array('breadcrumbs' => array(), 'options' => $this->getDefaultOptions()));

        $this->request->expects($this->atLeastOnce())->method('get')
            ->will(
                $this->returnValueMap(
                    array(
                        array('_route', 'my_route'),
                        array('_route_params', array())
                    )
                )
            );
        $this->router->expects($this->never())->method('generate');

        $this->environment->expects($this->once())->method('loadTemplate')->with('BDevRoutingExtraBundle::bdev_breadcrumb.html.twig')->will($this->returnValue($template));

        $this->extension->render(array());
    }

    protected static function getDefaultOptions()
    {
        return array(
            'label_attribute' => array('breadcrumb', 'label'),
            'showSingle' => true,
            'listClass' => 'breadcrumb',
            'separator' => '>',
            'separatorClass' => 'divider',
            'template' => 'BDevRoutingExtraBundle::bdev_breadcrumb.html.twig'
        );
    }
}