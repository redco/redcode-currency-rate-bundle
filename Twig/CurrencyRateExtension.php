<?php

namespace RedCode\CurrencyRateBundle\Twig;

use RedCode\Currency\Rate\CurrencyConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author maZahaca
 */
class CurrencyRateExtension extends AbstractExtension
{
    private CurrencyConverter $converter;

    public function __construct(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
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
