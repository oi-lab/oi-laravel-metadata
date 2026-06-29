<?php

namespace OiLab\OiLaravelMetadata\Console\Commands;

use Illuminate\Console\Command;
use OiLab\OiLaravelMetadata\Support\SettingsInstaller;

class InstallSettingsCommand extends Command
{
    protected $signature = 'metadata:install-settings';

    protected $description = 'Seed default metadata settings into the host application Setting model when present';

    public function handle(SettingsInstaller $installer): int
    {
        if (! $installer->canInstall()) {
            $this->warn('No usable Setting model found — skipping metadata settings installation.');

            return self::SUCCESS;
        }

        $created = $installer->install();

        if ($created === []) {
            $this->info('All metadata settings are already present. Nothing to do.');

            return self::SUCCESS;
        }

        foreach ($created as $key) {
            $this->line("Created setting: <info>{$key}</info>");
        }

        $this->info(sprintf('Installed %d metadata setting(s).', count($created)));

        return self::SUCCESS;
    }
}
