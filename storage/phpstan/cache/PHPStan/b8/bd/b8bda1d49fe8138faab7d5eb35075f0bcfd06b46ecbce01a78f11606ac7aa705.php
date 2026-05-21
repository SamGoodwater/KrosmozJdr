<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Resource.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Resource
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-54ea8ab9137d0a4d011d13b84239f9ae6784f9627f1a5b854477a76d3aa0e94d-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Resource',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Resource.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Resource',
    'shortName' => 'Resource',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string|null $dofusdb_id
 * @property int|null $official_id
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string $level
 * @property string|null $price
 * @property string|null $weight
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property Carbon|null $deleted_at
 * @property int|null $resource_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read ResourceType|null $resourceType
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 *
 * @method static \\Database\\Factories\\Entity\\ResourceFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereAutoUpdate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereEffect($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereDofusVersion($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereOfficialId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource wherePrice($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereRarity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereResourceTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource whereWeight($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Resource withoutTrashed()
 *
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, ObjectEffect> $objectEffects
 * @property-read int|null $object_effects_count
 * @property-read Collection<int, resource> $recipeIngredients
 * @property-read int|null $recipe_ingredients_count
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 98,
    'endLine' => 259,
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
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 119,
            'startFilePos' => 5270,
            'endTokenPos' => 119,
            'endFilePos' => 5274,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 130,
            'startFilePos' => 5309,
            'endTokenPos' => 130,
            'endFilePos' => 5315,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 141,
            'startFilePos' => 5353,
            'endTokenPos' => 141,
            'endFilePos' => 5362,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 109,
            'startTokenPos' => 152,
            'startFilePos' => 5400,
            'endTokenPos' => 152,
            'endFilePos' => 5409,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 109,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/resources\'',
          'attributes' => 
          array (
            'startLine' => 112,
            'endLine' => 112,
            'startTokenPos' => 165,
            'startFilePos' => 5497,
            'endTokenPos' => 165,
            'endFilePos' => 5521,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 112,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 115,
            'endLine' => 115,
            'startTokenPos' => 178,
            'startFilePos' => 5662,
            'endTokenPos' => 178,
            'endFilePos' => 5680,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 115,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'RARITY' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'RARITY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[0 => \'Commun\', 1 => \'Peu commun\', 2 => \'Rare\', 3 => \'Très rare\', 4 => \'Légendaire\', 5 => \'Unique\']',
          'attributes' => 
          array (
            'startLine' => 117,
            'endLine' => 124,
            'startTokenPos' => 187,
            'startFilePos' => 5703,
            'endTokenPos' => 231,
            'endFilePos' => 5858,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 117,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_id\', \'official_id\', \'name\', \'description\', \'effect\', \'level\', \'price\', \'weight\', \'rarity\', \'dofus_version\', \'state\', \'read_level\', \'write_level\', \'image\', \'auto_update\', \'resource_type_id\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 131,
            'endLine' => 149,
            'startTokenPos' => 242,
            'startFilePos' => 5984,
            'endTokenPos' => 295,
            'endFilePos' => 6339,
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
        'startLine' => 131,
        'endLine' => 149,
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
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'official_id\' => \'integer\', \'rarity\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'auto_update\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 156,
            'endLine' => 162,
            'startTokenPos' => 306,
            'startFilePos' => 6466,
            'endTokenPos' => 343,
            'endFilePos' => 6646,
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
        'startLine' => 156,
        'endLine' => 162,
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
 * Get the user that created the resource.
 */',
        'startLine' => 167,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
        'aliasName' => NULL,
      ),
      'resourceType' => 
      array (
        'name' => 'resourceType',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the type of the resource.
 */',
        'startLine' => 175,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
 * Les consommables utilisant cette ressource.
 */',
        'startLine' => 183,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
 * Les créatures utilisant cette ressource.
 */',
        'startLine' => 191,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
        'docComment' => '/**
 * Les objets utilisant cette ressource.
 */',
        'startLine' => 199,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
        'aliasName' => NULL,
      ),
      'recipeIngredients' => 
      array (
        'name' => 'recipeIngredients',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Recette de fabrication : ressources (ingrédients) nécessaires avec quantités.
 * Une ressource craftable est fabriquée à partir d\'autres ressources.
 *
 * @return BelongsToMany<resource, resource>
 */',
        'startLine' => 210,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
 * Les scénarios associés à cette ressource.
 */',
        'startLine' => 223,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
 * Les campagnes associées à cette ressource.
 */',
        'startLine' => 231,
        'endLine' => 234,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
 * Les hotels de vente associées à cette ressource.
 */',
        'startLine' => 239,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
 * Usages d\'effets unifiés (effect_usage) pour cette ressource.
 */',
        'startLine' => 247,
        'endLine' => 250,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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
        'startLine' => 255,
        'endLine' => 258,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Resource',
        'implementingClassName' => 'App\\Models\\Entity\\Resource',
        'currentClassName' => 'App\\Models\\Entity\\Resource',
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