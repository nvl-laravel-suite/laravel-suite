# NVL Laravel Suite

`nvl/laravel-suite` 3.x is a Composer metapackage that installs the 21 independently published NVL Laravel packages. It contains no application code or service provider. Each package has its own repository, release tags, configuration, and documentation.

## Install

Use PHP 8.4+ and Laravel 13:

```bash
composer require nvl/laravel-suite:^3.0
```

Follow the [installation and publishing guide](INSTALLATION.md) before migrating an existing application. It lists every package's configuration, migration, skill, translation, view, and adoption publish tag.

For a smaller application, install only the packages you need, for example:

```bash
composer require nvl/core:^2.0 nvl/filterable:^2.0
```

Composer resolves each package's declared dependencies. Core contains the shared Support and Data namespaces. Filterable remains an independent package. Tenancy is required by packages that use its contracts, but tenant behavior starts disabled. The full suite also installs Auth, whose package ingress is enabled by default and can select package authentication models; review its adoption requirements before installing the suite in an existing application. Consult each package README before enabling routes, migrations, or integrations.

Package-owned agent skills can be published from the relevant provider with its documented `vendor:publish` tag. Core provides both `support-skills` and `data-skills`; for example, Filterable provides `filterable-skills`. Installing the suite does not publish those files automatically.

## Packages

**Foundation and localization:** [Core](https://github.com/nvl-laravel-suite/core), [Filterable](https://github.com/nvl-laravel-suite/filterable), [Primitives](https://github.com/nvl-laravel-suite/primitives), [Tenancy](https://github.com/nvl-laravel-suite/tenancy), [Translatable](https://github.com/nvl-laravel-suite/translatable), [Translations](https://github.com/nvl-laravel-suite/translations).

**Application capabilities:** [Activity](https://github.com/nvl-laravel-suite/activity), [Auth](https://github.com/nvl-laravel-suite/auth), [Comments](https://github.com/nvl-laravel-suite/comments), [Content](https://github.com/nvl-laravel-suite/content), [CSV](https://github.com/nvl-laravel-suite/csv), [Forms](https://github.com/nvl-laravel-suite/forms), [Mail Notifications](https://github.com/nvl-laravel-suite/mail-notifications), [Media](https://github.com/nvl-laravel-suite/media), [Metafields](https://github.com/nvl-laravel-suite/metafields), [Pages](https://github.com/nvl-laravel-suite/pages), [SEO](https://github.com/nvl-laravel-suite/seo), [Settings](https://github.com/nvl-laravel-suite/settings), [Tasks](https://github.com/nvl-laravel-suite/tasks), [Taxonomy](https://github.com/nvl-laravel-suite/taxonomy), [Templates](https://github.com/nvl-laravel-suite/templates).

These repositories are published from a private source monorepo. Browse the [organization profile](https://github.com/nvl-laravel-suite) for package descriptions and use each package's README for its public documentation. Find the [affected package repository](https://github.com/orgs/nvl-laravel-suite/repositories) and open an issue there, or open a [suite issue](https://github.com/nvl-laravel-suite/laravel-suite/issues) for installation and composition questions. Maintainers apply changes in the source monorepo before publishing a mirror release; see [Contributing](CONTRIBUTING.md).

## Versions and support

The 3.x suite requires compatible 2.x versions of all 21 packages. The historical 2.x suite was a bundled library; upgrading to 3.x changes the Composer dependency graph. See the package READMEs for activation and migration guidance. Report vulnerabilities through the affected package's private vulnerability reporting form, or use the [suite's private form](https://github.com/nvl-laravel-suite/laravel-suite/security/advisories/new) if the affected package is uncertain.

Released under the [MIT License](LICENSE).
