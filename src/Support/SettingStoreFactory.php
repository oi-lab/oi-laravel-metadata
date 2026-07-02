<?php

namespace OiLab\OiLaravelMetadata\Support;

use OiLab\OiLaravelMetadata\Contracts\SettingStore;
use OiLab\OiLaravelMetadata\Stores\ConfigModelSettingStore;
use OiLab\OiLaravelMetadata\Stores\OiLaravelSettingsStore;
use OiLab\OiLaravelSettings\SettingsManager;

/**
 * Resolves the active {@see SettingStore} implementation.
 *
 * Resolution order:
 *   1. an explicit store class bound via `oi-laravel-metadata.settings.store`;
 *   2. the `oi-lab/oi-laravel-settings` adapter when that package is installed;
 *   3. the generic config-model store (backwards-compatible fallback).
 *
 * Config is read on every call so runtime overrides are always honoured.
 */
class SettingStoreFactory
{
    public static function make(): SettingStore
    {
        $explicit = config('oi-laravel-metadata.settings.store');

        if (is_string($explicit) && $explicit !== '') {
            return app($explicit);
        }

        if (class_exists(SettingsManager::class)) {
            return app(OiLaravelSettingsStore::class);
        }

        return app(ConfigModelSettingStore::class);
    }
}
