<?php

declare(strict_types=1);

namespace App\Comments;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Nvl\Comments\Contracts\CommentTargetResolver;

/** Resolves only publicly available host articles for the route alias. */
final class ArticleCommentTargetResolver implements CommentTargetResolver
{
    public function alias(): string
    {
        return 'article';
    }

    public function resolve(string $identifier): ?Model
    {
        return Article::query()->where('is_public', true)->find($identifier);
    }
}
