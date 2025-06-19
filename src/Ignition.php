<?php

namespace webrgp\ignitionplugin;

use Craft;
use craft\base\Plugin;
use webrgp\ignition\web\IgnitionErrorHandler;

/**
 * @property-read IgnitionRenderer $ignitionRenderer
 */
class Ignition extends Plugin
{
    function init()
    {
        parent::init();

        Craft::$app->set('errorHandler', [
            'class' => IgnitionErrorHandler::class,
            'errorAction' => 'templates/render-error',
        ]);

        Craft::$app->getErrorHandler()->register();
    }
}
