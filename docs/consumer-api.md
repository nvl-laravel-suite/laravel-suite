# Native consumer APIs

NVL Light exposes focused domain services, ordinary Eloquent models, enums, validated value objects and documented arrays. Package READMEs describe the supported entry points; source PHPDoc is authoritative for arguments, patch semantics and return shapes. See the [package index](https://github.com/nvl-laravel-suite).

Applications own orchestration Actions, DTOs, HTTP controllers/routes, request validation, API resources, exception rendering and frontend contracts. Map untrusted requests into native inputs and pass explicit persisted actors. Never derive a privileged actor, owner or permission from a submitted identifier. System actors identify background work and still require the package's explicit policy admission.

Models support native Eloquent use. Domain services enforce their documented authorization, locking, invariants, cleanup and event behavior; direct writes do not invoke those complete workflows. Read services retain their documented privacy, batching and projection guarantees. Avoid exposing entire models or private relations as application JSON.

## Optional ownership and integrations

Unowned applications need no owner context. Enabled Ownership uses configured resource declarations and host admission policies; it complements business authorization. Preserve canonical class/key/table/connection identity and reviewed adoption state. See [Ownership](https://github.com/nvl-laravel-suite/ownership). The retired Tenancy bridge is unavailable in Light.

Install optional adapters only when the application selects their features. Provider presence is not permission. Configure the selected package's bindings, schema, registries, storage and queues before calling it.

## Testing and effects

Use native container substitutes at real integration seams and package factories for persisted fixtures. Gateway fakes isolate external calls; they do not prove local persistence, policy, rollback or recovery behavior. Exercise those with real service calls and the relevant database/storage profile.

Package event guides describe payloads and publication timing. Source-aware after-commit publication is process-local and is not durable delivery or an exactly-once guarantee. Preserve explicit queue ownership envelopes and recovery journals. Applications select their own exception-to-response mapping and must not expose diagnostic details.

## Operation contracts

Each package ships a generated `resources/consumer-api.json` catalog (Core uses `support/resources/consumer-api.json`). Only advertised `@api` symbols are supported extension surfaces; `@internal` collaborators can change. Source PHPDoc provides native array shapes for IDE completion and PHPStan. Reuse those aliases with `@phpstan-import-type` in consumer code when useful; no DTO library is required.

Read each operation's contract before mapping a request. In particular:

| Concern | Consumer rule |
| --- | --- |
| Accepted fields | Pass only the documented keys; models' fillable fields are not a service input schema. |
| Omission and null | Preserve both. Null clears only where the operation permits it. |
| Update mode | Check whether the operation patches fields or requires a complete edit. Pages edits require slug, kind and status; Forms edits patch supplied fields. |
| Revisions | Supply the documented expected revision from the previously read state. A stale failure requires a reread and a deliberate retry. |
| Actor | Supply the trusted principal or explicitly admitted system identity. Actor IDs in input never grant authority. |
| Result | Map returned models, collections or bounded arrays into consumer resources. Keep secret-bearing domain results private. |
| Failure | Catch the documented domain exception at the application boundary; the host selects HTTP status and presentation. |
| Effects | Services own their transaction and recovery protocol. Committed callbacks follow the source connection's outer commit. |

A package's schema-defined values or arbitrary metadata may remain `array<string, mixed>` deliberately. Keep their validation in the owning service instead of adding a generic payload framework.

## Model extensions

Configure a concrete subclass of the advertised native model before providers boot. Each package README lists its actual model slots; Auth groups slots under their feature configuration. The choice is frozen for the application lifetime and applies to native writes, reads, factories, relationships and worker restoration.

For example, a consumer can extend `Nvl\Pages\Models\Page`, add a `subtitle` cast or a relation, and configure:

```php
// config/nvl-pages.php
return ['models' => ['page' => App\Models\Page::class]];
```

Retain required casts, identity, ownership scopes, query/persistence hooks and native invariant listeners. Add behavior with relations, accessors, observers or supported contracts. Configure storage through package-level `connection` and `tables` settings; an independently overridden model table or connection is not a supported replacement seam. Added columns need host migrations. Model extension does not add accepted service mutation fields: use an advertised extension mapper where provided, or compose a separately authorized host operation.

Package model configuration does not replace arbitrary technical receipt, lock or journal records. Preserve host-authored morph identities and their stored values. Replacement classes and deployment configuration must be available in queue workers too.

## Container extensions and composition

Bind focused host contracts in an application provider's `register()` method, before operations resolve. Package defaults use host-preserving bindings. For example:

```php
$this->app->bind(
    Nvl\Pages\Contracts\PageAuthorization::class,
    App\Policies\PageAccess::class,
);
```

`PageAccess` implements the native interface and must throw on denial, including unapproved background calls. The package still owns input validation, ownership, revisions, locking and transactions. Selected provider integrations must also satisfy their advertised recovery and connection contracts.

An application Action may compose complete operations under an outer transaction **when they share the actual connection**. For example, create a parent and child through `PageLifecycle`, then read them through `PageReader`. If child authorization fails, roll back the parent with the host transaction. Native service calls need no HTTP request or DTO. See executable [model replacement and composition examples](https://github.com/nvl-laravel-suite/pages/blob/main/tests/Ownership/ConfiguredModelTest.php). Separate connections or external systems need an explicit recovery/outbox design; a host transaction cannot make them atomic.
