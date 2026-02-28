Conventions de nommage

Ce document définit les règles de nommage à respecter dans le projet.
Objectif : cohérence, lisibilité et prévisibilité.

Un nom doit être explicite, stable et sans ambiguïté.

Règles générales

Utiliser uniquement l’anglais dans le code.

Toujours privilégier des noms explicites et complets.

Ne jamais utiliser d’abréviations (hp, str, cfg, tmp, etc.).

Un nom doit décrire l’intention métier, pas l’implémentation technique.

Éviter les synonymes multiples pour un même concept.

Respecter strictement les conventions propres à chaque environnement (Laravel, VueJS, etc.).

Toute génération de code par un LLM doit respecter ces règles sans exception.

Laravel
Fichiers

Format : kebab-case

Exemple : my-class-file.php

Classes & Enums

Format : PascalCase

Exemple : MyClass, UserRole

Méthodes

Format : camelCase

Exemple : getUserProfile(), calculateTotalDamage()

Variables & Propriétés

Format : snake_case

Exemple : user_name, total_damage

Constantes & Enum Cases

Format : SCREAMING_SNAKE_CASE

Exemple : MAX_LEVEL, CRITICAL_HIT

VueJS
Composants

Format : PascalCase

Exemple : UserProfileCard.vue

Props & Events

Format : camelCase

Exemple : userId, isVisible, updateStatus

Fichiers JS / TS

Format : kebab-case

Exemple : use-user-permissions.ts, format-date.ts

Helpers

Langue : anglais obligatoire

Format : camelCase

Le nom doit décrire clairement l’action effectuée

Toujours commencer par un verbe

Exemples :

formatDate

calculateDamageReduction

getUserRole

mapEffectToCharacteristic

Caractéristiques et propriétés métier

(Stats, éléments, effets, mapping DofusDB, seeders, migrations, etc.)

Règles

Anglais obligatoire

Aucune abréviation

Noms complets et explicites

Se référer au document : CHARACTERISTIC_PROPERTY_NAMING_REFERENCE.md

Exemples corrects

actionPoints

movementPoints

fireResistance

criticalHitChance

Exemples interdits

ap

mp

resFire

crit

Règles complémentaires recommandées
Booléens

Toujours commencer par :

is (ex : isVisible)

has (ex : hasPermission)

can (ex : canEdit)

should (ex : shouldApplyEffect)

Collections

Toujours utiliser le pluriel :

users

effects

characteristics

Base de données

Tables : snake_case au pluriel (users, game_effects)

Colonnes : snake_case

Clés étrangères : {model}_id (ex : user_id)

Exigences pour les LLM

Tout code généré doit :

Respecter strictement ces conventions.

Ne jamais introduire d’abréviations métier.

Réutiliser les noms déjà existants dans le projet.

Maintenir une cohérence stricte avec les conventions Laravel et VueJS.

Ne pas inventer de nouveaux synonymes pour des concepts métier existants.