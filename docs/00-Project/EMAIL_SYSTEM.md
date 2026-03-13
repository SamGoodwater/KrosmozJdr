# Système d’emails — Krosmoz-JDR

Documentation du système d’envoi d’emails : templates, Mailables, configuration.

## 1. Configuration

- **Fichier** : `config/mail.php`
- **Variables .env** : `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- **En développement** : mailer par défaut `log` — les mails sont écrits dans `storage/logs/laravel.log`
- **Production** : configurer SMTP, Resend, Postmark ou un autre driver dans `.env`

## 2. Structure des templates

```
resources/views/emails/
├── layout.blade.php       # Layout de base (header, content, footer)
├── verify-email.blade.php # Template vérification email
└── components/            # Composants réutilisables (futur)
```

Le layout expose `@yield('title')`, `@yield('content')`, `@yield('footer')`.

## 3. Mailables

- **`App\Mail\BaseMailable`** : Mailable de base (layout, conventions)
- **`App\Mail\VerifyEmailMail`** : Email de vérification (lien signé, 60 min par défaut)

Pour créer un nouvel email : créer une Mailable dans `app/Mail/`, utiliser une vue dans `resources/views/emails/`.

## 4. Notifications et Mailables

Les notifications peuvent utiliser des Mailables au lieu de `MailMessage` :

```php
public function toMail($notifiable)
{
    return new VerifyEmailMail($notifiable);
}
```

## 5. Vérification d’email

- **Comptes classiques** : envoi d’un lien signé après inscription, middleware `verified` sur les routes `user.*`
- **Comptes OAuth** (GitHub, Discord, Steam) : pas de vérification, `email_verified_at` défini à la liaison
- **Logic** : `User::hasVerifiedEmail()` retourne `true` si `email_verified_at` OU si un compte OAuth est lié
- **Notification** : `App\Notifications\VerifyEmailNotification` utilise `VerifyEmailMail`
- **Config expiration** : `config/auth.php` → `verification.expire` (minutes, variable `EMAIL_VERIFICATION_EXPIRE`)

## 6. Utilisation ailleurs

Pour notifications, rappels, etc. : créer une Mailable dans `app/Mail/`, une vue dans `emails/`, et l’envoyer via `Mail::to($user)->send(new MaMailable($data))` ou via une Notification.
