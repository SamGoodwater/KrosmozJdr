<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable de base pour tous les emails Krosmoz-JDR.
 *
 * Les sous-classes héritent du layout emails.layout et peuvent surcharger
 * subject, view et données.
 *
 * @see docs/00-Project/EMAIL_SYSTEM.md
 */
abstract class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Vue Blade par défaut (layout + slot content).
     * Les sous-classes peuvent surcharger via build() ou content().
     */
    protected string $defaultView = 'emails.layout';
}
