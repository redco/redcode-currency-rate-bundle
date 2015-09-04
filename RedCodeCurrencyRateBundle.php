<?php

namespace RedCode\CurrencyRateBundle;

use RedCode\CurrencyRateBundle\DependencyInjection\Compiler\RegisterProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RedCodeCurrencyRateBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterProvidersPass());
    }
}
