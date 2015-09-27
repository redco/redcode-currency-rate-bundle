<?php

namespace RedCode\CurrencyRateBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\OptimisticLockException;
use RedCode\CurrencyRateBundle\Entity\Currency;

class CreateBaseCurrenciesCommand extends ContainerAwareCommand
{
    private static $baseCurrencies = [
        'AUD', 'AZN', 'GBP', 'AMD', 'BYR', 'BGN', 'BRL', 'HUF', 'DKK', 'USD', 'EUR', 'INR', 'KZT', 'CAD', 'KGS', 'CNY',
        'MDL', 'NOK', 'PLN', 'RON', 'XDR', 'SGD', 'TJS', 'TRY', 'TMT', 'UZS', 'UAH', 'CZK', 'SEK', 'CHF', 'ZAR', 'KRW',
        'JPY', 'RUB', 'HRK', 'HKD', 'IDR', 'ILS', 'MXN', 'MYR', 'NZD', 'PHP', 'THB'
    ];

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('redcode:create:base:currencies')
            ->setDescription('Create base currencies');
    }

    /**
     * {@inheritDoc}
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws \LogicException
     * @throws InvalidArgumentException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $currencyClass = $this->getContainer()->getParameter('redcode.currency.class');

        $totalCount = 0;

        foreach (self::$baseCurrencies as $code) {
            if (null === $em->getRepository($currencyClass)->findOneBy(['code' => $code])) {
                /** @var Currency $currency */
                $currency = new $currencyClass();
                $currency->setCode($code);

                $em->persist($currency);
                $totalCount++;
            }
        }
        $em->flush();

        $output->writeln(sprintf('%s currencies created.', $totalCount));
    }
}
