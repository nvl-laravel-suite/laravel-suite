# Native consumer APIs

NVL Light exposes focused domain services, ordinary Eloquent models, enums, validated value objects and documented arrays. Package READMEs describe the supported entry points; source PHPDoc is authoritative for arguments, patch semantics and return shapes. See the [package index](../README.md).

Applications own orchestration Actions, DTOs, HTTP controllers/routes, request validation, API resources, exception rendering and frontend contracts. Map untrusted requests into native inputs and pass explicit persisted actors. Never derive a privileged actor, owner or permission from a submitted identifier. System actors identify background work and still require the package's explicit policy admission.

Models support native Eloquent use. Domain services enforce their documented authorization, locking, invariants, cleanup and event behavior; direct writes do not invoke those complete workflows. Read services retain their documented privacy, batching and projection guarantees. Avoid exposing entire models or private relations as application JSON.

## Portable package skills

Every native package ships a generic Agent Skills folder at `resources/boost/skills/nvl-<capability>`; Core's capability is `nvl-support`. Laravel Boost discovers these from Composer packages with `php artisan boost:install --skills`. A package's `nvl-<package>-skills` tag copies its complete tree to `.agents/skills`. References remain relative to that tree and work without this monorepo. The Composer metapackage has no runtime files; its dependencies supply the actual skills and publishable resources.

Each entrypoint routes to package-specific usage recipes, generated installation/publication guidance and a generated API reference. Providers are Composer-discovered, not published into application code. Config, migrations, translations and views retain their declared separate tags. A published migration choice disables the vendor loader before execution; existing applied migrations stay immutable. Publish migrations once: Laravel retimestamps them and repeat publication can create duplicate dated copies even without `--force`. Later config/skill updates should use their own tags.

For maintainers, update the reviewed contracts with `composer contracts:update` after an intentional API change, update the affected usage recipe, then run `composer skills:sync`. That command regenerates installation/API references and synchronizes entire skill trees into the development aggregate and Core re-export. `php tools/package-skill-docs.php --check` detects stale generated references. The focused publishing/skill/documentation tests verify Laravel Boost discovery, real Laravel publication in an isolated consumer, portable links, PHP recipe syntax/imports and complete mirror contents. No published helper requires this development toolchain.

## Model replacement

Extend the native primary model, then select the exact concrete class in configuration before providers boot. The selected class flows through native services, relationships, factories, queued restoration and Ownership registration. Configure one model per resource for the deployment; changing that class after resource compilation is rejected. Existing native morph identities remain valid. Required UUID keys, native casts, persistence hooks and ownership declarations remain protected. Add application casts, relationships, accessors, traits and observers within those guarantees.

Each row below uses `nvl-<package>.models.<key>`:

| Package | Keys and native models |
| --- | --- |
| activity | `activity_log`: ActivityLog |
| billing | `account`: BillingAccount; `subscription`: BillingSubscription |
| comments | `comment`: Comment |
| content | `block`: ContentBlock |
| forms | `form`: Form |
| mail-notifications | `notification`: MailNotification; `scheduled_message`: ScheduledMailMessage |
| media | `media`: Media |
| metafields | `definition`: MetafieldDefinition |
| pages | `page`: Page |
| payments | `attempt`: PaymentAttempt; `refund`: PaymentRefund |
| seo | `profile`: SeoProfile; `redirect`: SeoRedirect |
| settings | `setting`: Setting |
| tasks | `task`: Task |
| taxonomy | `term`: Term |
| templates | `template`: Template |
| translations | `override`: TranslationOverride |

Auth retains its feature-specific User, Role, Permission, PersonalAccessToken and OwnerMembership slots and independently configured RBAC principal. Model-agnostic packages consume host models through their existing contracts. Protocol rows such as leases, receipts, locks, delivery attempts, translation children and provider journals remain native implementation details.

Activity, Billing, Payments, Mail and SEO replacements retain their package-configured tables and actual connections because dependent native records share those transactions. Configure their package storage to move the complete graph together. A subclass-only storage split is rejected. Settings also supports a subclass-specific source connection. Follow each package's own storage contract and publish its migrations before installation when the host needs to adjust storage; retain required keys, indexes and constraints.

## Repeatable package structure

Use this recipe when maintaining or adding a package. Include each directory when the package has that responsibility. Core's existing `support/src` root owns the `Nvl\Support` namespace; other code packages use `src`.

| Location | Responsibility |
| --- | --- |
| `Services` | Cohesive complete operations and their internal collaborators |
| `Models`, `Casts`, `Relations`, `Traits` | Persistence, model composition and safe query behavior |
| `Contracts` | Host authorization, replaceable infrastructure and cross-package capabilities |
| `ValueObjects`, `Enums` | Validated domain identities, rules and bounded states |
| `Providers` | Bindings, configuration, optional integration registration and resource publishing |
| `Console/Commands`, `Jobs` | Thin operational adapters to complete services |
| `Integrations`, `Ownership` | Explicit optional capabilities and isolation/adoption adapters |
| `Definitions`, `Support` | Package-specific declarations and implementation details |
| `config`, `resources/config` | Complete defaults and a minimal published override template |
| `database`, `tests` | Owned persistence and the tests proving its behavior |

Domain-specific directories such as Media `Conversions`/`Slots`, Content `Schema`/`FieldTypes`, Templates `Rendering`, and Primitives `Rules` describe real capabilities. Keep those names. A new package does not need an empty copy of every directory.

Every service class explicitly declares `@api` or `@internal`. The source-owned `@api` declarations generate `resources/consumer-api.json`; consumers can find the supported entry points without relying on helper class names. Internal services may assume an enclosing authorized operation and transaction. Constructors and helper accessibility alone do not promise a complete consumer operation.

Use purpose-based service names such as `Reader`, `Writer`, `Lifecycle`, `Assignments`, `Renderer` or `Checkout`. Keep one implementation of each capability; a public naming convention does not require a forwarding facade or interface for every concrete class. Consumers substitute policies and provider gateways through the declared contracts.

## Operation contract

PHP parameters and object properties use camelCase. Shared native array controls use `expected_revision`, `per_page`, `idempotency_key` and `translation_mode`. External provider payloads retain their protocol's names. Document domain records explicitly, including allowed keys, nullable values, replacement versus patch behavior, pagination bounds and loaded relations. Use PHPStan shapes/type aliases for fixed records and typed maps for genuinely dynamic metadata. Preserve the distinction between an omitted field and an explicitly permitted null.

A public service validates its own input, resolves trusted identity, enforces authorization and ownership, and owns the transaction/locking required by its operation. The ordering must conceal inaccessible records and reject invalid input before writes. A shared checklist supplies these guarantees; domains retain the checks their operation actually needs. Consumers may compose operations in an outer transaction on the same connection. Delivery follows that outer commit. Cross-connection writes and external providers require their documented recovery protocol.

Models contain casts, relationships, scopes and persistence declarations. Services contain lifecycle changes, transactions and delivery decisions. Read services return admitted models, collections/paginators or bounded privacy-safe records. Raw model serialization is a consumer decision and must not expose secrets or private relations.

Providers use conditional bindings for host extension contracts. Stateless configuration/registries can be singletons; ownership, locale and mutable request/job caches use scoped lifetimes. Preserve host bindings and publication groups. Merge complete package defaults before applying the published override template. Optional integration code must boot with the optional library physically absent and reject explicitly requested unavailable operations.

Reuse the existing Core configuration, configured-model resolution, ownership ports and connection-aware domain-event dispatcher. Keep domain workflows in their owning package. Extract a new shared primitive only when equivalent implementations have the same semantics, including ordering, failure and persisted-hash behavior. Durable outboxes, provider journals and replay rules remain explicit domain protocols.

## Implementation references

Settings `SettingReader`/`SettingWriter` and `SettingRecord` show fixed result shapes, revision-checked mutation, cache invalidation and an enclosing transaction. Media demonstrates filesystem staging and cleanup; Payments demonstrates provider operation journals and recovery. These are implementation references to adapt to a domain, rather than required parent classes.

Package READMEs record the entry points and meaningful differences for every capability. `PackageParityTest`, `LightRuntimeContractTest`, the consumer catalog contracts and owning service tests enforce the recipe. For a new operation, exercise invalid/protected input, policy rejection, null/omission, transaction rollback and applicable revision/replay/recovery behavior directly through its public service. Reuse existing scenarios instead of duplicating the same invariant through HTTP.

## Optional ownership and integrations

Unowned applications need no owner context. Enabled Ownership uses configured resource declarations and host admission policies; it complements business authorization. Preserve canonical class/key/table/connection identity and reviewed adoption state. See [Ownership](https://github.com/nvl-laravel-suite/ownership#readme). The retired Tenancy bridge is unavailable in Light.

Install optional adapters only when the application selects their features. Provider presence is not permission. Configure the selected package's bindings, schema, registries, storage and queues before calling it.

## Testing and effects

Use native container substitutes at real integration seams and package factories for persisted fixtures. Gateway fakes isolate external calls; they do not prove local persistence, policy, rollback or recovery behavior. Exercise those with real service calls and the relevant database/storage profile.

Package event guides describe payloads and publication timing. Source-aware after-commit publication is process-local and is not durable delivery or an exactly-once guarantee. Preserve explicit queue ownership envelopes and recovery journals. Applications select their own exception-to-response mapping and must not expose diagnostic details.
