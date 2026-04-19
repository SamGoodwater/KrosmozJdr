<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/CharacteristicSpell.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\CharacteristicSpell
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-99ed8d935ecb1a21fdf481bff26897ddea5798d88c63e5c5a84db5078c008d27-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\CharacteristicSpell',
        'filename' => '/var/www/KrosmozJdr/app/Models/CharacteristicSpell.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\CharacteristicSpell',
    'shortName' => 'CharacteristicSpell',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Définition d’une caractéristique pour l’entité spell (groupe sort).
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
 * @property array|null $value_available
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic $characteristic
 * @property-read Section|null $normsHelpSection
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereConversionDofusSample($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereConversionFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereConversionFunction($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereConversionKrosmozSample($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereConversionSampleRows($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereDbColumn($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereDefaultValue($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereDofusdbCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereEntity($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereFormulaDisplay($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereMin($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereNormsConditions($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereNormsDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereNormsGrid($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereNormsHelpSectionId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|CharacteristicSpell whereValueAvailable($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 67,
    'endLine' => 123,
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
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
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
            'startFilePos' => 4664,
            'endTokenPos' => 61,
            'endFilePos' => 4666,
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
      'ENTITY_SPELL' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'name' => 'ENTITY_SPELL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'spell\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 72,
            'startFilePos' => 4702,
            'endTokenPos' => 72,
            'endFilePos' => 4708,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'ENTITIES' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'name' => 'ENTITIES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[self::ENTITY_SPELL]',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 85,
            'startFilePos' => 4769,
            'endTokenPos' => 89,
            'endFilePos' => 4788,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'characteristic_spell\'',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 69,
            'startTokenPos' => 48,
            'startFilePos' => 4543,
            'endTokenPos' => 48,
            'endFilePos' => 4564,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'characteristic_id\', \'dofusdb_characteristic_id\', \'entity\', \'db_column\', \'min\', \'max\', \'formula\', \'formula_display\', \'default_value\', \'conversion_formula\', \'conversion_function\', \'conversion_dofus_sample\', \'conversion_krosmoz_sample\', \'conversion_sample_rows\', \'norms_grid\', \'norms_conditions\', \'norms_description\', \'norms_help_section_id\', \'value_available\']',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 100,
            'startTokenPos' => 100,
            'startFilePos' => 4847,
            'endTokenPos' => 159,
            'endFilePos' => 5365,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 100,
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
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_characteristic_id\' => \'integer\', \'conversion_dofus_sample\' => \'array\', \'conversion_krosmoz_sample\' => \'array\', \'conversion_sample_rows\' => \'array\', \'norms_grid\' => \'array\', \'norms_conditions\' => \'array\', \'value_available\' => \'array\', \'norms_help_section_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 112,
            'startTokenPos' => 170,
            'startFilePos' => 5430,
            'endTokenPos' => 228,
            'endFilePos' => 5781,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 112,
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
        'startLine' => 114,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'currentClassName' => 'App\\Models\\CharacteristicSpell',
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
        'startLine' => 119,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CharacteristicSpell',
        'implementingClassName' => 'App\\Models\\CharacteristicSpell',
        'currentClassName' => 'App\\Models\\CharacteristicSpell',
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