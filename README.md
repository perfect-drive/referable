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
    /*
     * The directories to search for Referable classes.
     */
    'directories' => [
        app_path('Enums'),
        app_path('Models'),
    ],

    /*
     * The middleware array to use for the Referable routes.
     */
    'middleware' => ['api', 'auth:sanctum'],

    /*
     * The key name to use for the referable key in the json response.
     */
    'key_name' => 'value',

    /*
     * The value name to use for the referable value in the json response.
     */
    'value_name' => 'title',

    /*
     * The base url for the referable routes.
     */
    'base_url' => 'spa/referable/',
];
```

## Usage

### On a Model:
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

This will register the following route:
```
GET /spa/referable/project_type
```

### On an Enum:
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
This will register the following route:
```
GET /spa/referable/user_status
```

### Model Scopes
The 'ReferableScope' attribute can be used to define a custom scope (on a Model) to filter the referable items and create an additional route for it.

```
#[ReferableScope]
public function scopeActive(Builder $query): Builder
{
    return $query->where('active', true);
}
```
This will register the following route:
```
GET /spa/referable/project_type/active
```
### Enum Scopes
You can use the 'ReferableScope' attribute to define a custom scope method (on an Enum) to filter the referable items and create an additional route for it.

```
#[ReferableScope]
public function active(): bool
{
    return in_array($this, [
        self::FIRST,
        self::SECOND,
    ], true);
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

If you discover any security related issues, please email [carl.klein@perfect-drive.nl](mailto:carl.klein@perfect-drive.nl).

## Credits

- [Carl Klein](https://github.com/perfect-drive)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
