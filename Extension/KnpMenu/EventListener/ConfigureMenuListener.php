<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\EventListener;

use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;

class ConfigureMenuListener
{
    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $dispatcher = $event->getDispatcher();

        if ($menu->getCurrentUri() === null && $dispatcher instanceof ContainerAwareEventDispatcher) {
            $menu->setCurrentUri($dispatcher->getContainer()->get('request')->getRequestUri());
        }
    }
}
