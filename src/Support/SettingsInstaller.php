<?php

namespace OiLab\OiLaravelMetadata\Support;

use OiLab\OiLaravelMetadata\Contracts\SettingStore;

/**
 * SettingsInstaller
 *
 * Seeds the package's default metadata settings through the active
 * {@see SettingStore}. Idempotent: existing
 * keys are never overwritten and the installer no-ops gracefully when no
 * settings store is available.
 */
class SettingsInstaller
{
    public function canInstall(): bool
    {
        return SettingStoreFactory::make()->isAvailable();
    }

    /**
     * @return list<string> The keys created during this run.
     */
    public function install(): array
    {
        $store = SettingStoreFactory::make();

        if (! $store->isAvailable()) {
            return [];
        }

        /** @var array<string, string> $defaults */
        $defaults = config('oi-laravel-metadata.settings.defaults', []);

        $created = [];

        foreach ($defaults as $key => $value) {
            if ($store->has($key)) {
                continue;
            }

            $store->set($key, (string) $value);

            $created[] = $key;
        }

        return $created;
    }
}
