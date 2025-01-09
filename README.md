# Ignition Error Handler for Craft CMS

Error handling alternative for Craft CMS that uses [Ignition](https://github.com/spatie/ignition) for better developer experience.

![Screenshot of ignition](https://spatie.github.io/ignition/ignition.png)

## Requirements

- Craft CMS 4.3.0 or higher
- PHP 8.0 or higher

## Installation

You can install the plugin via Composer:

```bash
composer require webrgp/craft-ignition

# OR if you only want to use it in development
composer require webrgp/craft-ignition --dev
```

This extension will bootstrap itself automatically once installed. Now you can enjoy Ignition's beautiful error pages in your Craft CMS project.

## Customizing Ignition

You can configure Ignition by adding the following settings to your `.env` file:

```env
CRAFT_IGNITION_EDITOR=vscode
CRAFT_IGNITION_THEME=light
CRAFT_IGNITION_REMOTE_SITES_PATH=/var/www/html
CRAFT_IGNITION_LOCAL_SITES_PATH=/Users/yourusername/Code/YourProject
CRAFT_IGNITION_SHARE_ENDPOINT=https://flareapp.io/api/public-reports
CRAFT_IGNITION_ENABLE_SHARE_BUTTON=true
CRAFT_IGNITION_ENABLE_RUNNABLE_SOLUTIONS=true
CRAFT_IGNITION_HIDE_SOLUTIONS=false
```

## How It Works

This package introduces the `IgnitionErrorHandler` class, which extends Craft's default `ErrorHandler` class. It overrides the `$exceptionView` property to use this package's custom exception view file, which renders Ignition's error page.

## Flare Middleware

This package also includes a few middleware classes that add Craft specific data to the Ignition error report and prevent Ignition from sharing sensitive information with Flare:

### AddCraftInfo middleware

This middleware Application Info, Plugins, and Modules information present in Craft's System Report to the Ignition's and Flare's error report.

### CraftSensitiveKeywords middleware

This middleware prevents Ignition from sharing sensitive information with Flare. It removes sensitive information from the error report before sharing it with Flare by testing each body parameter against Craft Security's [isSensitive](https://github.com/craftcms/cms/blob/2b2de25bfac0e359bcae62e0e6995bfdb4229eaa/src/services/Security.php#L176-L178) method.

You can customize the sensitive keywords by overriding the `sensitiveKeywords` in the Security component of the Craft app config:

```php
return [
    'components' => [
        'security' => [
            'class' => \craft\services\Security::class,
            'sensitiveKeywords' => [
                'lorem',
            ],
        ],
    ]
];
```

[These are the default sensitive keywords](https://github.com/craftcms/cms/blob/2b2de25bfac0e359bcae62e0e6995bfdb4229eaa/src/config/app.php#L112-L121) in Craft CMS.

### Censored Headers middleware

Besides the sensitive keywords, this module also censors the following headers from the error report:

- `API-KEY`
- `Authorization`
- `Cookie`
- `Set-Cookie`
- `X-CSRF-TOKEN`
- `X-XSRF-TOKEN`
- `ip`
- `x-forwarded-for`
- `x-real-ip`
- `x-request-ip`
- `x-client-ip`
- `cf-connecting-ip`
- `fastly-client-ip`
- `true-client-ip`
- `forwarded`
- `proxy-client-ip`
- `wl-proxy-client-ip`

## Roadmap

- [ ] Add environment variable to disable Craft Ignition.
- [ ] Add Craft-specific solutions to Ignition.

## Acknowledgements

Thanks to [Andrew Welch](https://github.com/khalwat) for sharing his [nystudio107/plugindev](https://github.com/nystudio107/plugindev) with me!

## License

This plugin is open-sourced software licensed under the MIT license.
