# Laravel System Log

[![Latest Version on Packagist](https://img.shields.io/packagist/v/steadfast-collective/laravel-system-log.svg?style=flat-square)](https://packagist.org/packages/steadfast-collective/laravel-system-log)
[![Tests](https://img.shields.io/github/actions/workflow/status/steadfast-collective/laravel-system-log/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/steadfast-collective/laravel-system-log/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/steadfast-collective/laravel-system-log.svg?style=flat-square)](https://packagist.org/packages/steadfast-collective/laravel-system-log)

Provides a System Log model and helpers for contextful logging with FilamentPHP compatibility.

The main interface for this package is the `HasSystemLogger` trait which can be added to any class you want to track.

For example you might add it to an Eloquent Model which is synced over an API and use it to track requests and problems.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-system-log.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-system-log)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require steadfast-collective/laravel-system-log
```

## Usage
You can add HasSystemLogger to any model you want to easily log from.

### Eloquent Model Example
In this example we use SystemLogger to log when a local Eloquent model has been synced with an external API. We track the internal and external references.

```php

class Product extends Model
{
    use HasSystemLogger;

    public function submitRequest($product)
    {
        $this->addSystemLog(
            messasge: 'Making PUT Request',
            'log_level' => 'debug',
            context: [
                'product' => $product,
            ]
        );

        ...
    }

    public function getInternalId()
    {
        return $this->key();
    }

    public function getInternalType()
    {
        return get_class($this);
    }

    public function getExternalId()
    {
        return $this->external_api_id;
    }

    public function getExternalType()
    {
        return 'products';
    }
}

$product = new Product;
$product->addSystemLog('Successfully Sunced',

```

This creates a `SystemLog` model in your database, and also called `Log::$debug($message)`.

The `SystemLog` model will have some properties set:

| Field         | Value                                                       | Description |
| ------------- | ----------------------------------------------------------- | -------------------------------------------------------------- |
| internal_id   | Defaults to $this->key() for Eloquent models                | A unique identifier for the object in your local system        |
| internal_type | Defaults to the classname                                   | A descriptive name for the type of object in your local system |
| external_id.  | Defaults to null - create getExternalId() to set            | A unique identifier for this object in a remote system.        |
| external_type | Defaults to internal_type - create getExternalType() to set | A descriptive name for this type of object in a remote system  |
| log_level     | PSR-7 log levels (defaults to 'info')                       |                                                                |
| message       | Any string of your choosing (required)                      | A description of what is being logged                          |
| context       | An array of related data                                    | Any other data you want to include with this logger            |

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sami Walbury](https://github.com/patabugen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
