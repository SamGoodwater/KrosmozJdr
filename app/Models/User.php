<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $image
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


    const ROLES = [
        'guest' => 'guest',
        'user' => 'user',
        'player' => 'player',
        "game_master" => "game_master",
        'contributor' => 'contributor',
        'moderator' => 'moderator',
        'admin' => 'admin',
        'super_admin' => 'super_admin'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function verifyRole(string $role): bool
    {
        if (!in_array($role, self::ROLES) || !in_array($this->role, self::ROLES)) {
            return false;
        }
        if ($this->role === self::ROLES['super_admin']) {
            return true;
        }

        switch ($role) {
            case self::ROLES['guest']:
                return in_array($this->role, [
                    self::ROLES['guest'],
                    self::ROLES['user'],
                    self::ROLES['player'],
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['user']:
                return in_array($this->role, [
                    self::ROLES['user'],
                    self::ROLES['player'],
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['player']:
                return in_array($this->role, [
                    self::ROLES['player'],
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['game_master']:
                return in_array($this->role, [
                    self::ROLES['game_master'],
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['contributor']:
                return in_array($this->role, [
                    self::ROLES['contributor'],
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['moderator']:
                return in_array($this->role, [
                    self::ROLES['moderator'],
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['admin']:
                return in_array($this->role, [
                    self::ROLES['admin'],
                    self::ROLES['super_admin']
                ]);

            case self::ROLES['super_admin']:
                return in_array($this->role, [
                    self::ROLES['super_admin']
                ]);

            default:
                return false;
        }
    }

    public function imagePath(): string
    {
        return Storage::url($this->image);
    }
}
