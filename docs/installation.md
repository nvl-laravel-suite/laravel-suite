# Installing NVL Light

Light targets PHP 8.4 and Laravel 13. This branch prepares family 7; use a matching release when available. Do not combine published 6.x packages with the native contracts described here.

Install only the package capabilities you need, or select the suite metapackage for its declared composition. Composer installs required dependencies and Laravel normally discovers their providers. Optional integrations require their own dependencies and configuration. Check the installed package's manifest and README rather than assuming every suite feature is enabled.

## Configure and migrate

Use only canonical `nvl-<package>` configuration roots, `NVL_` environment names and `nvl-<package>-<resource>` publish tags. Generic roots, old storage/table/queue aliases and compatibility switches are unavailable. Nested maps merge recursively; a supplied list replaces the default list, including an empty list. Package infrastructure inherits non-null Core defaults and then Laravel defaults where the owning resolver documents inheritance. Use `connection`, logical `tables` keys, `queue.connection`, `queue.name`, `locks.store` and `authorization.guard` rather than historical aliases. Operation-specific lock stores live at `locks.<operation>.store`.


Package defaults load without publishing. Publish configuration only when overrides are needed, for example `php artisan vendor:publish --tag=nvl-media-config`. Review copied configuration on upgrades; publishing does not merge future defaults into host files.

For stateful packages choose exactly one migration source: vendor loading or published host migrations. Keep vendor loading disabled when executing published copies. Existing table names are not proof of migration ownership. Back up storage and history, review the owning package’s supported migration/adoption plan, then execute native Laravel migrations for the selected paths and connection. Core exposes no generic schema preflight or upgrade command. Follow the owning package’s migration and adoption instructions.

Configure registered resources, explicit policies, host adapters, queues and optional storage/provider dependencies before enabling operations. Use the package's Doctor command where supplied. Rebuild configuration/event caches and restart workers after deployment.

## Optional setup tools

Laravel `vendor:publish` and package-specific Doctor commands work without a suite installer. To opt into the shared developer commands, add `Nvl\Support\Providers\DoctorServiceProvider::class` to `bootstrap/providers.php`. This provider exposes `nvl:install` and `nvl:doctor` only in console execution; it is not auto-discovered by Core.

Run `php artisan nvl:install pages --dry-run --format=json` to preview common configuration for loaded packages. Omit `--dry-run` only when you want files written. Existing files are preserved; `--force` creates backups before replacement. Installation never enables features, runs migrations or creates application controllers. Run `php artisan nvl:doctor --strict --format=json` after configuring the selected packages. An installation preview reports binding metadata; it does not prove schema or runtime readiness.

## Development guidance

Publish the selected package's skill using its documented Laravel publish tag, such as `nvl-media-skills`, or discover bundled guidance through Laravel Boost. Review local customizations before replacing copied skills. Skills do not enable runtime features.

The consumer owns HTTP endpoints, Actions, data classes, response resources and frontend types. See [getting started](getting-started.md), [native consumer APIs](consumer-api.md), and each installed package's README for its actual service and configuration contract.

## Available publish tags

<!-- BEGIN GENERATED PUBLISH TAGS -->
| Package | Available canonical publish tags |
| --- | --- |
| `nvl/core` | `nvl-core-config`, `nvl-core-skills`, `nvl-core-translations` |
| `nvl/ownership` | `nvl-ownership-config`, `nvl-ownership-migrations`, `nvl-ownership-skills` |
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
| `nvl/mail-notifications` | `nvl-mail-notifications-adoption`, `nvl-mail-notifications-config`, `nvl-mail-notifications-migrations`, `nvl-mail-notifications-skills`, `nvl-mail-notifications-translations` |
| `nvl/content` | `nvl-content-config`, `nvl-content-migrations`, `nvl-content-skills`, `nvl-content-translations` |
| `nvl/comments` | `nvl-comments-config`, `nvl-comments-migrations`, `nvl-comments-skills`, `nvl-comments-translations` |
| `nvl/templates` | `nvl-templates-config`, `nvl-templates-migrations`, `nvl-templates-skills`, `nvl-templates-translations`, `nvl-templates-views` |
| `nvl/metafields` | `nvl-metafields-config`, `nvl-metafields-migrations`, `nvl-metafields-skills`, `nvl-metafields-translations` |
| `nvl/pages` | `nvl-pages-config`, `nvl-pages-migrations`, `nvl-pages-skills`, `nvl-pages-translations` |
| `nvl/tasks` | `nvl-tasks-config`, `nvl-tasks-migrations`, `nvl-tasks-skills`, `nvl-tasks-translations` |
| `nvl/translations` | `nvl-translations-config`, `nvl-translations-migrations`, `nvl-translations-skills`, `nvl-translations-translations` |
| `nvl/seo` | `nvl-seo-config`, `nvl-seo-migrations`, `nvl-seo-skills`, `nvl-seo-translations` |
| `nvl/forms` | `nvl-forms-config`, `nvl-forms-migrations`, `nvl-forms-skills`, `nvl-forms-translations` |
<!-- END GENERATED PUBLISH TAGS -->
