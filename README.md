# NVL Laravel Suite

`nvl/laravel-suite` 3.x is a Composer metapackage that installs the 21 independently published NVL Laravel packages. It contains no application code or service provider. Each package has its own repository, release tags, configuration, and documentation.

## Install

Use PHP 8.4+ and Laravel 13:

```bash
composer require nvl/laravel-suite:^3.0
```

For a smaller application, install only the packages you need, for example:

```bash
composer require nvl/core:^2.0 nvl/filterable:^2.0
```

Composer resolves each package's declared dependencies. Core contains the shared Support and Data namespaces. Filterable remains an independent package. Tenancy is required by packages that use its contracts, but tenant behavior starts disabled; installing a package does not enable its features. Consult each package README before enabling routes, migrations, or integrations.

Package-owned agent skills can be published from the relevant provider with its documented `vendor:publish` tag. Core provides both `support-skills` and `data-skills`; for example, Filterable provides `filterable-skills`. Installing the suite does not publish those files automatically.

## Packages

**Foundation and localization:** [Core](https://github.com/nvl-laravel-suite/core), [Filterable](https://github.com/nvl-laravel-suite/filterable), [Primitives](https://github.com/nvl-laravel-suite/primitives), [Tenancy](https://github.com/nvl-laravel-suite/tenancy), [Translatable](https://github.com/nvl-laravel-suite/translatable), [Translations](https://github.com/nvl-laravel-suite/translations).

**Application capabilities:** [Activity](https://github.com/nvl-laravel-suite/activity), [Auth](https://github.com/nvl-laravel-suite/auth), [Comments](https://github.com/nvl-laravel-suite/comments), [Content](https://github.com/nvl-laravel-suite/content), [CSV](https://github.com/nvl-laravel-suite/csv), [Forms](https://github.com/nvl-laravel-suite/forms), [Mail Notifications](https://github.com/nvl-laravel-suite/mail-notifications), [Media](https://github.com/nvl-laravel-suite/media), [Metafields](https://github.com/nvl-laravel-suite/metafields), [Pages](https://github.com/nvl-laravel-suite/pages), [SEO](https://github.com/nvl-laravel-suite/seo), [Settings](https://github.com/nvl-laravel-suite/settings), [Tasks](https://github.com/nvl-laravel-suite/tasks), [Taxonomy](https://github.com/nvl-laravel-suite/taxonomy), [Templates](https://github.com/nvl-laravel-suite/templates).

The [source monorepo](https://github.com/nicolasvlachos/nvl-laravel-suite) contains the package code, integration workbench, [capability catalog](https://github.com/nicolasvlachos/nvl-laravel-suite/blob/main/packages.md), and [release guide](https://github.com/nicolasvlachos/nvl-laravel-suite/blob/main/docs/releasing.md). Development changes and pull requests belong there; the organization repositories are publication mirrors.

## Versions and support

The 3.x suite requires compatible 2.x versions of all 21 packages. The historical 2.x suite was a bundled library; upgrading to 3.x changes the Composer dependency graph. See the package READMEs for activation and migration guidance. Report security issues through the [source repository's private advisory form](https://github.com/nicolasvlachos/nvl-laravel-suite/security/advisories/new).

Released under the [MIT License](LICENSE).
