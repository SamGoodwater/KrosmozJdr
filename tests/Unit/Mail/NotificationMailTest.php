<?php

namespace Tests\Unit\Mail;

use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCaseNoDatabase;

/**
 * Tests unitaires pour la Mailable NotificationMail.
 *
 * Aucun RefreshDatabase : ces tests ne touchent pas à la BDD (sauf test_notification_mail_can_be_sent
 * qui utilise Mail::fake et une adresse en dur).
 *
 * @see App\Mail\NotificationMail
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
class NotificationMailTest extends TestCaseNoDatabase
{
    public function test_notification_mail_renders_with_expected_content(): void
    {
        $mailable = new NotificationMail(
            subject: 'Test de notification',
            greeting: 'Bonjour !',
            lines: ['Première ligne.', 'Deuxième ligne avec détails.'],
            actionUrl: 'https://example.com/action',
            actionText: 'Voir les détails',
            footer: 'Merci d\'utiliser Krosmoz JDR.',
        );

        $mailable->assertSeeInHtml('Test de notification');
        $mailable->assertSeeInHtml('Bonjour !');
        $mailable->assertSeeInHtml('Première ligne.');
        $mailable->assertSeeInHtml('Deuxième ligne avec détails.');
        $mailable->assertSeeInHtml('Voir les détails');
        $mailable->assertSeeInHtml('https://example.com/action');
        $mailable->assertSeeInHtml('Krosmoz JDR');
    }

    public function test_notification_mail_renders_without_action_when_null(): void
    {
        $mailable = new NotificationMail(
            subject: 'Sans action',
            greeting: 'Bonjour',
            lines: ['Un message simple.'],
            actionUrl: null,
            actionText: null,
            footer: null,
        );

        $mailable->assertSeeInHtml('Un message simple.');
        $mailable->assertDontSeeInHtml('Voir les détails');
    }

    public function test_notification_mail_has_correct_subject(): void
    {
        $mailable = new NotificationMail(
            subject: 'Modification d\'une entité',
            greeting: 'Bonjour !',
            lines: [],
        );

        $mailable->assertHasSubject('Modification d\'une entité');
    }

    public function test_notification_mail_can_be_sent(): void
    {
        Mail::fake();

        Mail::to('test@example.com')->send(new NotificationMail(
            subject: 'Envoi test',
            greeting: 'Bonjour',
            lines: ['Contenu du mail.'],
        ));

        Mail::assertSent(NotificationMail::class);
    }

    public function test_notification_mail_plain_text_includes_raw_url(): void
    {
        $mailable = new NotificationMail(
            subject: 'Test',
            greeting: 'Bonjour',
            lines: [],
            actionUrl: 'http://localhost/verify?expires=1&signature=abc',
            actionText: 'Cliquer ici',
        );

        $mailable->assertSeeInText('http://localhost/verify?expires=1&signature=abc');
    }
}
