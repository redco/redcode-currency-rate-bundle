<?php

namespace RedCode\CurrencyRateBundle\Manager;

use Doctrine\ORM\EntityManager;
use RedCode\Currency\ICurrencyManager;
use RedCode\CurrencyRateBundle\Entity\Currency;

/**
 * @author maZahaca
 */
class CurrencyManager implements ICurrencyManager
{
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
     * @param $code
     */
    public function addCode($code)
    {
        /** @var Currency $currency */
        $currency = new $this->currencyClassName();
        $currency->setCode($code);

        $this->em->persist($currency);
        $this->em->flush();
    }
}
