<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Capability.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Capability
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-8d863bae534627c2436ee829818028eca501c0b5d8234eebe6720ca85bc9581e-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Capability',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Capability.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Capability',
    'shortName' => 'Capability',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string $level
 * @property string $pa
 * @property string $po
 * @property bool $po_editable
 * @property string $time_before_use_again
 * @property string $casting_time
 * @property string $duration
 * @property string $element
 * @property bool $is_magic
 * @property bool $ritual_available
 * @property string|null $powerful
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
 * @property-read Collection<int, Specialization> $specializations
 * @property-read int|null $specializations_count
 *
 * @method static \\Database\\Factories\\Entity\\CapabilityFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereCastingTime($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereDuration($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereEffect($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereElement($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereIsMagic($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability wherePa($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability wherePo($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability wherePoEditable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability wherePowerful($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereRitualAvailable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereTimeBeforeUseAgain($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Capability withoutTrashed()
 *
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 83,
    'endLine' => 176,
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
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 99,
            'startFilePos' => 4676,
            'endTokenPos' => 99,
            'endFilePos' => 4680,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 110,
            'startFilePos' => 4715,
            'endTokenPos' => 110,
            'endFilePos' => 4721,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 92,
            'endLine' => 92,
            'startTokenPos' => 121,
            'startFilePos' => 4759,
            'endTokenPos' => 121,
            'endFilePos' => 4768,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 92,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 132,
            'startFilePos' => 4806,
            'endTokenPos' => 132,
            'endFilePos' => 4815,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/capabilities\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 145,
            'startFilePos' => 4903,
            'endTokenPos' => 145,
            'endFilePos' => 4930,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[slug]\'',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 158,
            'startFilePos' => 5071,
            'endTokenPos' => 158,
            'endFilePos' => 5089,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'effect\', \'level\', \'pa\', \'po\', \'po_editable\', \'time_before_use_again\', \'casting_time\', \'duration\', \'element\', \'is_magic\', \'ritual_available\', \'powerful\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 108,
            'endLine' => 128,
            'startTokenPos' => 171,
            'startFilePos' => 5283,
            'endTokenPos' => 230,
            'endFilePos' => 5679,
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
        'startLine' => 108,
        'endLine' => 128,
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
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'element\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'po_editable\' => \'boolean\', \'is_magic\' => \'boolean\', \'ritual_available\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 135,
            'endLine' => 142,
            'startTokenPos' => 241,
            'startFilePos' => 5806,
            'endTokenPos' => 285,
            'endFilePos' => 6025,
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
        'startLine' => 135,
        'endLine' => 142,
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
 * Get the user that created the capability.
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'currentClassName' => 'App\\Models\\Entity\\Capability',
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
        'docComment' => '/**
 * Les spécialisations associées à cette capacité.
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'currentClassName' => 'App\\Models\\Entity\\Capability',
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
 * Les créatures associées à cette capacité.
 */',
        'startLine' => 163,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'currentClassName' => 'App\\Models\\Entity\\Capability',
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
        'docComment' => '/**
 * Classes (breeds) qui référencent cette capacité.
 */',
        'startLine' => 171,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Capability',
        'implementingClassName' => 'App\\Models\\Entity\\Capability',
        'currentClassName' => 'App\\Models\\Entity\\Capability',
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