<?php

declare(strict_types=1);

namespace App\Comments;

use App\Models\Article;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Nvl\Comments\Contracts\CommentAuthorization;
use Nvl\Comments\Contracts\CommentBatchQueryScope;
use Nvl\Comments\Contracts\CommentQueryScope;
use Nvl\Comments\Data\CommentActorData;
use Nvl\Comments\Enums\CommentAbility;
use Nvl\Comments\Enums\CommentAudience;
use Nvl\Comments\Models\Comment;
use Nvl\Comments\Services\ConfiguredCommentAuthorization;
use Nvl\Support\Owners\OwnerBatch;

/** Admits public articles and retains the package's visibility and author rules. */
final readonly class ApplicationCommentAuthorization implements CommentAuthorization, CommentBatchQueryScope, CommentQueryScope
{
    public function __construct(private ConfiguredCommentAuthorization $baseline) {}

    /** @param array<string, mixed> $context */
    public function allows(CommentAbility $ability, CommentActorData $actor, ?Comment $comment = null, ?Model $target = null, CommentAudience $audience = CommentAudience::Public, array $context = []): bool
    {
        if ($target !== null && (! $target instanceof Article || ! $target->is_public)) {
            return false;
        }

        return $this->baseline->allows($ability, $actor, $comment, $target, $audience, $context);
    }

    /** @param Builder<Comment> $query */
    public function scopeComments(Builder $query, CommentActorData $actor, Model $target, CommentAudience $audience, CommentAbility $ability): void
    {
        if (! $target instanceof Article || ! $target->is_public) {
            throw new AuthorizationException;
        }
        $this->baseline->scopeComments($query, $actor, $target, $audience, $ability);
    }

    public function authorizeOwners(OwnerBatch $owners, CommentActorData $actor, CommentAudience $audience): void
    {
        foreach ($owners->owners() as $owner) {
            if (! $this->allows(CommentAbility::List, $actor, target: $owner, audience: $audience)) {
                throw new AuthorizationException;
            }
        }
    }

    /** @param Builder<Comment> $query */
    public function scopeBatchComments(Builder $query, OwnerBatch $owners, CommentActorData $actor, CommentAudience $audience): void
    {
        $this->baseline->scopeBatchComments($query, $owners, $actor, $audience);
    }

    /** @param Builder<Comment> $query */
    public function scopeHostComments(Builder $query, Model $ownerPrototype, CommentActorData $actor, CommentAudience $audience): void
    {
        if (! $ownerPrototype instanceof Article) {
            throw new AuthorizationException;
        }
        $query->whereHasMorph('commentable', [Article::class], fn (Builder $owners) => $owners->where('is_public', true));
        $this->baseline->scopeHostComments($query, $ownerPrototype, $actor, $audience);
    }
}
