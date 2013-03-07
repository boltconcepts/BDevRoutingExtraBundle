<?php
namespace BDev\Bundle\RoutingExtraBundle\Dumper;

use BDev\Bundle\RoutingExtraBundle\RawRouteCollection;
use BDev\Bundle\RoutingExtraBundle\RouteCompilerInterface;

class PhpCollectionDumper implements CollectionDumperInterface
{
    protected $collection;

    protected $routeCompiler;

    public function __construct(RawRouteCollection $collection, RouteCompilerInterface $compiler)
    {
        $this->collection = $collection;
        $this->routeCompiler = $compiler;
    }

    /**
     * {@inheritDoc}
     */
    public function dump(array $options = array())
    {
        $options = array_merge(
            array(
                'class'      => 'ProjectRoutingExtraCollection',
                'base_class' => 'BDev\\Bundle\\RoutingExtraBundle\\RouteCollection',
            ),
            $options
        );

        return <<<EOF
<?php

use BDev\\Bundle\\RoutingExtraBundle\\Route;

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the BDev Routing UI Bundle.
 */
class {$options['class']} extends {$options['base_class']}
{
    static private \$declaredRoutes = {$this->generateDeclaredRoutes()};

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

{$this->generateGetRouteMethod()}

{$this->generateGetParentMethod()}
}

EOF;
    }

    /**
     * Generates PHP code representing an array of defined routes
     * together with the route properties (e.g. requirements).
     *
     * @return string PHP code
     */
    protected function generateDeclaredRoutes()
    {
        $routes = "array(\n";
        foreach ($this->getCollection()->all() as $routeInfo) {
            $route = $this->routeCompiler->compile($routeInfo);

            $name = (string)$route->getRoute();

            $properties = array(
                'route' => $name,
                'routeRequirements' => $route->getRouteRequirements(),
                'parent' => $route->getParent(),
                'options' => $route->getOptions()
            );

            $routes .= sprintf("        %s => %s,\n", var_export($name, true), preg_replace("/\n\\s*/", '', var_export($properties, true)));
        }
        $routes .= '    )';

        return $routes;
    }

    /**
     * Generates PHP code representing the `getRoute` method that implements the CollectionGeneratorInterface.
     *
     * @return string PHP code
     */
    protected function generateGetRouteMethod()
    {
        return <<<EOF
    public function getRoute(\$route)
    {
        if (!isset(self::\$declaredRoutes[\$route])) {
            return null;
        }
        \$info = self::\$declaredRoutes[\$route];
        return new Route(
            \$info['route'],
            \$info['options'],
            \$info['parent'],
            \$info['routeRequirements']
        );
    }
EOF;
    }

    /**
     * Generates PHP code representing the `getParent` method that implements the CollectionGeneratorInterface.
     *
     * @return string PHP code
     */
    protected function generateGetParentMethod()
    {
        return <<<EOF
    public function getParent(\$route)
    {
        return isset(self::\$declaredRoutes[\$route]) ? \$this->getRoute(self::\$declaredRoutes[\$route]['parent']) : null;
    }
EOF;
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection()
    {
        return $this->collection;
    }
}