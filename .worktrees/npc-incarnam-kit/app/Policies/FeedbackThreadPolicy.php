<?php

namespace App\Policies;

use App\Models\FeedbackThread;
use App\Models\User;

/**
 * Autorisations des conversations de feedback.
 *
 * @example
 * $this->authorize('view', $thread);
 */
class FeedbackThreadPolicy
{
    public function view(User $user, FeedbackThread $thread): bool
    {
        return $user->isAdmin() || $thread->user_id === $user->id;
    }

    public function reply(User $user, FeedbackThread $thread): bool
    {
        return ! $thread->isClosed() && ($user->isAdmin() || $thread->user_id === $user->id);
    }

    public function replyAsStaff(User $user, FeedbackThread $thread): bool
    {
        return ! $thread->isClosed() && $user->isAdmin();
    }

    public function updateStatus(User $user, FeedbackThread $thread): bool
    {
        return $user->isAdmin();
    }
}
