<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Generator\Dumper;

use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\MenuCompilerInterface;
use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenuCollection;

class PhpMenuGeneratorDumper implements MenuGeneratorDumperInterface
{
    protected $collection;

    protected $compiler;

    public function __construct(RawMenuCollection $collection, MenuCompilerInterface $compiler)
    {
        $this->collection = $collection;
        $this->compiler = $compiler;
    }

    /**
     * {@inheritDoc}
     */
    public function dump(array $options = array())
    {
        $options = array_merge(array(
            'class'      => 'ProjectMenuGenerator',
            'base_class' => 'BDev\\Bundle\\RoutingExtraBundle\\Extension\\KnpMenu\\Generator\\MenuGenerator',
        ), $options);

        return <<<EOF
<?php

use BDev\Bundle\RoutingExtraBundle\Plugin\KnpMenuBundle\Exception\MenuNotFoundException;

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the BDev Routing UI Bundle.
 */
class {$options['class']} extends {$options['base_class']}
{
    static private \$declaredMenus = {$this->generateDeclaredMenus()};

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

{$this->generateGetMenuMethod()}

{$this->generateHasMenuMethod()}
}

EOF;
    }

    /**
     * Gets the collection to dump.
     *
     * @return \BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenuCollection A RawMenuCollection instance
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Generates PHP code representing an array of defined menus
     * together with the menus properties (e.g. requirements).
     *
     * @return string PHP code
     */
    protected function generateDeclaredMenus()
    {
        $menus = "array(\n";
        foreach ($this->getCollection()->all() as $menu) {
            $name = (string)$menu->getName();
            $properties = $this->compiler->compile($menu);

            $menus .= sprintf("        %s => %s,\n", var_export($name, true), preg_replace("/\n\\s*/", '', var_export($properties, true)));
        }
        $menus .= '    )';

        return $menus;
    }


    /**
     * Generates PHP code representing the `getMenu` method that implements the MenuGeneratorInterface.
     *
     * @return string PHP code
     */
    protected function generateGetMenuMethod()
    {
        return <<<EOF
    public function get(\$name)
    {
        if (!isset(self::\$declaredMenus[\$name])) {
            throw new MenuNotFoundException(sprintf('Menu "%s" does not exist.', \$name));
        }
        return self::\$declaredMenus[\$name];
    }
EOF;
    }

    /**
     * Generates PHP code representing the `hasMenu` method that implements the MenuGeneratorInterface.
     *
     * @return string PHP code
     */
    protected function generateHasMenuMethod()
    {
        return <<<EOF
    public function has(\$name)
    {
        return isset(self::\$declaredMenus[\$name]);
    }
EOF;
    }
}