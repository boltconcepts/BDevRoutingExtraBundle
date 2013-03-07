<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\RouterInterface;

class MenuCompiler implements MenuCompilerInterface
{
    /**
     * Compile a menu to it's array representation that can be used by @see \Knp\Menu\MenuFactory
     *
     * @param RawMenu $menu
     * @return array
     */
    public function compile(RawMenu $menu)
    {
        $itemTree = $this->buildTree($menu->getItems());

        $options = array();
        foreach ($menu->getOptions() as $k => $v) {
            $options[lcfirst(Container::camelize($k))] = $v;
        }

        return array_merge(
            $options,
            array(
                'name' => $menu->getName(),
                'children' => $itemTree
            )
        );
    }

    protected function buildTree(array $items)
    {
        $tree = array();

        $references = array();
        foreach ($items as &$item) {
            if (isset($references[$item['route']])) {
                $item['children'] = $references[$item['route']]['children'];
            }
            $references[$item['route']] = &$item;
            if ($item['parent'] === null) {
                $tree[$item['route']] = &$item;
            } else {
                $references[$item['parent']]['children'][] = &$item;
            }

            unset($item['parent']);
            if (isset($item['uri'])) {
                unset($item['route']);
            }
        }

        return $tree;
    }
}