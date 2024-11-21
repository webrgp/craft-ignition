<?php

namespace webrgp\ignition\web;

use Craft;
use craft\helpers\App;
use Spatie\Ignition\Config\IgnitionConfig;
use Spatie\Ignition\Ignition;
use yii\web\NotFoundHttpException;

class IgnitionErrorHandler extends \craft\web\ErrorHandler
{
    // Public Properties
    // ========================================================================
    public ?string $editor = null;

    public ?string $theme = null;

    public ?string $remoteSitesPath = null;

    public ?string $localSitesPath = null;

    public ?string $shareEndpoint = null;

    public ?bool $enableShareButton = null;

    public ?bool $enableRunnableSolutions = null;

    public ?array $editorOptions = null;

    public ?bool $hideSolutions = null;

    /**
     * @inheritdoc
     */
    protected function renderException($exception): void
    {
        $request = Craft::$app->has('request', true) ? Craft::$app->getRequest() : null;

        // Return JSON for JSON requests
        if ($request && $request->getAcceptsJson()) {
            parent::renderException($exception);
            return;
        }

        // Show a broken image for image requests
        if (
            $exception instanceof NotFoundHttpException &&
            $request &&
            $request->getAcceptsImage() &&
            Craft::$app->getConfig()->getGeneral()->brokenImagePath
        ) {
            $this->errorAction = 'app/broken-image';
        }

        // Show the full exception view for all exceptions when Dev Mode is enabled (don't skip `UserException`s)
        // or if the user is an admin and has indicated they want to see it
        elseif ($this->showExceptionDetails()) {
            $this->getIgnition()->handleException($exception);
            return;
        }

        parent::renderException($exception);
    }

    /**
     * Initializes and configures an Ignition instance.
     *
     * This method retrieves the Ignition configuration, creates an Ignition instance,
     * and applies the configuration if available. It also sets the application path,
     * determines whether exceptions should be displayed based on the development mode,
     * and specifies that the application is not running in a production environment.
     *
     * @return Ignition The configured Ignition instance.
     */
    private function getIgnition(): Ignition
    {
        $config = $this->getIgnitionConfig();

        $ignition = Ignition::make();

        if (!empty($config)) {
            $ignition->setConfig(new IgnitionConfig($config));
        }

        $applicationPath = Craft::getAlias('@root');

        $ignition
            ->applicationPath($applicationPath)
            ->shouldDisplayException(App::devMode())
            ->runningInProductionEnvironment(false);

        return $ignition;
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
        $config = array_filter([
            'editor' => $this->editor ?? App::env('CRAFT_IGNITION_EDITOR') ?? null,
            'theme' => $this->theme ?? App::env('CRAFT_IGNITION_THEME') ?? 'auto',
            'remote_sites_path' => $this->remoteSitesPath ?? App::env('CRAFT_IGNITION_REMOTE_SITES_PATH') ?? null,
            'local_sites_path' => $this->localSitesPath ?? App::env('CRAFT_IGNITION_LOCAL_SITES_PATH') ?? null,
            'share_endpoint' => $this->shareEndpoint ?? App::env('CRAFT_IGNITION_SHARE_ENDPOINT') ?? null,
            'enable_share_button' => $this->enableShareButton ?? App::env('CRAFT_IGNITION_ENABLE_SHARE_BUTTON') ?? null,
            'enable_runnable_solutions' => $this->enableRunnableSolutions ?? App::env('CRAFT_IGNITION_ENABLE_RUNNABLE_SOLUTIONS') ?? null,
            'hide_solutions' => $this->hideSolutions ?? App::env('CRAFT_IGNITION_HIDE_SOLUTIONS') ?? null,
            'editor_options' => $this->editorOptions ?? null, // No env var for this
        ], fn($value) => $value !== null);

        return $config;
    }
}
