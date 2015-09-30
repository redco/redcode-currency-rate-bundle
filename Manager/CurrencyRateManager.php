<?php

namespace RedCode\CurrencyRateBundle\Manager;

use Doctrine\ORM\EntityManager;
use RedCode\Currency\ICurrency;
use RedCode\Currency\Rate\ICurrencyRate;
use RedCode\Currency\Rate\ICurrencyRateManager;
use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;

/**
 * @author maZahaca
 */
class CurrencyRateManager implements ICurrencyRateManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $currencyRateClassName;

    public function __construct(EntityManager $em, $currencyRateClassName)
    {
        $this->em = $em;
        $this->currencyRateClassName = $currencyRateClassName;
        if (!$currencyRateClassName || (!$this->em->getMetadataFactory()->hasMetadataFor($currencyRateClassName) && !$this->em->getClassMetadata($currencyRateClassName))) {
            throw new \Exception("Class for currency rate \"{$currencyRateClassName}\" not found");
        }
    }

    private static $reflection;

    /**
     * Get reflection class.
     *
     * @return \ReflectionClass
     */
    public function reflectionClass()
    {
        return self::$reflection ?: self::$reflection = new \ReflectionClass($this->currencyRateClassName);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance(ICurrency $currency, ICurrencyRateProvider $provider, \DateTime $date, $rate, $nominal)
    {
        $dateNew = clone $date;
        $dateNew->setTime(0, 0, 0);
        $newRate = $this->reflectionClass()->newInstance();
        $newRate->setCurrency($currency);
        $newRate->setProviderName($provider->getName());
        $newRate->setDate($date);
        $newRate->setRate($rate);
        $newRate->setNominal($nominal);

        return $newRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate(ICurrency $currency, ICurrencyRateProvider $provider, \DateTime $rateDate = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('r')
            ->from($this->currencyRateClassName, 'r')
            ->leftJoin('r.currency', 'c')
            ->where($qb->expr()->eq('c.code', ':currency'))
            ->andWhere($qb->expr()->eq('r.providerName', ':provider'))
            ->setParameters(['currency' => $currency->getCode(), 'provider' => $provider->getName()])
            ->orderBy('r.date', 'DESC')
        ;
        if (isset($rateDate)) {
            $qb
                ->andWhere($qb->expr()->eq('r.date', ':date'))
                ->setParameter('date', $rateDate->format('Y-m-d 00:00:00'));
        }
        $result = $qb->getQuery()->getResult();
        $result = reset($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRates($rates)
    {
        if (is_array($rates)) {
            foreach ($rates as &$rate) {
                /* @var ICurrencyRate $rate */
                $found = $this->em->getRepository($this->currencyRateClassName)->findBy([
                    'date' => $rate->getDate(),
                    'providerName' => $rate->getProviderName(),
                    'currency' => $rate->getCurrency()->getId(),
                ]);
                if (count($found)) {
                    $found = reset($found);
                    $found->setRate($rate->getRate());
                    $found->setNominal($rate->getNominal());
                    $rate = $found;
                }
                $this->em->persist($rate);
            }
            $this->em->flush();
        }
    }
}
