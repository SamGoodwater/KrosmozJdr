<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Scrapping/ScrappingEntityMapping.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Scrapping\ScrappingEntityMapping
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-c8b2bfff337998fee2f10ecf1fe24a2997a11c0738cf60f0b4511725a1d19bb8-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'filename' => '/var/www/KrosmozJdr/app/Models/Scrapping/ScrappingEntityMapping.php',
      ),
    ),
    'namespace' => 'App\\Models\\Scrapping',
    'name' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
    'shortName' => 'ScrappingEntityMapping',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Règle de mapping : une clé logique (ex. level, name) pour une source+entité DofusDB.
 *
 * Lie un chemin API (from_path) à une ou plusieurs cibles Krosmoz (model.field) avec formatters.
 *
 * @property int $id
 * @property string $source
 * @property string $entity
 * @property string $mapping_key
 * @property string $from_path
 * @property bool $from_lang_aware
 * @property int|null $characteristic_id
 * @property array|null $formatters
 * @property string|null $spell_level_aggregation first|max|min|last (agrégation multi spell-level)
 * @property int $sort_order
 * @example ScrappingEntityMapping::where(\'source\', \'dofusdb\')->where(\'entity\', \'monster\')->orderBy(\'sort_order\')->get();
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic|null $characteristic
 * @property-read Collection<int, Characteristic> $characteristics
 * @property-read int|null $characteristics_count
 * @property-read Collection<int, ScrappingEntityMappingTarget> $targets
 * @property-read int|null $targets_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereEntity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereFormatters($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereFromLangAware($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereFromPath($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereMappingKey($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereSortOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereSource($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereSpellLevelAggregation($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScrappingEntityMapping whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 55,
    'endLine' => 133,
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
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping_entity_mappings\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 68,
            'startFilePos' => 3162,
            'endTokenPos' => 68,
            'endFilePos' => 3188,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'source\', \'entity\', \'mapping_key\', \'from_path\', \'from_lang_aware\', \'characteristic_id\', \'formatters\', \'spell_level_aggregation\', \'sort_order\']',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 70,
            'startTokenPos' => 79,
            'startFilePos' => 3247,
            'endTokenPos' => 108,
            'endFilePos' => 3468,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 70,
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
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'from_lang_aware\' => \'boolean\', \'formatters\' => \'array\', \'sort_order\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 77,
            'startTokenPos' => 119,
            'startFilePos' => 3533,
            'endTokenPos' => 142,
            'endFilePos' => 3647,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 77,
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
      'characteristic' => 
      array (
        'name' => 'characteristic',
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
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'aliasName' => NULL,
      ),
      'characteristics' => 
      array (
        'name' => 'characteristics',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Caractéristiques liées via la table pivot (plusieurs caractéristiques par règle).
 *
 * @return BelongsToMany<Characteristic, $this>
 */',
        'startLine' => 89,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'aliasName' => NULL,
      ),
      'targets' => 
      array (
        'name' => 'targets',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @return HasMany<ScrappingEntityMappingTarget, $this> */',
        'startLine' => 100,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'aliasName' => NULL,
      ),
      'getTargetsForConversion' => 
      array (
        'name' => 'getTargetsForConversion',
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
 * Cibles au format ConversionService / vues : liste de [model, field].
 * Utilise la relation targets (à charger avec with(\'targets\') si besoin).
 *
 * @return list<array{model: string, field: string}>
 */',
        'startLine' => 111,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'aliasName' => NULL,
      ),
      'toSummaryArray' => 
      array (
        'name' => 'toSummaryArray',
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
 * Résumé pour affichage (ex. panneau 3 caractéristique, liste sans formatters).
 * Une seule forme pour éviter de dupliquer la structure dans les contrôleurs.
 *
 * @return array{id: int, source: string, entity: string, mapping_key: string, from_path: string, targets: list<array{model: string, field: string}>}
 */',
        'startLine' => 122,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Scrapping',
        'declaringClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'implementingClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
        'currentClassName' => 'App\\Models\\Scrapping\\ScrappingEntityMapping',
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