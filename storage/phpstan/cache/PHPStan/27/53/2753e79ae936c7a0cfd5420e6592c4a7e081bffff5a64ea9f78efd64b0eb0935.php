<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Characteristic.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Characteristic
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-583eb8e59bd8eac31839ef7fc6c1847a26d23efa43bf7dedd6e969dc1dcf912a-8.4.17-6.70.0.0',
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
 * @property string|null $unit
 * @property string $type
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
 *
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
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 76,
    'endLine' => 182,
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
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 101,
            'startFilePos' => 4143,
            'endTokenPos' => 101,
            'endFilePos' => 4173,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
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
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 114,
            'startFilePos' => 4312,
            'endTokenPos' => 114,
            'endFilePos' => 4318,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection icons (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
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
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 123,
            'startFilePos' => 4345,
            'endTokenPos' => 123,
            'endFilePos' => 4361,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
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
          'code' => '[\'key\', \'name\', \'short_name\', \'helper\', \'descriptions\', \'icon\', \'icon_false\', \'color\', \'value_overrides\', \'hide_when_empty\', \'unit\', \'type\', \'sort_order\', \'group\', \'linked_to_characteristic_id\']',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 106,
            'startTokenPos' => 134,
            'startFilePos' => 4420,
            'endTokenPos' => 181,
            'endFilePos' => 4740,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 106,
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
          'code' => '[\'sort_order\' => \'integer\', \'value_overrides\' => \'array\', \'hide_when_empty\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 113,
            'startTokenPos' => 192,
            'startFilePos' => 4805,
            'endTokenPos' => 215,
            'endFilePos' => 4924,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 113,
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
        'startLine' => 118,
        'endLine' => 121,
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
        'startLine' => 128,
        'endLine' => 131,
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
        'startLine' => 136,
        'endLine' => 139,
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
        'startLine' => 144,
        'endLine' => 153,
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
        'startLine' => 160,
        'endLine' => 163,
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
        'startLine' => 165,
        'endLine' => 168,
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
        'startLine' => 170,
        'endLine' => 173,
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
                'startLine' => 175,
                'endLine' => 175,
                'startTokenPos' => 517,
                'startFilePos' => 6642,
                'endTokenPos' => 517,
                'endFilePos' => 6645,
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
            'startLine' => 175,
            'endLine' => 175,
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
        'startLine' => 175,
        'endLine' => 181,
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