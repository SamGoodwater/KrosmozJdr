<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/PageSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\PageSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f04e81dade36f98e0b003357de523a2c927e4f071707774a8bff5577f670dab7-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\PageSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/PageSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\PageSeeder',
    'shortName' => 'PageSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Seed les pages de contribution : page « Nous rejoindre », présentation
 * du groupe puis une sous-page par type d\'entité (créature, objet, sort),
 * chacune avec une introduction puis des sections par caractéristique normée
 * (texte + charte interactive).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 24,
    'endLine' => 791,
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
      'ESSENTIAL_GROUP' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'name' => 'ESSENTIAL_GROUP',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '"L\'Essentiels"',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 85,
            'startFilePos' => 751,
            'endTokenPos' => 85,
            'endFilePos' => 764,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'ENTITY_GROUPS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'name' => 'ENTITY_GROUPS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'creature\' => [\'title\' => \'Créatures\', \'slug\' => \'contribution-creatures\', \'icon\' => \'fa-solid fa-dragon\', \'intro\' => \'<h2>Chartes des caractéristiques — Créatures</h2>\' . \'<p>Cette section présente les <strong>normes de référence</strong> pour les caractéristiques des créatures \' . \'(monstres, classes jouables, PNJ). Chaque charte définit les valeurs attendues selon le <strong>niveau</strong> (1–20) \' . \'et la <strong>puissance</strong> (très faible → très forte).</p>\' . \'<p>Utilise ces chartes pour vérifier qu\\\'une créature est équilibrée par rapport aux références du jeu. \' . \'Les conditions de lecture permettent d\\\'ajuster la ligne de puissance ou le niveau en fonction d\\\'autres caractéristiques.</p>\', \'entity\' => \'*\'], \'object\' => [\'title\' => \'Objets\', \'slug\' => \'contribution-objets\', \'icon\' => \'fa-solid fa-shield-halved\', \'intro\' => \'<h2>Chartes des caractéristiques — Objets</h2>\' . \'<p>Cette section présente les <strong>normes de référence</strong> pour les bonus d\\\'équipement. \' . \'Les valeurs sont calibrées selon les <strong>règles 5.2.4</strong> (Équipements et panoplies) : \' . \'+1-2 aux niveaux 1-5, +2-3 aux niveaux 6-10, +3-4 aux niveaux 11-15, +4-5 aux niveaux 16-20.</p>\' . \'<p>Un objet dont les bonus dépassent significativement la ligne « neutre » de sa charte est potentiellement \' . \'déséquilibré. Les objets rares ou légendaires peuvent atteindre la ligne « fort » ou « très fort ».</p>\', \'entity\' => \'*\'], \'spell\' => [\'title\' => \'Sorts\', \'slug\' => \'contribution-sorts\', \'icon\' => \'fa-solid fa-wand-sparkles\', \'intro\' => \'<h2>Chartes des caractéristiques — Sorts</h2>\' . \'<p>Cette section présente les <strong>normes de référence</strong> pour les effets de sorts. \' . \'Les dégâts, soins et boucliers sont calibrés selon les <strong>règles 5.2.3</strong> (Sorts et aptitudes), \' . \'avec une progression de ~1d6 (niveau 1) à ~5d6+mod (niveau 20).</p>\' . \'<p>Les conditions de lecture prennent en compte le coût en PA et la zone d\\\'effet : \' . \'un sort coûteux (5+ PA) peut avoir des dégâts supérieurs, tandis qu\\\'un sort en zone (≥2 cases) \' . \'devrait avoir des dégâts réduits par cible.</p>\', \'entity\' => \'*\']]',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 67,
            'startTokenPos' => 98,
            'startFilePos' => 867,
            'endTokenPos' => 280,
            'endFilePos' => 3566,
          ),
        ),
        'docComment' => '/** Groupes d\'entités avec métadonnées pour les pages. */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'ESSENTIAL_PAGES' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'name' => 'ESSENTIAL_PAGES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'bien-demarrer\' => [\'title\' => \'Bien démarrer\', \'slug\' => \'essentiels-bien-demarrer\', \'icon\' => null, \'menu_order\' => 10, \'intro_title\' => \'Vue d’ensemble\', \'intro_html\' => \'<p>Guide express pour lancer une partie rapidement.</p>\' . \'<p><strong>À retenir :</strong> les mécaniques clés tiennent sur une seule lecture.</p>\' . \'<p><strong>À faire :</strong> relire cette page avant chaque session.</p>\' . \'<p><strong>À éviter :</strong> ouvrir tout le règlement pour une simple vérification.</p>\', \'sections\' => [[\'slug\' => \'concept\', \'title\' => \'Essentiel — Concept du jeu en 2 minutes\', \'html\' => \'<ul><li>Jets de dés pour résoudre l’incertitude.</li><li>Ressources à gérer : PA, PM, Wakfu.</li><li>Alternance exploration / combat / progression.</li></ul>\'], [\'slug\' => \'materiel\', \'title\' => \'Action — Ce qu’il faut pour jouer\', \'html\' => \'<ul><li>Une fiche de personnage.</li><li>Des dés.</li><li>Ces pages résumées + la page Caractéristiques en support.</li></ul>\'], [\'slug\' => \'boucle\', \'title\' => \'Essentiel — Boucle de jeu\', \'html\' => \'<ul><li>Exploration et décisions.</li><li>Résolution des actions (tests, compétences, réactions).</li><li>Conflit/combat si nécessaire.</li><li>Récompenses et progression.</li></ul>\'], [\'slug\' => \'lexique\', \'title\' => \'Vigilance — Lexique rapide\', \'html\' => \'<ul><li><strong>PA</strong> : actions principales.</li><li><strong>PM</strong> : déplacement.</li><li><strong>CA</strong> : défense.</li><li><strong>Wakfu</strong> : réserve spéciale.</li><li><strong>Maîtrise/Expertise</strong> : bonus de compétence.</li></ul>\']]], \'creation\' => [\'title\' => \'Créer son personnage (rapide)\', \'slug\' => \'essentiels-creation-personnage\', \'icon\' => null, \'menu_order\' => 20, \'intro_title\' => \'Checklist de création\', \'intro_html\' => \'<p>Créez un personnage jouable avec un parcours court et fiable.</p>\' . \'<p><strong>À retenir :</strong> stats -> classe -> spécialisation -> équipement.</p>\' . \'<p><strong>À faire :</strong> sécuriser un rôle clair dès le départ.</p>\' . \'<p><strong>À éviter :</strong> disperser les points sur trop de caractéristiques.</p>\', \'sections\' => [[\'slug\' => \'etapes\', \'title\' => \'Action — Étapes de création\', \'html\' => \'<ol><li>Répartir les caractéristiques.</li><li>Choisir classe et spécialisation.</li><li>Sélectionner aptitudes/capacités.</li><li>S’équiper.</li></ol>\'], [\'slug\' => \'caracs\', \'title\' => \'Essentiel — Caractéristiques utiles\', \'html\' => \'<ul><li>Priorisez 2-3 stats liées à votre rôle.</li><li>Évitez les profils trop dispersés en début de campagne.</li><li>Utilisez la page Caractéristiques pour vérifier les bornes.</li></ul>\'], [\'slug\' => \'classe-spe\', \'title\' => \'Essentiel — Classe + spécialisation\', \'html\' => \'<ul><li>La classe définit l’identité globale.</li><li>La spécialisation précise votre rôle (dégâts, contrôle, soutien, etc.).</li><li>Visez une synergie simple avant les optimisations avancées.</li></ul>\'], [\'slug\' => \'equipement\', \'title\' => \'Vigilance — Équipement de départ\', \'html\' => \'<ul><li>Choisissez du matériel cohérent avec votre rôle.</li><li>Privilégiez la fiabilité avant les gains marginaux.</li><li>Les prix sont indicatifs et peuvent varier en campagne.</li></ul>\']]], \'actions-hors-combat\' => [\'title\' => \'Actions en jeu (hors combat)\', \'slug\' => \'essentiels-actions-hors-combat\', \'icon\' => null, \'menu_order\' => 30, \'intro_title\' => \'Exploration et interactions\', \'intro_html\' => \'<p>Résumé des décisions les plus fréquentes hors affrontement direct.</p>\' . \'<p><strong>À retenir :</strong> annoncer clairement intention, action et timing.</p>\' . \'<p><strong>À faire :</strong> garder un rythme de table fluide.</p>\' . \'<p><strong>À éviter :</strong> multiplier les vérifications longues en partie.</p>\', \'sections\' => [[\'slug\' => \'exploration\', \'title\' => \'Action — Exploration\', \'html\' => \'<ul><li>Observer l’environnement.</li><li>Se déplacer intelligemment.</li><li>Identifier risques/opportunités (pièges, ressources, PNJ).</li></ul>\'], [\'slug\' => \'temps\', \'title\' => \'Vigilance — Gestion du temps\', \'html\' => \'<ul><li>Le temps influe sur repos, trajets et préparation.</li><li>Annoncez clairement les durées d’action au MJ.</li><li>Anticiper évite les pénalités de rythme.</li></ul>\'], [\'slug\' => \'competences\', \'title\' => \'Essentiel — Tests de compétences\', \'html\' => \'<ul><li>Lancer le dé.</li><li>Ajouter modificateurs + maîtrise/expertise.</li><li>Comparer à la difficulté.</li></ul>\'], [\'slug\' => \'reactions\', \'title\' => \'Action — Réactions hors combat\', \'html\' => \'<ul><li>Utiles pour répondre vite à un imprévu.</li><li>À déclarer clairement (intention + action).</li><li>Servez-vous-en pour protéger le groupe ou saisir une fenêtre tactique.</li></ul>\']]], \'combat\' => [\'title\' => \'Combat (résumé pratique)\', \'slug\' => \'essentiels-combat\', \'icon\' => null, \'menu_order\' => 40, \'intro_title\' => \'Combat en une page\', \'intro_html\' => \'<p>Règles minimales pour mener un combat lisible et rapide.</p>\' . \'<p><strong>À retenir :</strong> position, initiative, ressources, états.</p>\' . \'<p><strong>À faire :</strong> annoncer les actions dans un ordre simple.</p>\' . \'<p><strong>À éviter :</strong> oublier les effets persistants entre les tours.</p>\', \'sections\' => [[\'slug\' => \'mise-en-place\', \'title\' => \'Action — Mise en place\', \'html\' => \'<ul><li>Positions initiales.</li><li>Initiative.</li><li>États actifs.</li><li>Objectif du combat.</li></ul>\'], [\'slug\' => \'tour-actions\', \'title\' => \'Essentiel — Tour de jeu et actions\', \'html\' => \'<ul><li>Choisir l’action prioritaire.</li><li>Optimiser le déplacement (PM).</li><li>Gérer les ressources restantes (PA/Wakfu).</li></ul>\'], [\'slug\' => \'reactions\', \'title\' => \'Action — Système de réaction\', \'html\' => \'<ul><li>Déclenchement hors tour.</li><li>Respect des conditions.</li><li>Très utile pour la défense et le contrôle.</li></ul>\'], [\'slug\' => \'sante-etats\', \'title\' => \'Vigilance — Santé, dégâts, états\', \'html\' => \'<ul><li>Suivre PV et boucliers.</li><li>Appliquer les états sans oubli.</li><li>Vérifier les effets qui persistent d’un tour à l’autre.</li></ul>\']]], \'sorts-aptitudes\' => [\'title\' => \'Sorts, aptitudes, capacités\', \'slug\' => \'essentiels-sorts-aptitudes\', \'icon\' => null, \'menu_order\' => 50, \'intro_title\' => \'Pouvoirs de personnage\', \'intro_html\' => \'<p>Résumé des mécaniques qui gouvernent vos pouvoirs actifs.</p>\' . \'<p><strong>À retenir :</strong> coût, portée, ligne de vue, conditions.</p>\' . \'<p><strong>À faire :</strong> garder le Wakfu pour les moments décisifs.</p>\' . \'<p><strong>À éviter :</strong> surconsommer les ressources tôt dans le combat.</p>\', \'sections\' => [[\'slug\' => \'typologie\', \'title\' => \'Essentiel — Types de sorts\', \'html\' => \'<ul><li>Dégâts</li><li>Soutien/soin</li><li>Contrôle</li><li>Mobilité</li><li>Invocation</li></ul>\'], [\'slug\' => \'lancement\', \'title\' => \'Action — Lancement en pratique\', \'html\' => \'<ul><li>Vérifier coût (PA).</li><li>Vérifier portée et ligne de vue.</li><li>Vérifier conditions/réactions éventuelles.</li></ul>\'], [\'slug\' => \'wakfu\', \'title\' => \'Vigilance — Réserve de Wakfu\', \'html\' => \'<ul><li>Ressource rare.</li><li>À garder pour les moments décisifs.</li><li>Coordonnez son usage avec le groupe.</li></ul>\'], [\'slug\' => \'synergies\', \'title\' => \'Essentiel — Aptitudes et synergies\', \'html\' => \'<ul><li>Associez vos capacités à vos sorts principaux.</li><li>Évitez les builds trop complexes en début de campagne.</li><li>Privilégiez la cohérence de rôle.</li></ul>\']]], \'economie-progression\' => [\'title\' => \'Économie, équipement, progression\', \'slug\' => \'essentiels-economie-progression\', \'icon\' => null, \'menu_order\' => 60, \'intro_title\' => \'Progression utile\', \'intro_html\' => \'<p>L’essentiel pour progresser efficacement sans entrer dans l’optimisation lourde.</p>\' . \'<p><strong>À retenir :</strong> cohérence de build > bonus isolés.</p>\' . \'<p><strong>À faire :</strong> vérifier les bornes avant tout achat/forgemagie.</p>\' . \'<p><strong>À éviter :</strong> casser l’économie de campagne avec des dépenses excessives.</p>\', \'sections\' => [[\'slug\' => \'rarete-loot\', \'title\' => \'Essentiel — Rareté, loot, récompenses\', \'html\' => \'<ul><li>La rareté donne une bonne estimation de puissance.</li><li>Les récompenses doivent rester cohérentes avec le niveau du groupe.</li></ul>\'], [\'slug\' => \'equip-panoplie\', \'title\' => \'Action — Équipement et panoplies\', \'html\' => \'<ul><li>Visez la cohérence de build.</li><li>Ne cumulez pas des bonus hors bornes.</li><li>Les synergies valent souvent plus que les pics isolés.</li></ul>\'], [\'slug\' => \'metiers-fm\', \'title\' => \'Vigilance — Métiers et forgemagie\', \'html\' => \'<ul><li>La forgemagie ajuste un équipement.</li><li>Respecter les maxima évite les déséquilibres.</li><li>Utiliser la page Caractéristiques pour contrôler les bornes.</li></ul>\'], [\'slug\' => \'conseils\', \'title\' => \'Vigilance — Conseils d’achat\', \'html\' => \'<ul><li>Les prix sont indicatifs.</li><li>Prioriser l’impact en jeu avant le prestige.</li><li>Éviter les dépenses qui cassent l’économie de campagne.</li></ul>\']]], \'caracteristiques\' => [\'title\' => \'Caractéristiques\', \'slug\' => \'caracteristiques\', \'icon\' => null, \'menu_order\' => 70, \'intro_title\' => \'Accès rapide\', \'intro_html\' => \'<p>Point d’entrée vers les bornes de conception : formules, min/max, valeurs par défaut, équipement et forgemagie.</p>\' . \'<p><strong>À retenir :</strong> ce tableau sert de référence rapide pour valider une valeur.</p>\' . \'<p><strong>À faire :</strong> croiser avec le contexte de campagne si besoin.</p>\' . \'<p><strong>À éviter :</strong> considérer les prix comme fixes (ils sont <strong>indicatifs</strong>).</p>\', \'sections\' => [], \'include_reference_table\' => true]]',
          'attributes' => 
          array (
            'startLine' => 83,
            'endLine' => 199,
            'startTokenPos' => 293,
            'startFilePos' => 4055,
            'endTokenPos' => 1336,
            'endFilePos' => 15510,
          ),
        ),
        'docComment' => '/**
 * Pages "résumé joueur/joueuse" : chaque entrée = 1 page, chaque item "sections" = 1 section texte.
 *
 * @var array<string, array{
 *   title: string,
 *   slug: string,
 *   icon: string|null,
 *   menu_order: int,
 *   intro_title: string,
 *   intro_html: string,
 *   sections: list<array{slug: string, title: string, html: string}>,
 *   include_reference_table?: bool
 * }>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 83,
        'endLine' => 199,
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
        'startLine' => 201,
        'endLine' => 315,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'nousRejoindreIntroHtml' => 
      array (
        'name' => 'nousRejoindreIntroHtml',
        'parameters' => 
        array (
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
        'docComment' => NULL,
        'startLine' => 317,
        'endLine' => 344,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
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
            'startLine' => 349,
            'endLine' => 349,
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
        'startLine' => 349,
        'endLine' => 373,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
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
            'startLine' => 376,
            'endLine' => 376,
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
            'startLine' => 377,
            'endLine' => 377,
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
            'startLine' => 378,
            'endLine' => 378,
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
            'startLine' => 379,
            'endLine' => 379,
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
            'startLine' => 380,
            'endLine' => 380,
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
            'startLine' => 381,
            'endLine' => 381,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'enableRichReferences' => 
          array (
            'name' => 'enableRichReferences',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 382,
                'endLine' => 382,
                'startTokenPos' => 2239,
                'startFilePos' => 22860,
                'endTokenPos' => 2239,
                'endFilePos' => 22864,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 382,
            'endLine' => 382,
            'startColumn' => 9,
            'endColumn' => 42,
            'parameterIndex' => 6,
            'isOptional' => true,
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
        'startLine' => 375,
        'endLine' => 402,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'ensureCharacteristicNormsSection' => 
      array (
        'name' => 'ensureCharacteristicNormsSection',
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
            'startLine' => 405,
            'endLine' => 405,
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
            'startLine' => 406,
            'endLine' => 406,
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
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'characteristicKey' => 
          array (
            'name' => 'characteristicKey',
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
            'startLine' => 408,
            'endLine' => 408,
            'startColumn' => 9,
            'endColumn' => 33,
            'parameterIndex' => 3,
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
            'startLine' => 409,
            'endLine' => 409,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 4,
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
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 9,
            'endColumn' => 22,
            'parameterIndex' => 5,
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
            'startLine' => 411,
            'endLine' => 411,
            'startColumn' => 9,
            'endColumn' => 18,
            'parameterIndex' => 6,
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
            'startLine' => 412,
            'endLine' => 412,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 7,
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
        'startLine' => 404,
        'endLine' => 433,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'ensureCharacteristicReferenceTableSection' => 
      array (
        'name' => 'ensureCharacteristicReferenceTableSection',
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
            'startLine' => 436,
            'endLine' => 436,
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
            'startLine' => 437,
            'endLine' => 437,
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
            'startLine' => 438,
            'endLine' => 438,
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
            'startLine' => 439,
            'endLine' => 439,
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
            'startLine' => 440,
            'endLine' => 440,
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
            'startLine' => 441,
            'endLine' => 441,
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
            'startLine' => 442,
            'endLine' => 442,
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
        'startLine' => 435,
        'endLine' => 468,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'seedEssentialPages' => 
      array (
        'name' => 'seedEssentialPages',
        'parameters' => 
        array (
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
            'startLine' => 470,
            'endLine' => 470,
            'startColumn' => 41,
            'endColumn' => 55,
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
        'startLine' => 470,
        'endLine' => 522,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'seedLibrariesPages' => 
      array (
        'name' => 'seedLibrariesPages',
        'parameters' => 
        array (
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
            'startLine' => 524,
            'endLine' => 524,
            'startColumn' => 41,
            'endColumn' => 55,
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
        'startLine' => 524,
        'endLine' => 584,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'libraryEntityTableType' => 
      array (
        'name' => 'libraryEntityTableType',
        'parameters' => 
        array (
          'entityKey' => 
          array (
            'name' => 'entityKey',
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
            'startLine' => 586,
            'endLine' => 586,
            'startColumn' => 45,
            'endColumn' => 61,
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
        'docComment' => NULL,
        'startLine' => 586,
        'endLine' => 602,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
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
            'startLine' => 607,
            'endLine' => 607,
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
            'startLine' => 607,
            'endLine' => 607,
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
            'startLine' => 607,
            'endLine' => 607,
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
        'startLine' => 607,
        'endLine' => 627,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'ensureEntityTableSection' => 
      array (
        'name' => 'ensureEntityTableSection',
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
            'startLine' => 630,
            'endLine' => 630,
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
            'startLine' => 631,
            'endLine' => 631,
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
            'startLine' => 632,
            'endLine' => 632,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 2,
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
            'startLine' => 633,
            'endLine' => 633,
            'startColumn' => 9,
            'endColumn' => 22,
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
            'startLine' => 634,
            'endLine' => 634,
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
            'startLine' => 635,
            'endLine' => 635,
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
        'startLine' => 629,
        'endLine' => 683,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'loadCharacteristicNames' => 
      array (
        'name' => 'loadCharacteristicNames',
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
 * @return array<string, string>
 */',
        'startLine' => 688,
        'endLine' => 701,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'loadCharacteristicNamesFromDefinitionFiles' => 
      array (
        'name' => 'loadCharacteristicNamesFromDefinitionFiles',
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
 * Libellés depuis les définitions JSON (fallback si la table `characteristics` est vide).
 *
 * @return array<string, string>
 */',
        'startLine' => 708,
        'endLine' => 729,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
        'aliasName' => NULL,
      ),
      'characteristicKeysWithNormsFromDefinitions' => 
      array (
        'name' => 'characteristicKeysWithNormsFromDefinitions',
        'parameters' => 
        array (
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
            'startLine' => 736,
            'endLine' => 736,
            'startColumn' => 65,
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
 * Clés ayant au moins une norme dans les définitions JSON du groupe.
 *
 * @return list<string>
 */',
        'startLine' => 736,
        'endLine' => 773,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
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
        'startLine' => 775,
        'endLine' => 790,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\PageSeeder',
        'implementingClassName' => 'Database\\Seeders\\PageSeeder',
        'currentClassName' => 'Database\\Seeders\\PageSeeder',
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