<?php

namespace OiLab\OiLaravelMetadata\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * SettingResolver
 *
 * Reads global metadata values from the host application's key/value Setting
 * model when it is available, gracefully falling back to the package config
 * defaults otherwise. This keeps the package decoupled from any specific
 * settings implementation.
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
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key] ?? $default ?? $this->configDefault($key);
        }

        $value = $this->fetch($key);

        $this->cache[$key] = $value;

        return $value ?? $default ?? $this->configDefault($key);
    }

    /**
     * Determine whether the host application exposes a usable Setting model.
     */
    public function isAvailable(): bool
    {
        $model = $this->modelClass();

        if ($model === null || ! class_exists($model)) {
            return false;
        }

        try {
            return Schema::hasTable((new $model)->getTable());
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Fetch a raw value from the Setting model, or null when unavailable.
     */
    protected function fetch(string $key): ?string
    {
        if (! $this->isAvailable()) {
            return null;
        }

        $model = $this->modelClass();
        $keyColumn = config('oi-laravel-metadata.settings.key_column', 'key');
        $valueColumn = config('oi-laravel-metadata.settings.value_column', 'value');

        try {
            /** @var Model|null $record */
            $record = $model::query()->where($keyColumn, $key)->first();
        } catch (Throwable) {
            return null;
        }

        $value = $record?->getAttribute($valueColumn);

        return $value === null ? null : (string) $value;
    }

    /**
     * Resolve the configured Setting model class.
     *
     * @return class-string<Model>|null
     */
    protected function modelClass(): ?string
    {
        return config('oi-laravel-metadata.settings.model');
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
