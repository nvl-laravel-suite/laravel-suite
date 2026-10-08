# Build a standalone Comments feature

This tutorial uses `nvl/comments:^5.0` in a Laravel host with PHP 8.4+. Composer installs its required Core and Filterable dependencies. Install only the package you need; the suite's workbench commands are not part of consumer applications. The declared Laravel/PHP support matrix and external acceptance status are tracked in [verification policy](../README.md#verification-and-releases).

The copyable host source is in [the Comments examples](examples/getting-started/). Its two PHP acceptance tests and strict TypeScript compilation passed. Keep the host's existing application provider and merge the shown methods rather than replacing unrelated application registration.

## Install and publish

```sh
composer require nvl/comments:^5.0
php artisan nvl:install comments --dry-run
php artisan nvl:install comments
```

The installer selects loaded packages, publishes common configuration without overwriting existing files, and reports selected required bindings. It does not enable routes, adopt authentication/tenancy, create schema, refresh caches or contact providers. `--force` is an explicit replacement with a backup. Review `config/nvl-comments.php` before proceeding. Complete defaults remain in the installed package; publish the complete defaults with the package's canonical `nvl-<package>-config` tag. The installer publishes minimal common settings; advanced settings remain available in the full defaults. There are two configuration tiers: common (minimal publication) and advanced (full publication). Required host adapters are reported separately when their capability is selected.

Choose one migration owner. For a new host, leave automatic package migration loading enabled and do not publish the same migrations. Existing package tables need the schema preflight and documented upgrade path before loading new vendor migrations:

```sh
php artisan nvl:schema:preflight --format=json
php artisan migrate
```

Back up existing production data and review [the upgrade guide](../UPGRADING.md) before running a schema upgrade. Do not install or enable Tenancy/Auth for this tutorial.

## Bind authorization and register the host owner

Copy [Article](examples/getting-started/app/Models/Article.php), [ArticleCommentTargetResolver](examples/getting-started/app/Comments/ArticleCommentTargetResolver.php), and [ApplicationCommentAuthorization](examples/getting-started/app/Comments/ApplicationCommentAuthorization.php). Give the host's `articles` table its native integer `id`, `title`, boolean `is_public`, and timestamps. The test fixture creates that table for its own examples; production uses your normal host migration.

Merge [AppServiceProvider's bindings](examples/getting-started/app/Providers/AppServiceProvider.php) into the host provider. It binds `CommentAuthorization`, `CommentQueryScope` and `CommentBatchQueryScope`, declares `Article::class` in `nvl-core.owners`, and allowlists the `article` HTTP target resolver. Merge your existing owner declarations instead of discarding them. The Laravel morph alias is `article`; the host integer key stays an integer on Article and becomes its exact string representation in public identity maps. Package UUIDs never require converting host primary keys to UUIDs.

The example policy admits only public articles, then delegates comment visibility and author permissions to the package's public baseline. Adapt it explicitly for private content or moderation. Never accept a target class, tenant ID, author ID or authorization decision from browser JSON. Keep moderation defaults; an author's pending comment is visible to that author through the Member audience, not automatically published to everyone.

## Write once and read in a batch

Copy [PostArticleComment](examples/getting-started/app/Comments/PostArticleComment.php). Inject its `CreateCommentContract` dependency in application workflows. At a host controller boundary, validate request input and supply the authenticated host principal:

```php
use App\Comments\PostArticleComment;
use Nvl\Comments\Contracts\ListOwnerCommentSummariesContract;
use Nvl\Comments\Data\CommentActorData;
use Nvl\Comments\Enums\CommentAudience;

$actor = CommentActorData::fromAuthenticatable($request->user());
$id = $postArticleComment->execute($article, $validated['body'], $actor);
$summaries = $commentSummaries->execute([$article], $actor, CommentAudience::Member);
```

Here `$postArticleComment` and `$commentSummaries` are injected `PostArticleComment` and `ListOwnerCommentSummariesContract`. The batch supports up to 100 persisted owners and a bounded latest list, including explicit empty summaries. Use this reader instead of querying package models, calling the package-owned `comments()` relationship as a query, or loading the full relation for a page of articles. Returned package models expose documented identity/data; mutations continue through contracts.

Return the reader's DTO as an explicit host response or Inertia prop. See [frontend consumption](frontend-consumption.md). Enable a package HTTP route group only after selecting the host middleware, authorization and prefix/name configuration. Prefer URLs generated from your host's named routes.

## Test the host feature

Install the host's normal Pest/Mockery/Faker development tools. Copy [CommentsQuickstartTest](examples/getting-started/CommentsQuickstartTest.php) into its `tests/Feature` folder. The first case persists a host Article and calls the real public write/read contracts. The second substitutes `CreateCommentContract` in the native container and uses `Comment::factory()->withoutParents()->make()` as a detached return fixture. Comments ships focused contracts and factories; it does not ship an invented general Comments fake. If your host migration already creates `articles`, remove the fixture-only `beforeEach` schema-creation block from the copied test. Keep the host migration as the single schema owner; the standalone maintenance fixture uses that block only because it has no host articles migration.

For a real owned fixture, use `Comment::factory()->forOwner($persistedArticle)->create()`. Ordinary factory `make()` may create declared package parents; `withoutParents()->make()` suppresses that expansion and admission for detached unit fixtures. Factories create rows, not authorization/workflow or provider effects. Supply active persisted tenants/parents when you explicitly enable tenancy; do not fabricate foreign owner IDs.

```sh
php artisan test --compact tests/Feature/CommentsQuickstartTest.php
```

Use `Event::fake()`, `Queue::fake()`, `Mail::fake()` or `Storage::fake()` only around the effects your host test intends to isolate. Commit timing tests must exercise real commits and listeners rather than an event fake or an always-open test transaction.

Add the optional shipped consumer rules to your host PHPStan configuration:

```neon
includes:
    - vendor/nvl/core/support/consumer-audit.neon
parameters:
    nvlConsumer:
        testPaths:
            - tests
        tableNames: []
        exceptions: []
```

The rules inspect installed public catalogs without booting the suite. They flag internal APIs, package-owned queries/writes and capability-relation queries. They do not prove runtime authorization or arbitrary dynamic PHP. Keep exceptions narrow with a file, identifier, symbol and reason.

## Select safe failure presentation

Rendering is opt-in. In `bootstrap/app.php`, retain host exception configuration and add a handler only for package failures:

```php
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Nvl\Support\Contracts\PackageException;
use Nvl\Support\Http\PackageExceptionRenderer;

->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (PackageException $exception, Request $request) {
        return app(PackageExceptionRenderer::class)->render($exception, $request);
    });
})
```

The renderer returns `null` for non-JSON or marker-only failures so Laravel/host handling continues. Its selected JSON shape is `{message, code, context}` with an object-valued empty context. It uses safe translated copy and the host-selected request locale, not exception diagnostic messages. Keep existing Laravel validation/authentication/authorization/not-found handling. Missing required adapters are configuration failures (`binding_required`, 500), not a real user's authorization denial.

## Deploy with explicit checks

Generate and check the installed DTO sources, then run the host tests and strict Doctor:

```sh
php artisan nvl:data:types:generate --fail-on-warning
php artisan nvl:data:types:check --fail-on-warning
php artisan nvl:doctor --strict --format=json
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan queue:restart
```

Use the generated declarations at `resources/js/types/generated.d.ts`; do not edit them. Rebuild the frontend and deploy schema/code/config/types as one reviewed release, then restart long-lived workers. Drain old serialized event payloads before migrating the six deprecated event names and model-bearing payloads; rebuild event caches. Source-connection callbacks are process-local and are not a durable outbox.

If using Content, keep its compiled cache off until configured deliberately; build it for the current release before enabling required-cache enforcement. Follow Content's own cache-token/deployment instructions. Optional Media S3, image and scanner, Templates PDF, Payments/Billing Stripe/Cashier, and Auth integrations need their documented feature dependencies and host bindings; installing Comments does not require those features. Core logging defaults to the host-preserving `nvl` channel with normal verbosity; CSV defaults quiet, while warnings/errors remain visible.

The 5.0.0 local Dagger release gate and fresh public installation passed on PHP 8.4/Laravel 13, with MySQL/PostgreSQL persistence and sealed Tenancy checks. Additional compatibility legs remain separate; see [verification policy](../README.md#verification-and-releases) for coverage. Verify the configured host before enabling traffic.

Enable `vendor/nvl/core/support/consumer-audit.neon` in your PHPStan includes. Owner relation boundaries are enforced statically, not at runtime; use the public contracts described above.
