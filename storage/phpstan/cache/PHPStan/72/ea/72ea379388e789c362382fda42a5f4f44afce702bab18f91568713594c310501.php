<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Scrapping/ScrappingEntityMappingTarget.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Scrapping\ScrappingEntityMappingTarget
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-d382db627c8a46678e2de006edbd51227301f7d88e259ff536639b10ba653b2c-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'filename' => '/var/www/KrosmozJdr/app/Models/Scrapping/ScrappingEntityMappingTarget.php',
      ),
    ),
    'namespace' => 'App\\Models\\Scrapping',
    'name' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
    'shortName' => 'ScrappingEntityMappingTarget',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Cible d\'une règle de mapping : un couple (model, field) Krosmoz.
 *
 * Une règle peut avoir plusieurs cibles (ex. item → resources, consumables, items).
 *
 * @property int $id
 * @property int $scrapping_entity_mapping_id
 * @property string $target_model
 * @property string $target_field
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ScrappingEntityMapping $scrappingEntityMapping
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereScrappingEntityMappingId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereSortOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereTargetField($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereTargetModel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMappingTarget whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 36,
    'endLine' => 86,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping_entity_mapping_targets\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 48,
            'startFilePos' => 1898,
            'endTokenPos' => 48,
            'endFilePos' => 1931,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 58,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'scrapping_entity_mapping_id\', \'target_model\', \'target_field\', \'sort_order\']',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 46,
            'startTokenPos' => 59,
            'startFilePos' => 1990,
            'endTokenPos' => 73,
            'endFilePos' => 2105,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 46,
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
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'sort_order\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 51,
            'startTokenPos' => 84,
            'startFilePos' => 2170,
            'endTokenPos' => 93,
            'endFilePos' => 2211,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 51,
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
      'scrappingEntityMapping' => 
      array (
        'name' => 'scrappingEntityMapping',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'aliasName' => NULL,
      ),
      'toConversionPair' => 
      array (
        'name' => 'toConversionPair',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne le couple (model, field) au format attendu par ConversionService et par les vues (liste, panneau caractéristique).
 * Une seule représentation pour éviter la duplication dans ScrappingMappingService et CharacteristicController.
 *
 * @return array{model: string, field: string}
 */',
        'startLine' => 64,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'aliasName' => NULL,
      ),
      'toResponseArray' => 
      array (
        'name' => 'toResponseArray',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne la cible au format attendu par le formulaire d’édition (admin mappings).
 *
 * @return array{id: int, target_model: string, target_field: string, sort_order: int}
 */',
        'startLine' => 77,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMappingTarget',
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