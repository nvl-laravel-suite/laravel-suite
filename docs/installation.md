# Installing NVL Light

Light targets PHP 8.4 and Laravel 13. This branch prepares family 7; use a matching release when available. Do not combine published 6.x packages with the native contracts described here.

Install only the package capabilities you need, or select the suite metapackage for its declared composition. Composer installs required dependencies and Laravel normally discovers their providers. Optional integrations require their own dependencies and configuration. Check the installed package's manifest and README rather than assuming every suite feature is enabled.

## Configure and migrate

Package defaults load without publishing. Publish configuration only when overrides are needed, for example `php artisan vendor:publish --tag=nvl-media-config`. Review copied configuration on upgrades; publishing does not merge future defaults into host files.

For stateful packages choose exactly one migration source: vendor loading or published host migrations. Keep vendor loading disabled when executing published copies. Existing table names are not proof of migration ownership. Back up storage and history, review the applicable schema/adoption plan, then run `nvl:schema:preflight` for the same connection and selected paths as the real migration.

Configure registered resources, explicit policies, host adapters, queues and optional storage/provider dependencies before enabling operations. Use the package's Doctor command where supplied. Rebuild configuration/event caches and restart workers after deployment.

## Development guidance

Publish the selected package's skill using its documented Laravel publish tag, such as `nvl-media-skills`, or discover bundled guidance through Laravel Boost. Review local customizations before replacing copied skills. Skills do not enable runtime features.

The consumer owns HTTP endpoints, Actions, data classes, response resources and frontend types. See [getting started](getting-started.md), [native consumer APIs](consumer-api.md), and each installed package's README for its actual service and configuration contract.
