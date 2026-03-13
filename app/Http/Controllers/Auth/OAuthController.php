<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OAuthAccount;
use App\Models\User;
use App\Services\NotificationService;
use App\Support\OAuthConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur OAuth pour GitHub, Discord et Steam.
 *
 * Gère : connexion, inscription, liaison au compte existant.
 * Seuls les providers avec credentials configurés dans .env sont autorisés.
 */
class OAuthController extends Controller
{
    /**
     * Redirige vers le provider OAuth (connexion ou inscription).
     */
    public function redirect(Request $request, string $provider): RedirectResponse|Response
    {
        if (! OAuthConfig::isProviderEnabled($provider) || ! in_array($provider, OAuthAccount::PROVIDERS, true)) {
            abort(404);
        }

        if (Auth::check() && $request->boolean('link')) {
            $request->session()->put('oauth.intended', 'link');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Redirige vers le provider OAuth pour lier le compte (utilisateur déjà connecté).
     */
    public function redirectLink(Request $request, string $provider): RedirectResponse|Response
    {
        if (! OAuthConfig::isProviderEnabled($provider) || ! in_array($provider, OAuthAccount::PROVIDERS, true)) {
            abort(404);
        }

        $request->session()->put('oauth.intended', 'link');

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Traite le callback OAuth.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        if (! OAuthConfig::isProviderEnabled($provider) || ! in_array($provider, OAuthAccount::PROVIDERS, true)) {
            abort(404);
        }

        $oauthUser = Socialite::driver($provider)->user();
        $providerId = (string) $oauthUser->getId();
        $providerEmail = $oauthUser->getEmail();
        $providerName = $oauthUser->getName() ?: $oauthUser->getNickname() ?: $providerId;
        $avatarUrl = $oauthUser->getAvatar();

        $intendedLink = $request->session()->pull('oauth.intended') === 'link' && Auth::check();

        $oauthAccount = OAuthAccount::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($oauthAccount) {
            $user = $oauthAccount->user;
            if ($intendedLink && $user->id !== Auth::id()) {
                // Compte OAuth déjà lié à un autre utilisateur : proposer le transfert
                $request->session()->put('oauth.transfer_pending', [
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'provider_email' => $providerEmail,
                    'provider_name' => $providerName,
                    'avatar_url' => $avatarUrl,
                    'other_user_id' => $user->id,
                    'other_user_name' => $user->name,
                ]);

                return redirect()->route('oauth.transfer-offer');
            }
            if ($intendedLink && $user->id === Auth::id()) {
                return redirect()->route('user.settings')
                    ->with('success', 'Ce compte ' . $provider . ' est déjà lié.');
            }
            $this->updateOAuthAccount($oauthAccount, $providerEmail, $providerName, $avatarUrl);
            Auth::login($user, true);
            $user->update(['last_login_at' => now()]);
            try {
                NotificationService::notifyLastConnection($user);
            } catch (\Throwable $e) {
                report($e);
            }
            return redirect()->intended(route('user.show', absolute: false));
        }

        if ($intendedLink && Auth::check()) {
            $user = Auth::user();
            $user->update(['email_verified_at' => now()]);
            OAuthAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerId,
                'provider_email' => $providerEmail,
                'provider_name' => $providerName,
                'avatar_url' => $avatarUrl,
            ]);
            $this->maybeUpdateUserFromOAuth($user, $providerName, $providerEmail, $avatarUrl);
            return redirect()->to(route('user.settings') . '#connections')
                ->with('success', 'Compte ' . $provider . ' lié avec succès.');
        }

        $user = null;
        if ($providerEmail) {
            $user = User::query()->where('email', $providerEmail)->first();
        }
        if ($user) {
            // Compte existant avec même email : demander confirmation avant liaison
            $request->session()->put('oauth.pending_link', [
                'provider' => $provider,
                'provider_id' => $providerId,
                'provider_email' => $providerEmail,
                'provider_name' => $providerName,
                'avatar_url' => $avatarUrl,
                'existing_user_id' => $user->id,
            ]);
            return redirect()->route('oauth.confirm-link');
        }

        $user = $this->createUserFromOAuth($provider, $providerId, $providerEmail, $providerName, $avatarUrl);
        $user->update(['email_verified_at' => now()]);
        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $providerId,
            'provider_email' => $providerEmail,
            'provider_name' => $providerName,
            'avatar_url' => $avatarUrl,
        ]);
        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);
        try {
            NotificationService::notifyNewUserCreated($user);
        } catch (\Throwable $e) {
            report($e);
        }
        return redirect()->intended(route('user.show', absolute: false));
    }

    private function updateOAuthAccount(OAuthAccount $account, ?string $email, ?string $name, ?string $avatarUrl): void
    {
        $account->update([
            'provider_email' => $email,
            'provider_name' => $name,
            'avatar_url' => $avatarUrl,
        ]);
    }

    private function maybeUpdateUserFromOAuth(User $user, string $name, ?string $email, ?string $avatarUrl): void
    {
        $updates = [];
        if ($user->avatar === null && $avatarUrl) {
            $updates['avatar'] = $avatarUrl;
        }
        if ($email && ($user->email === null || Str::contains($user->email ?? '', '@oauth.placeholder'))) {
            $updates['email'] = $email;
        }
        if (! empty($updates)) {
            $user->update($updates);
        }
    }

    private function createUserFromOAuth(
        string $provider,
        string $providerId,
        ?string $email,
        string $name,
        ?string $avatarUrl
    ): User {
        $email = $email ?: $provider . '_' . $providerId . '@oauth.placeholder';
        $name = $this->ensureUniqueName($name);
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => null,
            'role' => User::ROLE_USER,
            'avatar' => $avatarUrl,
        ]);
    }

    private function ensureUniqueName(string $name): string
    {
        $base = Str::limit($name, 50);
        $candidate = $base;
        $suffix = 0;
        while (User::query()->where('name', $candidate)->exists()) {
            $suffix++;
            $candidate = Str::limit($base, 45) . '_' . $suffix;
        }
        return $candidate;
    }

    /**
     * Affiche la page de confirmation de liaison (email déjà associé à un compte).
     */
    public function showConfirmLink(Request $request): RedirectResponse|\Inertia\Response
    {
        $pending = $request->session()->get('oauth.pending_link');
        if (! $pending || ! isset($pending['existing_user_id'])) {
            return redirect()->route('login')->with('error', 'Session expirée. Réessaie de te connecter.');
        }

        $user = User::query()->with('oauthAccounts')->find($pending['existing_user_id']);
        if (! $user) {
            $request->session()->forget('oauth.pending_link');
            return redirect()->route('login')->with('error', 'Compte introuvable.');
        }

        $linkedProviders = $user->oauthAccounts->pluck('provider')->toArray();
        $providerLabels = ['github' => 'GitHub', 'discord' => 'Discord', 'steam' => 'Steam'];

        return Inertia::render('Pages/auth/OAuthConfirmLink', [
            'email' => $pending['provider_email'] ?? $user->email,
            'provider' => $pending['provider'],
            'providerLabel' => $providerLabels[$pending['provider']] ?? $pending['provider'],
            'existingProviders' => array_map(fn ($p) => $providerLabels[$p] ?? $p, $linkedProviders),
        ]);
    }

    /**
     * Confirme la liaison du provider au compte existant.
     */
    public function confirmLink(Request $request): RedirectResponse
    {
        $pending = $request->session()->pull('oauth.pending_link');
        if (! $pending || ! isset($pending['existing_user_id'])) {
            return redirect()->route('login')->with('error', 'Session expirée. Réessaie de te connecter.');
        }

        $user = User::query()->find($pending['existing_user_id']);
        if (! $user) {
            return redirect()->route('login')->with('error', 'Compte introuvable.');
        }

        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $pending['provider'],
            'provider_id' => $pending['provider_id'],
            'provider_email' => $pending['provider_email'] ?? null,
            'provider_name' => $pending['provider_name'] ?? '',
            'avatar_url' => $pending['avatar_url'] ?? null,
        ]);
        $user->update(['email_verified_at' => now()]);
        $this->maybeUpdateUserFromOAuth($user, $pending['provider_name'] ?? '', $pending['provider_email'] ?? null, $pending['avatar_url'] ?? null);
        Auth::login($user, true);
        $user->update(['last_login_at' => now()]);
        try {
            NotificationService::notifyLastConnection($user);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->to(route('user.settings') . '#connections')
            ->with('success', 'Compte ' . ($pending['provider'] ?? '') . ' lié avec succès. Tu es connecté.');
    }

    /**
     * Annule la liaison en cours et redirige vers le login.
     */
    public function cancelLink(Request $request): RedirectResponse
    {
        $request->session()->forget('oauth.pending_link');

        return redirect()->route('login')->with('info', 'Connexion annulée.');
    }

    /**
     * Affiche la page proposant de transférer une liaison OAuth d'un autre compte vers le compte actuel.
     */
    public function showTransferOffer(Request $request): RedirectResponse|\Inertia\Response
    {
        $pending = $request->session()->get('oauth.transfer_pending');
        if (! $pending || ! isset($pending['provider'], $pending['other_user_id'], $pending['other_user_name'])) {
            return redirect()->route('user.settings')->with('error', 'Session expirée. Réessaie de lier ton compte.');
        }

        $providerLabels = ['github' => 'GitHub', 'discord' => 'Discord', 'steam' => 'Steam'];

        return Inertia::render('Pages/auth/OAuthTransferOffer', [
            'provider' => $pending['provider'],
            'providerLabel' => $providerLabels[$pending['provider']] ?? $pending['provider'],
            'otherUserName' => $pending['other_user_name'],
        ]);
    }

    /**
     * Confirme le transfert : délie le provider de l'autre utilisateur et le lie au compte actuel.
     */
    public function confirmTransfer(Request $request): RedirectResponse
    {
        $pending = $request->session()->pull('oauth.transfer_pending');
        if (! $pending || ! isset($pending['provider'], $pending['provider_id'], $pending['other_user_id'])) {
            return redirect()->route('user.settings')->with('error', 'Session expirée. Réessaie de lier ton compte.');
        }

        $oauthAccount = OAuthAccount::query()
            ->where('provider', $pending['provider'])
            ->where('provider_id', $pending['provider_id'])
            ->where('user_id', $pending['other_user_id'])
            ->first();

        if (! $oauthAccount) {
            return redirect()->route('user.settings')->with('error', 'Ce compte n\'est plus lié à l\'autre utilisateur.');
        }

        $currentUser = Auth::user();
        $oauthAccount->update(['user_id' => $currentUser->id]);
        $currentUser->update(['email_verified_at' => now()]);
        $this->maybeUpdateUserFromOAuth(
            $currentUser,
            $pending['provider_name'] ?? '',
            $pending['provider_email'] ?? null,
            $pending['avatar_url'] ?? null
        );

        return redirect()->to(route('user.settings') . '#connections')
            ->with('success', 'Compte ' . $pending['provider'] . ' transféré et lié avec succès.');
    }

    /**
     * Annule le transfert OAuth en cours et redirige vers les paramètres.
     */
    public function cancelTransfer(Request $request): RedirectResponse
    {
        $request->session()->forget('oauth.transfer_pending');

        return redirect()->to(route('user.settings') . '#connections')
            ->with('info', 'Transfert annulé.');
    }
}
