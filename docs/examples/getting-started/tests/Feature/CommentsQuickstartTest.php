<?php

declare(strict_types=1);

use App\Comments\PostArticleComment;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Nvl\Comments\Contracts\CreateCommentContract;
use Nvl\Comments\Contracts\ListOwnerCommentSummariesContract;
use Nvl\Comments\Data\CommentActorData;
use Nvl\Comments\Data\Mutations\CreateCommentData;
use Nvl\Comments\Enums\CommentAudience;
use Nvl\Comments\Models\Comment;

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
    $actor = CommentActorData::fromAuthenticatable(User::factory()->create());
    app(PostArticleComment::class)->execute($article, 'Hello from the host', $actor);
    $summaries = app(ListOwnerCommentSummariesContract::class)->execute([$article], $actor, CommentAudience::Member);
    expect($summaries->owners->{$article->getMorphClass()}->{(string) $article->getKey()}->visibleCount)->toBe(1);
});

it('isolates the host workflow with a native contract mock', function (): void {
    $article = new Article(['title' => 'Mocked workflow', 'is_public' => true]);
    $actor = new CommentActorData('member', '7');
    $result = Comment::factory()->withoutParents()->make(['id' => '91e383d4-3a78-4d93-a23d-aee913109582']);
    $fake = Mockery::mock(CreateCommentContract::class);
    $fake->shouldReceive('execute')->once()->with($article, Mockery::on(fn (CreateCommentData $data): bool => $data->body === 'Test'), $actor)->andReturn($result);
    $this->app->instance(CreateCommentContract::class, $fake);
    expect($this->app->make(PostArticleComment::class)->execute($article, 'Test', $actor))->toBe($result->id);
});
