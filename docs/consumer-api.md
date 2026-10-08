# Public consumer API

Use the installed package's `@api` catalog and README as the supported contract. Core ships `support/resources/consumer-api.json`; each leaf ships `resources/consumer-api.json`. Catalogs describe public symbols and the documented fields/identity methods of returned package model handles. They do not grant ownership of package persistence. The [getting-started guide](getting-started.md) demonstrates an authorized public write/read and a host-only contract mock.

## Inject capabilities

Inject the focused `Nvl\<Package>\Contracts\...` interface instead of constructing the concrete Action. Native conditional defaults preserve a host binding whether installed before the provider or deliberately replaced later. Methods retain the native arguments, result types, defaults and exceptions. A returned Eloquent model is a documented identity/data handle: call package contracts for another read or mutation; do not continue with package-model queries, writes, raw owned-table SQL or a package-owned capability relationship as a query.

Use Comments `ListOwnerCommentSummariesContract`, Media `ListOwnerMediaSlotsContract`, Metafields `ListAuthorizedOwnersMetafieldsContract` and Taxonomy `ListOwnerTaxonomyTermsContract` for bounded many-owner reads. They preserve Laravel morph identity plus native keys, explicit empty results, request order and package/host authorization. Use the documented host-query adapters for correlated reads. Extension contracts exposing a Builder are deliberate constrained integration seams; their existence is not permission to query arbitrary package tables.

## Test your application

Bind Mockery or a native implementation of the exact public interface into your test application's container. Script the native return DTO/model handle; assert your host arguments. Comments has no universal library fake. Media provides `FakeMediaLibrary`/`FakeMediaUploadBuilder`; Payments provides `FakePaymentGateway`; Billing provides `FakeBillingGateway` and `FakeSubscriptionReader`. Their `fake($container)` helpers use the supplied application, with no global registry or implicit IO. Script FIFO outcomes explicitly; unscripted calls fail, and attempts are recorded before configured failure. They do not execute real package lifecycles.

Runtime factories serve the supported package model inventory. Ordinary `make()` may persist declared package parents. Use `withoutParents()->make()` for detached handles, and explicit native persisted owners/parents for relational `create()` fixtures. Foreign-connection or tenant-mismatched parents fail admission. Enabling tenancy requires explicit active persisted directory/context state; factories do not opt it in. Media fixtures create no files, Payments/Billing fixtures make no Stripe calls, and pending Template fixtures prove neither source definitions nor renderer readiness. Core/CSV/Filterable/Primitives/Translatable have no invented persistent factory. Settings `InteractsWithSettings` registers definitions only.

## Optional PHPStan enforcement

```neon
includes:
    - vendor/nvl/core/support/consumer-audit.neon
parameters:
    nvlConsumer:
        testPaths: [tests]
        tableNames: []
        exceptions: []
```

The shipped rules discover installed Composer package catalogs without Laravel/suite boot. Diagnostic identifiers are `nvl.consumer.internalApi`, `nvl.consumer.packageQuery`, `nvl.consumer.packageWrite`, `nvl.consumer.capabilityRelation` and `nvl.consumer.ownedTable`. Configure exact additional table ownership only when the host overrides names. Exceptions specify `file`, `identifier`, `symbol`, and a reason; do not use a global ignore pattern to erase ownership diagnostics. The rules cannot prove dynamic PHP or runtime authorization. The unpublished suite workbench command is not a consumer dependency; its maintenance runtime/adoption output is separate.

## Events and failures

Every package has `docs/events.md` and a schema-1 `resources/event-catalog.json`, including explicit empty catalogs. The 56 migrated and four added domain event families expose immutable schemaVersion=1 and model-free snapshots. Source-aware callbacks register inside actual writer transactions and publish after their outer commit; rollback discards them. This is process-local publication, not durable delivery or exactly-once processing. Mail observational listener/report failures remain contained. Native Auth framework events retain their original classes; custom/non-Eloquent storage interfaces without a connection seam retain the documented synchronous limitation.

Only these six old names are aliases during major 5 (removal no earlier than major 6):

| Old name | Canonical name |
| --- | --- |
| `Nvl\Activity\Events\ActivityLogPurgeQueuedEvent` | `Nvl\Activity\Events\ActivityLogPurgeQueued` |
| `Nvl\Media\Events\MediaUploadedEvent` | `Nvl\Media\Events\MediaUploaded` |
| `Nvl\Metafields\Events\MetafieldSetEvent` | `Nvl\Metafields\Events\MetafieldSet` |
| `Nvl\Metafields\Events\MetafieldsSyncedEvent` | `Nvl\Metafields\Events\MetafieldsSynced` |
| `Nvl\Forms\Events\FormChangedEvent` | `Nvl\Forms\Events\FormChanged` |
| `Nvl\Forms\Events\FormEntryChangedEvent` | `Nvl\Forms\Events\FormEntryChanged` |

Move imports, exact listeners/fakes and old model-field access to the canonical schemas. Suffix wildcard listeners need explicit migration; aliases are not a second wildcard dispatch. Laravel's existing dispatcher remains installed. The EventAliases adapter canonicalizes explicit registrations for a custom dispatcher without assuming its internals. Drain old queued objects, rebuild event cache and restart workers.

`PackageException` is the recognizable marker; `RespondableException` declares safe metadata. Opt into `PackageExceptionRenderer` in the host's existing exception setup for exactly `{message,code,context}` JSON. Non-JSON, marker-only and unrelated failures retain host handling. `binding_required` is a configuration error, distinct from authorization denial by an actual adapter. Read package README tables for code/status/context/translation keys. Do not show diagnostic messages, previous exception text or binding class names to users. Host request locale determines translated copy. See [frontend consumption](frontend-consumption.md).

## Evidence scope

The 5.0.0 family passed the final local Dagger release gate and fresh public PHP 8.4/Laravel 13 installation. The [verification policy](../README.md#verification-and-releases) distinguishes verified owning contracts/native-service checks from additional compatibility and independent archive matrices requiring separate evidence. Hosts must exercise their own bindings, authorization and adoption.

Owner capability relations `metafields()`, `comments()`, `nvlMediaAssociations()` and `termables()` are enforced statically, not at runtime. They remain ordinary Eloquent relations for package internals. Enable `vendor/nvl/core/support/consumer-audit.neon` in the host PHPStan configuration, and use public workflow contracts and batch readers in consumer code. The static rules do not replace runtime authorization.
