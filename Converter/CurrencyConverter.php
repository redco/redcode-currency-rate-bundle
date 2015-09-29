<?php

namespace RedCode\CurrencyRateBundle\Converter;

use RedCode\Currency\ICurrency;
use RedCode\Currency\ICurrencyManager;
use RedCode\Currency\Rate\Exception\CurrencyNotFoundException;
use RedCode\Currency\Rate\Exception\ProviderNotFoundException;
use RedCode\Currency\Rate\Exception\RateNotFoundException;
use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;
use RedCode\CurrencyRateBundle\Provider\ProviderFactory;
use RedCode\Currency\Rate\ICurrencyRateManager;
use RedCode\Currency\Rate\ICurrencyRate;

class CurrencyConverter
{
    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    /**
     * @var ICurrencyRateManager
     */
    private $rateManager;

    /**
     * @var ICurrencyManager
     */
    private $currencyManager;

    /**
     * @param ProviderFactory $providerFactory
     * @param ICurrencyRateManager $rateManager
     * @param ICurrencyManager $currencyManager
     */
    public function __construct(ProviderFactory $providerFactory, ICurrencyRateManager $rateManager, ICurrencyManager $currencyManager)
    {
        $this->providerFactory  = $providerFactory;
        $this->rateManager      = $rateManager;
        $this->currencyManager  = $currencyManager;
    }

    /**
     * Convert value in different currency
     * @param ICurrency|string $from ICurrency object or currency code
     * @param ICurrency|string $to ICurrency object or currency code
     * @param float $value value to convert in currency $from
     * @param string|null $provider provider name
     * @param \DateTime|bool|null $rateDate Date for rate (default - today, false - any date)
     * @throws ProviderNotFoundException
     * @throws RateNotFoundException
     * @throws CurrencyNotFoundException
     * @return float
     */
    public function convert($from, $to, $value, $provider = null, $rateDate = null)
    {
        $to         = $this->getCurrency($to);
        $from       = $this->getCurrency($from);
        $providers  = $provider === null ? $this->providerFactory->getAll() : [$this->providerFactory->get($provider)];
        $providers  = array_filter($providers);
        if (!count($providers)) {
            throw new ProviderNotFoundException($provider);
        }

        if (null === $rateDate) {
            $rateDate = new \DateTime();
        } else {
            $rateDate = ($rateDate instanceof \DateTime)
                ? $rateDate
                : new \DateTime();
        }

        $rateDate->setTime(0, 0, 0);

        $foundValue = null;

        $errorParams = [
            'currency' => null,
            'provider' => null
        ];

        foreach ($providers as $provider) {
            /** @var ICurrencyRateProvider $provider */
            if (!$provider->getBaseCurrency()) {
                throw new ProviderNotFoundException($provider->getName());
            }

            if ($from->getCode() !== $provider->getBaseCurrency()->getCode()) {
                /** @var ICurrencyRate $fromRate  */
                $fromRate = $this->rateManager->getRate($from, $provider, $rateDate);
                if (!$fromRate) {
                    $errorParams['currency'] = $from;
                    $errorParams['provider'] = $provider;
                    continue;
                }

                if (!$provider->isInversed()) {
                    $valueBase = $value / ($fromRate->getRate() * $fromRate->getNominal());
                } else {
                    $valueBase = $fromRate->getRate() / $fromRate->getNominal() * $value;
                }
            } else {
                $valueBase = $value;
            }

            if ($to->getCode() !== $provider->getBaseCurrency()->getCode()) {
                /** @var ICurrencyRate $toRate  */
                $toRate = $this->rateManager->getRate($to, $provider, $rateDate);
                if (!$toRate) {
                    $errorParams['currency'] = $to;
                    $errorParams['provider'] = $provider;
                    continue;
                }
                if (!$provider->isInversed()) {
                    $toRate = $toRate->getNominal() * $toRate->getRate();
                } else {
                    $toRate = $toRate->getNominal() / $toRate->getRate();
                }
            } else {
                $toRate = 1.0;
            }

            $foundValue = $toRate * $valueBase;

            if ($foundValue !== null) {
                break;
            }
        }

        if ($foundValue === null) {
            throw new RateNotFoundException($errorParams['currency'], $errorParams['provider'], $rateDate);
        }

        return $foundValue;
    }

    /**
     * @param string|ICurrency $currency
     * @return ICurrency
     * @throws CurrencyNotFoundException
     */
    protected function getCurrency($currency)
    {
        if (!($currency instanceof ICurrency)) {
            $code = $currency;
            $currency = $this->currencyManager->getCurrency($code);
            if (!($currency instanceof ICurrency)) {
                throw new CurrencyNotFoundException($code);
            }
        }
        return $currency;
    }
}