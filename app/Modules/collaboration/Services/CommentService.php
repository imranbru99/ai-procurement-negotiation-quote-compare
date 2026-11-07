<?php

namespace App\Modules\collaboration\Services;

use App\Models\User;
use App\Modules\collaboration\Models\Comment;
use App\Modules\collaboration\Notifications\UserMentionedInComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CommentService
{
    public function add(Model $commentable, User $author, string $body, bool $internalOnly = false, ?array $mentionUserIds = null): Comment
    {
        $mentions = $this->resolveMentions($body, $mentionUserIds);

        $comment = Comment::query()->create([
            'tenant_id' => $commentable->getAttribute('tenant_id'),
            'user_id' => $author->id,
            'commentable_type' => $commentable->getMorphClass(),
            'commentable_id' => $commentable->getKey(),
            'body' => $body,
            'mentions' => $mentions->pluck('id')->values()->all(),
            'internal_only' => $internalOnly,
        ]);

        foreach ($mentions as $mentioned) {
            if ($mentioned->id !== $author->id) {
                $mentioned->notify(new UserMentionedInComment($comment));
            }
        }

        return $comment->load('user');
    }

    /**
     * @return Collection<int, User>
     */
    protected function resolveMentions(string $body, ?array $mentionUserIds): Collection
    {
        if ($mentionUserIds !== null) {
            return User::query()->whereIn('id', $mentionUserIds)->get();
        }

        preg_match_all('/@\[(\d+)\]/', $body, $matches);
        $ids = array_map('intval', $matches[1] ?? []);

        if ($ids === []) {
            return collect();
        }

        return User::query()->whereIn('id', $ids)->get();
    }
}
