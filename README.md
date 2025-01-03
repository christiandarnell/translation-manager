# Translation Manager

GUI for scanning and managing your Laravel translations.

<img width="1206" alt="Preview" src="https://user-images.githubusercontent.com/7644596/161340228-ae6f73fd-35fc-4ce4-b14c-722a9efb0c93.png">

## Installation

Install this package by running the following commands in your terminal:

```shell
composer require christiandarnell/translation-manager
php artisan vendor:publish --tag="tm-assets"
```

The first command will add the package to your composer's dependency list and automatically register its service provider.

The second one will publish package's frontend assets (CSS, JS and fonts) to the `/public/vendor/translation-manager` folder.

You can now open the dashboard by visiting `your-site.com/translation-manager`.

## Config

To customize package's configuration, run this command in your terminal:

```shell
php artisan vendor:publish --tag="tm-config"
```

### Available Options

```php
[
    'locales' => [
        'en' => 'English',
        // ...
    ],
    'folders' => [
        'app/Http/Controllers',
        'resources/views',
    ],
];
```

## Scanning

Click on the "Scan Files" button in the package's dashboard to scan folders for translation strings. 

> You may change the list of folders to scan by customizing the config at `/config/tm.php`.

This process will create or override language files in the `/lang` directory.

e.g. It will create `/lang/fr.json` and `/lang/es.json` if your `tm.locales` configuration is as follows:

```php
[
    'en' => 'English',
    'fr' => 'Français',
    'es' => 'Español',
];
```

Locale specified in `app.fallback_locale` is set as the default language thus its language file is not created.

## Changelog

The [CHANGELOG](CHANGELOG.md) file will tell you about all changes to this package.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Alex Torscho](https://github.com/atorscho)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
