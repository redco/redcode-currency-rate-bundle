<?php

namespace RedCode\CurrencyRateBundle\Command;

use RedCode\Currency\Rate\ICurrencyRate;
use RedCode\Currency\Rate\Provider\ICurrencyRateProvider;
use RedCode\Currency\Rate\Provider\ProviderFactory;
use RedCode\CurrencyRateBundle\Manager\CurrencyManager;
use RedCode\CurrencyRateBundle\Manager\CurrencyRateManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Exception\ProviderNotFoundException;

/**
 * @author maZahaca
 */
class LoadCurrencyRatesCommand extends Command
{
    protected static $defaultName = 'redcode:currency:rate:load';

    protected CurrencyRateManager $currencyRateManager;
    protected CurrencyManager $currencyManager;
    protected ProviderFactory $providerFactory;

    public function __construct(
        CurrencyRateManager $currencyRateManager,
        CurrencyManager $currencyManager,
        ProviderFactory $providerFactory
    ) {
        parent::__construct();

        $this->currencyRateManager = $currencyRateManager;
        $this->currencyManager = $currencyManager;
        $this->providerFactory = $providerFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('providerName', InputArgument::OPTIONAL, 'Provider name for rates (cbr)', null)
            ->addArgument('date', InputArgument::OPTIONAL, 'Date for loading rates in format YYYY-MM-DD (default date now)', date('Y-m-d'))
            ->setDescription('Load currency rates from cbr.ru');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providerName = $input->getArgument('providerName');

        $date = $input->getArgument('date');
        $date = new \DateTime($date);

        $rates = $this->updateRates($date, $providerName);
        foreach ($rates as $rate) {
            $output->writeln(sprintf('%s. Loaded rate for %s', $rate->getProviderName(), $rate->getCurrency()->getCode()));
        }
    }

    /**
     * @param \DateTime|null $date
     * @param ICurrencyRateProvider|string|null $provider
     *
     * @return ICurrencyRate[]
     * @throws ProviderNotFoundException
     *
     */
    public function updateRates($date = null, $provider = null)
    {
        if (!$date || !($date instanceof \DateTime)) {
            $date = new \DateTime();
        }

        if ($provider && !($provider instanceof ICurrencyRateProvider)) {
            if (is_string($provider)) {
                $providerName = $provider;
                $provider = $this->providerFactory->get($provider);
                if (!($provider instanceof ICurrencyRateProvider)) {
                    throw new ProviderNotFoundException("CurrencyRateProvider for name {$providerName} not found");
                }
            } else {
                throw new ProviderNotFoundException('CurrencyRateProvider not found');
            }
        }

        $providers = $provider === null ? $this->providerFactory->getAll() : [$provider];

        $resultRates = [];
        foreach ($providers as $provider) {
            /* @var $provider ICurrencyRateProvider */
            $rates = $provider->getRates($this->currencyManager->getAll(), $date);
            $this->currencyRateManager->saveRates($rates);
            $resultRates = array_merge($resultRates, $rates);
        }

        return $resultRates;
    }
}
