<?php
namespace RedCode\CurrencyRateBundle\Entity;

use RedCode\CurrencyRateBundle\Model\Currency as BaseCurrency;

/**
 * @author maZahaca
 */
abstract class Currency extends BaseCurrency
{
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getId()
    {
        return $this->id;
    }
}
