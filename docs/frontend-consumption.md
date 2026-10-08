# Consume NVL data from a frontend

NVL packages provide typed data and authorized backend capabilities. Your host owns routes, authentication, request validation, request locale, UI and frontend framework dependencies. Start with [the standalone Comments tutorial](getting-started.md).

## Generate the installed contracts

```sh
php artisan nvl:data:types:generate --fail-on-warning
php artisan nvl:data:types:check --fail-on-warning
```

The default entry point is `resources/js/types/generated.d.ts`, which references per-package ambient namespaces. Include it in your host TypeScript project. Use exact names such as `Nvl.Comments.Data.Mutations.CreateCommentData` and `Nvl.Comments.Data.OwnerCommentSummaries`; these are global declarations, not imports from a fictional npm package.

[consumer.ts](examples/getting-started/typescript/consumer.ts) contains the shared input, page props, response guard and fetch example; [tsconfig.json](examples/getting-started/typescript/tsconfig.json) includes that source. Copy both files into `resources/js/nvl-comments/` in the host. The reference points to the default generated declarations in `resources/js/types/generated.d.ts`; adjust it if the host uses a custom output directory. The shared example passed strict TypeScript compilation against generated declarations. After generating types in the host, run:

```sh
npx tsc --noEmit -p resources/js/nvl-comments/tsconfig.json
```

Run generation/check in the host verification pipeline whenever backend DTOs change. Maintainers use local Dagger. Commit the generated declarations according to your application's policy and fail CI on stale output. TypeScript checks development contracts; it cannot validate untrusted JSON at runtime or grant permissions.

## Inertia, React and Vue props

Have an authorized host controller pass the reader's serialized DTO and named-route URL, for example `summaries` and `createUrl`. Never accept owner morph types or tenant scope from an Inertia prop and treat them as authorization. The [shared ArticleCommentsProps](examples/getting-started/typescript/consumer.ts) type is framework-independent:

```tsx
// React or an Inertia React page in a host that has selected those dependencies.
import type { ArticleCommentsProps } from './consumer';
export function ArticleComments({ articleId, summaries }: ArticleCommentsProps) {
    return <div data-article-id={articleId}>{summaries.order.length} article summaries</div>;
}
```

```vue
<script setup lang="ts">
import type { ArticleCommentsProps } from './consumer';
const props = defineProps<ArticleCommentsProps>();
</script>
<template><div :data-article-id="props.articleId">{{ props.summaries.order.length }} article summaries</div></template>
```

Install React, Vue or the relevant Inertia adapter only in the host selecting that framework. The shared source has no framework dependency. Framework-specific examples require the host's framework CI; their execution is not claimed by compiling the shared TypeScript file.

## Submit and handle failures

Use a host-generated named-route URL or an explicitly configured API URL. The shared fetch example sends JSON with `Accept: application/json`, a caller-supplied CSRF token and same-origin credentials. Use your selected bearer/session transport rules for cross-origin APIs. Request DTO names do not replace server-side validation, target resolution or authorization. Render plain/Markdown comment text safely; disable unsafe HTML or apply the host's sanitizer.

Laravel validation uses its established validation response. Separately, an opted-in Core package exception renderer produces exactly:

```json
{"message":"The operation could not be completed.","code":"operation_failed","context":{}}
```

The frontend may branch on stable `code` for a translated local interaction, while `message` is already safe server-localized copy. Preserve `context` as an object and use only the keys documented by the package's README error table. Handle `binding_required` as unavailable configuration and report it operationally; do not expose adapter class names or raw exception text. Keep 401/403/404/409/410/422/429/500 behavior from the selected backend failure rather than treating every response as 422. Request locale remains the host's responsibility.

The TypeScript guard in the fixture checks the envelope shape at runtime. It deliberately leaves successful JSON as `unknown` until the host validates its chosen response. Do not cast arbitrary response JSON to a generated DTO and assume it is trustworthy.

## Reads, effects and migrations

Use C1 batched readers for Comments owner summaries and Media slots. Do not ship backend package-model queries or full capability relation dumps merely because a UI needs a count. Consumer test doubles belong in tests: Mockery/native container bindings, the shipped Media/Stripe fakes, and runtime model factories isolate host workflows but are not production adapters.

After an event schema migration, drain old jobs, replace the six deprecated names and suffix-based wildcard assumptions, rebuild event caches, and restart workers. Event schema versions and scheduled-mail factory payload versions are separate contracts. Callbacks publish after the actual local source commit; they are not durable delivery or exactly-once promises. Consult each package's `docs/events.md` and README error section.
