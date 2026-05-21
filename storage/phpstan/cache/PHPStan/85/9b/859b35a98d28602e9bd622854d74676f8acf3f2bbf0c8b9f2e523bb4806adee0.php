<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/ItemType.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\ItemType
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-25dd48901fb89e2bab82513afb0347d8da2a665e65e47ff7f51d6d4c6a5d2d93-8.4.17-6.70.0.1',
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
 *
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
 *
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property Carbon|null $last_seen_at
 * @property-read Collection<int, CharacteristicObject> $allowedCharacteristicObjects
 * @property-read int|null $allowed_characteristic_objects_count
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType allowed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType blocked()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType pending()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereDecision($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereDofusdbTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereLastSeenAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ItemType whereSeenCount($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 64,
    'endLine' => 218,
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
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 87,
            'startFilePos' => 3406,
            'endTokenPos' => 87,
            'endFilePos' => 3410,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
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
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 98,
            'startFilePos' => 3445,
            'endTokenPos' => 98,
            'endFilePos' => 3451,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
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
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 109,
            'startFilePos' => 3489,
            'endTokenPos' => 109,
            'endFilePos' => 3498,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
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
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 120,
            'startFilePos' => 3536,
            'endTokenPos' => 120,
            'endFilePos' => 3545,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
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
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 131,
            'startFilePos' => 3585,
            'endTokenPos' => 131,
            'endFilePos' => 3593,
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
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 142,
            'startFilePos' => 3633,
            'endTokenPos' => 142,
            'endFilePos' => 3641,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
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
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 153,
            'startFilePos' => 3681,
            'endTokenPos' => 153,
            'endFilePos' => 3689,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
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
            'startLine' => 88,
            'endLine' => 98,
            'startTokenPos' => 164,
            'startFilePos' => 3815,
            'endTokenPos' => 193,
            'endFilePos' => 4014,
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
        'startLine' => 88,
        'endLine' => 98,
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
            'startLine' => 105,
            'endLine' => 111,
            'startTokenPos' => 204,
            'startFilePos' => 4141,
            'endTokenPos' => 241,
            'endFilePos' => 4331,
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
        'startLine' => 105,
        'endLine' => 111,
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
            'startLine' => 116,
            'endLine' => 116,
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
        'startLine' => 116,
        'endLine' => 119,
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
            'startLine' => 124,
            'endLine' => 124,
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
        'startLine' => 124,
        'endLine' => 127,
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
            'startLine' => 132,
            'endLine' => 132,
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
        'startLine' => 132,
        'endLine' => 135,
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
            'startLine' => 143,
            'endLine' => 143,
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
        'startLine' => 143,
        'endLine' => 154,
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
            'startLine' => 161,
            'endLine' => 161,
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
                'startLine' => 161,
                'endLine' => 161,
                'startTokenPos' => 435,
                'startFilePos' => 5712,
                'endTokenPos' => 435,
                'endFilePos' => 5715,
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
            'startLine' => 161,
            'endLine' => 161,
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
        'startLine' => 161,
        'endLine' => 190,
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
        'startLine' => 195,
        'endLine' => 198,
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
        'startLine' => 203,
        'endLine' => 206,
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
        'startLine' => 213,
        'endLine' => 217,
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