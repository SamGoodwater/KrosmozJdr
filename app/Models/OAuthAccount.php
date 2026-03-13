<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Compte OAuth lié à un utilisateur (GitHub, Discord).
 *
 * Stocke les identifiants externes et permet la liaison/déliaison des fournisseurs.
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string|null $provider_email
 * @property string|null $provider_name
 * @property string|null $avatar_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 */
class OAuthAccount extends Model
{
    /** @var string Nom de la table (évite la pluralisation incorrecte "o_auth_accounts"). */
    protected $table = 'oauth_accounts';

    public const PROVIDER_GITHUB = 'github';

    public const PROVIDER_DISCORD = 'discord';

    public const PROVIDER_STEAM = 'steam';

    public const PROVIDERS = [self::PROVIDER_GITHUB, self::PROVIDER_DISCORD, self::PROVIDER_STEAM];

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_email',
        'provider_name',
        'avatar_url',
    ];

    /**
     * Relation vers l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer par provider.
     *
     * @param \Illuminate\Database\Eloquent\Builder<OAuthAccount> $query
     * @return \Illuminate\Database\Eloquent\Builder<OAuthAccount>
     */
    public function scopeProvider(\Illuminate\Database\Eloquent\Builder $query, string $provider): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope pour filtrer par utilisateur.
     *
     * @param \Illuminate\Database\Eloquent\Builder<OAuthAccount> $query
     * @return \Illuminate\Database\Eloquent\Builder<OAuthAccount>
     */
    public function scopeForUser(\Illuminate\Database\Eloquent\Builder $query, int $userId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('user_id', $userId);
    }
}
