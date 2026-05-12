<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/CreationPagesSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\CreationPagesSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-71ef0fc9558762d49fb9a3954ce7f412fe0b9eac8c879254f9bc49c3cde724d9-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\CreationPagesSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/CreationPagesSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\CreationPagesSeeder',
    'shortName' => 'CreationPagesSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Arborescence « Création » : aide à la création d’entités (chartes / normes).
 * Une sous-page par **groupe technique** (spell, creature, object) : toutes les caractéristiques
 * d’un groupe partagent le même référentiel de sens ; les types d’entité (monstre, consommable, etc.)
 * se distinguent par le contenu, pas par des chartes dupliquées.
 *
 * Prérequis : seeders des caractéristiques et pivots (norms_grid) exécutés avant
 * (ex. {@see CreatureCharacteristicSeeder}, {@see ObjectCharacteristicSeeder}, {@see SpellCharacteristicSeeder}).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 22,
    'endLine' => 314,
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
      'SUBPAGES' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'name' => 'SUBPAGES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[[\'title\' => \'Sorts et capacités\', \'slug\' => \'creation-sorts\', \'icon\' => \'fa-solid fa-wand-sparkles\', \'group\' => \'spell\', \'entity\' => \'*\', \'intro\' => \'<h2>Groupe sort / capacités — chartes</h2>\' . \'<p>Ce catalogue couvre <strong>toutes</strong> les caractéristiques du groupe <em>sort</em> : \' . \'dégâts, soins, portée, PA, buffs, etc. Les <strong>capacités</strong> s’appuient sur le même groupe \' . \'en base : une seule page évite de dupliquer les mêmes grilles.</p>\' . \'<p>Complète avec des sections texte (conseils, exemples) si besoin.</p>\', \'advice\' => \'<p><strong>Conseils d’équilibrage</strong></p>\' . \'<ul>\' . \'<li>Commence par la ligne <strong>Neutre</strong> au niveau visé, puis applique uniquement les modificateurs réellement pertinents.</li>\' . \'<li>Pour un sort à très forte portée ou à grande zone, évite de dépasser les bornes hautes de dégâts/soins au même niveau.</li>\' . \'<li>Un coût PA élevé peut justifier une valeur plus haute, mais garde une cohérence avec les limites min/max affichées sous le tableau.</li>\' . \'<li>Les capacités utilitaires (entrave, placement, contrôle) compensent souvent la valeur brute : privilégie alors une lecture plus prudente.</li>\' . \'</ul>\'], [\'title\' => \'Créatures\', \'slug\' => \'creation-creatures\', \'icon\' => \'fa-solid fa-dragon\', \'group\' => \'creature\', \'entity\' => \'*\', \'intro\' => \'<h2>Groupe créature — chartes</h2>\' . \'<p>Référentiel unique pour monstres, classes jouables, PNJ : PV, stats, CA, maîtrises, etc. \' . \'Le sens des caractéristiques est commun à tout le groupe <em>creature</em>.</p>\', \'advice\' => \'<p><strong>Conseils de construction</strong></p>\' . \'<ul>\' . \'<li>Si la créature est censée encaisser, privilégie d’abord les <strong>points de vie</strong> puis la défense, avant d’augmenter les dégâts.</li>\' . \'<li>Évite de cumuler des valeurs hautes sur trop d’axes (PV, dégâts, mobilité, contrôle) au même niveau.</li>\' . \'<li>Pour les créatures rapides ou techniques, monte plutôt mobilité/initiative et garde des PV plus modérés.</li>\' . \'<li>Vérifie systématiquement que les valeurs finales restent dans les bornes min/max du niveau cible.</li>\' . \'</ul>\'], [\'title\' => \'Objets\', \'slug\' => \'creation-objets\', \'icon\' => \'fa-solid fa-box-open\', \'group\' => \'object\', \'entity\' => \'*\', \'intro\' => \'<h2>Groupe objet — chartes</h2>\' . \'<p>Un seul catalogue pour équipements, consommables, ressources, panoplies : les bonus et portées \' . \'objet partagent les mêmes échelles. Tu documentes les nuances (slot, rareté, usage) en texte autour du catalogue.</p>\', \'advice\' => \'<p><strong>Conseils de calibration</strong></p>\' . \'<ul>\' . \'<li>Sur les bas niveaux, reste proche des paliers faibles/modérés ; réserve les valeurs fortes aux objets rares ou à fortes contraintes.</li>\' . \'<li>Évite les objets qui surclassent la progression normale du niveau (surtout sur plusieurs stats en même temps).</li>\' . \'<li>Pour consommables et ressources, adapte la puissance à la fréquence d’obtention et au coût d’accès.</li>\' . \'<li>Utilise les limites min/max comme garde-fou avant validation finale.</li>\' . \'</ul>\']]',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 83,
            'startTokenPos' => 62,
            'startFilePos' => 1066,
            'endTokenPos' => 304,
            'endFilePos' => 4939,
          ),
        ),
        'docComment' => '/**
 * Sous-pages : une par groupe (`characteristic_*` pivot), `entity` = *.
 *
 * @var list<array{title: string, slug: string, icon: string, group: string, entity: string, intro: string, advice: string}>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'DEPRECATED_CHILD_SLUGS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'name' => 'DEPRECATED_CHILD_SLUGS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'creation-monstres\', \'creation-equipement\', \'creation-ressources\', \'creation-consommables\', \'creation-capacites\']',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 97,
            'startTokenPos' => 317,
            'startFilePos' => 5207,
            'endTokenPos' => 334,
            'endFilePos' => 5367,
          ),
        ),
        'docComment' => '/**
 * Anciennes sous-pages (6 pages par type d’entité) remplacées par 3 pages par groupe.
 * Suppression logique au re-seed pour éviter les doublons dans le menu.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 97,
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
        'startLine' => 99,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'aliasName' => NULL,
      ),
      'removeDeprecatedCreationChildren' => 
      array (
        'name' => 'removeDeprecatedCreationChildren',
        'parameters' => 
        array (
          'parent' => 
          array (
            'name' => 'parent',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Page',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 55,
            'endColumn' => 66,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 180,
        'endLine' => 193,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'aliasName' => NULL,
      ),
      'createOrRestorePage' => 
      array (
        'name' => 'createOrRestorePage',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 198,
            'endLine' => 198,
            'startColumn' => 42,
            'endColumn' => 58,
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
            'name' => 'App\\Models\\Page',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, mixed>  $attributes
 */',
        'startLine' => 198,
        'endLine' => 218,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'aliasName' => NULL,
      ),
      'ensureTextSection' => 
      array (
        'name' => 'ensureTextSection',
        'parameters' => 
        array (
          'page' => 
          array (
            'name' => 'page',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Page',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 221,
            'endLine' => 221,
            'startColumn' => 9,
            'endColumn' => 18,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'slug' => 
          array (
            'name' => 'slug',
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
            'startLine' => 222,
            'endLine' => 222,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'title' => 
          array (
            'name' => 'title',
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
            'startLine' => 223,
            'endLine' => 223,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'contentHtml' => 
          array (
            'name' => 'contentHtml',
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
            'startLine' => 224,
            'endLine' => 224,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'order' => 
          array (
            'name' => 'order',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 225,
            'endLine' => 225,
            'startColumn' => 9,
            'endColumn' => 18,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'creatorId' => 
          array (
            'name' => 'creatorId',
            'default' => NULL,
            'type' => 
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
                      'name' => 'int',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 226,
            'endLine' => 226,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Section',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 220,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'aliasName' => NULL,
      ),
      'ensureCatalogSection' => 
      array (
        'name' => 'ensureCatalogSection',
        'parameters' => 
        array (
          'page' => 
          array (
            'name' => 'page',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Page',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 9,
            'endColumn' => 18,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'slug' => 
          array (
            'name' => 'slug',
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
            'startLine' => 245,
            'endLine' => 245,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'title' => 
          array (
            'name' => 'title',
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
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'group' => 
          array (
            'name' => 'group',
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
            'startLine' => 247,
            'endLine' => 247,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 248,
            'endLine' => 248,
            'startColumn' => 9,
            'endColumn' => 22,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'order' => 
          array (
            'name' => 'order',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 249,
            'endLine' => 249,
            'startColumn' => 9,
            'endColumn' => 18,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'creatorId' => 
          array (
            'name' => 'creatorId',
            'default' => NULL,
            'type' => 
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
                      'name' => 'int',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 250,
            'endLine' => 250,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 6,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Section',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 243,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'aliasName' => NULL,
      ),
      'ensureSection' => 
      array (
        'name' => 'ensureSection',
        'parameters' => 
        array (
          'page' => 
          array (
            'name' => 'page',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Page',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 276,
            'endLine' => 276,
            'startColumn' => 36,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'slug' => 
          array (
            'name' => 'slug',
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
            'startLine' => 276,
            'endLine' => 276,
            'startColumn' => 48,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 276,
            'endLine' => 276,
            'startColumn' => 62,
            'endColumn' => 78,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Section',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, mixed>  $attributes
 */',
        'startLine' => 276,
        'endLine' => 296,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'aliasName' => NULL,
      ),
      'resolveDefaultCreatorId' => 
      array (
        'name' => 'resolveDefaultCreatorId',
        'parameters' => 
        array (
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
                  'name' => 'int',
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
        'docComment' => NULL,
        'startLine' => 298,
        'endLine' => 313,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreationPagesSeeder',
        'currentClassName' => 'Database\\Seeders\\CreationPagesSeeder',
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