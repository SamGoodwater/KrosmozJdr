<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/ItemType.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\ItemType
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ee431c38eae5e5a1790f02e10932b96dfc2985b75b7d1200356f178a21e5f6ee-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Type\\ItemType',
        'filename' => '/var/www/KrosmozJdr/app/Models/Type/ItemType.php',
      ),
    ),
    'namespace' => 'App\\Models\\Type',
    'name' => 'App\\Models\\Type\\ItemType',
    'shortName' => 'ItemType',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @method static \\Database\\Factories\\Type\\ItemTypeFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType withoutTrashed()
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property Carbon|null $last_seen_at
 * @property-read Collection<int, CharacteristicObject> $allowedCharacteristicObjects
 * @property-read int|null $allowed_characteristic_objects_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType allowed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType blocked()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType pending()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereDecision($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereDofusdbTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereLastSeenAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereSeenCount($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 60,
    'endLine' => 214,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 87,
            'startFilePos' => 3394,
            'endTokenPos' => 87,
            'endFilePos' => 3398,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 98,
            'startFilePos' => 3433,
            'endTokenPos' => 98,
            'endFilePos' => 3439,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 109,
            'startFilePos' => 3477,
            'endTokenPos' => 109,
            'endFilePos' => 3486,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 120,
            'startFilePos' => 3524,
            'endTokenPos' => 120,
            'endFilePos' => 3533,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'DECISION_PENDING' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'DECISION_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 131,
            'startFilePos' => 3573,
            'endTokenPos' => 131,
            'endFilePos' => 3581,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'DECISION_ALLOWED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'DECISION_ALLOWED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'allowed\'',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 142,
            'startFilePos' => 3621,
            'endTokenPos' => 142,
            'endFilePos' => 3629,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'DECISION_BLOCKED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'DECISION_BLOCKED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'blocked\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 153,
            'startFilePos' => 3669,
            'endTokenPos' => 153,
            'endFilePos' => 3677,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'dofusdb_type_id\', \'decision\', \'seen_count\', \'last_seen_at\', \'state\', \'read_level\', \'write_level\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 94,
            'startTokenPos' => 164,
            'startFilePos' => 3803,
            'endTokenPos' => 193,
            'endFilePos' => 4002,
          ),
        ),
        'docComment' => '/**
 * The attributes that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 94,
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
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\', \'dofusdb_type_id\' => \'integer\', \'seen_count\' => \'integer\', \'last_seen_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 107,
            'startTokenPos' => 204,
            'startFilePos' => 4129,
            'endTokenPos' => 241,
            'endFilePos' => 4319,
          ),
        ),
        'docComment' => '/**
 * The attributes that should be cast.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 107,
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
      'scopeAllowed' => 
      array (
        'name' => 'scopeAllowed',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 34,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope: types explicitement autorisés (whitelist).
 */',
        'startLine' => 112,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'scopePending' => 
      array (
        'name' => 'scopePending',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 34,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope: types en attente de validation UX.
 */',
        'startLine' => 120,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'scopeBlocked' => 
      array (
        'name' => 'scopeBlocked',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 128,
            'endLine' => 128,
            'startColumn' => 34,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Scope: types bloqués (blacklist).
 */',
        'startLine' => 128,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'isDofusdbTypeAllowed' => 
      array (
        'name' => 'isDofusdbTypeAllowed',
        'parameters' => 
        array (
          'typeId' => 
          array (
            'name' => 'typeId',
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
            'startLine' => 139,
            'endLine' => 139,
            'startColumn' => 49,
            'endColumn' => 59,
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
 * Indique si un typeId DofusDB est explicitement autorisé en base.
 *
 * Comportement:
 * - Si le type n\'existe pas encore, il est créé en `decision=pending` et la méthode retourne false.
 */',
        'startLine' => 139,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'touchDofusdbType' => 
      array (
        'name' => 'touchDofusdbType',
        'parameters' => 
        array (
          'typeId' => 
          array (
            'name' => 'typeId',
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
            'startLine' => 157,
            'endLine' => 157,
            'startColumn' => 45,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'label' => 
          array (
            'name' => 'label',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 157,
                'endLine' => 157,
                'startTokenPos' => 435,
                'startFilePos' => 5700,
                'endTokenPos' => 435,
                'endFilePos' => 5703,
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
            'startLine' => 157,
            'endLine' => 157,
            'startColumn' => 58,
            'endColumn' => 78,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enregistre/actualise un typeId DofusDB détecté (pour revue dans le dashboard).
 *
 * @param  string|null  $label  Libellé optionnel pour initialiser ou améliorer `name`.
 */',
        'startLine' => 157,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'createdBy' => 
      array (
        'name' => 'createdBy',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the user that created the item type.
 */',
        'startLine' => 191,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'items' => 
      array (
        'name' => 'items',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les objets de ce type.
 */',
        'startLine' => 199,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
        'aliasName' => NULL,
      ),
      'allowedCharacteristicObjects' => 
      array (
        'name' => 'allowedCharacteristicObjects',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Définitions de caractéristiques (groupe object) qui sont réservées à ce type d\'équipement.
 *
 * @return BelongsToMany<CharacteristicObject, self>
 */',
        'startLine' => 209,
        'endLine' => 213,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ItemType',
        'implementingClassName' => 'App\\Models\\Type\\ItemType',
        'currentClassName' => 'App\\Models\\Type\\ItemType',
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