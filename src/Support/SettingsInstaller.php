<?php

namespace OiLab\OiLaravelMetadata\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * SettingsInstaller
 *
 * Seeds the package's default metadata settings into the host application's
 * key/value Setting model. The operation is idempotent: existing keys are left
 * untouched, and the installer no-ops gracefully when no Setting model exists.
 */
class SettingsInstaller
{
    /**
     * Whether the host application exposes a usable Setting model.
     */
    public function canInstall(): bool
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
     * Seed any missing default settings.
     *
     * @return list<string> The keys that were created during this run.
     */
    public function install(): array
    {
        if (! $this->canInstall()) {
            return [];
        }

        $model = $this->modelClass();
        $keyColumn = config('oi-laravel-metadata.settings.key_column', 'key');
        $valueColumn = config('oi-laravel-metadata.settings.value_column', 'value');

        /** @var array<string, string> $defaults */
        $defaults = config('oi-laravel-metadata.settings.defaults', []);

        $created = [];

        foreach ($defaults as $key => $value) {
            /** @var Model|null $existing */
            $existing = $model::query()->where($keyColumn, $key)->first();

            if ($existing !== null) {
                continue;
            }

            $model::query()->create([
                $keyColumn => $key,
                $valueColumn => $value,
            ]);

            $created[] = $key;
        }

        return $created;
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
}
