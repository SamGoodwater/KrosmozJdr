<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/DataSubjectRequest.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\DataSubjectRequest
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-9185da625daf11a9ba7bf9b43efa03bf3431ca53bc00e99d195992aef6e3db87-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\DataSubjectRequest',
        'filename' => '/var/www/KrosmozJdr/app/Models/DataSubjectRequest.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\DataSubjectRequest',
    'shortName' => 'DataSubjectRequest',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property Carbon $requested_at
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $processed_at
 * @property Carbon|null $expires_at
 * @property array<array-key, mixed>|null $meta
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereConfirmedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereExpiresAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereIpAddress($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereMeta($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereProcessedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereRequestedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereStatus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereType($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereUserAgent($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DataSubjectRequest whereUserId($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 43,
    'endLine' => 86,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
      'TYPE_EXPORT' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'TYPE_EXPORT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'export\'',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 52,
            'startFilePos' => 2452,
            'endTokenPos' => 52,
            'endFilePos' => 2459,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'TYPE_ERASURE' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'TYPE_ERASURE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'erasure\'',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 49,
            'startTokenPos' => 63,
            'startFilePos' => 2495,
            'endTokenPos' => 63,
            'endFilePos' => 2503,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'STATUS_PENDING' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'STATUS_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 74,
            'startFilePos' => 2541,
            'endTokenPos' => 74,
            'endFilePos' => 2549,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 51,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'STATUS_PROCESSING' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'STATUS_PROCESSING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'processing\'',
          'attributes' => 
          array (
            'startLine' => 53,
            'endLine' => 53,
            'startTokenPos' => 85,
            'startFilePos' => 2590,
            'endTokenPos' => 85,
            'endFilePos' => 2601,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'STATUS_COMPLETED' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'STATUS_COMPLETED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'completed\'',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 96,
            'startFilePos' => 2641,
            'endTokenPos' => 96,
            'endFilePos' => 2651,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'STATUS_FAILED' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'STATUS_FAILED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'failed\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 107,
            'startFilePos' => 2688,
            'endTokenPos' => 107,
            'endFilePos' => 2695,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'STATUS_CANCELLED' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'STATUS_CANCELLED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'cancelled\'',
          'attributes' => 
          array (
            'startLine' => 59,
            'endLine' => 59,
            'startTokenPos' => 118,
            'startFilePos' => 2735,
            'endTokenPos' => 118,
            'endFilePos' => 2745,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'user_id\', \'type\', \'status\', \'requested_at\', \'confirmed_at\', \'processed_at\', \'expires_at\', \'meta\', \'ip_address\', \'user_agent\']',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 72,
            'startTokenPos' => 127,
            'startFilePos' => 2775,
            'endTokenPos' => 159,
            'endFilePos' => 2988,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 72,
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
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'requested_at\' => \'datetime\', \'confirmed_at\' => \'datetime\', \'processed_at\' => \'datetime\', \'expires_at\' => \'datetime\', \'meta\' => \'array\']',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 80,
            'startTokenPos' => 168,
            'startFilePos' => 3015,
            'endTokenPos' => 205,
            'endFilePos' => 3198,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 80,
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
        'docComment' => NULL,
        'startLine' => 82,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\DataSubjectRequest',
        'implementingClassName' => 'App\\Models\\DataSubjectRequest',
        'currentClassName' => 'App\\Models\\DataSubjectRequest',
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