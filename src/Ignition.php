<?php

namespace webrgp\ignition;

use Craft;
use craft\console\Application as CraftConsoleApp;
use craft\web\Application as CraftWebApp;
use webrgp\ignition\services\IgnitionRenderer;
use webrgp\ignition\web\IgnitionErrorHandler;
use yii\base\BootstrapInterface;
use yii\base\Module;

/**
 * @property-read IgnitionRenderer $ignitionRenderer
 */
class Ignition extends Module implements BootstrapInterface
{
    // Constants
    // =========================================================================

    public const ID = 'ignition';

    // Protected Static Properties
    // =========================================================================

    protected bool $ignitionAdded = false;

    /**
     * @inerhitdoc
     */
    public function __construct($id = self::ID, $parent = null, $config = [])
    {
        /**
         * Explicitly set the $id parameter, as earlier versions of Yii2 look for a
         * default parameter, and depend on $id being explicitly set:
         * https://github.com/yiisoft/yii2/blob/f3d1534125c9c3dfe8fa65c28a4be5baa822e721/framework/di/Container.php#L436-L448
         */
        parent::__construct($id, $parent, $config);
    }

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

        // Set the instance of this module class, so we can later access it with `Ignition::getInstance()`
        static::setInstance($this);

        // Configure our module
        $this->configureModule();

        $app->set('errorHandler', [
            'class' => IgnitionErrorHandler::class,
        ]);

        $app->getErrorHandler()->register();

        Craft::info('Ignition module bootstrapped', __METHOD__);
    }

    /**
     * Configure our module
     *
     * @return void
     */
    protected function configureModule(): void
    {
        // Register our module
        Craft::$app->setModule($this->id, $this);

        // Register our components
        $this->registerComponents();
    }

    private function registerComponents(): void
    {
        $this->setComponents([
            'ignitionRenderer' => IgnitionRenderer::class,
        ]);
    }
}
