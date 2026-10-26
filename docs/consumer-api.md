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
