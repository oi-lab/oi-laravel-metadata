<?php

namespace OiLab\OiLaravelMetadata;

use Illuminate\Support\ServiceProvider;
use OiLab\OiLaravelMetadata\Console\Commands\InstallAiSkillCommand;
use OiLab\OiLaravelMetadata\Console\Commands\InstallSettingsCommand;
use OiLab\OiLaravelMetadata\Services\MetaService;
use OiLab\OiLaravelMetadata\Services\OgService;
use OiLab\OiLaravelMetadata\Support\SettingResolver;
use OiLab\OiLaravelMetadata\Support\SettingsInstaller;

class OiLaravelMetadataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oi-laravel-metadata.php', 'oi-laravel-metadata');

        $this->app->singleton(SettingResolver::class);
        $this->app->singleton(SettingsInstaller::class);
        $this->app->singleton(MetaService::class);
        $this->app->singleton(OgService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallAiSkillCommand::class,
                InstallSettingsCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/oi-laravel-metadata.php' => config_path('oi-laravel-metadata.php'),
            ], 'oi-laravel-metadata-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'oi-laravel-metadata-migrations');

            $this->publishes([
                __DIR__.'/../resources/stubs/ai-skill.md' => base_path('.claude/skills/oilab-laravel-metadata/SKILL.md'),
            ], 'oi-laravel-metadata-skill');
        }
    }
}
