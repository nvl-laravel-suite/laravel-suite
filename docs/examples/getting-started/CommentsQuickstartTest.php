<?php

declare(strict_types=1);

use App\Comments\PostArticleComment;
use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Nvl\Comments\Enums\CommentAudience;
use Nvl\Comments\Models\Comment;
use Nvl\Comments\Services\CommentReader;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Schema::create('articles', function (Blueprint $table): void {
        $table->id();
        $table->string('title');
        $table->boolean('is_public');
        $table->timestamps();
    });
});

it('writes and reads authorized comments through public contracts', function (): void {
    $article = Article::query()->create(['title' => 'First article', 'is_public' => true]);
    $actor = User::factory()->create();
    app(PostArticleComment::class)->execute($article, 'Hello from the host', $actor);
    $summaries = app(CommentReader::class)->forOwners([$article], $actor, CommentAudience::Member);
    expect($summaries['values'][$article->getMorphClass()][(string) $article->getKey()]['visibleCount'])->toBe(1);
});

it('keeps a private host article denied through the same native workflow', function (): void {
    $article = Article::query()->create(['title' => 'Private article', 'is_public' => false]);
    $actor = User::factory()->create();
    expect(fn () => app(PostArticleComment::class)->execute($article, 'Test', $actor))->toThrow(AuthorizationException::class);
    expect(Comment::query()->count())->toBe(0);
});
