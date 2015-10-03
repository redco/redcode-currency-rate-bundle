<?php

namespace RedCode\CurrencyRateBundle\Entity;

use RedCode\CurrencyRateBundle\Model\Currency as BaseCurrency;

/**
 * @author maZahaca
 */
abstract class Currency extends BaseCurrency
{
    /**
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
