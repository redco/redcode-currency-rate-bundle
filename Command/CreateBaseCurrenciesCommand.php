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

class CreateBaseCurrenciesCommand extends ContainerAwareCommand
{
    private static $baseCurrencies = [
        'AUD', 'AZN', 'GBP', 'AMD', 'BYR', 'BGN', 'BRL', 'HUF', 'DKK', 'USD', 'EUR', 'INR', 'KZT', 'CAD', 'KGS', 'CNY',
        'MDL', 'NOK', 'PLN', 'RON', 'XDR', 'SGD', 'TJS', 'TRY', 'TMT', 'UZS', 'UAH', 'CZK', 'SEK', 'CHF', 'ZAR', 'KRW',
        'JPY', 'RUB', 'HRK', 'HKD', 'IDR', 'ILS', 'MXN', 'MYR', 'NZD', 'PHP', 'THB',
    ];

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('redcode:create:base:currencies')
            ->setDescription('Create base currencies');
    }

    /**
     * {@inheritdoc}
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
        $doctrine = $this->getContainer()->get('doctrine');
        $currencyManager = $this->getContainer()->get('redcode.currency.manager');
        $className = $this->getContainer()->getParameter('redcode.currency.class');

        $totalCount = 0;

        foreach (self::$baseCurrencies as $code) {
            if (null === $doctrine->getRepository($className)->findOneBy(['code' => $code])) {
                $currencyManager->addCode($code);
                ++$totalCount;
            }
        }

        $output->writeln(sprintf('%s currencies created.', $totalCount));
    }
}
