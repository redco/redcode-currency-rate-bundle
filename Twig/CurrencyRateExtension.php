<?php

namespace RedCode\CurrencyRateBundle\Twig;

use RedCode\Currency\Rate\CurrencyConverter;

/**
 * @author maZahaca
 */
class CurrencyRateExtension extends \Twig_Extension
{
    /**
     * @var CurrencyConverter
     */
    private $converter;

    public function __construct(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'currency.rate';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'rc_currency_convert',
                function ($twigOptions, $from, $to, $value, $provider = null) {
                    return $this->converter->convert(
                        $from,
                        $to,
                        $value,
                        $provider,
                        false
                    );
                },
                [
                    'needs_environment' => false,
                    'needs_context' => true,
                ]
            ),
        ];
    }
}
