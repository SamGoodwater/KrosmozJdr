<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackMessageRequest;
use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use App\Models\User;
use App\Notifications\FeedbackThreadNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Espace utilisateur des conversations de feedback.
 *
 * @example
 * Route::get('/feedback', [FeedbackThreadController::class, 'index']);
 */
class FeedbackThreadController extends Controller
{
    public function index(): Response
    {
        $threads = FeedbackThread::query()
            ->where('user_id', Auth::id())
            ->latest('last_message_at')
            ->paginate(12)
            ->through(fn (FeedbackThread $thread): array => $this->threadSummary($thread));

        return Inertia::render('Pages/feedback/Index', [
            'threads' => $threads,
        ]);
    }

    public function show(FeedbackThread $feedback): Response
    {
        $this->authorize('view', $feedback);

        if ($feedback->user_unread_count > 0) {
            $feedback->forceFill(['user_unread_count' => 0])->save();
        }

        $feedback->load(['messages.author:id,name', 'user:id,name']);

        return Inertia::render('Pages/feedback/Show', [
            'thread' => $this->threadPayload($feedback),
        ]);
    }

    public function storeMessage(StoreFeedbackMessageRequest $request, FeedbackThread $feedback): RedirectResponse
    {
        $this->authorize('reply', $feedback);

        $user = $request->user();
        $validated = $request->validated();
        $attachment = $validated['attachment'] ?? null;
        $attachmentPath = $attachment ? $attachment->store('feedback/attachments', 'public') : null;

        DB::transaction(function () use ($feedback, $user, $validated, $attachmentPath, $attachment): void {
            FeedbackMessage::create([
                'feedback_thread_id' => $feedback->id,
                'author_id' => $user->id,
                'author_role' => FeedbackMessage::AUTHOR_USER,
                'body' => (string) $validated['message'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachment?->getClientOriginalName(),
            ]);

            $feedback->forceFill([
                'status' => FeedbackThread::STATUS_OPEN,
                'last_message_at' => now(),
                'staff_unread_count' => $feedback->staff_unread_count + 1,
            ])->save();
        });

        try {
            User::query()
                ->where('role', '>=', User::ROLE_ADMIN)
                ->where('id', '!=', $user->id)
                ->get()
                ->each(function (User $admin) use ($feedback, $user): void {
                    if ($admin->wantsNotificationForType('feedback_user_reply')) {
                        $admin->notify(new FeedbackThreadNotification($feedback, $user, 'feedback_user_reply'));
                    }
                });
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Réponse envoyée.');
    }

    private function threadSummary(FeedbackThread $thread): array
    {
        return [
            'id' => $thread->id,
            'type' => $thread->type,
            'status' => $thread->status,
            'subject_preview' => $thread->subject_preview,
            'last_message_at' => $thread->last_message_at?->toIso8601String(),
            'user_unread_count' => $thread->user_unread_count,
            'url' => route('feedback.show', $thread),
        ];
    }

    private function threadPayload(FeedbackThread $thread): array
    {
        return [
            ...$this->threadSummary($thread),
            'source_url' => $thread->url,
            'messages' => $thread->messages->map(fn (FeedbackMessage $message): array => [
                'id' => $message->id,
                'author_role' => $message->author_role,
                'author_name' => $message->author?->name ?? 'Utilisateur supprimé',
                'body' => $message->body,
                'attachment_name' => $message->attachment_name,
                'attachment_url' => $message->attachment_url,
                'created_at' => $message->created_at?->toIso8601String(),
            ])->values(),
        ];
    }
}
