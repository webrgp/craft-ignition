<?php

namespace webrgp\ignition\middleware;

use Composer\InstalledVersions;
use Craft;
use craft\base\PluginInterface;
use craft\helpers\App;
use OutOfBoundsException;
use Spatie\FlareClient\Report;
use yii\base\Module;

class AddCraftInfo
{
    protected array $info = [];

    public function __construct()
    {
        $this->info = [
            'appInfo' => self::_appInfo(),
            'plugins' => self::_craftPlugins(),
            'modules' => self::_craftModules(),
        ];
    }

    public function handle(Report $report, $next)
    {
        $report->setApplicationVersion($this->info["appInfo"]["Craft edition & version"]);
        // Craft::dd($report);
        foreach ($this->info as $key => $value) {
            $report->group($key, $value);
        }

        return $next($report);
    }

    /**
     * Returns application info.
     *
     * @return array
     */
    private static function _appInfo(): array
    {
        $craftEdition = version_compare(Craft::$app->getVersion(), '5.0.0', '<')
            ? App::editionName(Craft::$app->getEdition())
            : Craft::$app->edition->name;

        $info = [
            'PHP version' => App::phpVersion(),
            'OS version' => PHP_OS . ' ' . php_uname('r'),
            'Database driver & version' => self::_dbDriver(),
            'Image driver & version' => self::_imageDriver(),
            'Craft edition & version' => sprintf('Craft %s %s', $craftEdition, Craft::$app->getVersion()),
        ];

        if (!class_exists(InstalledVersions::class, false)) {
            $path = Craft::$app->getPath()->getVendorPath() . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'InstalledVersions.php';
            if (file_exists($path)) {
                require $path;
            }
        }

        if (class_exists(InstalledVersions::class, false)) {
            self::_addVersion($info, 'Yii version', 'yiisoft/yii2');
            self::_addVersion($info, 'Twig version', 'twig/twig');
            self::_addVersion($info, 'Guzzle version', 'guzzlehttp/guzzle');
        }

        return $info;
    }

    private static function _craftPlugins(): array
    {
        return collect(Craft::$app->getPlugins()->getAllPlugins())
            ->mapWithKeys(function(PluginInterface $plugin) {
                return [$plugin->name => $plugin->version];
            })
            ->toArray();
    }

    private static function _craftModules(): array
    {
        return collect(Craft::$app->getModules())
            ->filter(fn($module) => !($module instanceof PluginInterface))
            ->mapWithKeys(function($module, $id) {
                if ($module instanceof Module) {
                    return [$id => get_class($module)];
                }
                if (is_string($module)) {
                    return [$id => $module];
                }
                if (is_array($module) && isset($module['class'])) {
                    return [$id => $module['class']];
                }
                return [$id => Craft::t('app', 'Unknown type')];
            })
            ->toArray();
    }

    /**
     * @param array $info
     * @param string $label
     * @param string $packageName
     */
    private static function _addVersion(array &$info, string $label, string $packageName): void
    {
        try {
            $version = InstalledVersions::getPrettyVersion($packageName) ?? InstalledVersions::getVersion($packageName);
        } catch (OutOfBoundsException) {
            return;
        }

        if ($version !== null) {
            $info[$label] = $version;
        }
    }

    /**
     * Returns the DB driver name and version
     *
     * @return string
     */
    private static function _dbDriver(): string
    {
        $db = Craft::$app->getDb();
        $label = $db->getDriverLabel();
        $version = App::normalizeVersion($db->getSchema()->getServerVersion());
        return "$label $version";
    }

    /**
     * Returns the image driver name and version
     *
     * @return string
     */
    private static function _imageDriver(): string
    {
        $imagesService = Craft::$app->getImages();

        if ($imagesService->getIsGd()) {
            $driverName = 'GD';
        } else {
            $driverName = 'Imagick';
        }

        return $driverName . ' ' . $imagesService->getVersion();
    }
}
