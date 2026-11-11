# NVL Laravel Suite

The suite is a Composer metapackage selecting independent NVL packages. It has no application provider or domain services of its own. NVL Light targets PHP 8.4 and Laravel 13 and is published as the coordinated breaking family 7. Published 6.x releases use earlier contracts.

Prefer individual packages when the application needs a smaller dependency graph. Use the metapackage manifest for the exact selection. Billing and Payments are separate optional integrations. Native Ownership is configured explicitly; the retired Tenancy bridge is not a Light integration.

This Composer metapackage installs no files and therefore has no provider, config, migrations or skill folder of its own. Every installed runtime package supplies its own portable Agent Skill at `resources/boost/skills`, discovered by `php artisan boost:install --skills`. Publish configuration and migrations through the specific package tags documented in that skill; do not invent suite wrapper assets.

Applications own Actions, DTOs, controllers, routes, response resources and frontend types. Packages expose native services, Eloquent models, enums, domain values and documented arrays. Read the installed package's README and source PHPDoc for its policies, signatures and optional adapters.

Start with [installation](docs/installation.md), [getting started](docs/getting-started.md), [consumer APIs](docs/consumer-api.md), and [UPGRADING.md](UPGRADING.md). Package repositories in the [NVL organization](https://github.com/nvl-laravel-suite) are public release mirrors; their versioned release history remains historical evidence.

Choose one migration source, configure required host bindings and run relevant Doctor commands before enabling features. Installation does not establish authorization or operational readiness. Preserve source connections, durable operation IDs, queue envelopes and recovery journals during deployment; rebuild caches and restart workers.

Released under the [MIT License](LICENSE).
