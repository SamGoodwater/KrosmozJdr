<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Npc.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Npc
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-e67b1b23ef3214f825d0f242909cf147d1b643187d2b4b261743e8a129ee8e1e-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Npc',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Npc.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Npc',
    'shortName' => 'Npc',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $story
 * @property string|null $historical
 * @property string|null $age
 * @property string|null $size
 * @property int|null $breed_id
 * @property int|null $specialization_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Breed|null $breed
 * @property-read Creature|null $creature
 * @property-read Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Shop|null $shop
 * @property-read Specialization|null $specialization
 *
 * @method static \\Database\\Factories\\Entity\\NpcFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereAge($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereBreedId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereCreatureId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereHistorical($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereSize($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereSpecializationId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereStory($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereUpdatedAt($value)
 *
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Npc whereWriteLevel($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 57,
    'endLine' => 145,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'creature_id\', \'story\', \'historical\', \'age\', \'size\', \'breed_id\', \'specialization_id\', \'state\', \'read_level\', \'write_level\']',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 78,
            'startTokenPos' => 54,
            'startFilePos' => 2857,
            'endTokenPos' => 86,
            'endFilePos' => 3067,
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
        'startLine' => 67,
        'endLine' => 78,
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
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 88,
            'startTokenPos' => 97,
            'startFilePos' => 3194,
            'endTokenPos' => 113,
            'endFilePos' => 3271,
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
        'startLine' => 85,
        'endLine' => 88,
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
      'creature' => 
      array (
        'name' => 'creature',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the creature associated with the NPC.
 */',
        'startLine' => 93,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
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
 * Les panoplies associées à ce PNJ.
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
        'aliasName' => NULL,
      ),
      'breed' => 
      array (
        'name' => 'breed',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the breed (affichée « Classe ») associated with the NPC.
 */',
        'startLine' => 109,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
        'aliasName' => NULL,
      ),
      'specialization' => 
      array (
        'name' => 'specialization',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the specialization associated with the NPC.
 */',
        'startLine' => 117,
        'endLine' => 120,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
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
 * Les scénarios associés à ce PNJ.
 */',
        'startLine' => 125,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
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
 * Les campagnes associées à ce PNJ.
 */',
        'startLine' => 133,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
        'aliasName' => NULL,
      ),
      'shop' => 
      array (
        'name' => 'shop',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * La hotel de vente associée à ce PNJ.
 */',
        'startLine' => 141,
        'endLine' => 144,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Npc',
        'implementingClassName' => 'App\\Models\\Entity\\Npc',
        'currentClassName' => 'App\\Models\\Entity\\Npc',
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