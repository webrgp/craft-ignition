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
```

That's it! Now you can enjoy Ignition's beautiful error pages in your Craft CMS project.

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

Or directly to the component, in your `config/app.php` file:

```php
return [
    // ...
    'components' => [
        'errorHandler' => [
            'class' => \webrgp\ignition\IgnitionErrorHandler::class,
            'editor' => 'vscode',
            'theme' => 'light',
            'remote_sites_path' => '\your\remote\sites\path',
            'local_sites_path' => '\your\local\sites\path',
            'shareEndpoint' => 'https://flareapp.io/api/public-reports',
            'enableShareButton' => false,
            'enableRunnableSolutions' => false,
            'hideSolutions' => true,
            'editorOptions' => [],
        ],
    ],
];
```

**Note:** The settings in the `config/app.php` file will override the ones in the `.env` file.

## How It Works

This package introduces the `IgnitionErrorHandler` class, which extends Craft's default `ErrorHandler` class. It overrides the `renderException` method to use Ignition's `renderException` method instead.

This way, you can enjoy Ignition's beautiful error pages while keeping the rest of Craft's error handling functionality in place.

## License

This plugin is open-sourced software licensed under the MIT license.
