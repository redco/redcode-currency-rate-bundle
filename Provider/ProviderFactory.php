<?php

namespace RedCode\CurrencyRateBundle\Provider;

use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;
use RedCode\CurrencyRateBundle\Exception\IncorrectProviderException;

class ProviderFactory
{
    /** @var  array */
    private $providers;

    /**
     * @param ProviderChain $providerChain
     *
     * @throws \Exception
     */
    public function __construct(ProviderChain $providerChain)
    {
        foreach ($providerChain->getProviders() as $provider) {
            if (!($provider instanceof ICurrencyRateProvider)) {
                throw new IncorrectProviderException('Provider must be instance of ICurrencyRateProvider');
            }
            $this->providers[$provider->getName()] = $provider;
        }
    }

    /**
     * @param $name
     *
     * @return null
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->providers)) {
            return $this->providers[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->providers;
    }
}
