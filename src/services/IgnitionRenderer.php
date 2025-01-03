<?php

namespace webrgp\ignition\services;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use Spatie\FlareClient\Flare;
use Spatie\FlareClient\FlareMiddleware\CensorRequestHeaders;
use Spatie\Ignition\Config\IgnitionConfig;
use Spatie\Ignition\Ignition as SpatieIgnition;
use Throwable;
use webrgp\ignition\Ignition;
use webrgp\ignition\middleware\AddCraftInfo;
use webrgp\ignition\middleware\CraftSensitiveKeywords;
use webrgp\ignition\models\IgnitionSettings;

class IgnitionRenderer extends Component
{
    private ?\Spatie\Ignition\Ignition $ignition = null;

    // Public Methods
    // =========================================================================

    public array $ignitionConfig = [];

    public ?string $applicationPath = null;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->ignitionConfig = $this->getIgnitionConfig();
        $this->applicationPath = Craft::getAlias('@root');
        $this->ignition = $this->initIgnition();

        parent::init();
    }

    public function handleException(Throwable $exception): void
    {
        $this->ignition->handleException($exception);
    }

    /**
     * Retrieves the Ignition configuration settings.
     *
     * This method collects various configuration settings for Ignition from the class properties
     * or environment variables. It filters out any null values and returns the resulting array.
     *
     * @return array The Ignition configuration settings.
     */
    private function getIgnitionConfig(): array
    {
        $config = new IgnitionSettings([
            'editor' => App::env('CRAFT_IGNITION_EDITOR') ?? null,
            'theme' => App::env('CRAFT_IGNITION_THEME') ?? 'auto',
            'remote_sites_path' => App::env('CRAFT_IGNITION_REMOTE_SITES_PATH') ?? null,
            'local_sites_path' => App::env('CRAFT_IGNITION_LOCAL_SITES_PATH') ?? null,
            'share_endpoint' => App::env('CRAFT_IGNITION_SHARE_ENDPOINT') ?? null,
            'enable_share_button' => App::env('CRAFT_IGNITION_ENABLE_SHARE_BUTTON') ?? null,
            'enable_runnable_solutions' => App::env('CRAFT_IGNITION_ENABLE_RUNNABLE_SOLUTIONS') ?? null,
            'hide_solutions' => App::env('CRAFT_IGNITION_HIDE_SOLUTIONS') ?? null,
        ]);

        $config = array_filter($config->toArray(), fn($value) => $value !== null);

        return $config;
    }

    /**
     * Initializes and configures an Ignition instance.
     *
     * This method retrieves the Ignition configuration, creates an Ignition instance,
     * and applies the configuration if available. It also sets the application path,
     * determines whether exceptions should be displayed based on the development mode,
     * and specifies that the application is not running in a production environment.
     *
     * @return SpatieIgnition The configured Ignition instance.
     */
    private function initIgnition(): SpatieIgnition
    {
        $ignition = SpatieIgnition::make();
        $ignition->setConfig(new IgnitionConfig($this->ignitionConfig));

        $middlewares = self::getFlareMiddlewares();

        return $ignition
            ->applicationPath($this->applicationPath)
            ->shouldDisplayException(App::devMode())
            ->runningInProductionEnvironment(false)
            ->configureFlare(function(Flare $flare) use ($middlewares) {
                $flare->registerMiddleware($middlewares);
            });
    }

    /**
     * Returns an array of Flare middlewares.
     *
     * This method returns an array of Flare middlewares that are used to modify the
     * data that is sent to Flare. The middlewares are used to censor sensitive data
     * and add Craft-specific information to the report.
     *
     * @return array<FlareMiddleware>
     */
    private static function getFlareMiddlewares(): array
    {
        return [
            new AddCraftInfo(),
            new CensorRequestHeaders([
                'API-KEY',
                'Authorization',
                'Cookie',
                'Set-Cookie',
                'X-CSRF-TOKEN',
                'X-XSRF-TOKEN',
                // IP headers
                'ip',
                'x-forwarded-for',
                'x-real-ip',
                'x-request-ip',
                'x-client-ip',
                'cf-connecting-ip',
                'fastly-client-ip',
                'true-client-ip',
                'forwarded',
                'proxy-client-ip',
                'wl-proxy-client-ip',
            ]),
            new CraftSensitiveKeywords(),
        ];
    }
}
