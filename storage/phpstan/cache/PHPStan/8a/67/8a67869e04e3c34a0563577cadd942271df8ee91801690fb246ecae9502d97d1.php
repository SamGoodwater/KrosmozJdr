<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Core/Conversion/SpellEffects/SpellEffectConversionFormulaResolver.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectConversionFormulaResolver
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f92aa81f688caf1be3a55af7b1b20c9a1b646e10d8e332ef7dfa010552f14835-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Core/Conversion/SpellEffects/SpellEffectConversionFormulaResolver.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
    'name' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
    'shortName' => 'SpellEffectConversionFormulaResolver',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Résout la characteristic_key (groupe spell) à utiliser pour la conversion de la valeur
 * d\'un sous-effet, selon l\'action (sub_effect_slug) et les params (ex. characteristic).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 193,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'ACTION_TO_CHARACTERISTIC' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'ACTION_TO_CHARACTERISTIC',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'frapper\' => \'dommages_spell\', \'soigner\' => \'soin_spell\', \'protéger\' => \'bouclier_spell\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 20,
            'startTokenPos' => 35,
            'startFilePos' => 602,
            'endTokenPos' => 58,
            'endFilePos' => 723,
          ),
        ),
        'docComment' => '/** Mapping action (sub_effect_slug) → characteristic_key pour les Type 2 action (dommages, soin, bouclier). */',
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'MOVEMENT_KIND_TO_CHARACTERISTIC' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'MOVEMENT_KIND_TO_CHARACTERISTIC',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'movement\' => \'movement_distance_spell\', \'jump\' => \'jump_distance_spell\', \'teleport\' => \'teleport_distance_spell\', \'push\' => \'push_distance_spell\', \'pull\' => \'pull_distance_spell\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 29,
            'startTokenPos' => 71,
            'startFilePos' => 868,
            'endTokenPos' => 108,
            'endFilePos' => 1095,
          ),
        ),
        'docComment' => '/** Mapping type de déplacement → characteristic_key pour convertir les cases. */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'LIFE_STEAL_CHARACTERISTIC_KEY' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'LIFE_STEAL_CHARACTERISTIC_KEY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'vol_vie_spell\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 121,
            'startFilePos' => 1278,
            'endTokenPos' => 121,
            'endFilePos' => 1292,
          ),
        ),
        'docComment' => '/** Vol de vie : sous-effet unique « frapper » + params.life_steal_formula (conversion Dofus sur la même base « d »). */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'PER_CHARACTERISTIC_SLUGS' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'PER_CHARACTERISTIC_SLUGS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'booster\', \'retirer\', \'voler-caracteristiques\']',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 39,
            'startTokenPos' => 134,
            'startFilePos' => 1441,
            'endTokenPos' => 145,
            'endFilePos' => 1519,
          ),
        ),
        'docComment' => '/** Actions avec conversion par caractéristique (booster, retirer, voler-caracteristiques). */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'ENTITY_SPELL' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'ENTITY_SPELL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'spell\'',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 42,
            'startTokenPos' => 158,
            'startFilePos' => 1620,
            'endTokenPos' => 158,
            'endFilePos' => 1626,
          ),
        ),
        'docComment' => '/** Entité pour toutes les conversions d\'effets de sort. */',
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'IGNORED_KEYS' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'IGNORED_KEYS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'echec_critique\', \'prospection\']',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 48,
            'startTokenPos' => 171,
            'startFilePos' => 1749,
            'endTokenPos' => 179,
            'endFilePos' => 1804,
          ),
        ),
        'docComment' => '/** Clés courtes désactivées (caractéristiques retirées du groupe spell). */',
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'IGNORED_CHARACTERISTIC_KEYS' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'IGNORED_CHARACTERISTIC_KEYS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'echec_critique_spell\', \'magic_find_spell\']',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 109,
            'startTokenPos' => 537,
            'startFilePos' => 4073,
            'endTokenPos' => 545,
            'endFilePos' => 4139,
          ),
        ),
        'docComment' => '/** characteristic_keys désactivées (retirées du groupe spell : echec_critique, prospection). */',
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'SPELL_KEY_ALIASES' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'name' => 'SPELL_KEY_ALIASES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[
    \'pa\' => \'action_points_variation_spell\',
    \'po\' => \'range_spell\',
    \'pm\' => \'movement_points_spell\',
    \'range\' => \'range_spell\',
    \'movement_points\' => \'movement_points_spell\',
    \'fuite\' => \'dodge_spell\',
    \'tacle\' => \'tackle_spell\',
    \'dodge\' => \'dodge_spell\',
    \'tackle\' => \'tackle_spell\',
    \'strong\' => \'strong_spell\',
    \'vitality\' => \'vitality_spell\',
    \'sagesse\' => \'sagesse_spell\',
    \'chance\' => \'chance_spell\',
    \'agi\' => \'agi_spell\',
    \'intel\' => \'intel_spell\',
    \'critical\' => \'critical_spell\',
    \'res_terre\' => \'res_terre_spell\',
    \'res_feu\' => \'res_feu_spell\',
    \'res_eau\' => \'res_eau_spell\',
    \'res_air\' => \'res_air_spell\',
    \'res_neutre\' => \'res_neutre_spell\',
    \'do_fixe_multiple\' => \'do_fixe_multiple_spell\',
    \'esquive_pa\' => \'dodge_action_points_spell\',
    \'esquive_pm\' => \'dodge_movement_points_spell\',
    \'poussée\' => \'push_damage_reduction_spell\',
    \'poussee\' => \'push_damage_reduction_spell\',
    \'critiques\' => \'critical_damage_reduction_spell\',
    \'res_fixe_terre\' => \'fixed_resistance_terre_spell\',
    \'res_fixe_feu\' => \'fixed_resistance_feu_spell\',
    \'res_fixe_eau\' => \'fixed_resistance_eau_spell\',
    \'res_fixe_air\' => \'fixed_resistance_air_spell\',
    \'res_fixe_neutre\' => \'fixed_resistance_neutre_spell\',
    // —— Type 2 creature (équivalents spell des caractéristiques creature) ——
    \'ini\' => \'initiative_spell\',
    \'initiative\' => \'initiative_spell\',
    \'ca\' => \'armor_class_spell\',
    \'armor_class\' => \'armor_class_spell\',
    \'touch\' => \'hit_bonus_spell\',
    \'hit_bonus\' => \'hit_bonus_spell\',
    \'invocation\' => \'summoning_spell\',
    \'invocations\' => \'summoning_spell\',
    \'summoning\' => \'summoning_spell\',
    \'heal_bonus\' => \'heal_bonus_spell\',
    \'do_neutre\' => \'fixed_damage_neutral_spell\',
    \'do_terre\' => \'fixed_damage_earth_spell\',
    \'do_feu\' => \'fixed_damage_fire_spell\',
    \'do_air\' => \'fixed_damage_air_spell\',
    \'do_eau\' => \'fixed_damage_water_spell\',
    \'do_sagesse\' => \'fixed_damage_sagesse_spell\',
    \'do_vitalite\' => \'fixed_damage_vitalite_spell\',
    \'res_sagesse\' => \'res_sagesse_spell\',
    \'res_vitalite\' => \'res_vitalite_spell\',
    \'save_vitality\' => \'save_vitality_spell\',
    \'save_wisdom\' => \'save_wisdom_spell\',
    \'save_strength\' => \'save_strength_spell\',
    \'save_intelligence\' => \'save_intelligence_spell\',
    \'save_chance\' => \'save_chance_spell\',
    \'save_agility\' => \'save_agility_spell\',
    \'wakfu_reserve\' => \'wakfu_reserve_spell\',
    \'mastery_bonus\' => \'mastery_bonus_spell\',
]',
          'attributes' => 
          array (
            'startLine' => 112,
            'endLine' => 173,
            'startTokenPos' => 558,
            'startFilePos' => 4279,
            'endTokenPos' => 975,
            'endFilePos' => 7060,
          ),
        ),
        'docComment' => '/** Mapping clés courtes (mapping DofusDB) → characteristic_key du groupe spell en BDD. */',
        'attributes' => 
        array (
        ),
        'startLine' => 112,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'resolveCharacteristicKeyForConversion' => 
      array (
        'name' => 'resolveCharacteristicKeyForConversion',
        'parameters' => 
        array (
          'subEffectSlug' => 
          array (
            'name' => 'subEffectSlug',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 59,
            'endColumn' => 79,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
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
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 82,
            'endColumn' => 94,
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
 * Retourne la characteristic_key (groupe spell) pour appliquer la conversion, ou null si pas de conversion.
 *
 * @param  string  $subEffectSlug  Slug du sous-effet (frapper, soigner, booster, …)
 * @param  array<string, mixed>  $params  Params du sous-effet (characteristic, value_formula, …)
 * @return string|null Clé pour DofusConversionService (ex. power_spell, action_points_variation_spell) ou null
 */',
        'startLine' => 57,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'aliasName' => NULL,
      ),
      'resolveLifeStealCharacteristicKeyForConversion' => 
      array (
        'name' => 'resolveLifeStealCharacteristicKeyForConversion',
        'parameters' => 
        array (
          'params' => 
          array (
            'name' => 'params',
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
            'startLine' => 95,
            'endLine' => 95,
            'startColumn' => 68,
            'endColumn' => 80,
            'parameterIndex' => 0,
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
 * Clé de conversion pour le montant « PV volés » lorsque `life_steal_formula` est renseignée (frapper).
 */',
        'startLine' => 95,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'aliasName' => NULL,
      ),
      'normalizeSpellKey' => 
      array (
        'name' => 'normalizeSpellKey',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 178,
            'endLine' => 178,
            'startColumn' => 40,
            'endColumn' => 50,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Normalise la clé pour le groupe spell (alias ou suffixe _spell).
 */',
        'startLine' => 178,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
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