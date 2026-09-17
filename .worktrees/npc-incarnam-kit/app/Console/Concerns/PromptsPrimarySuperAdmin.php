<?php

declare(strict_types=1);

namespace App\Console\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Création interactive du premier compte super_admin (après UserSeeder ou en standalone).
 */
trait PromptsPrimarySuperAdmin
{
    /**
     * Si la commande définit l’option `--skip-super-admin-prompt`, elle est respectée.
     * Sinon (ex. `project:super-admin`), la saisie a lieu si interactif et aucun super_admin humain.
     *
     * @throws \RuntimeException Si l’utilisateur abandonne la saisie après erreurs de validation
     */
    protected function runPrimarySuperAdminPrompt(): void
    {
        if ($this->getDefinition()->hasOption('skip-super-admin-prompt')
            && (bool) $this->option('skip-super-admin-prompt')) {
            $this->warn('  Création du super_admin ignorée (--skip-super-admin-prompt).');

            return;
        }

        if (! $this->input->isInteractive()) {
            $this->warn('  Mode non interactif : aucun super_admin créé. Utilisez `php artisan project:super-admin` ou `project:init` en interactif.');

            return;
        }

        if (User::query()->where('role', User::ROLE_SUPER_ADMIN)->where('is_system', false)->exists()) {
            $existing = User::query()->where('role', User::ROLE_SUPER_ADMIN)->where('is_system', false)->first();
            $this->line('  Un super_admin humain existe déjà ('.($existing?->email ?? '?').'), saisie ignorée.');

            return;
        }

        $this->newLine();
        $this->info('  Compte super_admin principal');
        $this->line('  Saisissez l’email, le pseudo et le mot de passe du premier administrateur.');

        while (true) {
            $email = Str::lower(trim((string) $this->ask('  Adresse e-mail')));
            $name = trim((string) $this->ask('  Pseudo (nom affiché)'));
            $password = (string) $this->secret('  Mot de passe');
            $passwordConfirm = (string) $this->secret('  Confirmation du mot de passe');

            $validator = Validator::make(
                [
                    'email' => $email,
                    'name' => $name,
                    'password' => $password,
                    'password_confirmation' => $passwordConfirm,
                ],
                [
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email'],
                    'name' => ['required', 'string', 'max:255'],
                    'password' => ['required', 'string', 'confirmed', Password::defaults()],
                ],
                [
                    'email.required' => 'L’e-mail est requis.',
                    'email.email' => 'L’e-mail n’est pas valide.',
                    'email.unique' => 'Cette adresse est déjà utilisée.',
                    'name.required' => 'Le pseudo est requis.',
                    'password.required' => 'Le mot de passe est requis.',
                    'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $this->error('  '.$message);
                }
                if (! $this->confirm('  Réessayer ?', true)) {
                    throw new \RuntimeException('Création du super_admin annulée.');
                }

                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
                'avatar' => User::DEFAULT_AVATAR,
                'email_verified_at' => now(),
                'notifications_enabled' => true,
                'notification_channels' => [User::NOTIFICATION_CHANNELS[0]],
                'is_system' => false,
            ]);

            $this->info('  Super_admin créé : '.$email);
            $this->newLine();

            return;
        }
    }
}
