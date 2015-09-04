<?php

namespace RedCode\CurrencyRateBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('redcode.currency.rate.provider.factory')) {
            return;
        }

        $definition = $container->getDefinition('redcode.currency.rate.provider.factory');

        $providers = [];
        foreach ($container->findTaggedServiceIds('redcode.currency.rate.provider') as $id => $tagAttributes) {
            $providers[] = $id;
        }

        //add providers if any
        if (!empty($providers)) {
            //add method call for each registered provider
            foreach ($providers as $id) {
                $definition->addMethodCall('addProvider', [new Reference($id)]);
            }
        }
    }
}
