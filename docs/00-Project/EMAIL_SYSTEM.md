# Système d’emails — Krosmoz-JDR

Documentation du système d’envoi d’emails : templates, Mailables, configuration.

## 1. Configuration

- **Fichier** : `config/mail.php`
- **Variables .env** : `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- **En développement** : mailer par défaut `log` — les mails sont écrits dans `storage/logs/laravel.log`. Pour les liens signés (ex. vérification email), copier l’URL depuis la partie `text/plain` du log (URL avec `&` brut), pas depuis le HTML (`&amp;` invalide la signature). Préférer [Mailpit](https://github.com/axllent/mailpit) ou [MailHog](https://github.com/mailhog/MailHog) pour tester les liens par clic.
- **Production** : configurer SMTP, Resend, Postmark ou un autre driver dans `.env`

## 2. Structure des templates

```
resources/views/emails/
├── layout.blade.php          # Layout de base (header, content, footer)
├── verify-email.blade.php    # Template vérification email (HTML)
├── verify-email-text.blade.php # Version texte brut vérification (URL avec & pour copie depuis logs)
├── notification.blade.php    # Template générique notifications (HTML)
├── notification-text.blade.php # Version texte brut notifications
├── feedback.blade.php        # Retour utilisateur (HTML)
├── feedback-text.blade.php   # Version texte brut feedback
└── components/               # Composants réutilisables (futur)
```

Le layout expose `@yield('title')`, `@yield('content')`, `@yield('footer')`.

## 3. Mailables

- **`App\Mail\VerifyEmailMail`** : Email de vérification (lien signé, 60 min par défaut)
- **`App\Mail\NotificationMail`** : Mailable générique pour les notifications métier (entités, profil, digest, etc.). Utilise le layout emails commun. Utilisé par les notifications Laravel (ProfileModifiedNotification, EntityModifiedNotification, NewUserCreatedNotification, etc.)
- **`App\Mail\FeedbackMail`** : Retour utilisateur (bug, erreur, suggestion, autre) envoyé aux admins. Voir [FEEDBACK_SYSTEM.md](FEEDBACK_SYSTEM.md).

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
- **MAIL_MAILER=log** : les emails sont écrits dans `storage/logs/laravel.log`. Pour copier le lien de vérification, utilise la partie **texte brut** (Content-Type: text/plain) du log — l’URL y contient les bons caractères `&`. Si tu copies depuis la partie HTML, remplace `&amp;` par `&` dans l’URL. Alternative : [Mailpit](https://github.com/axllent/mailpit) ou [MailHog](https://github.com/mailhog/MailHog) pour prévisualiser les emails en local.

## 6. Utilisation ailleurs

Pour notifications, rappels, etc. : créer une Mailable dans `app/Mail/`, une vue dans `emails/`, et l’envoyer via `Mail::to($user)->send(new MaMailable($data))` ou via une Notification.
