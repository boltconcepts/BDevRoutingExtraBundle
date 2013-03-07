<?php
namespace BDev\Bundle\RoutingExtraBundle;

use BDev\Bundle\RoutingExtraBundle\DependencyInjection\BDevRoutingExtraExtension;
use BDev\Bundle\RoutingExtraBundle\DependencyInjection\Compiler\AddCacheWarmerPass;
use BDev\Bundle\RoutingExtraBundle\DependencyInjection\Compiler\ResolverPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class BDevRoutingExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ResolverPass());
        $container->addCompilerPass(new AddCacheWarmerPass());
    }

    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new BDevRoutingExtraExtension();
        }
        return $this->extension;
    }
}
