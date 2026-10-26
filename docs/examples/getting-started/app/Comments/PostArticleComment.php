<?php

declare(strict_types=1);

namespace App\Comments;

use App\Models\Article;
use Illuminate\Contracts\Auth\Authenticatable;
use Nvl\Comments\Enums\CommentAudience;
use Nvl\Comments\Services\CommentWriter;
use Nvl\Support\Enums\SystemActor;

/** Application orchestration over the complete native comment operation. */
final readonly class PostArticleComment
{
    public function __construct(private CommentWriter $comments) {}

    public function execute(Article $article, string $body, Authenticatable|SystemActor $actor): string
    {
        return $this->comments->create($article, ['body' => $body], $actor, CommentAudience::Member)->id;
    }
}
