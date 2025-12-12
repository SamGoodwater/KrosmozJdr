<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->input('identifier');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // Essayer de s'authentifier avec email ou pseudo
        $credentials = [
            'password' => $password,
        ];

        // Si c'est un email
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $identifier;
        } else {
            // Sinon c'est un pseudo
            $credentials['name'] = $identifier;
        }

        if (! Auth::attempt($credentials, $remember)) {
            /**
             * Diagnostic : différencier un "mauvais mot de passe" d'un compte supprimé / non autorisé.
             *
             * @description
             * `Auth::attempt` retourne false si :
             * - aucun utilisateur ne correspond aux critères (email/pseudo),
             * - ou si le mot de passe ne correspond pas,
             * - ou si l'utilisateur est soft-deleted (global scope SoftDeletes).
             *
             * Dans la pratique, un compte soft-deleted donne l'impression d'un bug "aléatoire"
             * (on a les bons identifiants) et pousse à réinitialiser la DB.
             * On détecte ce cas pour retourner un message explicite.
             *
             * @example
             * - Compte supprimé -> "Ce compte a été supprimé..."
             */
            $query = \App\Models\User::withTrashed();
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', $identifier);
            } else {
                $query->where('name', $identifier);
            }
            $matchingUser = $query->first();
            if ($matchingUser?->deleted_at) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'identifier' => 'Ce compte a été supprimé. Contactez un administrateur pour le restaurer.',
                ]);
            }
            if ($matchingUser && ! $matchingUser->canLogin()) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'identifier' => 'Ce compte ne peut pas se connecter.',
                ]);
            }

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        // Vérifier que l'utilisateur peut se connecter (les utilisateurs système ne peuvent pas)
        $user = Auth::user();
        if ($user && !$user->canLogin()) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('identifier')).'|'.$this->ip());
    }
}
