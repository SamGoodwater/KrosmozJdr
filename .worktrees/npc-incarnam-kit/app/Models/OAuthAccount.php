<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount provider(string $provider)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereProviderEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereProviderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OAuthAccount whereUserId($value)
 * @mixin \Eloquent
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
     * @param  Builder<OAuthAccount>  $query
     * @return Builder<OAuthAccount>
     */
    public function scopeProvider(Builder $query, string $provider): Builder
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope pour filtrer par utilisateur.
     *
     * @param  Builder<OAuthAccount>  $query
     * @return Builder<OAuthAccount>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
