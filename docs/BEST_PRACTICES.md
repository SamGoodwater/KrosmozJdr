# Bonnes Pratiques Globales Krosmoz-JDR

Note à destination pour la génération :

Toute la structure backend doit suivre strictement les conventions Laravel 12 (organisation des dossiers, nommage, routes, contrôleurs, modèles, migrations, tests, etc.).
Toute la structure frontend doit suivre strictement les conventions Vue 3 et Atomic Design (arborescence, nommage, usage de la Composition API, tests, etc.), intégrée dans le projet Laravel (pas de dossier frontend/ séparé).
Les routes RESTful, les tests unitaires, la documentation API (OpenAPI/Swagger) et l'organisation des fichiers doivent être générés selon les conventions officielles des frameworks et outils utilisés.
Aucune adaptation ou pattern maison n'est imposé.
Les fichiers de dépendances (`composer.json` pour le backend, `package.json` pour le frontend) seront générés et complétés au fur et à mesure des besoins du projet, selon les standards Laravel/Vue et les outils listés dans la documentation technique (dans le dossier `project/`).

## Bonnes pratiques pour le projet Krosmoz-JDR

- le projet se trouve dans le dossier ./project
- Respecter les standards de sécurité (validation des entrées, prévention des injections SQL, gestion des droits, protection contre les failles XSS et CSRF, anonymisation des données sensibles, gestion des permissions et rôles, audit des accès et des logs).
- Utiliser des conventions de nommage claires et standardisées pour les fichiers, variables, classes, méthodes, composants, props et events :
  - **Laravel** :
    - Fichiers : kebab-case (ex : `my-class-file.php`)
    - Classes & Enums : PascalCase (ex : `MyClass`)
    - Méthodes : camelCase (ex : `myMethod`)
    - Variables & Propriétés : snake_case (ex : `my_variable`)
    - Constantes & Enum Cases : SCREAMING_SNAKE_CASE (ex : `MY_CONSTANT`)
  - **VueJS** :
    - Composants : PascalCase (ex : `MyComponent.vue`)
    - Props & Events : camelCase (ex : `myProp`, `myEvent`)
    - Dossiers/fichiers JS/TS : kebab-case (ex : `my-composable.ts`)
- Organiser le code frontend selon Atomic Design ([voir DESIGN_GUIDE.md](./DESIGN_GUIDE.md)), dans la structure Laravel.

* Lorsque pertinent, organiser les dossiers et fichiers de composants (atoms, molecules, organisms) par thématiques ou fonctionnalités (ex : "form", "table", "user", "layout", etc.) afin de faciliter la navigation, la réutilisabilité et la maintenabilité du code.

- Documenter toute nouvelle fonctionnalité ou entité métier dans le fichier approprié ([ENTITIES_OVERVIEW.md](./ENTITIES_OVERVIEW.md), [CONTENT_OVERVIEW.md](./CONTENT_OVERVIEW.md), etc.).
- Protéger les données sensibles et respecter la RGPD (inclure le droit à l'oubli, l'export des données sur demande, et la suppression sécurisée).
- Utiliser des outils de build et de tests adaptés à la stack (Composer, pnpm, etc.).
- Mettre en place des tests unitaires et d'intégration pour le code métier (backend, frontend).
- Outils de tests recommandés : PHPUnit (Laravel), Vitest/Jest (Vue), Cypress (E2E).

## Bonnes pratiques communes

- Tous les logs doivent être en français, explicites, et indiquer clairement le contexte.
- Les tests doivent vérifier la présence des logs attendus, la cohérence des artefacts générés, et la communication inter-composants.
- Toute nouvelle fonctionnalité ou correction doit être relue et validée.
- Respecter la documentation et les conventions du projet.

## Liens utiles

- [Documentation technique](./TECHNOLOGIES.md)
- [Guide de design](./DESIGN_GUIDE.md)

## WYSIWYG (éditeur de texte riche)

- Le projet utilise [Tiptap](https://next.tiptap.dev/docs/editor/getting-started/install/vue3) comme éditeur WYSIWYG pour les pages dynamiques.
- Tiptap est intégré dans le frontend Vue 3, avec la Composition API et StarterKit.
- Les images et PDF sont les seuls fichiers autorisés à l'upload via l'éditeur.
- Les extensions Tiptap peuvent être ajoutées selon les besoins (tableaux, listes, etc.).

## Internationalisation (i18n)

- Les fichiers de langue sont organisés par locale (ex : `resources/lang/fr.json`, `en.json`, etc.).
- Utiliser les helpers Laravel natifs côté backend et [vue-i18n](https://vue-i18n.intlify.dev/) côté frontend.
- Pour ajouter une langue :
  1. Créer un fichier de langue (ex : `es.json`).
  2. Traduire toutes les clés existantes.
  3. Ajouter la langue dans la config Laravel et dans le frontend.
  4. Tester le switcher de langue sur toutes les pages.
- Les pages dynamiques stockent le contenu par langue (ex : champ `content_fr`, `content_en`, etc. ou table de traduction liée).
- L'éditeur WYSIWYG permet de sélectionner la langue à éditer.

## Conventions pour les helpers (Laravel & Vue)

- Nom explicite, en anglais, en camelCase (ex : `formatDate`, `getUserRole`).
- Chaque helper doit avoir un docblock en français expliquant :
  - Le but du helper
  - Les paramètres attendus (types, valeurs par défaut)
  - La valeur de retour
  - Un exemple d'utilisation
- Chaque helper doit être testé (PHPUnit pour Laravel, Vitest/Jest pour Vue).
- Pas de logique métier complexe dans les helpers : ils servent à factoriser des utilitaires simples et réutilisables.

Exemple de docblock pour un helper Laravel :

```php
/**
 * Formate une date au format français.
 *
 * @param  string|\DateTimeInterface  $date  Date à formater
 * @return string  Date formatée (ex : 01/01/2024)
 *
 * @example
 *   formatDate('2024-01-01'); // "01/01/2024"
 */
function formatDate($date) { /* ... */ }
```

## Formules dynamiques (calculs)

- Toujours utiliser la syntaxe des formules définie dans [CONTENT_OVERVIEW.md – section 5](./CONTENT_OVERVIEW.md#5-syntaxe-des-formules-krosmoz-jdr).
- Le parser de formules Krosmoz-JDR est développé sur-mesure (solution custom) pour répondre à la syntaxe métier (accolades, crochets, opérateurs, fonctions, conditions, min/max, etc.), en s'inspirant si besoin de `symfony/expression-language` pour l'évaluation sécurisée.
- Valider la syntaxe et la cohérence des formules lors de l'enregistrement ou de la modification.
- Documenter l'usage des formules dans le code (docblocks, exemples).
- Ajouter des tests unitaires pour le parsing et l'évaluation des formules.
- Les formules permettent de rendre les propriétés dynamiques et évolutives (voir la section dédiée dans la documentation technique).
---