<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/CreatureTrait.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\CreatureTrait
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-bc51f26a66cb93ac181632ed37fb11d4d05a629be364660ef2a478bb8e1a341d-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\CreatureTrait',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/CreatureTrait.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\CreatureTrait',
    'shortName' => 'CreatureTrait',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Référentiel des traits permanents de créature.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, Specialization> $specializations
 * @property-read int|null $specializations_count
 *
 * @method static \\Database\\Factories\\Entity\\CreatureTraitFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait withTrashed(bool $withTrashed = true)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CreatureTrait withoutTrashed()
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 61,
    'endLine' => 101,
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
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
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
            'startTokenPos' => 94,
            'startFilePos' => 3351,
            'endTokenPos' => 94,
            'endFilePos' => 3355,
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
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
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
            'startTokenPos' => 105,
            'startFilePos' => 3390,
            'endTokenPos' => 105,
            'endFilePos' => 3396,
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
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
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
            'startTokenPos' => 116,
            'startFilePos' => 3434,
            'endTokenPos' => 116,
            'endFilePos' => 3443,
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
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
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
            'startTokenPos' => 127,
            'startFilePos' => 3481,
            'endTokenPos' => 127,
            'endFilePos' => 3490,
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
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/creature-traits\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 138,
            'startFilePos' => 3524,
            'endTokenPos' => 138,
            'endFilePos' => 3554,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 62,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 149,
            'startFilePos' => 3603,
            'endTokenPos' => 149,
            'endFilePos' => 3621,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 158,
            'startFilePos' => 3651,
            'endTokenPos' => 178,
            'endFilePos' => 3734,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 111,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 187,
            'startFilePos' => 3761,
            'endTokenPos' => 200,
            'endFilePos' => 3815,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 79,
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'currentClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'aliasName' => NULL,
      ),
      'creatures' => 
      array (
        'name' => 'creatures',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 87,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'currentClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'aliasName' => NULL,
      ),
      'breeds' => 
      array (
        'name' => 'breeds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 92,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'currentClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'aliasName' => NULL,
      ),
      'specializations' => 
      array (
        'name' => 'specializations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 97,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'implementingClassName' => 'App\\Models\\Entity\\CreatureTrait',
        'currentClassName' => 'App\\Models\\Entity\\CreatureTrait',
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