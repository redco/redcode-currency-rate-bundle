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
        $totalCount = $this->getContainer()->get('redcode.currency.manager')->createBaseCurrencies();

        $output->writeln(sprintf('%s currencies created.', $totalCount));
    }
}
