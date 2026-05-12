<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Characteristic.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Characteristic
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0c41b60078e6e5db6460846dd8190f46b48b02a61b034d4fe9b7cf89aba66c4a-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Characteristic',
        'filename' => '/var/www/KrosmozJdr/app/Models/Characteristic.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Characteristic',
    'shortName' => 'Characteristic',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Caractéristique générale : propriétés communes et id unique.
 *
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet = 3 lignes).
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $short_name
 * @property string|null $helper
 * @property string|null $descriptions
 * @property string|null $icon
 * @property string|null $icon_false
 * @property string|null $color
 * @property array|null $value_overrides
 * @property bool $hide_when_empty
 * @property bool $hide_when_false
 * @property string|null $unit
 * @property string $type
 * @property string $status
 * @property int $sort_order
 * @property string|null $group
 * @property int|null $linked_to_characteristic_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, CharacteristicCreature> $creatureRows
 * @property-read int|null $creature_rows_count
 * @property-read Collection<int, Characteristic> $linkedCharacteristics
 * @property-read int|null $linked_characteristics_count
 * @property-read Characteristic|null $masterCharacteristic
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, CharacteristicObject> $objectRows
 * @property-read int|null $object_rows_count
 * @property-read Collection<int, CharacteristicSpell> $spellRows
 * @property-read int|null $spell_rows_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereColor($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereDescriptions($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereGroup($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereHelper($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereIcon($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereIconFalse($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereKey($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereLinkedToCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereShortName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereSortOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereType($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereUnit($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereValueOverrides($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereHideWhenEmpty($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereStatus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Characteristic whereHideWhenFalse($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 80,
    'endLine' => 206,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\HasMedia',
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Concerns\\HasMediaCustomNaming',
      1 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
    ),
    'immediateConstants' => 
    array (
      'STATUS_A_VALIDER' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'STATUS_A_VALIDER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'a_valider\'',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 104,
            'startFilePos' => 4491,
            'endTokenPos' => 104,
            'endFilePos' => 4501,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'STATUS_EN_COURS_VALIDATION' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'STATUS_EN_COURS_VALIDATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'en_cours_de_validation\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 115,
            'startFilePos' => 4551,
            'endTokenPos' => 115,
            'endFilePos' => 4574,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 71,
      ),
      'STATUS_VALIDEE' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'STATUS_VALIDEE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'validee\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 126,
            'startFilePos' => 4612,
            'endTokenPos' => 126,
            'endFilePos' => 4620,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'STATUSES' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'STATUSES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[self::STATUS_A_VALIDER, self::STATUS_EN_COURS_VALIDATION, self::STATUS_VALIDEE]',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 95,
            'startTokenPos' => 137,
            'startFilePos' => 4652,
            'endTokenPos' => 154,
            'endFilePos' => 4762,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/characteristics\'',
          'attributes' => 
          array (
            'startLine' => 98,
            'endLine' => 98,
            'startTokenPos' => 167,
            'startFilePos' => 4850,
            'endTokenPos' => 167,
            'endFilePos' => 4880,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 98,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 62,
      ),
      'MEDIA_FILE_PATTERN_ICONS' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'MEDIA_FILE_PATTERN_ICONS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'[key]\'',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 180,
            'startFilePos' => 5023,
            'endTokenPos' => 180,
            'endFilePos' => 5029,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection icons (placeholders: [key], [id], [name], …). */',
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'characteristics\'',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 189,
            'startFilePos' => 5056,
            'endTokenPos' => 189,
            'endFilePos' => 5072,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'key\', \'name\', \'short_name\', \'helper\', \'descriptions\', \'icon\', \'icon_false\', \'color\', \'value_overrides\', \'hide_when_empty\', \'hide_when_false\', \'unit\', \'type\', \'status\', \'sort_order\', \'group\', \'linked_to_characteristic_id\']',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 124,
            'startTokenPos' => 200,
            'startFilePos' => 5131,
            'endTokenPos' => 253,
            'endFilePos' => 5496,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 124,
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
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'sort_order\' => \'integer\', \'value_overrides\' => \'array\', \'hide_when_empty\' => \'boolean\', \'hide_when_false\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 127,
            'endLine' => 132,
            'startTokenPos' => 264,
            'startFilePos' => 5561,
            'endTokenPos' => 294,
            'endFilePos' => 5720,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 127,
        'endLine' => 132,
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
      'masterCharacteristic' => 
      array (
        'name' => 'masterCharacteristic',
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
 * Caractéristique maître si cette ligne est une caractéristique liée.
 */',
        'startLine' => 137,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
        'aliasName' => NULL,
      ),
      'linkedCharacteristics' => 
      array (
        'name' => 'linkedCharacteristics',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Caractéristiques liées qui réutilisent cette ligne comme source de configuration.
 *
 * @return HasMany<self>
 */',
        'startLine' => 147,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
        'aliasName' => NULL,
      ),
      'isLinked' => 
      array (
        'name' => 'isLinked',
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
 * Indique si la caractéristique est liée à une autre.
 */',
        'startLine' => 155,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
        'aliasName' => NULL,
      ),
      'effectiveCharacteristic' => 
      array (
        'name' => 'effectiveCharacteristic',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne la caractéristique effective (maître si liée, sinon elle-même).
 */',
        'startLine' => 163,
        'endLine' => 172,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
        'aliasName' => NULL,
      ),
      'creatureRows' => 
      array (
        'name' => 'creatureRows',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 174,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
        'aliasName' => NULL,
      ),
      'objectRows' => 
      array (
        'name' => 'objectRows',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 179,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
        'aliasName' => NULL,
      ),
      'spellRows' => 
      array (
        'name' => 'spellRows',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 184,
        'endLine' => 187,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
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
        'docComment' => NULL,
        'startLine' => 189,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
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
                'startLine' => 194,
                'endLine' => 194,
                'startTokenPos' => 596,
                'startFilePos' => 7438,
                'endTokenPos' => 596,
                'endFilePos' => 7441,
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
            'startLine' => 194,
            'endLine' => 194,
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
        'docComment' => NULL,
        'startLine' => 194,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Characteristic',
        'implementingClassName' => 'App\\Models\\Characteristic',
        'currentClassName' => 'App\\Models\\Characteristic',
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