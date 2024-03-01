# Automatically create routes for Models and Enums to make them referable in your SPA frontend

[![Latest Version on Packagist](https://img.shields.io/packagist/v/perfect-drive/referable.svg?style=flat-square)](https://packagist.org/packages/perfect-drive/referable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/perfect-drive/referable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/perfect-drive/referable/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/perfect-drive/referable/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/perfect-drive/referable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/perfect-drive/referable.svg?style=flat-square)](https://packagist.org/packages/perfect-drive/referable)

To populate drowdowns (or other ways of reference) in an SPA form a separate endpoint can be used to create the array of name/value pairs. This package allows you to automatically create routes for your Models and Enums to make them referable in your Laravel backed SPA frontend. 

## Installation

You can install the package via composer:

```bash
composer require perfect-drive/referable
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="referable-config"
```

This is the contents of the published config file:

```php
return [
    'key_name' => 'value',

    'value_name' => 'title',

    'base_url' => 'spa/referable/',
];
```

## Usage

On a Model:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ReferableInterface;
use App\Traits\ReferableModel;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model implements ReferableInterface
{
    use ReferableModel;
```

On an Enum:
```php
<?php

declare(strict_types=1);

namespace App\Enums\User;

use App\Interfaces\ReferableInterface;
use App\Traits\ReferableEnum;

enum UserStatus: string implements ReferableInterface
{
    use ReferableEnum;
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Carl Klein](https://github.com/perfect-drive)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
