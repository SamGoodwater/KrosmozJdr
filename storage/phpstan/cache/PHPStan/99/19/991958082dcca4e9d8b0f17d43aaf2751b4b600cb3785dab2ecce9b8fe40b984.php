<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Shop.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Shop
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5650c262c6557c0ec0c4cf91e5c371d9efebbff12c391877ad4494fd205f73f2-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Shop',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Shop.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Shop',
    'shortName' => 'Shop',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $location
 * @property int $price
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $npc_id
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Npc|null $npc
 * @property-read Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \\Database\\Factories\\Entity\\ShopFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereLocation($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereNpcId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop wherePrice($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Shop withoutTrashed()
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 70,
    'endLine' => 181,
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
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 94,
            'startFilePos' => 3746,
            'endTokenPos' => 94,
            'endFilePos' => 3750,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 105,
            'startFilePos' => 3785,
            'endTokenPos' => 105,
            'endFilePos' => 3791,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 116,
            'startFilePos' => 3829,
            'endTokenPos' => 116,
            'endFilePos' => 3838,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 127,
            'startFilePos' => 3876,
            'endTokenPos' => 127,
            'endFilePos' => 3885,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/shops\'',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 140,
            'startFilePos' => 3973,
            'endTokenPos' => 140,
            'endFilePos' => 3993,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 153,
            'startFilePos' => 4134,
            'endTokenPos' => 153,
            'endFilePos' => 4152,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'location\', \'price\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\', \'npc_id\']',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 105,
            'startTokenPos' => 164,
            'startFilePos' => 4278,
            'endTokenPos' => 196,
            'endFilePos' => 4479,
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
        'startLine' => 94,
        'endLine' => 105,
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
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'price\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 112,
            'endLine' => 116,
            'startTokenPos' => 207,
            'startFilePos' => 4606,
            'endTokenPos' => 230,
            'endFilePos' => 4713,
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
        'startLine' => 112,
        'endLine' => 116,
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
 * Get the user that created the shop.
 */',
        'startLine' => 121,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
        'aliasName' => NULL,
      ),
      'npc' => 
      array (
        'name' => 'npc',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the NPC associated with the shop.
 */',
        'startLine' => 129,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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
 * Les objets vendus dans cette hotel de vente.
 */',
        'startLine' => 137,
        'endLine' => 140,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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
 * Les panoplies vendues dans cette hotel de vente.
 */',
        'startLine' => 145,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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
 * Les consommables vendus dans cette hotel de vente.
 */',
        'startLine' => 153,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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
 * Les ressources vendues dans cette hotel de vente.
 */',
        'startLine' => 161,
        'endLine' => 164,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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
 * Les scénarios associés à cette hotel de vente.
 */',
        'startLine' => 169,
        'endLine' => 172,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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
 * Les campagnes associées à cette hotel de vente vente.
 */',
        'startLine' => 177,
        'endLine' => 180,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Shop',
        'implementingClassName' => 'App\\Models\\Entity\\Shop',
        'currentClassName' => 'App\\Models\\Entity\\Shop',
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