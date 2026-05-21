<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Breed.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Breed
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-bbeef98add0917859231430aed7ea6335e75b3059341c92ebdc951baa45cc089-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Breed',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Breed.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Breed',
    'shortName' => 'Breed',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Entité Breed (affichée « Classe » côté utilisateur).
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string|null $description_fast
 * @property string|null $description
 * @property string|null $evolution
 * @property string|null $life_dice
 * @property string|null $specificity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property string|null $icon
 * @property bool $auto_update
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @method static \\Database\\Factories\\Entity\\BreedFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed query()
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereAutoUpdate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereDescriptionFast($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereDofusVersion($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereIcon($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereLife($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereLifeDice($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereOfficialId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereSpecificity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed withTrashed(bool $withTrashed = true)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Breed withoutTrashed()
 * @property string|null $life
 * @property-read Collection<int, BreedElementOrientation> $elementOrientations
 * @property-read int|null $element_orientations_count
 * @property-read BreedSpellPivot|null $pivot
 * @method static Builder<static>|Breed visibleToUser(?\\App\\Models\\User $user)
 * @property-read Collection<int, Language> $languages
 * @property-read int|null $languages_count
 * @method static Builder<static>|Breed whereEvolution($value)
 * @property-read Collection<int, CreatureTrait> $creatureTraits
 * @property-read int|null $creature_traits_count
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 91,
    'endLine' => 334,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\HasMedia',
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Concerns\\HasEntityImageMedia',
      1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/breeds\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 116,
            'startFilePos' => 5078,
            'endTokenPos' => 116,
            'endFilePos' => 5099,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'MEDIA_FILE_PATTERN_ICONS' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'MEDIA_FILE_PATTERN_ICONS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'icon-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 129,
            'startFilePos' => 5238,
            'endTokenPos' => 129,
            'endFilePos' => 5255,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection icons (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 102,
            'endLine' => 102,
            'startTokenPos' => 140,
            'startFilePos' => 5304,
            'endTokenPos' => 140,
            'endFilePos' => 5322,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 106,
            'startTokenPos' => 160,
            'startFilePos' => 5389,
            'endTokenPos' => 160,
            'endFilePos' => 5393,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 108,
            'endLine' => 108,
            'startTokenPos' => 171,
            'startFilePos' => 5428,
            'endTokenPos' => 171,
            'endFilePos' => 5434,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 108,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 182,
            'startFilePos' => 5472,
            'endTokenPos' => 182,
            'endFilePos' => 5481,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 112,
            'endLine' => 112,
            'startTokenPos' => 193,
            'startFilePos' => 5519,
            'endTokenPos' => 193,
            'endFilePos' => 5528,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 112,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'breeds\'',
          'attributes' => 
          array (
            'startLine' => 104,
            'endLine' => 104,
            'startTokenPos' => 149,
            'startFilePos' => 5349,
            'endTokenPos' => 149,
            'endFilePos' => 5356,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 104,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'official_id\', \'dofusdb_id\', \'name\', \'description_fast\', \'description\', \'evolution\', \'life_dice\', \'specificity\', \'dofus_version\', \'state\', \'read_level\', \'write_level\', \'image\', \'icon\', \'auto_update\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 119,
            'endLine' => 136,
            'startTokenPos' => 204,
            'startFilePos' => 5654,
            'endTokenPos' => 254,
            'endFilePos' => 6002,
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
        'startLine' => 119,
        'endLine' => 136,
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
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\', \'auto_update\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 143,
            'endLine' => 147,
            'startTokenPos' => 265,
            'startFilePos' => 6129,
            'endTokenPos' => 288,
            'endFilePos' => 6242,
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
        'startLine' => 143,
        'endLine' => 147,
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
      'scopeVisibleToUser' => 
      array (
        'name' => 'scopeVisibleToUser',
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
            'startLine' => 156,
            'endLine' => 156,
            'startColumn' => 40,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'user' => 
          array (
            'name' => 'user',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 156,
            'endLine' => 156,
            'startColumn' => 56,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Filtre les classes visibles pour l\'utilisateur (index Inertia, API tableau).
 *
 * Brouillon/raw : createur ou role >= MJ. Playable : role >= read_level. Pas d\'archive pour non-admin.
 *
 * @param  Builder<static>  $query
 */',
        'startLine' => 156,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
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
 * Get the user that created the breed.
 */',
        'startLine' => 187,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'npcs' => 
      array (
        'name' => 'npcs',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les PNJ associés à cette breed.
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'spells' => 
      array (
        'name' => 'spells',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les sorts associés à cette breed (pivot : niveau PJ, emplacement, ordre des choix).
 */',
        'startLine' => 203,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'capabilities' => 
      array (
        'name' => 'capabilities',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Capacités associées à la classe (liste plate, sans emplacement).
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'creatureTraits' => 
      array (
        'name' => 'creatureTraits',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 219,
        'endLine' => 224,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'languages' => 
      array (
        'name' => 'languages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 226,
        'endLine' => 232,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'elementOrientations' => 
      array (
        'name' => 'elementOrientations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Orientations par voix élémentaire (air, terre, feu, eau).
 */',
        'startLine' => 237,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'elementOrientationsMap' => 
      array (
        'name' => 'elementOrientationsMap',
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
 * @return array<string, string|null> air|earth|fire|water => orientation_key|null
 */',
        'startLine' => 245,
        'endLine' => 256,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
        'aliasName' => NULL,
      ),
      'getSpellSlotsGrouped' => 
      array (
        'name' => 'getSpellSlotsGrouped',
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
 * Regroupe les sorts chargés par (character_level, slot_index) pour l’API / la fiche.
 *
 * @return list<array{character_level: int, slot_index: int, spells: Collection<int, Spell>}>
 */',
        'startLine' => 263,
        'endLine' => 313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
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
        'startLine' => 315,
        'endLine' => 319,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
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
                'startLine' => 321,
                'endLine' => 321,
                'startTokenPos' => 1411,
                'startFilePos' => 11991,
                'endTokenPos' => 1411,
                'endFilePos' => 11994,
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
            'startLine' => 321,
            'endLine' => 321,
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
        'startLine' => 321,
        'endLine' => 333,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Breed',
        'implementingClassName' => 'App\\Models\\Entity\\Breed',
        'currentClassName' => 'App\\Models\\Entity\\Breed',
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