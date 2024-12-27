<?php

namespace webrgp\ignition\models;

use Craft;
use craft\base\Model;
use Spatie\Ignition\Config\IgnitionConfig;

class IgnitionSettings extends Model
{

    /**
     * @const array Themes
     */
    public const THEMES = ['auto', 'dark', 'light'];

    // Public Properties
    // =========================================================================

    /**
     * @var ?string IDE editor to open files in
     */
    public $editor = null;

    /**
     * @var ?string Theme to use. Options: 'light', 'dark', 'auto'
     */
    public $theme = null;

    /**
     * @var ?string Path to remote sites
     */
    public $remote_sites_path = null;

    /**
     * @var ?string Path to local sites
     */
    public $local_sites_path = null;

    /**
     * @var ?string Share endpoint
     */
    public $share_endpoint = null;

    /**
     * @var ?bool Enable share button
     */
    public $enable_share_button = null;

    /**
     * @var ?bool Enable runnable solutions
     */
    public $enable_runnable_solutions = null;

    /**
     * @var ?bool Hide solutions
     */
    public $hide_solutions = null;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = [
            [['editor', 'theme', 'remote_sites_path', 'local_sites_path', 'share_endpoint'], 'trim'],
            [['editor', 'theme', 'remote_sites_path', 'local_sites_path', 'share_endpoint'], 'string'],
            [['enable_share_button', 'enable_runnable_solutions', 'hide_solutions'], 'boolean'],
            ['theme', 'filter', 'filter' => [$this, 'normalizeTheme']],
            ['editor', 'filter', 'filter' => [$this, 'normalizeEditor']],
        ];

        return $rules;
    }

    /**
     * Normalizes the theme value.
     *
     * @param string $value
     * @return string
     */
    public function normalizeTheme(string $value): string
    {
        $theme = strtolower($value);

        // check if the theme is valid
        if (in_array($theme, self::THEMES)) {
            return $theme;
        }

        // return the default theme
        return self::THEMES[0];
    }

    /**
     * Normalizes the editor value.
     *
     * @param string $value
     * @return string
     */
    public function normalizeEditor(string $value): string
    {
        $editor = strtolower($value);

        $igitionConfig = (new IgnitionConfig)->toArray();
        $editorOptions = array_keys($igitionConfig['editorOptions']) ?? null;

        // check if the editor is valid
        if (in_array($editor, $editorOptions)) {
            return $editor;
        }

        // return the default editor
        return 'vscode';
    }
}
