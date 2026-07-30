<?php

namespace App\Notifications;

use App\Models\FeedbackThread;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification de conversation feedback (nouveau retour ou réponse).
 *
 * @example
 * $admin->notify(new FeedbackThreadNotification($thread, $user, 'feedback_new_thread'));
 */
class FeedbackThreadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FeedbackThread $thread,
        public User $actor,
        public string $configType,
    ) {}

    public function via($notifiable): array
    {
        return $notifiable->getChannelsForNotificationType($this->configType);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->mailSubject())
            ->greeting('Bonjour !')
            ->line($this->message())
            ->action('Ouvrir le retour', $this->urlFor($notifiable));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'config_type' => $this->configType,
            'feedback_thread_id' => $this->thread->id,
            'feedback_type' => $this->thread->type,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'message' => $this->message(),
            'url' => $this->urlFor($notifiable),
        ];
    }

    private function mailSubject(): string
    {
        return match ($this->configType) {
            'feedback_staff_reply' => 'Réponse à ton retour Krosmoz JDR',
            'feedback_user_reply' => 'Nouvelle réponse utilisateur à un retour',
            default => 'Nouveau retour utilisateur',
        };
    }

    private function message(): string
    {
        return match ($this->configType) {
            'feedback_staff_reply' => "Un membre de l'équipe a répondu à ton retour.",
            'feedback_user_reply' => "{$this->actor->name} a répondu à un retour.",
            default => "Nouveau retour {$this->thread->type} envoyé par {$this->actor->name}.",
        };
    }

    private function urlFor($notifiable): string
    {
        if ($notifiable instanceof User && $notifiable->isAdmin()) {
            return route('admin.feedback.show', $this->thread);
        }

        return route('feedback.show', $this->thread);
    }
}
