<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Specialization.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Specialization
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ad2626e7d28a81b192d561de1cc7a2f3a2ce4590c6fed8a915747c581fa25c39-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Specialization',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Specialization.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Specialization',
    'shortName' => 'Specialization',
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
 * @property-read Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @method static \\Database\\Factories\\Entity\\SpecializationFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization withoutTrashed()
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property string|null $short_description
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read Collection<int, CreatureTrait> $creatureTraits
 * @property-read int|null $creature_traits_count
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Collection<int, \\App\\Models\\Entity\\Resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Section> $sections
 * @property-read int|null $sections_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Specialization whereShortDescription($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 71,
    'endLine' => 183,
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
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 104,
            'startFilePos' => 3980,
            'endTokenPos' => 104,
            'endFilePos' => 3984,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 115,
            'startFilePos' => 4019,
            'endTokenPos' => 115,
            'endFilePos' => 4025,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 126,
            'startFilePos' => 4063,
            'endTokenPos' => 126,
            'endFilePos' => 4072,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 137,
            'startFilePos' => 4110,
            'endTokenPos' => 137,
            'endFilePos' => 4119,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/specializations\'',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 85,
            'startTokenPos' => 150,
            'startFilePos' => 4207,
            'endTokenPos' => 150,
            'endFilePos' => 4237,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 62,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 163,
            'startFilePos' => 4378,
            'endTokenPos' => 163,
            'endFilePos' => 4396,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'short_description\', \'description\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 104,
            'startTokenPos' => 174,
            'startFilePos' => 4522,
            'endTokenPos' => 200,
            'endFilePos' => 4697,
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
        'startLine' => 95,
        'endLine' => 104,
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
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 111,
            'endLine' => 114,
            'startTokenPos' => 211,
            'startFilePos' => 4824,
            'endTokenPos' => 227,
            'endFilePos' => 4901,
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
        'startLine' => 111,
        'endLine' => 114,
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
 * Get the user that created the specialization.
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
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
 * Les capacités associées à cette spécialisation.
 */',
        'startLine' => 127,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
        'docComment' => NULL,
        'startLine' => 134,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
        'startLine' => 141,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
        'docComment' => NULL,
        'startLine' => 148,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
        'docComment' => NULL,
        'startLine' => 155,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
        'docComment' => NULL,
        'startLine' => 162,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
        'aliasName' => NULL,
      ),
      'sections' => 
      array (
        'name' => 'sections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 169,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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
 * Les PNJ de cette spécialisation.
 */',
        'startLine' => 179,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Specialization',
        'implementingClassName' => 'App\\Models\\Entity\\Specialization',
        'currentClassName' => 'App\\Models\\Entity\\Specialization',
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