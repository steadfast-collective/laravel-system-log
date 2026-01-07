# Laravel System Log

[![Latest Version on Packagist](https://img.shields.io/packagist/v/steadfast-collective/laravel-system-log.svg?style=flat-square)](https://packagist.org/packages/steadfast-collective/laravel-system-log)
[![Tests](https://img.shields.io/github/actions/workflow/status/steadfast-collective/laravel-system-log/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/steadfast-collective/laravel-system-log/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/steadfast-collective/laravel-system-log.svg?style=flat-square)](https://packagist.org/packages/steadfast-collective/laravel-system-log)

Provides a System Log model and helpers for contextful logging with FilamentPHP compatibility.

The main interface for this package is the `HasSystemLogger` trait which can be added to any class you want to track from
to expose the `$this->addSystemLog()` method and to any object you want to pass as the `model` parameter to `addSystemLog`
for some automatic context adding.

For example you might add it to an Eloquent Model and the Action which submits it to an external API to track API calls and data.

There is also a `HasSystemLoggerAssertions` trait for use in tests to check that a SystemLog has been created.

## Installation

You can require and setup the packge with these commands:

```bash
composer require steadfast-collective/laravel-system-log

# Publish and run the migrations to create the system_logs table:
php artisan vendor:publish --tag="system-log-migrations"
php artisan migrate

# Publish the config file (optional)
php artisan vendor:publish --force --tag="system-log-config"

# Run the install command to publish the model and factory, and if you have Filament
# installed to create to resource. Use the `--panel=Admin` option to customise which
# Filament panel it's added to.
php artisan system-log:install
```

The generated model and panel are now ready for you to customise to fit into your application.

## Usage
You can add HasSystemLogger to any model you want to easily log from.

### Eloquent Model Example
In this example we use SystemLogger to log when a local Eloquent model has been synced with an external API.

We add `HasSystemLogger` to the both the Model and the API class. The Model so it can provide the internal/external ID details
and the API class for convenient access.

You could only use the system logger on the product and pass it in as the model, but I find this code more readable.

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

class SyncProduct
{
    function handle(Product $product)
    {
        $this->addSystemLog(
            'Syncing Product',
            model: $product,
            context: [
                'changedFields' = $product->getChanged(),
            ],
        );
    }
}

```

This creates a `SystemLog` model in your database, and also called `Log::$debug($message)`.

The `SystemLog` model will have some properties set:

| Field         | Value                                                       | Description |
| ------------- | ----------------------------------------------------------- | ------------------------------------------------------------------- |
| internal_id   | Defaults to $this->key() for Eloquent models                | A unique identifier for the object in your local system             |
| internal_type | Defaults to the classname                                   | A descriptive name for the type of object in your local system      |
| external_id.  | Defaults to null - create getExternalId() to set            | A unique identifier for this object in a remote system.             |
| external_type | Defaults to internal_type - create getExternalType() to set | A descriptive name for this type of object in a remote system       |
| log_level     | PSR-7 log levels (defaults to 'info')                       |                                                                     |
| code          | Any string of your choosing (required)                      | A code which represents what type of item this is (e.g. SYNC_ERROR) |
| message       | Any string of your choosing (required)                      | A description of what is being logged                               |
| context       | An array of related data                                    | Any other data you want to include with this logger                 |

### Testing
Use the `HasSystemLoggerAssertions` trait in your tests to assert that a SystemLog has (or has not) been created.

```php
use SteadfastCollective\LaravelSystemLog\Concerns\HasSystemLoggerAssertions;

class ProductTest extends TestCase
{
    use HasSystemLoggerAssertions;

    public function test_create_simple_system_log_no_context()
    {
        $product = new Product;
        $product->id = 12345;

        $product->doSomething();

        // Check doSomething created a SystemLog
        $this->assertSystemLogLogged(
            message: 'This is a test message',
            model: $product
        );
    }
}
```

### Formatting your log lines
When you create a SystemLog the message will also be logged to the standard Larvel logger.

You can customise this message by adding a `makeLogMessage` method to your class.

For example to prefix the log message with the class name wrapped in square brackes:

```php
class MyClass
{
    use HasSystemLogger;

    public function makeLogMessage(string $message): string
    {
        return '['.class_basename($this).'] '.$message;
    }
}
```

### FilamentPHP UI
This package comes with a UI generator which allows you to browse the system logs in a FilamentPHP Resource.

The UI is generated so you're free to customise it as you please, or update it fom the latest version
using `php artisan system-logs:install`.

The System Logs benefit from a very wide screen, add this snippet to your panel provider to make the
system logs page full width. Modify the path as necessary.

```
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Support\Enums\Width;

class AdminPanelProvider extends BasePanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->maxContentWidth((request()->path() === 'admin/system-logs') ? Width::Full : null);
    }
}

```
#### System Logs Listing
![Picture of a list of system logs showing a table with columns of relevant data](/resources/img/system-logs-list.png)

#### System Log Detail Page
![Picture of a single system log showing a table of relevant data for a single item](/resources/img/system-log-detail.png)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Bugs, Suggestions and PRs are welcome. Any new functionality must include tests. Get in touch if you'd like to contribute but aren't sure how.

## Git Hooks
While developing it's recommended you setup a git-hook to ensure you always have the correct versions and dependencies

Call the shared post-checkout hook when you checkout or update your branch:

```bash
echo "./scripts/git-hooks/post-checkout \"\$@\"" >> .git/hooks/post-checkout
chmod +x .git/hooks/post-checkout
echo "./scripts/git-hooks/post-update \"\$@\"" >> .git/hooks/post-update
chmod +x .git/hooks/post-update
```

If you need to manually run the hook you can simply run it `./scripts/git-hooks-post-update`

(beta) It's also a good idea to run code-style checks when you commit. This is a work in progress hook, the workflow could be smoother:

```bash
echo "./scripts/git-hooks/pre-commit \"\$@\"" >> .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
```


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sami Walbury](https://github.com/patabugen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
