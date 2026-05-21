<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Item.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Item
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f98be3b9b59711e714fa9e6fcc604161879d4179e55ca75ab973eda54cb00b3c-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Item',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Item.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Item',
    'shortName' => 'Item',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string|null $level
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $bonus
 * @property string|null $recipe
 * @property int|null $price_calculated
 * @property int|null $price_custom
 * @property string|null $price Total kamas affiché (entier, synchronisé depuis calculé + personnalisé)
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property Carbon|null $deleted_at
 * @property int|null $item_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read ItemType|null $itemType
 * @property-read Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 *
 * @method static \\Database\\Factories\\Entity\\ItemFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereAutoUpdate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereDofusVersion($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereEffect($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereItemTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereOfficialId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item wherePrice($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereRarity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereRecipe($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item withoutTrashed()
 *
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, ObjectEffect> $objectEffects
 * @property-read int|null $object_effects_count
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item wherePriceCalculated($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item wherePriceCustom($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 100,
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 114,
            'startFilePos' => 5333,
            'endTokenPos' => 114,
            'endFilePos' => 5337,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 125,
            'startFilePos' => 5372,
            'endTokenPos' => 125,
            'endFilePos' => 5378,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 109,
            'endLine' => 109,
            'startTokenPos' => 136,
            'startFilePos' => 5416,
            'endTokenPos' => 136,
            'endFilePos' => 5425,
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
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 111,
            'endLine' => 111,
            'startTokenPos' => 147,
            'startFilePos' => 5463,
            'endTokenPos' => 147,
            'endFilePos' => 5472,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 111,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/items\'',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 114,
            'startTokenPos' => 160,
            'startFilePos' => 5560,
            'endTokenPos' => 160,
            'endFilePos' => 5580,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 114,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 117,
            'endLine' => 117,
            'startTokenPos' => 173,
            'startFilePos' => 5721,
            'endTokenPos' => 173,
            'endFilePos' => 5739,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 117,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'official_id\', \'dofusdb_id\', \'name\', \'level\', \'description\', \'effect\', \'bonus\', \'recipe\', \'price_calculated\', \'price_custom\', \'rarity\', \'dofus_version\', \'state\', \'read_level\', \'write_level\', \'image\', \'auto_update\', \'item_type_id\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 124,
            'endLine' => 144,
            'startTokenPos' => 184,
            'startFilePos' => 5865,
            'endTokenPos' => 243,
            'endFilePos' => 6268,
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
        'startLine' => 124,
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
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'price_calculated\' => \'integer\', \'price_custom\' => \'integer\', \'rarity\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'auto_update\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 151,
            'endLine' => 158,
            'startTokenPos' => 254,
            'startFilePos' => 6395,
            'endTokenPos' => 298,
            'endFilePos' => 6617,
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
        'startLine' => 151,
        'endLine' => 158,
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
      'booted' => 
      array (
        'name' => 'booted',
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
        'startLine' => 160,
        'endLine' => 165,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 18,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
        'aliasName' => NULL,
      ),
      'totalPriceKamas' => 
      array (
        'name' => 'totalPriceKamas',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Total kamas (entier, plancher à 0) : part calculée + part personnalisée (peut être négative).
 */',
        'startLine' => 170,
        'endLine' => 176,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
        'aliasName' => NULL,
      ),
      'displayPriceKamas' => 
      array (
        'name' => 'displayPriceKamas',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'int',
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prix à exposer dans les vues lecture (null si total ≤ 0).
 */',
        'startLine' => 181,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
 * Get the user that created the item.
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
        'aliasName' => NULL,
      ),
      'itemType' => 
      array (
        'name' => 'itemType',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the type of the item.
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
 * Les ressources nécessaires à cet objet.
 */',
        'startLine' => 207,
        'endLine' => 210,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
        'aliasName' => NULL,
      ),
      'panoplies' => 
      array (
        'name' => 'panoplies',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les panoplies associées à cet objet.
 */',
        'startLine' => 215,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
 * Les scénarios associés à cet objet.
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
 * Les campagnes associées à cet objet.
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
 * Les hotels de vente associées à cet objet.
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
 * Usages d\'effets unifiés (effect_usage) pour cet item.
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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
        'declaringClassName' => 'App\\Models\\Entity\\Item',
        'implementingClassName' => 'App\\Models\\Entity\\Item',
        'currentClassName' => 'App\\Models\\Entity\\Item',
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