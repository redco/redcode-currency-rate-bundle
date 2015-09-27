<?php

namespace RedCode\CurrencyRateBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('redcode.currency.rate.provider_chain')) {
            return;
        }

        $definition = $container->findDefinition(
            'redcode.currency.rate.provider_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'redcode.currency.rate.provider'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addProvider',
                array(new Reference($id))
            );
        }
    }
}