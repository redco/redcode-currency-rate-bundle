<?php

namespace RedCode\CurrencyRateBundle;

use RedCode\CurrencyRateBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RedCodeCurrencyRateBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderCompilerPass());
    }
}
