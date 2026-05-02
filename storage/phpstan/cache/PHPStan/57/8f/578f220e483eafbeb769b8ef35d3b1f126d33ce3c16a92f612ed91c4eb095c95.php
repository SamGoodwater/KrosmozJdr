<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Campaign.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Campaign
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-500b23698c98d83636a784313f25d2a65c88b9f0baef658089517cff6674c250-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Campaign',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Campaign.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Campaign',
    'shortName' => 'Campaign',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property int $is_public
 * @property int $progress_state
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $created_by
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read Collection<int, Page> $pages
 * @property-read int|null $pages_count
 * @property-read Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \\Database\\Factories\\Entity\\CampaignFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereIsPublic($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereProgressState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereKeyword($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereSlug($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Campaign withoutTrashed()
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 83,
    'endLine' => 256,
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
      2 => 'Spatie\\MediaLibrary\\InteractsWithMedia',
      3 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
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
            'startTokenPos' => 107,
            'startFilePos' => 4388,
            'endTokenPos' => 107,
            'endFilePos' => 4392,
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
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
            'startTokenPos' => 118,
            'startFilePos' => 4427,
            'endTokenPos' => 118,
            'endFilePos' => 4433,
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
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
            'startTokenPos' => 129,
            'startFilePos' => 4471,
            'endTokenPos' => 129,
            'endFilePos' => 4480,
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
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
            'startTokenPos' => 140,
            'startFilePos' => 4518,
            'endTokenPos' => 140,
            'endFilePos' => 4527,
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/campaigns\'',
          'attributes' => 
          array (
            'startLine' => 97,
            'endLine' => 97,
            'startTokenPos' => 153,
            'startFilePos' => 4615,
            'endTokenPos' => 153,
            'endFilePos' => 4639,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 97,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
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
            'startTokenPos' => 166,
            'startFilePos' => 4780,
            'endTokenPos' => 166,
            'endFilePos' => 4798,
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
      'PROGRESS_STATES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'name' => 'PROGRESS_STATES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[0 => \'En cours\', 1 => \'Terminée\', 2 => \'En pause\', 3 => \'Annulée\']',
          'attributes' => 
          array (
            'startLine' => 102,
            'endLine' => 107,
            'startTokenPos' => 177,
            'startFilePos' => 4837,
            'endTokenPos' => 207,
            'endFilePos' => 4944,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'slug\', \'keyword\', \'is_public\', \'progress_state\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 114,
            'endLine' => 126,
            'startTokenPos' => 218,
            'startFilePos' => 5070,
            'endTokenPos' => 253,
            'endFilePos' => 5298,
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
        'endLine' => 126,
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'is_public\' => \'boolean\', \'progress_state\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 133,
            'endLine' => 138,
            'startTokenPos' => 264,
            'startFilePos' => 5425,
            'endTokenPos' => 294,
            'endFilePos' => 5575,
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
        'startLine' => 133,
        'endLine' => 138,
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
 * Get the user that created the campaign.
 */',
        'startLine' => 143,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
        'aliasName' => NULL,
      ),
      'users' => 
      array (
        'name' => 'users',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les utilisateurs associés à cette campagne.
 */',
        'startLine' => 151,
        'endLine' => 154,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les scénarios de cette campagne.
 */',
        'startLine' => 159,
        'endLine' => 162,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
        'aliasName' => NULL,
      ),
      'pages' => 
      array (
        'name' => 'pages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les pages de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les objets de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les consommables de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les ressources de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les hotels de vente vente de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les PNJ de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
        'aliasName' => NULL,
      ),
      'monsters' => 
      array (
        'name' => 'monsters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les monstres de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les sorts de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
 * Les panoplies de cette campagne.
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
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
        'startLine' => 236,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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
                'startLine' => 242,
                'endLine' => 242,
                'startTokenPos' => 693,
                'startFilePos' => 7854,
                'endTokenPos' => 693,
                'endFilePos' => 7857,
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
            'startLine' => 242,
            'endLine' => 242,
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
        'startLine' => 242,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Campaign',
        'implementingClassName' => 'App\\Models\\Entity\\Campaign',
        'currentClassName' => 'App\\Models\\Entity\\Campaign',
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