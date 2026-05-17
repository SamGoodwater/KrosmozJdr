<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/ResourceType.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\ResourceType
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ef60d73930de0107442edfb4bbb8fdc2fe865ce1f9e44382eef418b21a8dafc5-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Type\\ResourceType',
        'filename' => '/var/www/KrosmozJdr/app/Models/Type/ResourceType.php',
      ),
    ),
    'namespace' => 'App\\Models\\Type',
    'name' => 'App\\Models\\Type\\ResourceType',
    'shortName' => 'ResourceType',
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
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 *
 * @method static \\Database\\Factories\\Type\\ResourceTypeFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType withoutTrashed()
 *
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property Carbon|null $last_seen_at
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType allowed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType blocked()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType pending()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereDecision($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereDofusdbTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereLastSeenAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ResourceType whereSeenCount($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 61,
    'endLine' => 226,
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
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 82,
            'startFilePos' => 3322,
            'endTokenPos' => 82,
            'endFilePos' => 3326,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 68,
            'endLine' => 68,
            'startTokenPos' => 93,
            'startFilePos' => 3361,
            'endTokenPos' => 93,
            'endFilePos' => 3367,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 68,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 104,
            'startFilePos' => 3405,
            'endTokenPos' => 104,
            'endFilePos' => 3414,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 72,
            'endLine' => 72,
            'startTokenPos' => 115,
            'startFilePos' => 3452,
            'endTokenPos' => 115,
            'endFilePos' => 3461,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'DECISION_PENDING' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'DECISION_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 126,
            'startFilePos' => 3501,
            'endTokenPos' => 126,
            'endFilePos' => 3509,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'DECISION_ALLOWED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'DECISION_ALLOWED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'allowed\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 137,
            'startFilePos' => 3549,
            'endTokenPos' => 137,
            'endFilePos' => 3557,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'DECISION_BLOCKED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'DECISION_BLOCKED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'blocked\'',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 148,
            'startFilePos' => 3597,
            'endTokenPos' => 148,
            'endFilePos' => 3605,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'dofusdb_type_id\', \'state\', \'read_level\', \'write_level\', \'decision\', \'seen_count\', \'last_seen_at\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 95,
            'startTokenPos' => 159,
            'startFilePos' => 3731,
            'endTokenPos' => 188,
            'endFilePos' => 3930,
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
        'startLine' => 85,
        'endLine' => 95,
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
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\', \'dofusdb_type_id\' => \'integer\', \'seen_count\' => \'integer\', \'last_seen_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 102,
            'endLine' => 108,
            'startTokenPos' => 199,
            'startFilePos' => 4057,
            'endTokenPos' => 236,
            'endFilePos' => 4247,
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
        'startLine' => 102,
        'endLine' => 108,
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
            'startLine' => 119,
            'endLine' => 119,
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
 *
 * @param  Builder  $query
 * @return Builder
 *
 * @example
 * ResourceType::query()->allowed()->get();
 */',
        'startLine' => 119,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
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
            'startLine' => 130,
            'endLine' => 130,
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
 *
 * @param  Builder  $query
 * @return Builder
 */',
        'startLine' => 130,
        'endLine' => 133,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
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
            'startLine' => 141,
            'endLine' => 141,
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
 *
 * @param  Builder  $query
 * @return Builder
 */',
        'startLine' => 141,
        'endLine' => 144,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
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
            'startLine' => 159,
            'endLine' => 159,
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
 * - Si le type n\'existe pas encore, il est créé en `decision=pending` (à valider via UX)
 *   et la méthode retourne false (sécurité par défaut).
 *
 *
 * @example
 * if (ResourceType::isDofusdbTypeAllowed(15)) {
 *   // traiter comme ressource
 * }
 */',
        'startLine' => 159,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
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
            'startLine' => 180,
            'endLine' => 180,
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
                'startLine' => 180,
                'endLine' => 180,
                'startTokenPos' => 430,
                'startFilePos' => 6139,
                'endTokenPos' => 430,
                'endFilePos' => 6142,
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
            'startLine' => 180,
            'endLine' => 180,
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
 *
 * @example
 * ResourceType::touchDofusdbType(35, \'Fleur\');
 */',
        'startLine' => 180,
        'endLine' => 209,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
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
 * Get the user that created the resource type.
 */',
        'startLine' => 214,
        'endLine' => 217,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
        'aliasName' => NULL,
      ),
      'resources' => 
      array (
        'name' => 'resources',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les ressources de ce type.
 */',
        'startLine' => 222,
        'endLine' => 225,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ResourceType',
        'implementingClassName' => 'App\\Models\\Type\\ResourceType',
        'currentClassName' => 'App\\Models\\Type\\ResourceType',
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