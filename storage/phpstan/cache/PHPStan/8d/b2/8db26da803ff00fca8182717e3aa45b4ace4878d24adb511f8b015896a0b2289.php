<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Panoply.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Panoply
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-73257f387aa744c6fa9a377f1584d06244dcfdc18657c97095c9e76e0a7934ac-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Panoply',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Panoply.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Panoply',
    'shortName' => 'Panoply',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $bonus
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \\Database\\Factories\\Entity\\PanoplyFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply withoutTrashed()
 * @property string|null $dofusdb_id
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Panoply whereDofusdbId($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 57,
    'endLine' => 143,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 62,
            'endLine' => 62,
            'startTokenPos' => 67,
            'startFilePos' => 3074,
            'endTokenPos' => 67,
            'endFilePos' => 3078,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 62,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 78,
            'startFilePos' => 3113,
            'endTokenPos' => 78,
            'endFilePos' => 3119,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 66,
            'endLine' => 66,
            'startTokenPos' => 89,
            'startFilePos' => 3157,
            'endTokenPos' => 89,
            'endFilePos' => 3166,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 68,
            'endLine' => 68,
            'startTokenPos' => 100,
            'startFilePos' => 3204,
            'endTokenPos' => 100,
            'endFilePos' => 3213,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 68,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'bonus\', \'state\', \'read_level\', \'write_level\', \'created_by\', \'dofusdb_id\']',
          'attributes' => 
          array (
            'startLine' => 75,
            'endLine' => 84,
            'startTokenPos' => 111,
            'startFilePos' => 3339,
            'endTokenPos' => 137,
            'endFilePos' => 3507,
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
        'startLine' => 75,
        'endLine' => 84,
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
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 94,
            'startTokenPos' => 148,
            'startFilePos' => 3634,
            'endTokenPos' => 164,
            'endFilePos' => 3711,
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
        'startLine' => 91,
        'endLine' => 94,
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
 * Get the user that created the panoply.
 */',
        'startLine' => 99,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'currentClassName' => 'App\\Models\\Entity\\Panoply',
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
 * Les objets de cette panoplie.
 */',
        'startLine' => 107,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'currentClassName' => 'App\\Models\\Entity\\Panoply',
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
 * Les PNJ associés à cette panoplie.
 */',
        'startLine' => 115,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'currentClassName' => 'App\\Models\\Entity\\Panoply',
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
 * Les hotels de vente associées à cette panoplie.
 */',
        'startLine' => 123,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'currentClassName' => 'App\\Models\\Entity\\Panoply',
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
 * Les scénarios associés à cette panoplie.
 */',
        'startLine' => 131,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'currentClassName' => 'App\\Models\\Entity\\Panoply',
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
 * Les campagnes associées à cette panoplie.
 */',
        'startLine' => 139,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Panoply',
        'implementingClassName' => 'App\\Models\\Entity\\Panoply',
        'currentClassName' => 'App\\Models\\Entity\\Panoply',
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