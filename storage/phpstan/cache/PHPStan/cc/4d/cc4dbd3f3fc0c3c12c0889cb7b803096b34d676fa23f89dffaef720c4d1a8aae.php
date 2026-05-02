<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Consumable.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Consumable
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-3d72aa207c654ccd7ef016638fa08e86149ceafc0eeda9139268b94e84a11ed7-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Consumable',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Consumable.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Consumable',
    'shortName' => 'Consumable',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $level
 * @property string|null $recipe
 * @property string|null $price
 * @property int $rarity
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string $dofus_version
 * @property string|null $image
 * @property bool $auto_update
 * @property Carbon|null $deleted_at
 * @property int|null $consumable_type_id
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read ConsumableType|null $consumableType
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \\Database\\Factories\\Entity\\ConsumableFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereAutoUpdate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereConsumableTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereDofusVersion($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereEffect($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereOfficialId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable wherePrice($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereRarity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereRecipe($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Consumable withoutTrashed()
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, ObjectEffect> $objectEffects
 * @property-read int|null $object_effects_count
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 90,
    'endLine' => 217,
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
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 95,
            'startTokenPos' => 114,
            'startFilePos' => 5074,
            'endTokenPos' => 114,
            'endFilePos' => 5078,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 125,
            'startFilePos' => 5113,
            'endTokenPos' => 125,
            'endFilePos' => 5119,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 99,
            'endLine' => 99,
            'startTokenPos' => 136,
            'startFilePos' => 5157,
            'endTokenPos' => 136,
            'endFilePos' => 5166,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 99,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 147,
            'startFilePos' => 5204,
            'endTokenPos' => 147,
            'endFilePos' => 5213,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/consumables\'',
          'attributes' => 
          array (
            'startLine' => 104,
            'endLine' => 104,
            'startTokenPos' => 160,
            'startFilePos' => 5301,
            'endTokenPos' => 160,
            'endFilePos' => 5327,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 104,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 173,
            'startFilePos' => 5468,
            'endTokenPos' => 173,
            'endFilePos' => 5486,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'official_id\', \'dofusdb_id\', \'name\', \'description\', \'effect\', \'level\', \'recipe\', \'price\', \'rarity\', \'state\', \'read_level\', \'write_level\', \'dofus_version\', \'image\', \'auto_update\', \'consumable_type_id\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 132,
            'startTokenPos' => 184,
            'startFilePos' => 5612,
            'endTokenPos' => 237,
            'endFilePos' => 5969,
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
        'startLine' => 114,
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
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'rarity\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'auto_update\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 139,
            'endLine' => 144,
            'startTokenPos' => 248,
            'startFilePos' => 6096,
            'endTokenPos' => 278,
            'endFilePos' => 6240,
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
        'startLine' => 139,
        'endLine' => 144,
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
 * Get the user that created the consumable.
 */',
        'startLine' => 149,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
        'aliasName' => NULL,
      ),
      'consumableType' => 
      array (
        'name' => 'consumableType',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the type of the consumable.
 */',
        'startLine' => 157,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
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
 * Les ressources nécessaires à ce consommable.
 */',
        'startLine' => 165,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
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
 * Les créatures associées à ce consommable.
 */',
        'startLine' => 173,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
        'aliasName' => NULL,
      ),
      'scenarios' => 
      array (
        'name' => 'scenarios',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les scénarios associés à ce consommable.
 */',
        'startLine' => 181,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
        'aliasName' => NULL,
      ),
      'campaigns' => 
      array (
        'name' => 'campaigns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les campagnes associées à ce consommable.
 */',
        'startLine' => 189,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
        'aliasName' => NULL,
      ),
      'shops' => 
      array (
        'name' => 'shops',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les hotels de vente associées à ce consommable.
 */',
        'startLine' => 197,
        'endLine' => 200,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
        'aliasName' => NULL,
      ),
      'effectUsages' => 
      array (
        'name' => 'effectUsages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Usages d\'effets unifiés (effect_usage) pour ce consommable.
 */',
        'startLine' => 205,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
        'aliasName' => NULL,
      ),
      'objectEffects' => 
      array (
        'name' => 'objectEffects',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Effets d’objet structurés (action + caractéristique ou monstre + valeur).
 */',
        'startLine' => 213,
        'endLine' => 216,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Consumable',
        'implementingClassName' => 'App\\Models\\Entity\\Consumable',
        'currentClassName' => 'App\\Models\\Entity\\Consumable',
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