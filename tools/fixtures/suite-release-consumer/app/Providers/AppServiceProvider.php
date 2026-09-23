<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Nvl\Primitives\Contracts\ExchangeRateProvider;
use Nvl\Primitives\Services\ConfiguredExchangeRateProvider;
use Nvl\Tasks\Contracts\TaskAuthorization;
use Nvl\Tasks\Data\TaskActorData;
use Nvl\Tasks\Enums\TaskAbility;
use Nvl\Tasks\Models\Task;
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

        $this->app->bind(TaskAuthorization::class, static fn (): TaskAuthorization => new class implements TaskAuthorization
        {
            public function authorize(
                TaskAbility $ability,
                TaskActorData $actor,
                ?Task $task = null,
                ?Model $subject = null,
            ): void {
                throw new AuthorizationException('Task operations are disabled in the release consumer.');
            }
        });

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
                'tasks',
                'taxonomy',
                'templates',
                'translations',
            ] as $configuration) {
                Config::set("{$configuration}.migrations.enabled", false);
            }
        }
    }
}
