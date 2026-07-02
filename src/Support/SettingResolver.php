<?php

namespace OiLab\OiLaravelMetadata\Support;

use OiLab\OiLaravelMetadata\Contracts\SettingStore;

/**
 * SettingResolver
 *
 * Reads global metadata values through the active {@see SettingStore},
 * gracefully falling back to the package config defaults otherwise. Keeps the
 * package decoupled from any specific settings implementation while preferring
 * `oi-lab/oi-laravel-settings` when it is installed.
 */
class SettingResolver
{
    /**
     * In-memory cache of resolved settings keyed by setting key.
     *
     * @var array<string, string|null>
     */
    protected array $cache = [];

    /**
     * Resolve a setting value by key, falling back to the config default.
     */
    public function get(string $key, ?string $default = null): ?string
    {
        if (! array_key_exists($key, $this->cache)) {
            $this->cache[$key] = SettingStoreFactory::make()->get($key);
        }

        return $this->cache[$key] ?? $default ?? $this->configDefault($key);
    }

    /**
     * Determine whether a usable settings store is available.
     */
    public function isAvailable(): bool
    {
        return SettingStoreFactory::make()->isAvailable();
    }

    /**
     * Resolve the configured default value for a setting key.
     */
    protected function configDefault(string $key): ?string
    {
        $default = config('oi-laravel-metadata.settings.defaults.'.$key);

        return $default === null ? null : (string) $default;
    }
}
