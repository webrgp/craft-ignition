<?php

namespace webrgp\ignition;

use craft\console\Application as CraftConsoleApp;
use craft\web\Application as CraftWebApp;
use webrgp\ignition\web\IgnitionErrorHandler;
use yii\base\BootstrapInterface;

class Ignition implements BootstrapInterface
{
    /**
     * Bootstraps the application by registering the Ignition error handler.
     *
     * @param  \yii\base\Application  $app  The application instance.
     *
     * Only bootstraps if the application is an instance of CraftWebApp or CraftConsoleApp.
     * Registers the Ignition error handler and sets it to the application's errorHandler component.
     */
    public function bootstrap($app)
    {
        // Only bootstrap if this is a CraftWebApp
        if (!($app instanceof CraftWebApp || $app instanceof CraftConsoleApp)) {
            return;
        }

        // Register the Ignition error handler
        $app->set('errorHandler', [
            'class' => IgnitionErrorHandler::class,
        ]);

        $errorHandler = $app->getErrorHandler();

        $errorHandler->register();
    }
}
