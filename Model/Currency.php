<?php
namespace RedCode\CurrencyRateBundle\Model;

use RedCode\Currency\ICurrency;

/**
 * @author maZahaca
 */
abstract class Currency implements ICurrency
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var float
     */
    protected $code;

    /**
     * Get 3 symbols currency code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
