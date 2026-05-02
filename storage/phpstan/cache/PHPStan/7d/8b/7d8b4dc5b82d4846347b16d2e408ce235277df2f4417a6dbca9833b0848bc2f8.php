<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/ConsumableType.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\ConsumableType
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-896d89b9040ed6e3e3d76f035ee219fc46a0134ab207dea2502f93f1f414285a-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Type\\ConsumableType',
        'filename' => '/var/www/KrosmozJdr/app/Models/Type/ConsumableType.php',
      ),
    ),
    'namespace' => 'App\\Models\\Type',
    'name' => 'App\\Models\\Type\\ConsumableType',
    'shortName' => 'ConsumableType',
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
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @method static \\Database\\Factories\\Type\\ConsumableTypeFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType withoutTrashed()
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property Carbon|null $last_seen_at
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType allowed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType blocked()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType pending()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereDecision($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereDofusdbTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereLastSeenAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ConsumableType whereSeenCount($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 56,
    'endLine' => 198,
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
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 77,
            'startFilePos' => 3328,
            'endTokenPos' => 77,
            'endFilePos' => 3332,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 63,
            'endLine' => 63,
            'startTokenPos' => 88,
            'startFilePos' => 3367,
            'endTokenPos' => 88,
            'endFilePos' => 3373,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 63,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 99,
            'startFilePos' => 3411,
            'endTokenPos' => 99,
            'endFilePos' => 3420,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 110,
            'startFilePos' => 3458,
            'endTokenPos' => 110,
            'endFilePos' => 3467,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'DECISION_PENDING' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'DECISION_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 121,
            'startFilePos' => 3507,
            'endTokenPos' => 121,
            'endFilePos' => 3515,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'DECISION_ALLOWED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'DECISION_ALLOWED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'allowed\'',
          'attributes' => 
          array (
            'startLine' => 71,
            'endLine' => 71,
            'startTokenPos' => 132,
            'startFilePos' => 3555,
            'endTokenPos' => 132,
            'endFilePos' => 3563,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 71,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'DECISION_BLOCKED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'DECISION_BLOCKED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'blocked\'',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 143,
            'startFilePos' => 3603,
            'endTokenPos' => 143,
            'endFilePos' => 3611,
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
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'dofusdb_type_id\', \'decision\', \'seen_count\', \'last_seen_at\', \'state\', \'read_level\', \'write_level\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 90,
            'startTokenPos' => 154,
            'startFilePos' => 3737,
            'endTokenPos' => 183,
            'endFilePos' => 3936,
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
        'startLine' => 80,
        'endLine' => 90,
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
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\', \'dofusdb_type_id\' => \'integer\', \'seen_count\' => \'integer\', \'last_seen_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 103,
            'startTokenPos' => 194,
            'startFilePos' => 4063,
            'endTokenPos' => 231,
            'endFilePos' => 4253,
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
        'startLine' => 97,
        'endLine' => 103,
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
            'startLine' => 108,
            'endLine' => 108,
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
        'startLine' => 108,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
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
 * Scope: types en attente de validation UX.
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
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
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
 * Scope: types bloqués (blacklist).
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
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
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
            'startLine' => 135,
            'endLine' => 135,
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
        'startLine' => 135,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
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
            'startLine' => 153,
            'endLine' => 153,
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
                'startLine' => 153,
                'endLine' => 153,
                'startTokenPos' => 425,
                'startFilePos' => 5634,
                'endTokenPos' => 425,
                'endFilePos' => 5637,
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
            'startLine' => 153,
            'endLine' => 153,
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
        'startLine' => 153,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
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
 * Get the user that created the consumable type.
 */',
        'startLine' => 186,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
        'aliasName' => NULL,
      ),
      'consumables' => 
      array (
        'name' => 'consumables',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les consommables de ce type.
 */',
        'startLine' => 194,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ConsumableType',
        'implementingClassName' => 'App\\Models\\Type\\ConsumableType',
        'currentClassName' => 'App\\Models\\Type\\ConsumableType',
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