<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Item.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Item
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-eff97f9e819277e7681528aba7be9da0d44efb37af7c11bec3836f13237a9d37-8.4.17-6.70.0.0',
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
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, ObjectEffect> $objectEffects
 * @property-read int|null $object_effects_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item wherePriceCalculated($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Item wherePriceCustom($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 96,
    'endLine' => 255,
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
            'startLine' => 101,
            'endLine' => 101,
            'startTokenPos' => 114,
            'startFilePos' => 5321,
            'endTokenPos' => 114,
            'endFilePos' => 5325,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 101,
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
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 125,
            'startFilePos' => 5360,
            'endTokenPos' => 125,
            'endFilePos' => 5366,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
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
            'startLine' => 105,
            'endLine' => 105,
            'startTokenPos' => 136,
            'startFilePos' => 5404,
            'endTokenPos' => 136,
            'endFilePos' => 5413,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 105,
        'endLine' => 105,
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
            'startLine' => 107,
            'endLine' => 107,
            'startTokenPos' => 147,
            'startFilePos' => 5451,
            'endTokenPos' => 147,
            'endFilePos' => 5460,
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
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 160,
            'startFilePos' => 5548,
            'endTokenPos' => 160,
            'endFilePos' => 5568,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
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
            'startLine' => 113,
            'endLine' => 113,
            'startTokenPos' => 173,
            'startFilePos' => 5709,
            'endTokenPos' => 173,
            'endFilePos' => 5727,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 113,
        'endLine' => 113,
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
            'startLine' => 120,
            'endLine' => 140,
            'startTokenPos' => 184,
            'startFilePos' => 5853,
            'endTokenPos' => 243,
            'endFilePos' => 6256,
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
        'startLine' => 120,
        'endLine' => 140,
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
            'startLine' => 147,
            'endLine' => 154,
            'startTokenPos' => 254,
            'startFilePos' => 6383,
            'endTokenPos' => 298,
            'endFilePos' => 6605,
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
        'startLine' => 147,
        'endLine' => 154,
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
        'startLine' => 156,
        'endLine' => 161,
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
        'startLine' => 166,
        'endLine' => 172,
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
        'startLine' => 177,
        'endLine' => 182,
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
        'startLine' => 203,
        'endLine' => 206,
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
        'startLine' => 211,
        'endLine' => 214,
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
        'startLine' => 219,
        'endLine' => 222,
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
        'startLine' => 227,
        'endLine' => 230,
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
        'startLine' => 235,
        'endLine' => 238,
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
        'startLine' => 243,
        'endLine' => 246,
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
        'startLine' => 251,
        'endLine' => 254,
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