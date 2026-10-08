<?php

declare(strict_types=1);

namespace App\Comments;

use App\Models\Article;
use Nvl\Comments\Contracts\CreateCommentContract;
use Nvl\Comments\Data\CommentActorData;
use Nvl\Comments\Data\Mutations\CreateCommentData;

/** Application workflow substitutable without running package persistence. */
final readonly class PostArticleComment
{
    public function __construct(private CreateCommentContract $comments) {}

    public function execute(Article $article, string $body, CommentActorData $actor): string
    {
        return $this->comments->execute($article, new CreateCommentData(body: $body), $actor)->id;
    }
}
