<?php

namespace webrgp\ignition\web;

use webrgp\ignition\Ignition;

class IgnitionErrorHandler extends \craft\web\ErrorHandler
{
    /**
     * @inheritdoc
     */
    public $exceptionView = '@webrgp/ignition/web/views/exception.php';

    // Public Properties
    // ========================================================================

    public function renderIgnition($exception): void
    {
        Ignition::getInstance()->ignitionRenderer->handleException($exception);
    }
}
