# Rector Extras

Additional [Rector](https://getrector.com) rules for Laravel to roll it the FSAC way.

> ⚠️ This package has not yet reached v1 and may introduce breaking changes between releases. Pin to a specific version.

## Installation

As an internal tool this package is not currently available on Packagist. To install it, add a `vcs` repository entry to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/FullStackAppCo/rector-extras"
        }
    ]
}
```

Then require the package:

```bash
composer require --dev fsac/rector-extras "0.1.0"
```

## Usage

Register rules in your `rector.php`:

```php
use FullStackAppCo\RectorExtras\Rules\HelperFunctionToFacadeRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        HelperFunctionToFacadeRector::class,
    ]);
```

## Rules

- `HelperFunctionToFacadeRector` — converts Laravel's global helper functions to their equivalent facade calls, e.g. `config('app.name')` becomes `Config::get('app.name')`.

## License

[MIT](LICENSE.md)
