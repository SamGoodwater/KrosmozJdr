# Stack Technique et Outils Krosmoz-JDR

## Introduction

Le projet utilise les dernières recommandations en matière de sécurité, de qualité de code et d'organisation. Le code est en anglais, les commentaires en français. L'architecture suit le principe Atomic Design pour le frontend.

## Backend

- **Laravel 12** : https://laravel.com/docs/12.x
- **PHP 8.4**
- **MySQL** (nom de la base : krosmozDB, user : krosmozUser, mot de passe : krosmozPassword)
  - ⚠️ Les identifiants fournis sont pour le développement/test. En production, utiliser des variables d'environnement et ne jamais versionner les secrets.
- Les données proviennent d'une source extérieure, avec des données de test initiales.
- **Gestion des secrets** :
  - Utiliser le fichier `.env` de Laravel pour stocker les variables sensibles (DB, API keys, etc.).
  - Ne jamais versionner `.env` ou tout fichier contenant des secrets.

## Frontend

- **Vue.js** : https://fr.vuejs.org/guide/quick-start.html
- **SCSS**
- **Tailwind CSS** + **DaisyUI**
- **Tiptap** (éditeur WYSIWYG) : https://next.tiptap.dev/docs/editor/getting-started/install/vue3
- **esbuild** : @esbuild/win32-arm64
- **rollup** : @rollup/rollup-win32-arm64-msvc
- **fontawesome** : https://fontawesome.com/search
- **vue-i18n** pour l'internationalisation (stratégie : fichiers de langue séparés, fallback automatique)

## Outils de build et de tests

- **Composer** pour PHP
- **pnpm** pour le frontend (spécifique ARM)
- **Vitest** ou **Jest** pour les tests unitaires frontend (à préciser selon le choix du projet)
- **PHPUnit** pour les tests backend Laravel
- **Cypress** pour les tests end-to-end (recommandé)

## Intégration continue / Déploiement

- **CI/CD** :
  - Utilisation recommandée de GitHub Actions, GitLab CI ou équivalent pour automatiser les tests, le lint, le build et le déploiement.
  - Les pipelines doivent inclure :
    - Lint et formatage (PHP, JS, CSS)
    - Exécution des tests unitaires et E2E
    - Build frontend et backend
    - Déploiement automatisé (optionnel)

## Dépendances majeures

- Voir les fichiers `composer.json` (backend) et `package.json` (frontend) à la racine du projet ou dans le dossier `project/`.
- Ces fichiers seront complétés et mis à jour au fur et à mesure des besoins du projet, en fonction des dépendances réellement utilisées.

## Organisation du code

- `project/` : code source principal (backend Laravel, API, frontend VueJS intégré)
- `storage/` : images, icônes, fichiers uploadés (créé automatiquement par Laravel, voir DESIGN_GUIDE.md pour la gestion des assets)

## Gestion des assets

- Images, icônes, fichiers PDF sont stockés dans des dossiers dédiés (`/storage/icons/`, `/storage/logos/`, `/storage/files/`)
- Les images sont compressées et converties en webp si possible (voir DESIGN_GUIDE.md)
- Utilisation d'icônes FontAwesome et d'icônes Dofus spécifiques

## Structure détaillée des assets

- **Logos** :
  - `storage/images/Logos/logo.webp`
  - `storage/images/Logos/logo.svg`
  - `storage/images/Logos/logo.png`
  - `storage/images/Logos/logo.ico`
- **Aires des sorts** :
  - `storage/images/Icones/zones/X.svg` (X = nom de la zone)
- **Caractéristiques** :
  - `storage/images/Icones/caracteristiques/X.png` (X = nom de la caractéristique)
- **Fichiers uploadés** :
  - Pour une entité : `storage/entities/nom_de_l_entite/`
  - Pour une page : `storage/pages/`
  - Pour les autres fichiers : `storage/files/`

## Liens et ressources

- [Atomic Design](https://atomicdesign.bradfrost.com/)
- [DaisyUI](https://daisyui.com/docs/install/)
- [Tailwind CSS](https://v3.tailwindcss.com/docs/installation)
- [Vitest](https://vitest.dev/) / [Jest](https://jestjs.io/) / [Cypress](https://www.cypress.io/)

## Système de formules (calculs dynamiques)

- **Formules** :
  - Utilisation d'un système de formules pour les propriétés dynamiques (voir section dédiée dans CONTENT_OVERVIEW.md).
  - Les formules sont parsées, validées et évaluées côté backend.
  - La syntaxe, les opérateurs, les fonctions et les cas d'usage sont documentés.
  - Des tests unitaires et d'intégration garantissent la robustesse du système.
  - Le parser de formules Krosmoz-JDR est développé sur-mesure (solution custom) pour répondre à la syntaxe métier (accolades, crochets, opérateurs, fonctions, conditions, min/max, etc.), en s'inspirant si besoin de `symfony/expression-language` pour l'évaluation sécurisée.

> La documentation technique doit expliquer la syntaxe, l'intégration et l'utilisation des formules dans le projet.