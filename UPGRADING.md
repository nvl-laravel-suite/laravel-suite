# Upgrading the NVL Laravel Suite metapackage

## Major 6 ownership rollout

Compose 21 selected code packages with optional Ownership instead of mandatory Tenancy. Billing, Payments and the deprecated Tenancy bridge remain separate installs; installation alone never enables isolation or adopts host Auth.

Upgrade the selected NVL dependency closure together to `^6.0`. Ordinary unowned hosts retain the default unscoped declarations and need no ownership context. For isolation, use the installed Ownership README and UPGRADING guide, exact configured subclasses and reviewed adoption before accepting traffic. Disabling or removing Ownership is not rollback; Core retains durable denial guards. Keep existing tenant IDs and migration history. The explicit Tenancy bridge remains deprecated through major 6; its retirement is a separate reviewed migration no earlier than major 7.


## Major 5

All 21 suite members require `^5.0`. Billing and Payments remain separate optional `^5.0` installations. Every NVL family member shares major 5; minors and patches may advance independently. Stable 5.0.0 mirror tags are published and indexed by Packagist; a fresh PHP 8.4/Laravel 13 public installation is verified.

Review each installed package's upgrade guide before changing Composer constraints. Back up package storage and Laravel migration history, pause affected writes and workers, and choose one migration owner. Configure canonical `nvl-<package>.php` files and `NVL_<PACKAGE>_*` env inputs before rebuilding config caches. Legacy config/env inputs require explicit Core compatibility and are retained for one major.

Run `php artisan nvl:schema:upgrade --package=<installed-package> --claim-legacy --migration-owner=vendor --dry-run --format=json` only for NVL legacy storage you own. A published owner uses `--migration-owner=published`, explicitly declared copies and disabled vendor loading. Inspect and apply the identical reviewed selection, then run `php artisan nvl:schema:preflight` before Laravel migrations. Core preserves native and custom host migrators; it does not rewrite published files or claim host tables.

Auth ingress and principal, password-broker and Spatie storage adoption start disabled. Activity global model replacement, Cashier routes/models, global Markdown presentation, generic Media permission bridging, and Data transformer replacement also require explicit adoption. Enabling Tenancy is a separate choice. Rebuild route caches and restart workers after configuration changes.

Declare host owner model classes through `nvl-core.owners`; NVL stores Laravel's `getMorphClass()`. Retained mismatched identities are diagnosed without data rewrites. Use the new Media `HasNvlMedia` / `InteractsWithNvlMedia` capability and `nvlMedia()` API when composing with Spatie Media.

Run `php artisan nvl:doctor --strict --format=json` before resuming traffic. Inspect task outbox scheduler/lock readiness and native failed-job persistence. Retry quarantined envelopes with `nvl:queue:retry`; ordinary failed jobs retain native retry behavior. Major-5 tags require exact-source local Dagger evidence, including MySQL/PostgreSQL contracts and sealed Tenancy consumers, then public artifact verification. MariaDB and additional compatibility legs need separate evidence.

See the [installation guide](docs/installation.md) and each public package's `UPGRADING.md` for exact options, compatibility groups and route cutover.
