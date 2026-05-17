<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/OAuthAccount.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\OAuthAccount
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-06caf96e8aa626d978d259170401e49cc4569de739d21aaaa5508de41fec26f8-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\OAuthAccount',
        'filename' => '/var/www/KrosmozJdr/app/Models/OAuthAccount.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\OAuthAccount',
    'shortName' => 'OAuthAccount',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
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
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount forUser(int $userId)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount provider(string $provider)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereAvatarUrl($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereProvider($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereProviderEmail($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereProviderId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereProviderName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|OAuthAccount whereUserId($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 43,
    'endLine' => 94,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'PROVIDER_GITHUB' => 
      array (
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'name' => 'PROVIDER_GITHUB',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'github\'',
          'attributes' => 
          array (
            'startLine' => 48,
            'endLine' => 48,
            'startTokenPos' => 58,
            'startFilePos' => 2275,
            'endTokenPos' => 58,
            'endFilePos' => 2282,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'PROVIDER_DISCORD' => 
      array (
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'name' => 'PROVIDER_DISCORD',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'discord\'',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 50,
            'startTokenPos' => 69,
            'startFilePos' => 2322,
            'endTokenPos' => 69,
            'endFilePos' => 2330,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'PROVIDER_STEAM' => 
      array (
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'name' => 'PROVIDER_STEAM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'steam\'',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 52,
            'startTokenPos' => 80,
            'startFilePos' => 2368,
            'endTokenPos' => 80,
            'endFilePos' => 2374,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'PROVIDERS' => 
      array (
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'name' => 'PROVIDERS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[self::PROVIDER_GITHUB, self::PROVIDER_DISCORD, self::PROVIDER_STEAM]',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 54,
            'startTokenPos' => 91,
            'startFilePos' => 2407,
            'endTokenPos' => 105,
            'endFilePos' => 2475,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 99,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'oauth_accounts\'',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 47,
            'startFilePos' => 2221,
            'endTokenPos' => 47,
            'endFilePos' => 2236,
          ),
        ),
        'docComment' => '/** @var string Nom de la table (évite la pluralisation incorrecte "o_auth_accounts"). */',
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'user_id\', \'provider\', \'provider_id\', \'provider_email\', \'provider_name\', \'avatar_url\']',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 63,
            'startTokenPos' => 114,
            'startFilePos' => 2505,
            'endTokenPos' => 134,
            'endFilePos' => 2646,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 63,
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
      'user' => 
      array (
        'name' => 'user',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Relation vers l\'utilisateur.
 */',
        'startLine' => 68,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'currentClassName' => 'App\\Models\\OAuthAccount',
        'aliasName' => NULL,
      ),
      'scopeProvider' => 
      array (
        'name' => 'scopeProvider',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 79,
            'endLine' => 79,
            'startColumn' => 35,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 79,
            'endLine' => 79,
            'startColumn' => 51,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope pour filtrer par provider.
 *
 * @param  Builder<OAuthAccount>  $query
 * @return Builder<OAuthAccount>
 */',
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'currentClassName' => 'App\\Models\\OAuthAccount',
        'aliasName' => NULL,
      ),
      'scopeForUser' => 
      array (
        'name' => 'scopeForUser',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 34,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'userId' => 
          array (
            'name' => 'userId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 50,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope pour filtrer par utilisateur.
 *
 * @param  Builder<OAuthAccount>  $query
 * @return Builder<OAuthAccount>
 */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\OAuthAccount',
        'implementingClassName' => 'App\\Models\\OAuthAccount',
        'currentClassName' => 'App\\Models\\OAuthAccount',
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