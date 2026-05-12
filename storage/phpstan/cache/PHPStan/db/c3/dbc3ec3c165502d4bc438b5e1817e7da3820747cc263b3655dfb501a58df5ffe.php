<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/DofusdbEffectMappingSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\DofusdbEffectMappingSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-7542a390ee5f27831e9ed5a2c6ccb398893b2d478d4d13b84ebe49213b9ce8d8-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/DofusdbEffectMappingSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
    'shortName' => 'DofusdbEffectMappingSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Seed des mappings effectId DofusDB → sous-effet Krosmoz (source de vérité en dur).
 *
 * Charge les mappings soit depuis le fichier généré par l’API (si présent), soit depuis
 * la constante MAPPINGS ci-dessous. Pour régénérer le fichier depuis DofusDB :
 *
 *   php artisan scrapping:effects:map --output=database/seeders/data/dofusdb_effect_mappings_suggested.php
 *
 * Puis exécuter ce seeder pour écrire en base. Tu peux aussi éditer MAPPINGS ou le fichier
 * data pour ajouter/corriger des lignes à la main.
 *
 * Référence effectId : API DofusDB GET /effects/{id}.
 * Sous-effets : sub_effects (frapper, soigner, booster, retirer, déplacer, invoquer, autre, …).
 * characteristic_source : element | characteristic | none.
 * characteristic_key : null pour element/none ; clé Krosmoz si characteristic.
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 * @see docs/50-Fonctionnalités/Scrapping/CARACTERISTIQUES_EFFETS_PAR_ACTION.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 30,
    'endLine' => 83,
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
      'DATA_FILE' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'name' => 'DATA_FILE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '__DIR__ . \'/data/dofusdb_effect_mappings_suggested.php\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 52,
            'startFilePos' => 1395,
            'endTokenPos' => 56,
            'endFilePos' => 1449,
          ),
        ),
        'docComment' => '/** Fichier de mappings suggérés généré par scrapping:effects:map (optionnel). */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 86,
      ),
      'MAPPINGS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'name' => 'MAPPINGS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[96 => [\'frapper\', \'element\', null], 97 => [\'frapper\', \'element\', null], 98 => [\'frapper\', \'element\', null], 99 => [\'frapper\', \'element\', null], 100 => [\'frapper\', \'element\', null]]',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 45,
            'startTokenPos' => 69,
            'startFilePos' => 1654,
            'endTokenPos' => 146,
            'endFilePos' => 1881,
          ),
        ),
        'docComment' => '/**
 * Mappings par défaut (utilisés si le fichier data n’existe pas).
 * effectId => [sub_effect_slug, characteristic_source, characteristic_key].
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 45,
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
        'startLine' => 47,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'currentClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'aliasName' => NULL,
      ),
      'getMappings' => 
      array (
        'name' => 'getMappings',
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
 * @return array<int, array{0: string, 1: string, 2: string|null}>
 */',
        'startLine' => 72,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
        'currentClassName' => 'Database\\Seeders\\DofusdbEffectMappingSeeder',
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