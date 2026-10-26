# Frontend consumption

NVL Light provides backend services and models. The consumer owns HTTP endpoints, authentication, request locale, response resources and frontend types. It may use its own DTO/type generator, Inertia, React, Vue or another transport without adding those dependencies to NVL.

Validate requests at the host boundary, resolve trusted targets and explicit actors, then call the documented native service. Project only the fields the audience may see; never serialize private model attributes or full capability relations simply because they are loaded. Preserve reader privacy and batching guarantees.

Generate named-route URLs in the host and use its CSRF/session or bearer authentication rules. Validate untrusted JSON at runtime; a TypeScript type assertion is not validation. Map package exceptions through the application's response policy, preserving meaningful status distinctions and hiding diagnostics and provider credentials.

The consumer owns mutation and response types. There is no package TypeScript publication or Core generated-type command in Light. See [native APIs](consumer-api.md) and each [package guide](https://github.com/nvl-laravel-suite) for the source contracts being mapped.
