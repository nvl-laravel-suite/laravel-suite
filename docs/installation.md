# Installing NVL Laravel packages

NVL packages are published individually on Packagist from the public repositories in the [NVL Laravel Suite organization](https://github.com/nvl-laravel-suite). Use PHP 8.4 or newer and Laravel 13. A consumer application needs no access to the private source repository and no custom Composer repository entry.

## Choose a package composition

Install only the capability you need. Composer installs its declared dependencies, including `nvl/core` for shared Support and Data contracts:

```bash
composer require nvl/media:^2.0
```

To install all 21 code packages, use the Composer metapackage:

```bash
composer require nvl/laravel-suite:^4.0
```

Suite 4.x selects Tasks 3.x and Activity 2.3 or later in the 2.x series. Before upgrading from suite 3.x, follow the [Tasks 3 upgrade guide](https://github.com/nvl-laravel-suite/tasks/blob/main/UPGRADING.md), run the new Tasks migrations in your chosen ownership mode, and restart workers. Billing and Payments remain separate optional installs.

The metapackage contains no provider or application code. Installing it discovers every package provider and can add package migrations to the next `php artisan migrate`. In particular, `nvl/auth` is enabled by default and can select its own User and authentication models. Review [Auth installation](https://github.com/nvl-laravel-suite/auth#installation) and [principal adoption](https://github.com/nvl-laravel-suite/auth/blob/main/docs/principal-adoption.md) before adding the full suite to an existing application. Installing `nvl/tenancy` as a dependency leaves tenant behavior and its core migrations disabled by default.

Each code package has its own [README and upgrade guide](https://github.com/orgs/nvl-laravel-suite/repositories) for feature setup. Composer discovery normally registers its service provider; do not register a second copy manually. If the application disables Laravel package discovery, register every installed package provider, including dependencies, or restore discovery before using publish tags and commands.

## Configure a package

Package defaults load without publishing a config file. Publish a config file when the application needs to own and change it. For Media, for example:

```bash
php artisan vendor:publish --tag=media-config
```

Edit `config/media.php` before enabling routes or migrating an existing schema. Use each package's README to identify required adapters, storage disks, authorization, and feature flags. After changing configuration in an application that caches it, rebuild the cache during deployment:

```bash
php artisan config:clear
php artisan config:cache
```

Publishing copies a file into the application. Future package releases do not update that copy automatically. Compare it with the new package default during upgrades and merge intentional changes. Avoid `--force` on customized config or views unless you have reviewed the replacement.

## Decide who owns migrations

For a **clean installation** of a package with default automatic migrations, keep its migration switch enabled (`nvl-auth.migrations.enabled` for Auth; `<slug>.migrations.enabled` for the others), leave its `*-migrations` tag unpublished, then run:

```bash
php artisan migrate
```

For **application-owned migrations**, publish that package's migration tag and disable its automatic migration loading **before the first migrate**. Maintain the copied migrations as application files. Never run both copies. Laravel retimestamps published migration files, so matching filenames or an existing table are not proof that the two migration sources are safe to run together. Follow the affected package's upgrade guide before adopting existing tables or a custom connection.

Tenancy is different: `tenancy.migrations.enabled` defaults to `false`, and installing its package does not create the optional tenant directory. When an application is ready to adopt Tenancy, choose either automatic loading by enabling that setting **or** publish `tenancy-migrations` while leaving automatic loading disabled. Then migrate and follow the [Tenancy activation guide](https://github.com/nvl-laravel-suite/tenancy#configuration). Auth schema also depends on its enabled features; use the [Auth schema command and doctor](https://github.com/nvl-laravel-suite/auth#installation) before enabling another feature on an existing installation.

Core, CSV, Filterable, Primitives, Translatable, and the Suite metapackage have no package-owned domain migrations.

## Publish agent skills

Skills are optional development guidance; they do not activate runtime features. Publish only the installed packages whose guidance your team wants. For example:

```bash
php artisan vendor:publish --tag=media-skills
php artisan vendor:publish --tag=support-skills
php artisan vendor:publish --tag=data-skills
```

The tags copy package skills into the application's `.agents/skills/` directory. Core provides both `nvl-support` and `nvl-data`. The `tenancy-skills` tag includes both Tenancy skills. Review and commit the copied files according to your application's repository policy.

If the application uses Laravel Boost, install it as a development dependency and run `php artisan boost:install` for a new setup. After adding NVL packages later, run `php artisan boost:update --discover` to offer their bundled skills. Use this as an alternative to direct `vendor:publish`; review local skill changes before replacing customized guidance. See the [Laravel Boost guide](https://laravel.com/docs/13.x/boost#keeping-boost-resources-updated) for discovery behavior.

## Publish-tag reference

Run `php artisan vendor:publish --tag=<tag>` with a tag from this table. A dash means the package exposes no tag of that type. Config files land in `config/`; migration files in `database/migrations/`; skills in `.agents/skills/`. Package READMEs explain the destination and use of other resources.

| Package | Config tag | Migration tag | Skill tag | Other tags |
| --- | --- | --- | --- | --- |
| Core | `data-config` | — | `support-skills`, `data-skills` | `nvl-data-config` (config alias), `nvl-data-generated-types-tooling` |
| Filterable | — | — | `filterable-skills` | — |
| Tenancy | `tenancy-config` | `tenancy-migrations` (opt-in) | `tenancy-skills` | — |
| Activity | `activity-config` | `activity-migrations` | `activity-skills` | `activity-translations` |
| Auth | `auth-config` | `auth-migrations` | `auth-skills` | `auth-adoption` |
| Comments | `comments-config` | `comments-migrations` | `comments-skills` | — |
| Content | `content-config` | `content-migrations` | `content-skills` | `content-views` |
| CSV | — | — | `csv-skills` | — |
| Forms | `forms-config` | `forms-migrations` | `forms-skills` | `forms-translations` |
| Mail Notifications | `mail-notifications-config` | `mail-notifications-migrations` | `mail-notifications-skills` | `mail-notifications-adoption`, `mail-notifications-mail-views` |
| Media | `media-config` | `media-migrations` | `media-skills` | `media-translations` |
| Metafields | `metafields-config` | `metafields-migrations` | `metafields-skills` | `metafields-translations` |
| Pages | `pages-config` | `pages-migrations` | `pages-skills` | — |
| Primitives | `primitives-config` | — | `primitives-skills` | `primitives-translations` |
| SEO | `seo-config` | `seo-migrations` | `seo-skills` | — |
| Settings | `settings-config` | `settings-migrations` | `settings-skills` | — |
| Tasks | `tasks-config` | `tasks-migrations` | `tasks-skills` | — |
| Taxonomy | `taxonomy-config` | `taxonomy-migrations` | `taxonomy-skills` | — |
| Templates | `templates-config` | `templates-migrations` | `templates-skills` | `templates-views` |
| Translatable | `translatable-config` | — | `translatable-skills` | — |
| Translations | `translations-config` | `translations-migrations` | `translations-skills` | `translations-translations` |
| Laravel Suite | — | — | — | — |

For Core, `data-config` and `nvl-data-config` publish the same `config/nvl-data.php`; choose one. The generated-types tooling tag copies optional ESLint and Prettier fragments into the application root. Publishing translation tags copies override files; package translations are available without publishing them. Publishing Content or Templates views is optional when application-specific templates are needed.

## Check an installation and upgrade safely

Confirm Composer's resolved versions and pending migrations before deploying:

```bash
composer show 'nvl/*'
php artisan migrate:status
```

Packages with storage or deployment contracts provide read-only `nvl:<package>:doctor` commands; run the relevant command after configuration and migration. For example:

```bash
php artisan nvl:media:doctor --strict
```

When upgrading, read each installed package's `UPGRADING.md`, compare published config and view copies with the new defaults, and add new migrations through the same ownership mode already chosen. Refresh published or Boost-discovered skills only after reviewing local edits. Update the selected package and its dependencies with Composer, for example:

```bash
composer update nvl/media --with-all-dependencies
```
