# Installing NVL Laravel packages

NVL packages are published individually on Packagist from the public repositories in the [NVL Laravel Suite organization](https://github.com/nvl-laravel-suite). Use PHP 8.4–8.5 and Laravel 12–13. A consumer application needs no access to the private source repository and no custom Composer repository entry.

## Choose a package composition

Install only the capability you need. Composer installs its declared dependencies, including `nvl/core` for shared Support and Data contracts:

```bash
composer require nvl/media:^6.0
```

To install the 21 selected code packages, use the Composer metapackage:

```bash
composer require nvl/laravel-suite:^6.0
```

The prepared Suite 6 family selects major 6 of its 21 included code packages, including default-off Ownership instead of Tenancy. Billing, Payments and the deprecated Tenancy bridge remain separate optional installs. Follow [the upgrade guide](../UPGRADING.md) before adoption. These examples require 6.0.0 publication or a reviewed local archive repository; public 5.0.0 evidence does not establish 6.x availability. See the [verification policy](../README.md#verification-and-releases) for coverage.

The metapackage contains no provider or application code. Installing it discovers every package provider and can add package migrations to the next `php artisan migrate`. Auth's package ingress is disabled by default; enable it only after reviewing the host routes and bindings; adopting the host principal, password broker, and Spatie storage each requires explicit configuration. Review [Auth installation](https://github.com/nvl-laravel-suite/auth#installation) and [principal adoption](https://github.com/nvl-laravel-suite/auth/blob/main/docs/principal-adoption.md) before adding the full suite to an existing application. Installing Ownership leaves its enforcement and protocol migrations disabled by default. Existing Tenancy hosts explicitly retain the separate bridge during migration.

Each code package has its own [README and upgrade guide](https://github.com/orgs/nvl-laravel-suite/repositories) for feature setup. Composer discovery normally registers its service provider; do not register a second copy manually. If the application disables Laravel package discovery, register every installed package provider, including dependencies, or restore discovery before using publish tags and commands.

## Configure a package

Package defaults load without publishing a config file. Publish a config file when the application needs to own and change it. For Media, for example:

```bash
php artisan vendor:publish --tag=nvl-media-config
```

Edit `config/nvl-media.php` before enabling routes or migrating an existing schema. Use each package's README to identify required adapters, storage disks, authorization, and feature flags. After changing configuration in an application that caches it, rebuild the cache during deployment:

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

Tenancy is different: `nvl-tenancy.migrations.enabled` defaults to `false`, and installing its package does not create the optional tenant directory. When an application is ready to adopt Tenancy, choose either automatic loading by enabling that setting **or** publish `nvl-tenancy-migrations` while leaving automatic loading disabled. Then migrate and follow the [Tenancy activation guide](https://github.com/nvl-laravel-suite/tenancy#configuration). Auth schema also depends on its enabled features; use the [Auth schema command and doctor](https://github.com/nvl-laravel-suite/auth#installation) before enabling another feature on an existing installation.

Core, CSV, Filterable, Primitives, Translatable, and the Suite metapackage have no package-owned domain migrations.

## Publish agent skills

Skills are optional development guidance; they do not activate runtime features. Publish only the installed packages whose guidance your team wants. For example:

```bash
php artisan vendor:publish --tag=nvl-media-skills
php artisan vendor:publish --tag=nvl-core-skills
php artisan vendor:publish --tag=nvl-data-skills
```

The tags copy package skills into the application's `.agents/skills/` directory. Core provides both `nvl-support` and `nvl-data`. The `nvl-tenancy-skills` tag includes both Tenancy skills. Review and commit the copied files according to your application's repository policy.

If the application uses Laravel Boost, install it as a development dependency and run `php artisan boost:install` for a new setup. After adding NVL packages later, run `php artisan boost:update --discover` to offer their bundled skills. Use this as an alternative to direct `vendor:publish`; review local skill changes before replacing customized guidance. See the [Laravel Boost guide](https://laravel.com/docs/13.x/boost#keeping-boost-resources-updated) for discovery behavior.

## Publish-tag reference

Run `php artisan vendor:publish --tag=<tag>` with a tag from this table. A dash means the package exposes no tag of that type. Config files land in `config/`; migration files in `database/migrations/`; skills in `.agents/skills/`. Package READMEs explain the destination and use of other resources.

<!-- BEGIN GENERATED PUBLISH TAGS -->
| Package | Available canonical publish tags |
| --- | --- |
| `nvl/core` | `nvl-core-config`, `nvl-core-skills`, `nvl-core-translations`, `nvl-data-config`, `nvl-data-generated-types-tooling`, `nvl-data-skills` |
| `nvl/ownership` | `nvl-ownership-config`, `nvl-ownership-migrations`, `nvl-ownership-skills` |
| `nvl/tenancy` | `nvl-tenancy-config`, `nvl-tenancy-migrations`, `nvl-tenancy-skills`, `nvl-tenancy-translations` |
| `nvl/auth` | `nvl-auth-adoption`, `nvl-auth-config`, `nvl-auth-migrations`, `nvl-auth-skills`, `nvl-auth-translations` |
| `nvl/billing` | `nvl-billing-config`, `nvl-billing-migrations`, `nvl-billing-skills`, `nvl-billing-translations` |
| `nvl/payments` | `nvl-payments-config`, `nvl-payments-migrations`, `nvl-payments-skills`, `nvl-payments-translations` |
| `nvl/csv` | `nvl-csv-skills`, `nvl-csv-translations` |
| `nvl/filterable` | `nvl-filterable-skills`, `nvl-filterable-translations` |
| `nvl/translatable` | `nvl-translatable-config`, `nvl-translatable-skills`, `nvl-translatable-translations` |
| `nvl/primitives` | `nvl-primitives-config`, `nvl-primitives-skills`, `nvl-primitives-translations` |
| `nvl/settings` | `nvl-settings-config`, `nvl-settings-migrations`, `nvl-settings-skills`, `nvl-settings-translations` |
| `nvl/activity` | `nvl-activity-config`, `nvl-activity-migrations`, `nvl-activity-skills`, `nvl-activity-translations` |
| `nvl/taxonomy` | `nvl-taxonomy-config`, `nvl-taxonomy-migrations`, `nvl-taxonomy-skills`, `nvl-taxonomy-translations` |
| `nvl/media` | `nvl-media-config`, `nvl-media-migrations`, `nvl-media-skills`, `nvl-media-translations` |
| `nvl/mail-notifications` | `nvl-mail-notifications-adoption`, `nvl-mail-notifications-config`, `nvl-mail-notifications-mail-views`, `nvl-mail-notifications-migrations`, `nvl-mail-notifications-skills`, `nvl-mail-notifications-translations` |
| `nvl/content` | `nvl-content-config`, `nvl-content-migrations`, `nvl-content-skills`, `nvl-content-translations`, `nvl-content-views` |
| `nvl/comments` | `nvl-comments-config`, `nvl-comments-migrations`, `nvl-comments-skills`, `nvl-comments-translations` |
| `nvl/templates` | `nvl-templates-config`, `nvl-templates-migrations`, `nvl-templates-skills`, `nvl-templates-translations`, `nvl-templates-views` |
| `nvl/metafields` | `nvl-metafields-config`, `nvl-metafields-migrations`, `nvl-metafields-skills`, `nvl-metafields-translations` |
| `nvl/pages` | `nvl-pages-config`, `nvl-pages-migrations`, `nvl-pages-skills`, `nvl-pages-translations` |
| `nvl/tasks` | `nvl-tasks-config`, `nvl-tasks-migrations`, `nvl-tasks-skills`, `nvl-tasks-translations` |
| `nvl/translations` | `nvl-translations-config`, `nvl-translations-migrations`, `nvl-translations-skills`, `nvl-translations-translations` |
| `nvl/seo` | `nvl-seo-config`, `nvl-seo-migrations`, `nvl-seo-skills`, `nvl-seo-translations` |
| `nvl/forms` | `nvl-forms-config`, `nvl-forms-migrations`, `nvl-forms-skills`, `nvl-forms-translations` |
<!-- END GENERATED PUBLISH TAGS -->

Core publishes both Support and Data defaults with `nvl-core-config`; the retained `nvl-data-config` alias publishes Data defaults. Regenerate this table with `php -d memory_limit=1G tools/sync-installation-tags.php`. The generated-types tooling tag copies optional ESLint and Prettier fragments into the application root. Publishing translation tags copies override files; package translations are available without publishing them. Publishing Content or Templates views is optional when application-specific templates are needed.

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
