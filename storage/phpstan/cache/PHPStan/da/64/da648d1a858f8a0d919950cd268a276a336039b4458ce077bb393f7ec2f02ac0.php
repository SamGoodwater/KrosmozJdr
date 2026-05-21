<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/User.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\User
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-331839071100702128a12c79fb023ebad6e0a1e2b54fd6ebd498d53777627d4a-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\User',
        'filename' => '/var/www/KrosmozJdr/app/Models/User.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\User',
    'shortName' => 'User',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Modèle User central du projet Krosmoz JDR.
 *
 * Gère l\'authentification, les rôles, l\'avatar, les notifications et les relations avec les entités du jeu.
 *
 * Champs principaux :
 * - id, name, email, password, role, avatar
 * - notifications_enabled, notification_channels
 *
 * Relations :
 * - scénarios, campagnes, pages, sections, et entités créées
 *
 * @property int $id Identifiant unique
 * @property string $name Nom d\'utilisateur
 * @property string $email Email
 * @property string $password Mot de passe (hashé)
 * @property string $role Rôle (voir self::ROLES)
 * @property string|null $avatar Chemin de l\'avatar ou null
 * @property bool $notifications_enabled Notifications activées ?
 * @property array $notification_channels Canaux de notification
 * @method bool wantsNotification(string $type = null) L\'utilisateur veut-il des notifications ?
 * @method array notificationChannels() Retourne les canaux de notification
 * @method bool wantsProfileNotification() Toujours true (modif profil)
 * @method string avatarPath() URL de l\'avatar (jamais null)
 * @method bool verifyRole(string|int $role) Possède au moins le rôle donné
 * @method bool updateRole(User $user) Peut-il modifier le rôle d\'un autre ?
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Page> $createdPages
 * @property-read int|null $created_pages_count
 * @property-read Collection<int, Section> $createdSections
 * @property-read int|null $created_sections_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Page> $pages
 * @property-read int|null $pages_count
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @method static \\Database\\Factories\\UserFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereAvatar($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereEmail($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereNotificationChannels($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereNotificationsEnabled($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User wherePassword($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereRememberToken($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereRole($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User withoutTrashed()
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Collection<int, Condition> $createdConditions
 * @property-read int|null $created_conditions_count
 * @property-read Collection<int, Capability> $createdCapabilities
 * @property-read int|null $created_capabilities_count
 * @property-read Collection<int, Breed> $createdBreeds
 * @property-read int|null $created_breeds_count
 * @property-read Collection<int, ConsumableType> $createdConsumableTypes
 * @property-read int|null $created_consumable_types_count
 * @property-read Collection<int, Consumable> $createdConsumables
 * @property-read int|null $created_consumables_count
 * @property-read Collection<int, ItemType> $createdItemTypes
 * @property-read int|null $created_item_types_count
 * @property-read Collection<int, Item> $createdItems
 * @property-read int|null $created_items_count
 * @property-read Collection<int, MonsterRace> $createdMonsterRaces
 * @property-read int|null $created_monster_races_count
 * @property-read Collection<int, Panoply> $createdPanoplies
 * @property-read int|null $created_panoplies_count
 * @property-read Collection<int, ResourceType> $createdResourceTypes
 * @property-read int|null $created_resource_types_count
 * @property-read Collection<int, resource> $createdResources
 * @property-read int|null $created_resources_count
 * @property-read Collection<int, Scenario> $createdScenarios
 * @property-read int|null $created_scenarios_count
 * @property-read Collection<int, Shop> $createdShops
 * @property-read int|null $created_shops_count
 * @property-read Collection<int, Specialization> $createdSpecializations
 * @property-read int|null $created_specializations_count
 * @property-read Collection<int, SpellType> $createdSpellTypes
 * @property-read int|null $created_spell_types_count
 * @property-read Collection<int, Spell> $createdSpells
 * @property-read int|null $created_spells_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read string $role_name
 * @property Carbon|null $last_login_at
 * @property bool $is_system
 * @property array<array-key, mixed>|null $notification_preferences
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, OAuthAccount> $oauthAccounts
 * @property-read int|null $oauth_accounts_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereIsSystem($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereLastLoginAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|User whereNotificationPreferences($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 153,
    'endLine' => 742,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Foundation\\Auth\\User',
    'implementsClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\HasMedia',
      1 => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
      2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
      3 => 'Illuminate\\Notifications\\Notifiable',
      4 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      5 => 'Illuminate\\Auth\\MustVerifyEmail',
    ),
    'immediateConstants' => 
    array (
      'ROLES' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[
    0 => \'guest\',
    // Visiteur non connecté
    1 => \'user\',
    // Utilisateur inscrit
    2 => \'player\',
    // Joueur participant à une campagne/scénario
    3 => \'game_master\',
    // Meneur de jeu
    4 => \'admin\',
    // Administrateur
    5 => \'super_admin\',
]',
          'attributes' => 
          array (
            'startLine' => 160,
            'endLine' => 167,
            'startTokenPos' => 245,
            'startFilePos' => 8324,
            'endTokenPos' => 301,
            'endFilePos' => 8642,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 160,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'ROLE_GUEST' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLE_GUEST',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 169,
            'endLine' => 169,
            'startTokenPos' => 310,
            'startFilePos' => 8669,
            'endTokenPos' => 310,
            'endFilePos' => 8669,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 169,
        'endLine' => 169,
        'startColumn' => 5,
        'endColumn' => 25,
      ),
      'ROLE_USER' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLE_USER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1',
          'attributes' => 
          array (
            'startLine' => 171,
            'endLine' => 171,
            'startTokenPos' => 321,
            'startFilePos' => 8721,
            'endTokenPos' => 321,
            'endFilePos' => 8721,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 171,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 24,
      ),
      'ROLE_PLAYER' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLE_PLAYER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '2',
          'attributes' => 
          array (
            'startLine' => 173,
            'endLine' => 173,
            'startTokenPos' => 332,
            'startFilePos' => 8772,
            'endTokenPos' => 332,
            'endFilePos' => 8772,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 173,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 26,
      ),
      'ROLE_GAME_MASTER' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLE_GAME_MASTER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3',
          'attributes' => 
          array (
            'startLine' => 175,
            'endLine' => 175,
            'startTokenPos' => 343,
            'startFilePos' => 8853,
            'endTokenPos' => 343,
            'endFilePos' => 8853,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 175,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'ROLE_ADMIN' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLE_ADMIN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '4',
          'attributes' => 
          array (
            'startLine' => 177,
            'endLine' => 177,
            'startTokenPos' => 354,
            'startFilePos' => 8897,
            'endTokenPos' => 354,
            'endFilePos' => 8897,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 177,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 25,
      ),
      'ROLE_SUPER_ADMIN' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'ROLE_SUPER_ADMIN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '5',
          'attributes' => 
          array (
            'startLine' => 179,
            'endLine' => 179,
            'startTokenPos' => 365,
            'startFilePos' => 8948,
            'endTokenPos' => 365,
            'endFilePos' => 8948,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 179,
        'endLine' => 179,
        'startColumn' => 5,
        'endColumn' => 31,
      ),
      'NOTIFICATION_CHANNELS' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'NOTIFICATION_CHANNELS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'database\', \'email\']',
          'attributes' => 
          array (
            'startLine' => 181,
            'endLine' => 181,
            'startTokenPos' => 376,
            'startFilePos' => 9022,
            'endTokenPos' => 381,
            'endFilePos' => 9042,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 181,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'DEFAULT_AVATAR' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'DEFAULT_AVATAR',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'storage/images/avatar/default_avatar_head.webp\'',
          'attributes' => 
          array (
            'startLine' => 183,
            'endLine' => 183,
            'startTokenPos' => 392,
            'startFilePos' => 9080,
            'endTokenPos' => 392,
            'endFilePos' => 9127,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 183,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 83,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/users\'',
          'attributes' => 
          array (
            'startLine' => 186,
            'endLine' => 186,
            'startTokenPos' => 405,
            'startFilePos' => 9215,
            'endTokenPos' => 405,
            'endFilePos' => 9228,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 186,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_FILE_PATTERN_AVATARS' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'MEDIA_FILE_PATTERN_AVATARS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'avatar-[id]\'',
          'attributes' => 
          array (
            'startLine' => 189,
            'endLine' => 189,
            'startTokenPos' => 418,
            'startFilePos' => 9371,
            'endTokenPos' => 418,
            'endFilePos' => 9383,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection avatars (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 189,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 60,
      ),
      'SYSTEM_USER_ID' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'SYSTEM_USER_ID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 195,
            'endLine' => 195,
            'startTokenPos' => 429,
            'startFilePos' => 9572,
            'endTokenPos' => 429,
            'endFilePos' => 9572,
          ),
        ),
        'docComment' => '/**
 * ID de l\'utilisateur système (pour les imports automatiques)
 * Note: L\'ID réel peut varier, on utilise l\'email pour l\'identifier
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 195,
        'endLine' => 195,
        'startColumn' => 5,
        'endColumn' => 29,
      ),
      'SYSTEM_USER_EMAIL' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'SYSTEM_USER_EMAIL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'system@krosmozjdr.local\'',
          'attributes' => 
          array (
            'startLine' => 197,
            'endLine' => 197,
            'startTokenPos' => 440,
            'startFilePos' => 9670,
            'endTokenPos' => 440,
            'endFilePos' => 9694,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 197,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'email\', \'email_verified_at\', \'password\', \'role\', \'avatar\', \'notifications_enabled\', \'notification_channels\', \'notification_preferences\', \'is_system\', \'last_login_at\']',
          'attributes' => 
          array (
            'startLine' => 204,
            'endLine' => 216,
            'startTokenPos' => 453,
            'startFilePos' => 9875,
            'endTokenPos' => 488,
            'endFilePos' => 10145,
          ),
        ),
        'docComment' => '/**
 * The conditions that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 204,
        'endLine' => 216,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'hidden' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'hidden',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'password\', \'remember_token\']',
          'attributes' => 
          array (
            'startLine' => 223,
            'endLine' => 226,
            'startTokenPos' => 499,
            'startFilePos' => 10284,
            'endTokenPos' => 507,
            'endFilePos' => 10336,
          ),
        ),
        'docComment' => '/**
 * The conditions that should be hidden for serialization.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 223,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'email_verified_at\' => \'datetime\', \'password\' => \'hashed\', \'notifications_enabled\' => \'boolean\', \'notification_channels\' => \'array\', \'notification_preferences\' => \'array\', \'is_system\' => \'boolean\', \'last_login_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 233,
            'endLine' => 241,
            'startTokenPos' => 518,
            'startFilePos' => 10463,
            'endTokenPos' => 569,
            'endFilePos' => 10754,
          ),
        ),
        'docComment' => '/**
 * The conditions that should be cast.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 233,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'wantsNotification' => 
      array (
        'name' => 'wantsNotification',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 249,
                'endLine' => 249,
                'startTokenPos' => 587,
                'startFilePos' => 11119,
                'endTokenPos' => 587,
                'endFilePos' => 11122,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 39,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne true si l\'utilisateur souhaite recevoir des notifications (hors notification de profil).
 * Si $type est fourni, utilise les préférences par type (notification_preferences).
 *
 * @param  string|null  $type  Type de notification (clé config/notifications.php)
 */',
        'startLine' => 249,
        'endLine' => 256,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'getChannelsForNotificationType' => 
      array (
        'name' => 'getChannelsForNotificationType',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 265,
            'endLine' => 265,
            'startColumn' => 52,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne les canaux pour un type de notification donné (préférences par type ou défaut).
 * Forme préférence : { channels: [\'database\',\'mail\'], frequency: \'instant\' } ou legacy [ \'database\' ].
 *
 * @param  string  $type  Clé du type (ex: entity_modified, new_account_registered)
 * @return list<string> Canaux (\'database\', \'mail\') ou vide si désactivé
 */',
        'startLine' => 265,
        'endLine' => 296,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'getFrequencyForNotificationType' => 
      array (
        'name' => 'getFrequencyForNotificationType',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 304,
            'endLine' => 304,
            'startColumn' => 53,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne la fréquence pour un type de notification (instant, daily, weekly, monthly).
 *
 * @param  string  $type  Clé du type
 * @return string \'instant\'|\'daily\'|\'weekly\'|\'monthly\'
 */',
        'startLine' => 304,
        'endLine' => 315,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'wantsNotificationForType' => 
      array (
        'name' => 'wantsNotificationForType',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 322,
            'endLine' => 322,
            'startColumn' => 46,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indique si l\'utilisateur souhaite recevoir ce type de notification (au moins un canal).
 *
 * @param  string  $type  Clé du type
 */',
        'startLine' => 322,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'notificationChannels' => 
      array (
        'name' => 'notificationChannels',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne la liste des canaux de notification préférés de l\'utilisateur (défaut global).
 *
 * @return array Liste des canaux (ex: [\'database\', \'mail\'])
 */',
        'startLine' => 332,
        'endLine' => 335,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'wantsProfileNotification' => 
      array (
        'name' => 'wantsProfileNotification',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne toujours true : l\'utilisateur doit toujours être notifié pour la modification de son profil.
 */',
        'startLine' => 340,
        'endLine' => 343,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'registerMediaCollections' => 
      array (
        'name' => 'registerMediaCollections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Collections et conversions Media Library pour l\'avatar.
 */',
        'startLine' => 348,
        'endLine' => 351,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'registerMediaConversions' => 
      array (
        'name' => 'registerMediaConversions',
        'parameters' => 
        array (
          'media' => 
          array (
            'name' => 'media',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 356,
                'endLine' => 356,
                'startTokenPos' => 1175,
                'startFilePos' => 14739,
                'endTokenPos' => 1175,
                'endFilePos' => 14742,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 356,
            'endLine' => 356,
            'startColumn' => 46,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Conversions pour la collection avatars (WebP + miniature).
 */',
        'startLine' => 356,
        'endLine' => 369,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'avatarPath' => 
      array (
        'name' => 'avatarPath',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne l\'URL de l\'avatar de l\'utilisateur (jamais null).
 * Priorité : média collection avatars > colonne avatar (legacy) > défaut.
 *
 * @return string URL absolue de l\'avatar
 */',
        'startLine' => 377,
        'endLine' => 395,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'oauthAccounts' => 
      array (
        'name' => 'oauthAccounts',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relation vers les comptes OAuth liés.
 *
 * @return HasMany<OAuthAccount>
 */',
        'startLine' => 402,
        'endLine' => 405,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'hasVerifiedEmail' => 
      array (
        'name' => 'hasVerifiedEmail',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indique si l\'email est vérifié.
 * Retourne true si email_verified_at est défini OU si un provider OAuth est lié
 * (GitHub, Discord, Steam considèrent l\'identité comme vérifiée).
 */',
        'startLine' => 412,
        'endLine' => 419,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'sendEmailVerificationNotification' => 
      array (
        'name' => 'sendEmailVerificationNotification',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Envoie la notification de vérification d\'email (comptes classiques).
 * Utilise notre Mailable personnalisé et le layout emails.
 */',
        'startLine' => 425,
        'endLine' => 428,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'hasPassword' => 
      array (
        'name' => 'hasPassword',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indique si l\'utilisateur a un mot de passe défini (compte classique).
 */',
        'startLine' => 433,
        'endLine' => 436,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'hasOAuthProvider' => 
      array (
        'name' => 'hasOAuthProvider',
        'parameters' => 
        array (
          'provider' => 
          array (
            'name' => 'provider',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 441,
            'endLine' => 441,
            'startColumn' => 38,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indique si l\'utilisateur a lié un provider OAuth donné.
 */',
        'startLine' => 441,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'canUnlinkProvider' => 
      array (
        'name' => 'canUnlinkProvider',
        'parameters' => 
        array (
          'provider' => 
          array (
            'name' => 'provider',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 449,
            'endLine' => 449,
            'startColumn' => 39,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indique si l\'utilisateur peut délier ce provider (au moins une autre méthode de connexion restante).
 */',
        'startLine' => 449,
        'endLine' => 458,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'getRoleNameAttribute' => 
      array (
        'name' => 'getRoleNameAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne le nom du rôle de l\'utilisateur.
 *
 * @return string Nom du rôle
 */',
        'startLine' => 465,
        'endLine' => 468,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'roleValue' => 
      array (
        'name' => 'roleValue',
        'parameters' => 
        array (
          'role' => 
          array (
            'name' => 'role',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 476,
            'endLine' => 476,
            'startColumn' => 38,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'int',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne la valeur entière du rôle (par nom ou par valeur).
 *
 * @param  string|int  $role  Nom du rôle (ex: \'admin\') ou valeur entière (ex: 4)
 * @return int|null Valeur entière du rôle ou null si invalide
 */',
        'startLine' => 476,
        'endLine' => 483,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'verifyRole' => 
      array (
        'name' => 'verifyRole',
        'parameters' => 
        array (
          'role' => 
          array (
            'name' => 'role',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'int',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 490,
            'endLine' => 490,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si l\'utilisateur possède au moins le rôle donné (par nom ou par valeur).
 *
 * @param  string|int  $role  Nom du rôle (ex: \'admin\') ou valeur entière (ex: 4)
 */',
        'startLine' => 490,
        'endLine' => 501,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'updateRole' => 
      array (
        'name' => 'updateRole',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\User',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 508,
            'endLine' => 508,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si l\'utilisateur peut modifier le rôle d\'un autre utilisateur.
 *
 * @param  User  $user  Utilisateur à modifier
 */',
        'startLine' => 508,
        'endLine' => 516,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'isAdmin' => 
      array (
        'name' => 'isAdmin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si l\'utilisateur est un administrateur (admin ou super_admin).
 */',
        'startLine' => 521,
        'endLine' => 524,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'isSuperAdmin' => 
      array (
        'name' => 'isSuperAdmin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si l\'utilisateur est un super administrateur.
 *
 * Inclut le compte technique `is_system` (cron, scraping) — préférer
 * `isInteractiveSuperAdmin()` pour l’application web sécurisée.
 *
 * @example
 * $user->isSuperAdmin(); // true si role = super_admin
 *
 * @return bool True si role super_admin (y compris compte système)
 */',
        'startLine' => 537,
        'endLine' => 540,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'isInteractiveSuperAdmin' => 
      array (
        'name' => 'isInteractiveSuperAdmin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Super-administrateur « humain » : console instance / droits métiers web.
 *
 * Exclut `is_system` (impossible à authentifier de façon interactive).
 *
 * @example
 * $user->isInteractiveSuperAdmin();
 *
 * @return bool True si role super_admin et pas compte système
 */',
        'startLine' => 552,
        'endLine' => 555,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'isGameMaster' => 
      array (
        'name' => 'isGameMaster',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si l\'utilisateur est un game master ou supérieur.
 */',
        'startLine' => 560,
        'endLine' => 563,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdConditions' => 
      array (
        'name' => 'createdConditions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relations: entités créées par l\'utilisateur (hasMany)
 *
 * @return HasMany
 */',
        'startLine' => 570,
        'endLine' => 573,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdItems' => 
      array (
        'name' => 'createdItems',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 575,
        'endLine' => 578,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdConsumables' => 
      array (
        'name' => 'createdConsumables',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 580,
        'endLine' => 583,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdResources' => 
      array (
        'name' => 'createdResources',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 585,
        'endLine' => 588,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdCapabilities' => 
      array (
        'name' => 'createdCapabilities',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 590,
        'endLine' => 593,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdBreeds' => 
      array (
        'name' => 'createdBreeds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 595,
        'endLine' => 598,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdSpecializations' => 
      array (
        'name' => 'createdSpecializations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 600,
        'endLine' => 603,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdMonsterRaces' => 
      array (
        'name' => 'createdMonsterRaces',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 605,
        'endLine' => 608,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdShops' => 
      array (
        'name' => 'createdShops',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 610,
        'endLine' => 613,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdScenarios' => 
      array (
        'name' => 'createdScenarios',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 615,
        'endLine' => 618,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdSections' => 
      array (
        'name' => 'createdSections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 620,
        'endLine' => 623,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdPanoplies' => 
      array (
        'name' => 'createdPanoplies',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 625,
        'endLine' => 628,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdSpellTypes' => 
      array (
        'name' => 'createdSpellTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 630,
        'endLine' => 633,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdConsumableTypes' => 
      array (
        'name' => 'createdConsumableTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 635,
        'endLine' => 638,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdItemTypes' => 
      array (
        'name' => 'createdItemTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 640,
        'endLine' => 643,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdResourceTypes' => 
      array (
        'name' => 'createdResourceTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 645,
        'endLine' => 648,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdSpells' => 
      array (
        'name' => 'createdSpells',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 650,
        'endLine' => 653,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'createdPages' => 
      array (
        'name' => 'createdPages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 655,
        'endLine' => 658,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'scenarios' => 
      array (
        'name' => 'scenarios',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relations: scénarios auxquels l\'utilisateur participe (belongsToMany)
 *
 * @return BelongsToMany
 */',
        'startLine' => 665,
        'endLine' => 668,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'campaigns' => 
      array (
        'name' => 'campaigns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relations: campagnes auxquelles l\'utilisateur participe (belongsToMany)
 *
 * @return BelongsToMany
 */',
        'startLine' => 675,
        'endLine' => 678,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'pages' => 
      array (
        'name' => 'pages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relations: pages auxquelles l\'utilisateur participe (belongsToMany)
 *
 * @return BelongsToMany
 */',
        'startLine' => 685,
        'endLine' => 688,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'sections' => 
      array (
        'name' => 'sections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relations: sections auxquelles l\'utilisateur participe (belongsToMany)
 *
 * @return BelongsToMany
 */',
        'startLine' => 695,
        'endLine' => 698,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'canLogin' => 
      array (
        'name' => 'canLogin',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si l\'utilisateur peut se connecter
 * Les utilisateurs système ne peuvent pas se connecter
 */',
        'startLine' => 704,
        'endLine' => 707,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'booted' => 
      array (
        'name' => 'booted',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Un seul super_admin humain par instance (hors compte `is_system`).
 */',
        'startLine' => 712,
        'endLine' => 733,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
      'getSystemUser' => 
      array (
        'name' => 'getSystemUser',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'App\\Models\\User',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère l\'utilisateur système (pour les imports automatiques)
 */',
        'startLine' => 738,
        'endLine' => 741,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\User',
        'implementingClassName' => 'App\\Models\\User',
        'currentClassName' => 'App\\Models\\User',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));