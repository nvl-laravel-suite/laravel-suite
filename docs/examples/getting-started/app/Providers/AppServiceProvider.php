<?php

declare(strict_types=1);

namespace App\Providers;

use App\Comments\ApplicationCommentAuthorization;
use App\Comments\ArticleCommentTargetResolver;
use App\Models\Article;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Nvl\Comments\Contracts\CommentAuthorization;
use Nvl\Comments\Contracts\CommentBatchQueryScope;
use Nvl\Comments\Contracts\CommentQueryScope;

/** Explicit host policy and owner registration; merge into the host's existing provider. */
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CommentAuthorization::class, ApplicationCommentAuthorization::class);
        $this->app->bind(CommentQueryScope::class, ApplicationCommentAuthorization::class);
        $this->app->bind(CommentBatchQueryScope::class, ApplicationCommentAuthorization::class);
        $config = $this->app->make(Repository::class);
        $config->set('nvl-core.owners', [Article::class]);
        $config->set('nvl-comments.targets', ['article' => ArticleCommentTargetResolver::class]);
    }

    public function boot(): void
    {
        Relation::morphMap(['article' => Article::class]);
    }
}
