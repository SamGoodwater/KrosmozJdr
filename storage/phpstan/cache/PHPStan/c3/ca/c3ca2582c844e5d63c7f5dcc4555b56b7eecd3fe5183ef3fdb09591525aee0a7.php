<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Attribute.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Attribute
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-9ece6ed94f13a2e2f85ab4d86e0b4f4074e24c7e91d459ee7a91ca13224168d2-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Attribute',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Attribute.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Attribute',
    'shortName' => 'Attribute',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
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
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @method static \\Database\\Factories\\Entity\\AttributeFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Attribute withoutTrashed()
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 53,
    'endLine' => 112,
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
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 94,
            'startFilePos' => 2970,
            'endTokenPos' => 94,
            'endFilePos' => 2974,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 105,
            'startFilePos' => 3009,
            'endTokenPos' => 105,
            'endFilePos' => 3015,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 62,
            'endLine' => 62,
            'startTokenPos' => 116,
            'startFilePos' => 3053,
            'endTokenPos' => 116,
            'endFilePos' => 3062,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 62,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 127,
            'startFilePos' => 3100,
            'endTokenPos' => 127,
            'endFilePos' => 3109,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/attributes\'',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 140,
            'startFilePos' => 3197,
            'endTokenPos' => 140,
            'endFilePos' => 3222,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 153,
            'startFilePos' => 3363,
            'endTokenPos' => 153,
            'endFilePos' => 3381,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 85,
            'startTokenPos' => 164,
            'startFilePos' => 3507,
            'endTokenPos' => 187,
            'endFilePos' => 3653,
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
        'startLine' => 77,
        'endLine' => 85,
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
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 92,
            'endLine' => 95,
            'startTokenPos' => 198,
            'startFilePos' => 3780,
            'endTokenPos' => 214,
            'endFilePos' => 3857,
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
        'startLine' => 92,
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
        'docComment' => '/**
 * Get the user that created the attribute.
 */',
        'startLine' => 100,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'currentClassName' => 'App\\Models\\Entity\\Attribute',
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
        'docComment' => '/**
 * Les créatures associées à cet attribut.
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Attribute',
        'implementingClassName' => 'App\\Models\\Entity\\Attribute',
        'currentClassName' => 'App\\Models\\Entity\\Attribute',
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