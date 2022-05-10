<?php

namespace RedCode\CurrencyRateBundle\Command;

use RedCode\CurrencyRateBundle\Manager\CurrencyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class CreateBaseCurrenciesCommand extends Command
{
    private static array $baseCurrencies = [
        'AUD', 'AZN', 'GBP', 'AMD', 'BYR', 'BGN', 'BRL', 'HUF', 'DKK', 'USD', 'EUR', 'INR', 'KZT', 'CAD', 'KGS', 'CNY',
        'MDL', 'NOK', 'PLN', 'RON', 'XDR', 'SGD', 'TJS', 'TRY', 'TMT', 'UZS', 'UAH', 'CZK', 'SEK', 'CHF', 'ZAR', 'KRW',
        'JPY', 'RUB', 'HRK', 'HKD', 'IDR', 'ILS', 'MXN', 'MYR', 'NZD', 'PHP', 'THB',
    ];

    protected static $defaultName = 'redcode:create:base:currencies';

    private CurrencyManager $currencyManager;

    public function __construct(CurrencyManager $currencyManager)
    {
        parent::__construct();

        $this->currencyManager = $currencyManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setDescription('Create base currencies');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $totalCount = 0;

        foreach (self::$baseCurrencies as $code) {
            if (null === $this->currencyManager->getCurrency($code)) {
                $this->currencyManager->addCurrency($code);
                ++$totalCount;
            }
        }

        $output->writeln(sprintf('%s currencies created.', $totalCount));
    }
}
