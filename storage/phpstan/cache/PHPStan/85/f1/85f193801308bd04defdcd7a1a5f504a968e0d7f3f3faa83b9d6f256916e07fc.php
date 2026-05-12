<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/CharacteristicCreature.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\CharacteristicCreature
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-363983c7ec37df5341f86b4350c912dfbf0e3df57d90e8b990a36f49f80a9256-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\CharacteristicCreature',
        'filename' => '/var/www/KrosmozJdr/app/Models/CharacteristicCreature.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\CharacteristicCreature',
    'shortName' => 'CharacteristicCreature',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Définition d’une caractéristique pour une entité du groupe créature (monster, class, npc).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics
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
 * @property array|null $norms_grid Grille 5×20 : {power_level: [val_lvl1..val_lvl20]}
 * @property array|null $norms_conditions Conditions de lecture
 * @property string|null $norms_description Description libre de la norme
 * @property int|null $norms_help_section_id Section CMS (texte) affichée sous la charte
 * @property array|null $labels
 * @property array|null $validation
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereConversionDofusSample($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereConversionFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereConversionFunction($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereConversionKrosmozSample($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereConversionSampleRows($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereDbColumn($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereDefaultValue($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereDofusdbCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereEntity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereFormulaDisplay($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereLabels($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereMin($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereNormsConditions($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereNormsDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereNormsGrid($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereNormsHelpSectionId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicCreature whereValidation($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 67,
    'endLine' => 129,
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
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'ENTITY_ALL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'*\'',
          'attributes' => 
          array (
            'startLine' => 72,
            'endLine' => 72,
            'startTokenPos' => 61,
            'startFilePos' => 4891,
            'endTokenPos' => 61,
            'endFilePos' => 4893,
          ),
        ),
        'docComment' => '/** S\'applique à toutes les entités du groupe (défaut). */',
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'ENTITY_MONSTER' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'ENTITY_MONSTER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'monster\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 72,
            'startFilePos' => 4931,
            'endTokenPos' => 72,
            'endFilePos' => 4939,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'ENTITY_CLASS' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'ENTITY_CLASS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'class\'',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 83,
            'startFilePos' => 4975,
            'endTokenPos' => 83,
            'endFilePos' => 4981,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'ENTITY_NPC' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'ENTITY_NPC',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'npc\'',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 94,
            'startFilePos' => 5015,
            'endTokenPos' => 94,
            'endFilePos' => 5019,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'ENTITIES' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'ENTITIES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[self::ENTITY_MONSTER, self::ENTITY_CLASS, self::ENTITY_NPC]',
          'attributes' => 
          array (
            'startLine' => 81,
            'endLine' => 81,
            'startTokenPos' => 107,
            'startFilePos' => 5080,
            'endTokenPos' => 121,
            'endFilePos' => 5139,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 89,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'characteristic_creature\'',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 48,
            'startFilePos' => 4767,
            'endTokenPos' => 48,
            'endFilePos' => 4791,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'characteristic_id\', \'dofusdb_characteristic_id\', \'entity\', \'db_column\', \'min\', \'max\', \'formula\', \'formula_display\', \'default_value\', \'conversion_formula\', \'conversion_function\', \'conversion_dofus_sample\', \'conversion_krosmoz_sample\', \'conversion_sample_rows\', \'norms_grid\', \'norms_conditions\', \'norms_description\', \'norms_help_section_id\', \'labels\', \'validation\']',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 105,
            'startTokenPos' => 132,
            'startFilePos' => 5198,
            'endTokenPos' => 194,
            'endFilePos' => 5729,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 84,
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
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_characteristic_id\' => \'integer\', \'conversion_dofus_sample\' => \'array\', \'conversion_krosmoz_sample\' => \'array\', \'conversion_sample_rows\' => \'array\', \'norms_grid\' => \'array\', \'norms_conditions\' => \'array\', \'labels\' => \'array\', \'validation\' => \'array\', \'norms_help_section_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 108,
            'endLine' => 118,
            'startTokenPos' => 205,
            'startFilePos' => 5794,
            'endTokenPos' => 270,
            'endFilePos' => 6169,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 108,
        'endLine' => 118,
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
        'startLine' => 120,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'currentClassName' => 'App\\Models\\CharacteristicCreature',
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
        'startLine' => 125,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicCreature',
        'implementingClassName' => 'App\\Models\\CharacteristicCreature',
        'currentClassName' => 'App\\Models\\CharacteristicCreature',
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