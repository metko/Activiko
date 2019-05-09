# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/metko/activiko.svg?style=flat-square)](https://packagist.org/packages/metko/activiko)
[![Build Status](https://img.shields.io/travis/metko/activiko/master.svg?style=flat-square)](https://travis-ci.org/metko/activiko)
[![Quality Score](https://img.shields.io/scrutinizer/g/metko/activiko.svg?style=flat-square)](https://scrutinizer-ci.com/g/metko/activiko)
[![Total Downloads](https://img.shields.io/packagist/dt/metko/activiko.svg?style=flat-square)](https://packagist.org/packages/metko/activiko)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require metko/activiko
```

## Usage

Just use the trait Activiko in the model you want to be recorded.

``` php
use Metko\Activiko\Traits\RecordActiviko;
```
The model will record automaticaly the following events : 'created', 'update' and 'deleted'.

If you want to modify this globaly, you can update the "recordableEvents" in the config file. Or at the top of your model, just declare a property call recordable events:

``` php
protected static $recordableEvents = ['updated']; // Only the updated event for this model
```


### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email thomas.moiluiavon@gmail.com instead of using the issue tracker.

## Credits

- [Thomas Moiluiavon](https://github.com/metko)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).