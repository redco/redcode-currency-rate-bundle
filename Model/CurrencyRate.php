<?php
namespace RedCode\CurrencyRateBundle\Model;

use RedCode\Currency\ICurrency;
use RedCode\Currency\Rate\ICurrencyRate;
use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;

/**
 * @author maZahaca
 */
abstract class CurrencyRate implements ICurrencyRate
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var int
     */
    protected $nominal;

    /**
     * @var float
     */
    protected $rate;

    /**
     * @var ICurrency
     */
    protected $currency;

    /**
     * @var string
     */
    protected $providerName;

    public function __construct(ICurrency $currency, ICurrencyRateProvider $provider, \DateTime $date, $rate, $nominal)
    {
        $this->currency     = $currency;
        $this->providerName = $provider->getName();
        $this->date         = $date;
        $this->nominal      = $nominal;
        $this->rate         = $rate;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function getNominal()
    {
        return $this->nominal;
    }

    /**
     * @inheritdoc
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @inheritdoc
     */
    public function getProviderName()
    {
        return $this->providerName;
    }
}
