<?php

namespace RedCode\CurrencyRateBundle\Provider;

use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;

class ProviderChain
{
    private $providers;

    public function __construct()
    {
        $this->providers = array();
    }

    public function addProvider(ICurrencyRateProvider $provider)
    {
        $this->providers[] = $provider;
    }

    public function getProviders()
    {
        return $this->providers;
    }
}