<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Nvl\Primitives\Contracts\ExchangeRateProvider;
use Nvl\Primitives\Services\ConfiguredExchangeRateProvider;
use Nvl\Translations\Actions\Entries\UpdateTranslationEntryAction;
use Nvl\Translations\Actions\Sync\ImportTranslationsAction;
use Nvl\Translations\Actions\Sync\ScanTranslationsAction;
use Nvl\Translations\Contracts\ImportTranslationsContract;
use Nvl\Translations\Contracts\ScanTranslationsContract;
use Nvl\Translations\Contracts\TenantTranslationRepository;
use Nvl\Translations\Contracts\UpdateTranslationEntryContract;
use Nvl\Translations\Services\DatabaseTenantTranslationRepository;

/**
 * Supplies the minimum host-owned integration required by strict suite doctors.
 */
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ([
            ExchangeRateProvider::class => ConfiguredExchangeRateProvider::class,
            TenantTranslationRepository::class => DatabaseTenantTranslationRepository::class,
            UpdateTranslationEntryContract::class => UpdateTranslationEntryAction::class,
            ImportTranslationsContract::class => ImportTranslationsAction::class,
            ScanTranslationsContract::class => ScanTranslationsAction::class,
        ] as $contract => $implementation) {
            if (! interface_exists($contract) || ! class_exists($implementation)) {
                continue;
            }

            $factory = static fn (Application $app): object => $app->make($implementation);

            if ($contract === TenantTranslationRepository::class) {
                $this->app->scoped($contract, $factory);

                continue;
            }

            $this->app->bind($contract, $factory);
        }

        Config::set('taxonomy.owners.users', User::class);
        Config::set('taxonomy.taxonomies.category.allowed_owners', ['users']);
        Config::set('taxonomy.taxonomies.tag.allowed_owners', ['users']);

        if (Config::boolean('nvl-release-consumer.published_migrations')) {
            foreach ([
                'activity',
                'nvl-auth',
                'comments',
                'content',
                'forms',
                'media',
                'mail-notifications',
                'metafields',
                'pages',
                'seo',
                'settings',
                'taxonomy',
                'templates',
                'translations',
            ] as $configuration) {
                Config::set("{$configuration}.migrations.enabled", false);
            }
        }
    }
}
