<?php

namespace RedCode\CurrencyRateBundle\Manager;

use Doctrine\ORM\EntityManager;
use RedCode\Currency\ICurrencyManager;
use RedCode\CurrencyRateBundle\Entity\Currency;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\OptimisticLockException;

/**
 * @author maZahaca
 */
class CurrencyManager implements ICurrencyManager
{
    private static $baseCurrencies = [
        'AUD', 'AZN', 'GBP', 'AMD', 'BYR', 'BGN', 'BRL', 'HUF', 'DKK', 'USD', 'EUR', 'INR', 'KZT', 'CAD', 'KGS', 'CNY',
        'MDL', 'NOK', 'PLN', 'RON', 'XDR', 'SGD', 'TJS', 'TRY', 'TMT', 'UZS', 'UAH', 'CZK', 'SEK', 'CHF', 'ZAR', 'KRW',
        'JPY', 'RUB', 'HRK', 'HKD', 'IDR', 'ILS', 'MXN', 'MYR', 'NZD', 'PHP', 'THB',
    ];

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $currencyClassName;

    public function __construct(EntityManager $em, $currencyClassName)
    {
        $this->em = $em;
        $this->currencyClassName = $currencyClassName;
        if (!$currencyClassName || (!$this->em->getMetadataFactory()->hasMetadataFor($currencyClassName) && !$this->em->getClassMetadata($currencyClassName))) {
            throw new \Exception("Class for currency \"{$currencyClassName}\" not found");
        }
    }

    /** {@inheritdoc} */
    public function getCurrency($code)
    {
        return $this->em->getRepository($this->currencyClassName)->findOneBy(['code' => $code]);
    }

    /** {@inheritdoc} */
    public function getAll()
    {
        return $this->em->getRepository($this->currencyClassName)->findAll();
    }

    /**
     * @return int - Count of create currencies
     *
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     */
    public function createBaseCurrencies()
    {
        $totalCount = 0;

        foreach (self::$baseCurrencies as $code) {
            if (null === $this->em->getRepository($this->currencyClassName)->findOneBy(['code' => $code])) {
                /** @var Currency $currency */
                $currency = new $this->currencyClassName();
                $currency->setCode($code);

                $this->em->persist($currency);
                ++$totalCount;
            }
        }
        $this->em->flush();

        return $totalCount;
    }
}
