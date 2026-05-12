<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/CharacteristicSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\CharacteristicSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-222c77bf4310f2e9886060be04fc33c32f2fef5e960e1fcd254530ca2e4eff81-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\CharacteristicSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/CharacteristicSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\CharacteristicSeeder',
    'shortName' => 'CharacteristicSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Seed la table générale characteristics.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 295,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Seeder',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'SPELL_TO_CREATURE_STYLE_KEY' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'name' => 'SPELL_TO_CREATURE_STYLE_KEY',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'agi_spell\' => \'agility_creature\', \'intel_spell\' => \'intelligence_creature\', \'strong_spell\' => \'strength_creature\', \'sagesse_spell\' => \'wisdom_creature\', \'fixed_resistance_eau_spell\' => \'fixed_resistance_water_creature\', \'fixed_resistance_feu_spell\' => \'fixed_resistance_fire_creature\', \'fixed_resistance_terre_spell\' => \'fixed_resistance_earth_creature\', \'fixed_resistance_neutre_spell\' => \'fixed_resistance_neutral_creature\', \'res_air_spell\' => \'resistance_air_creature\', \'res_eau_spell\' => \'resistance_water_creature\', \'res_feu_spell\' => \'resistance_fire_creature\', \'res_neutre_spell\' => \'resistance_neutral_creature\', \'res_terre_spell\' => \'resistance_earth_creature\', \'res_sagesse_spell\' => \'wisdom_creature\', \'res_vitalite_spell\' => \'vitality_creature\', \'fixed_damage_sagesse_spell\' => \'wisdom_creature\', \'fixed_damage_vitalite_spell\' => \'vitality_creature\', \'do_fixe_multiple_spell\' => \'fixed_damage_multiple_creature\', \'bouclier_spell\' => \'armor_class_creature\', \'dommages_spell\' => \'fixed_damage_neutral_creature\', \'soin_spell\' => \'heal_bonus_creature\', \'vol_vie_spell\' => \'life_points_creature\', \'critical_spell\' => \'critical_hit_creature\', \'spell_range_max_spell\' => \'range_creature\', \'spell_range_min_spell\' => \'range_creature\']',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 50,
            'startTokenPos' => 62,
            'startFilePos' => 708,
            'endTokenPos' => 239,
            'endFilePos' => 2154,
          ),
        ),
        'docComment' => '/**
 * Clés `*_spell` → `*_creature` quand le nommage ne suit pas la règle suffixe `_spell` → `_creature`.
 * Les autres sorts alignés sur une caractéristique créature sont résolus automatiquement.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'run' => 
      array (
        'name' => 'run',
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
        'startLine' => 52,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'normalizeCharacteristicDisplayLabels' => 
      array (
        'name' => 'normalizeCharacteristicDisplayLabels',
        'parameters' => 
        array (
          'row' => 
          array (
            'name' => 'row',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 59,
            'endColumn' => 68,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
 * Retire le suffixe « (effet) » / « (effets) » du libellé et « eff. » en fin d’abréviation
 * (ex. « PM eff. » → « PM »), pour alléger l’affichage des caractéristiques liées aux sorts.
 *
 * @param  array<string, mixed>  $row
 * @return array<string, mixed>
 */',
        'startLine' => 176,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'applySpellVisualsFromCreatureCharacteristics' => 
      array (
        'name' => 'applySpellVisualsFromCreatureCharacteristics',
        'parameters' => 
        array (
          'rows' => 
          array (
            'name' => 'rows',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 67,
            'endColumn' => 77,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
 * Pour chaque caractéristique de groupe `spell`, recopie `icon` et `color` depuis la caractéristique
 * `creature` équivalente lorsqu’elle existe (données déjà harmonisées côté créature).
 *
 * @param  list<array<string, mixed>>  $rows
 * @return list<array<string, mixed>>
 */',
        'startLine' => 199,
        'endLine' => 230,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'resolveCreatureStyleSourceKey' => 
      array (
        'name' => 'resolveCreatureStyleSourceKey',
        'parameters' => 
        array (
          'spellRow' => 
          array (
            'name' => 'spellRow',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 52,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'creatureByKey' => 
          array (
            'name' => 'creatureByKey',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 235,
            'endLine' => 235,
            'startColumn' => 69,
            'endColumn' => 88,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
                  'name' => 'string',
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
 * @param  array<string, array<string, mixed>>  $creatureByKey
 */',
        'startLine' => 235,
        'endLine' => 260,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'loadIconsAndColorsDefaults' => 
      array (
        'name' => 'loadIconsAndColorsDefaults',
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
 * Surcharges visuelles optionnelles (réserve ; tout est porté par les JSON de définition).
 *
 * @return array{icons: array<string, string>, icons_false: array<string, string>, colors: array<string, string>, descriptions: array<string, string>, value_overrides: array<string, list<array<string, mixed>>>}
 */',
        'startLine' => 267,
        'endLine' => 270,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'loadCharacteristicRowsFromDefinitionFiles' => 
      array (
        'name' => 'loadCharacteristicRowsFromDefinitionFiles',
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
 * Une ligne par fichier `stem-groupe-definition.json` (bloc `characteristic`).
 *
 * @return list<array<string, mixed>>
 */',
        'startLine' => 277,
        'endLine' => 294,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicSeeder',
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