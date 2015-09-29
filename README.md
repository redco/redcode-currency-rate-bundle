Symfony2 bundle - currency rate loader
============================
## Steps to start ##
1. Install module to your app [from packagist](https://packagist.org/packages/redcode/currency-rate-bundle)
2. Add bundle into kernel

```php
$bundles = array(
    ...
    new \RedCode\CurrencyRateBundle\RedCodeCurrencyRateBundle(),
    ...
);
```

3\. Create Currency and CurrencyRate classes:

```php
/**
* @ORM\Entity
*/
class Currency extends \RedCode\CurrencyRateBundle\Entity\Currency
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;
}
```

```php
/**
 * @ORM\Entity
 */
class CurrencyRate extends \RedCode\CurrencyRateBundle\Entity\CurrencyRate
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var int
     */
    protected $nominal;

    /**
     * @var float
     */
    protected $rate;

    /**
     * @var \RedCode\Currency\ICurrency
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    protected $currency;

    /**
     * @var string
     */
    protected $providerName;
}
```
4\. Add section into config.yml file:

```yml
redcode_currency_rate:
    currency_rate_class: NameSpasePath\CurrencyRate
    currency_class: NameSpasePath\Currency
```

5\. Just run the commands:

a\. To create base currencies:
```console
./app/console redcode:create:base:currencies
```

b\. To create load currency rates:
```console
./app/console redcode:currency:rate:load
```

6\. And now, you can call currency rate converter by name - redcode.currency.rate.converter

```php
$converter = $container->get('redcode.currency.rate.converter');
$convertedValue = $converter->convert('USD', 'EUR', $value);
```
