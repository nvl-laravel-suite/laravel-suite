# NVL Laravel Suite

## Quickstart

Install individual capabilities where possible. The metapackage composes 21 members and has no runtime services; Billing, Payments and the deprecated Tenancy bridge are separate opt-in installs. Ownership is selected but defaults off. Follow the [standalone getting-started guide](https://github.com/nvl-laravel-suite/laravel-suite/blob/main/docs/getting-started.md), then use `nvl:install <loaded-package>` and each package's native bindings/configuration. Installation does not adopt host Auth/Tenancy or enable ingress.

## Release status

All 25 distributions are prepared at 6.0.0. The final merged source still needs its exact-commit release evidence before mirror dispatch, followed by tags, Packagist indexing and a fresh public installation. Historical public 5.x checks below do not certify 6.0.0. Enabled Ownership requires Laravel 13; Laravel 12 remains usable for disabled/unowned installations.

## Testing your app

Use each installed package's focused contracts, native container substitution and shipped fixtures. The metapackage has no model factory, fake or event schema. Include Core's optional consumer-audit.neon in host PHPStan; suite workbench tools are maintenance-only. See each member's README and events catalog for runtime contracts. Historical evidence: the published 5.x family is verified through the local Dagger release gate on PHP 8.4/Laravel 13, including owning suites, MySQL/PostgreSQL persistence contracts and sealed Tenancy consumers. Fresh public Composer installation, discovery and configuration/route caching are verified. PHP 8.5, Laravel 12, MariaDB and the full independent archive matrix require separate evidence. See the [verification and release policy](https://github.com/nvl-laravel-suite/laravel-suite#verification-and-releases).

`nvl/laravel-suite` 6.x is a Composer metapackage that installs the 21 independently published NVL Laravel packages. It contains no application code or service provider. Each package has its own repository, release tags, configuration, and documentation.

## Install

The verified release configuration is PHP 8.4 with Laravel 13. Composer also admits PHP 8.5 and Laravel 12; those compatibility legs require separate evidence. This source prepares 6.0.0; install from Packagist after its mirror tags are indexed. Until then use reviewed local archives. The prepared family uses:

```bash
composer require nvl/laravel-suite:^6.0
```

Follow the [installation and publishing guide](docs/installation.md) before migrating an existing application. It lists every package's configuration, migration, skill, translation, view, and adoption publish tag.

For a smaller application, install only the packages you need, for example:

```bash
composer require nvl/core:^6.0 nvl/filterable:^6.0
```

Composer resolves each package's declared dependencies. Core contains the shared Support and Data namespaces. Filterable remains an independent package. Tenancy is required by packages that use its contracts, but tenant behavior starts disabled. The full suite installs Auth with HTTP ingress and host adoption disabled. Principal, password broker and Spatie storage adoption are independent explicit choices; review their configured host targets before enabling them. Consult each package README before enabling routes, migrations, or integrations.

Package-owned agent skills can be published from the relevant provider with its documented `vendor:publish` tag. Use canonical `nvl-<package>-skills` tags documented by each provider; Core ships both Support and Data guidance. Installing the suite does not publish those files automatically.

## Packages

**Foundation and localization:** [Core](https://github.com/nvl-laravel-suite/core), [Filterable](https://github.com/nvl-laravel-suite/filterable), [Primitives](https://github.com/nvl-laravel-suite/primitives), [Tenancy](https://github.com/nvl-laravel-suite/tenancy), [Translatable](https://github.com/nvl-laravel-suite/translatable), [Translations](https://github.com/nvl-laravel-suite/translations).

**Application capabilities:** [Activity](https://github.com/nvl-laravel-suite/activity), [Auth](https://github.com/nvl-laravel-suite/auth), [Comments](https://github.com/nvl-laravel-suite/comments), [Content](https://github.com/nvl-laravel-suite/content), [CSV](https://github.com/nvl-laravel-suite/csv), [Forms](https://github.com/nvl-laravel-suite/forms), [Mail Notifications](https://github.com/nvl-laravel-suite/mail-notifications), [Media](https://github.com/nvl-laravel-suite/media), [Metafields](https://github.com/nvl-laravel-suite/metafields), [Pages](https://github.com/nvl-laravel-suite/pages), [SEO](https://github.com/nvl-laravel-suite/seo), [Settings](https://github.com/nvl-laravel-suite/settings), [Tasks](https://github.com/nvl-laravel-suite/tasks), [Taxonomy](https://github.com/nvl-laravel-suite/taxonomy), [Templates](https://github.com/nvl-laravel-suite/templates).

## How the repositories are maintained

The public package repositories are release mirrors of a private source monorepo. Each package has its own Composer dependencies and version tags. A compatible patch to one package can be released without publishing every other package or a new suite version. The suite metapackage needs its own release when its dependency constraints or public documentation change.

Maintainers merge and test changes in the source monorepo, then publish the affected public mirror tags. Packagist indexes those tags; consumer applications need only the public repositories. Direct edits and pull requests in a mirror do not update source. Browse the [organization profile](https://github.com/nvl-laravel-suite) for package descriptions and use each package's README for public documentation. Open an issue in the [affected package repository](https://github.com/orgs/nvl-laravel-suite/repositories), or a [suite issue](https://github.com/nvl-laravel-suite/laravel-suite/issues) for installation and composition questions. Proposed patches can be attached to an issue; see [Contributing](CONTRIBUTING.md).

## Versions and support

The prepared 6.x suite requires `^6.0` for all 21 selected members. Ownership replaces Tenancy in this composition; Billing, Payments and the deprecated Tenancy bridge are separate `^6.0` installs. All family majors move together; package minors and patches stay independent. Historical stable 5.0.0 tags for all 23 code packages and the metapackage are available on Packagist. Fresh public installation of the suite plus Billing and Payments passed, including package discovery and configuration/route caches. Historical suite 4.x selected Tasks 3.x and Activity 2.3; suite 3.x remained on Tasks 2.x. Before upgrading an existing application from suite 3.x, read the [Tasks 3 upgrade guide](https://github.com/nvl-laravel-suite/tasks/blob/main/UPGRADING.md): apply the new Tasks migrations using your existing migration ownership mode, review removed Content and Metafields integrations, and restart workers. The historical 2.x suite was a bundled library; upgrading from it also changes the Composer dependency graph. See each package README for activation and migration guidance. Report vulnerabilities through the affected package's private vulnerability reporting form, or use the [suite's private form](https://github.com/nvl-laravel-suite/laravel-suite/security/advisories/new) if the affected package is uncertain.

Released under the [MIT License](LICENSE).

## Canonical configuration ownership
This provider-free metapackage ships no config file or environment inputs of its own. Configure each installed capability through its shipped canonical files; Core ships both `nvl-core.php` and `nvl-data.php`.

## Verification and releases

Maintainer CI runs locally through a pinned Dagger engine on Docker. The release gate checks standards, maximum PHPStan analysis, root and owning package suites, MySQL 8.4/PostgreSQL 17 persistence contracts and native ownership cutover consumers with Redis and private storage. A Tenancy release additionally selects its sealed bridge proof. Passing evidence is bound to the exact clean source commit. The 5.0.0 gate passed at `4950de7e818ed1578b5753afe045d2c70637f32c` on 2026-10-08.

GitHub Actions is used only for explicitly requested package publishing. Pushes, pull requests and schedules start no hosted quality jobs; Dagger Cloud Checks are disabled. Each public repository is a release mirror of the private monorepo. Maintainers develop in source, verify locally, then publish an immutable package tag; Packagist indexes that tag. A compatible package patch can be released independently within family major 6.

Consumers use ordinary `composer require nvl/<package>:^6.0` without private repository access. Commit the application lockfile, review installed package defaults and upgrade guides, select migration ownership, bind host authorization, then run strict Doctor and the application's own tests before enabling features. Package verification covers the listed configurations; it does not certify an arbitrary host application or every declared compatibility leg.

## License

MIT. See [LICENSE](LICENSE).
