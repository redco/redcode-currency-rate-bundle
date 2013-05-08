<?php

namespace RedCode\CurrencyRateBundle\Command;

use Doctrine\ORM\EntityManager;
use RedCode\Currency\CurrencyManager;
use RedCode\Currency\Rate\CurrencyRateManager;
use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author maZahaca
 */
class LoadCurrencyRatesCommand extends ContainerAwareCommand {

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
                ->setName('redcode:currency:rate:load')
                ->addArgument('providerName', InputArgument::OPTIONAL, 'Provider name for rates (cbr)', 'cbr')
                ->addArgument('date', InputArgument::OPTIONAL, 'Date for loading rates in format YYYY-MM-DD (default date now)', date('Y-m-d'))
                ->setDescription('Load currency rates from cbr.ru')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providerName = $input->getArgument('providerName');

        $date = $input->getArgument('date');
        $date = new \DateTime($date);

        /** @var $provider ICurrencyRateProvider */
        $provider = $this->getContainer()->get('redcode.currency.rate.provider.factory')->get($providerName);
        if(!$provider) {
            throw new \Exception("CurrencyRateProvider for name {$providerName} not found");
        }

        /** @var $currencyManager CurrencyManager */
        $currencyManager = $this->getContainer()->get('redcode.currency.manager');

        /** @var $currencyRateManager CurrencyRateManager */
        $currencyRateManager = $this->getContainer()->get('redcode.currency.rate.manager');
        $rates = $provider->getRates($currencyManager->getAll(), $date);
        $currencyRateManager->saveRates($rates);
        if(count($rates)) {
            $output->writeln('Loaded rates for ' . implode(', ', array_keys($rates)));
        }
        else {
            $output->writeln('Nothing loaded');
        }
    }

    /**
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
