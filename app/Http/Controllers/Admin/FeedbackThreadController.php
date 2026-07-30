<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackMessageRequest;
use App\Http\Requests\UpdateFeedbackThreadStatusRequest;
use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use App\Notifications\FeedbackThreadNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Inbox admin des conversations de feedback.
 *
 * @example
 * Route::get('/admin/feedback', [FeedbackThreadController::class, 'index']);
 */
class FeedbackThreadController extends Controller
{
    public function index(): Response
    {
        $threads = FeedbackThread::query()
            ->with('user:id,name,email')
            ->latest('last_message_at')
            ->paginate(20)
            ->through(fn (FeedbackThread $thread): array => $this->threadSummary($thread));

        return Inertia::render('Admin/feedback/Index', [
            'threads' => $threads,
        ]);
    }

    public function show(FeedbackThread $feedback): Response
    {
        $this->authorize('view', $feedback);

        if ($feedback->staff_unread_count > 0) {
            $feedback->forceFill(['staff_unread_count' => 0])->save();
        }

        $feedback->load(['messages.author:id,name', 'user:id,name,email']);

        return Inertia::render('Admin/feedback/Show', [
            'thread' => $this->threadPayload($feedback),
        ]);
    }

    public function reply(StoreFeedbackMessageRequest $request, FeedbackThread $feedback): RedirectResponse
    {
        $this->authorize('replyAsStaff', $feedback);

        $user = $request->user();
        $validated = $request->validated();
        $attachment = $validated['attachment'] ?? null;
        $attachmentPath = $attachment ? $attachment->store('feedback/attachments', 'public') : null;

        DB::transaction(function () use ($feedback, $user, $validated, $attachmentPath, $attachment): void {
            FeedbackMessage::create([
                'feedback_thread_id' => $feedback->id,
                'author_id' => $user->id,
                'author_role' => FeedbackMessage::AUTHOR_STAFF,
                'body' => (string) $validated['message'],
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachment?->getClientOriginalName(),
            ]);

            $feedback->forceFill([
                'status' => FeedbackThread::STATUS_AWAITING_USER,
                'last_message_at' => now(),
                'user_unread_count' => $feedback->user_unread_count + 1,
            ])->save();
        });

        try {
            if ($feedback->user?->wantsNotificationForType('feedback_staff_reply')) {
                $feedback->user->notify(new FeedbackThreadNotification($feedback, $user, 'feedback_staff_reply'));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Réponse envoyée.');
    }

    public function updateStatus(UpdateFeedbackThreadStatusRequest $request, FeedbackThread $feedback): RedirectResponse
    {
        $this->authorize('updateStatus', $feedback);

        $validated = $request->validated();
        $feedback->forceFill([
            'status' => $validated['status'] ?? FeedbackThread::STATUS_OPEN,
        ])->save();

        return back()->with('success', 'Statut du retour mis à jour.');
    }

    private function threadSummary(FeedbackThread $thread): array
    {
        return [
            'id' => $thread->id,
            'type' => $thread->type,
            'status' => $thread->status,
            'subject_preview' => $thread->subject_preview,
            'last_message_at' => $thread->last_message_at?->toIso8601String(),
            'staff_unread_count' => $thread->staff_unread_count,
            'user' => $thread->user ? [
                'id' => $thread->user->id,
                'name' => $thread->user->name,
                'email' => $thread->user->email,
            ] : null,
            'url' => route('admin.feedback.show', $thread),
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
