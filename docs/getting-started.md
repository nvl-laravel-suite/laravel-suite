# Getting started with NVL Light

This guide describes the upcoming family 7 contracts on PHP 8.4 and Laravel 13. Select the matching release when available; published 6.x examples use older APIs.

1. Choose the package from the [package index](https://github.com/nvl-laravel-suite), then install its declared dependencies. Optional integrations are separate choices.
2. Read its configuration and native service PHPDoc. Publish configuration only when overrides are needed, using the package's documented Laravel publish tag.
3. For stateful packages, select exactly one migration source: enabled vendor loading or published host files. Back up existing storage and review migration/adoption plans before changes. Use schema preflight for the actual selected migration paths and connection.
4. Register host resources and bind required authorization or provider contracts. Keep features disabled until their schema and bindings are ready. An actor argument is identity, not implicit permission.
5. Inject the documented service into your own application Action or controller. Validate input, resolve the actor from trusted authentication, and map native results into your own response resources.
6. Test real authorized and denied operations, rollback, optional-feature absence and the selected storage/provider integration. Run the package Doctor where provided. Rebuild caches and restart workers during deployment.

For a concrete package, [Comments](https://github.com/nvl-laravel-suite/comments) documents its native writer, readers, policies and optional Media attachments. The host owns HTTP endpoints, request locale, serialization and frontend types. See [consumer APIs](consumer-api.md) and [frontend consumption](frontend-consumption.md).
