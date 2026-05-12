<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/CharacteristicObject.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\CharacteristicObject
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-04c33b4a262f21a5f8c0685206859aa573d6a7e7ea5dcde67f31ef9eacb76b88-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\CharacteristicObject',
        'filename' => '/var/www/KrosmozJdr/app/Models/CharacteristicObject.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\CharacteristicObject',
    'shortName' => 'CharacteristicObject',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Définition d’une caractéristique pour une entité du groupe objet (item, consumable, resource, panoply).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics (ex. item.effects[].characteristic)
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property string|null $conversion_function Identifiant d\'une fonction de conversion enregistrée
 * @property array|null $conversion_dofus_sample Niveau → valeur Dofus (ex. {"1":1,"200":200})
 * @property array|null $conversion_krosmoz_sample Niveau → valeur Krosmoz (ex. {"1":1,"20":20})
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 * @property array|null $norms_grid Grille 5×20 : {power_level: [val_lvl1..val_lvl20]}
 * @property array|null $norms_conditions Conditions de lecture
 * @property string|null $norms_description Description libre de la norme
 * @property int|null $norms_help_section_id Section CMS (texte) affichée sous la charte
 * @property array|null $value_available
 * @property-read Collection<int, ItemType> $allowedItemTypes
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $allowed_item_types_count
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereBasePricePerUnit($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereConversionDofusSample($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereConversionFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereConversionFunction($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereConversionKrosmozSample($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereConversionSampleRows($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereDbColumn($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereDefaultValue($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereDofusdbCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereEntity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereForgemagieAllowed($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereForgemagieMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereFormulaDisplay($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereMin($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereNormsConditions($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereNormsDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereNormsGrid($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereNormsHelpSectionId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereRunePricePerUnit($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicObject whereValueAvailable($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 77,
    'endLine' => 162,
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
      'ENTITY_ALL' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'ENTITY_ALL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'*\'',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 76,
            'startFilePos' => 5584,
            'endTokenPos' => 76,
            'endFilePos' => 5586,
          ),
        ),
        'docComment' => '/** S\'applique à toutes les entités du groupe (défaut). */',
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'ENTITY_ITEM' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'ENTITY_ITEM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'item\'',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 84,
            'startTokenPos' => 87,
            'startFilePos' => 5621,
            'endTokenPos' => 87,
            'endFilePos' => 5626,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 84,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'ENTITY_CONSUMABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'ENTITY_CONSUMABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'consumable\'',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 86,
            'startTokenPos' => 98,
            'startFilePos' => 5667,
            'endTokenPos' => 98,
            'endFilePos' => 5678,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 86,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'ENTITY_RESOURCE' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'ENTITY_RESOURCE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'resource\'',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 109,
            'startFilePos' => 5717,
            'endTokenPos' => 109,
            'endFilePos' => 5726,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'ENTITY_PANOPLY' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'ENTITY_PANOPLY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'panoply\'',
          'attributes' => 
          array (
            'startLine' => 90,
            'endLine' => 90,
            'startTokenPos' => 120,
            'startFilePos' => 5764,
            'endTokenPos' => 120,
            'endFilePos' => 5772,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 90,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'ENTITIES' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'ENTITIES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[self::ENTITY_ITEM, self::ENTITY_CONSUMABLE, self::ENTITY_RESOURCE, self::ENTITY_PANOPLY]',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 98,
            'startTokenPos' => 133,
            'startFilePos' => 5833,
            'endTokenPos' => 155,
            'endFilePos' => 5960,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'characteristic_object\'',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 63,
            'startFilePos' => 5462,
            'endTokenPos' => 63,
            'endFilePos' => 5484,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'characteristic_id\', \'dofusdb_characteristic_id\', \'entity\', \'db_column\', \'min\', \'max\', \'formula\', \'formula_display\', \'default_value\', \'conversion_formula\', \'conversion_function\', \'conversion_dofus_sample\', \'conversion_krosmoz_sample\', \'conversion_sample_rows\', \'norms_grid\', \'norms_conditions\', \'norms_description\', \'norms_help_section_id\', \'forgemagie_max\', \'base_price_per_unit\', \'rune_price_per_unit\', \'value_available\']',
          'attributes' => 
          array (
            'startLine' => 101,
            'endLine' => 124,
            'startTokenPos' => 166,
            'startFilePos' => 6019,
            'endTokenPos' => 234,
            'endFilePos' => 6625,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 101,
        'endLine' => 124,
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
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_characteristic_id\' => \'integer\', \'conversion_dofus_sample\' => \'array\', \'conversion_krosmoz_sample\' => \'array\', \'conversion_sample_rows\' => \'array\', \'norms_grid\' => \'array\', \'norms_conditions\' => \'array\', \'forgemagie_max\' => \'integer\', \'base_price_per_unit\' => \'decimal:2\', \'rune_price_per_unit\' => \'decimal:2\', \'value_available\' => \'array\', \'norms_help_section_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 127,
            'endLine' => 139,
            'startTokenPos' => 245,
            'startFilePos' => 6690,
            'endTokenPos' => 324,
            'endFilePos' => 7172,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 127,
        'endLine' => 139,
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
        'startLine' => 141,
        'endLine' => 144,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'currentClassName' => 'App\\Models\\CharacteristicObject',
        'aliasName' => NULL,
      ),
      'normsHelpSection' => 
      array (
        'name' => 'normsHelpSection',
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
        'startLine' => 146,
        'endLine' => 149,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'currentClassName' => 'App\\Models\\CharacteristicObject',
        'aliasName' => NULL,
      ),
      'allowedItemTypes' => 
      array (
        'name' => 'allowedItemTypes',
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
 * Types d\'équipement (item_types) pour lesquels cette caractéristique est autorisée.
 * Vide = tous les types ; sinon la caractéristique ne s\'applique qu\'aux types listés.
 *
 * @return BelongsToMany<ItemType, self>
 */',
        'startLine' => 157,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicObject',
        'implementingClassName' => 'App\\Models\\CharacteristicObject',
        'currentClassName' => 'App\\Models\\CharacteristicObject',
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