<?php

namespace OiLab\OiLaravelMetadata;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use OiLab\OiLaravelMetadata\Console\Commands\InstallAiSkillCommand;
use OiLab\OiLaravelMetadata\Console\Commands\InstallSettingsCommand;
use OiLab\OiLaravelMetadata\Services\JsonLdService;
use OiLab\OiLaravelMetadata\Services\MetaService;
use OiLab\OiLaravelMetadata\Services\OgService;
use OiLab\OiLaravelMetadata\Support\SeoContext;
use OiLab\OiLaravelMetadata\Support\SettingResolver;
use OiLab\OiLaravelMetadata\Support\SettingsInstaller;

class OiLaravelMetadataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oi-laravel-metadata.php', 'oi-laravel-metadata');

        $this->app->singleton(SeoContext::class);
        $this->app->singleton(SettingResolver::class);
        $this->app->singleton(SettingsInstaller::class);
        $this->app->singleton(MetaService::class);
        $this->app->singleton(OgService::class);
        $this->app->singleton(JsonLdService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->registerBladeDirectives();

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

    /**
     * Register the package Blade directives.
     *
     * `@meta`, `@og`, and `@jsonLd` render the metadata, Open Graph, and JSON-LD
     * of a source. The source is optional: with no argument each directive falls
     * back to the shared SEO subject (`Seo::for($model)`), itself auto-resolved
     * from the current route's model binding when nothing is set explicitly.
     */
    protected function registerBladeDirectives(): void
    {
        $render = static fn (string $service): callable => static function (string $expression) use ($service): string {
            $argument = trim($expression);

            return "<?php echo app({$service}::class)->render({$argument})->toHtml(); ?>";
        };

        Blade::directive('meta', $render('\OiLab\OiLaravelMetadata\Services\MetaService'));
        Blade::directive('og', $render('\OiLab\OiLaravelMetadata\Services\OgService'));
        Blade::directive('jsonLd', $render('\OiLab\OiLaravelMetadata\Services\JsonLdService'));
    }
}
