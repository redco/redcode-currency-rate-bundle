<?php

namespace RedCode\CurrencyRateBundle\Model;

use RedCode\Currency\ICurrency;
use RedCode\Currency\Rate\ICurrencyRate;

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

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public function getNominal()
    {
        return $this->nominal;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

     /**
      * {@inheritdoc}
      */
     public function setDate($date)
     {
         $this->date = $date;

         return $this;
     }

     /**
      * {@inheritdoc}
      */
     public function setNominal($nominal)
     {
         $this->nominal = $nominal;

         return $this;
     }

     /**
      * {@inheritdoc}
      */
     public function setRate($rate)
     {
         $this->rate = $rate;

         return $this;
     }

     /**
      * {@inheritdoc}
      */
     public function setCurrency(ICurrency $currency)
     {
         $this->currency = $currency;

         return $this;
     }

     /**
      * {@inheritdoc}
      */
     public function setProviderName($provider)
     {
         $this->providerName = $provider;

         return $this;
     }
}
