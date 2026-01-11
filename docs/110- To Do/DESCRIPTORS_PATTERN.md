# Pattern Descriptors — Contrat stable entre moteur et métier

**Date de création** : 2026-01-XX  
**Contexte** : Définition stricte du rôle et de la structure des descriptors

---

## 🎯 Le rôle exact du descriptor (en une phrase)

> **Le descriptor est un schéma déclaratif qui permet au moteur de générer des outils génériques autour d'une entité.**

Pas plus. Pas moins.

---

## 📜 Les règles absolues du descriptor

Grave-les dans le marbre.

### Règle 1 — Un descriptor ne contient aucune logique métier

❌ **PAS de logique :**
- ❌ `if rarity === 3`
- ❌ Formatage
- ❌ Calculs
- ❌ Conditions métier complexes

✅ **UNIQUEMENT de la déclaration :**
- ✅ `sortable: true`
- ✅ `filterable: 'select'`
- ✅ `required: true`
- ✅ `type: 'text'`

---

### Règle 2 — Un descriptor ne décrit pas une vue

❌ **PAS de description de vue :**
- ❌ Layout
- ❌ Ordre visuel Large / Compact / Minimal / Text
- ❌ HTML déguisé
- ❌ Structure de page

✅ **Il décrit comment un moteur peut générer :**
- ✅ Un tableau
- ✅ Un formulaire
- ✅ Un quickedit

---

### Règle 3 — Un descriptor est déterministe

À contexte identique (`capabilities`, `meta`),
👉 **le même descriptor produit toujours la même config**

- ✅ Pas d'état
- ✅ Pas d'effet de bord
- ✅ Pas de dépendances externes variables

---

### Règle 4 — Le descriptor parle le langage du moteur, pas du métier

Il dit :

✅ `sortable`
✅ `filterable`
✅ `editable`
✅ `bulk.enabled`
✅ `visibleFrom('sm')`
✅ `display('badge')`

Il ne dit **PAS** :

❌ "c'est important"
❌ "c'est joli"
❌ "ça fait sens"
❌ "affiche-le en premier"

---

## ❓ Les 4 questions qu'un descriptor doit répondre

Un bon descriptor doit répondre clairement à ces 4 questions :

### 1. Qui peut voir quoi ?
(permissions, visibilité)

### 2. Comment cette entité se liste ?
(tableau)

### 3. Comment elle s'édite ?
(formulaire simple)

### 4. Comment elle s'édite en masse ?
(quickedit)

👉 **Tout le reste est hors scope.**

---

## 🏗️ Structure recommandée du descriptor

### Vue d'ensemble

```javascript
export class ResourceDescriptor extends EntityDescriptor {
  static entity = 'resource'

  static table(ctx) {
    return TableConfig.create()
      .withPermissions({ quickEdit: ctx.canUpdateAny })
      .withColumns(columns => {
        // Configuration des colonnes
      })
  }

  static form(ctx) {
    return FormConfig.create()
      .group('Général', group => {
        // Configuration des champs
      })
  }

  static bulk(ctx) {
    return BulkConfig.create()
      .group('Général', group => {
        // Configuration du bulk edit
      })
  }
}
```

**Avantages :**
- ✅ Pas de gros objet JSON tentaculaire
- ✅ Des **builders explicites**, lisibles, testables
- ✅ Structure claire et modulaire

---

## 📊 TableConfig : la pièce maîtresse

### Philosophie

Le tableau est **la seule vue générée automatiquement**, donc :

* Il mérite une config riche
* Mais **plate et lisible**

### Structure recommandée

```javascript
TableConfig.create()
  .withPermissions({
    read: true,
    quickEdit: canUpdateAny
  })
  .withColumns(columns => {
    columns
      .add('image', col => col
        .header('Image')
        .visibleFrom('md')
        .display({
          xs: 'icon',
          md: 'thumb'
        })
      )

      .add('name', col => col
        .header('Nom')
        .sortable()
        .searchable()
        .display('route')
      )

      .add('rarity', col => col
        .header('Rareté')
        .sortable()
        .filterable('select')
        .display({
          xs: 'icon',
          sm: 'badge'
        })
      )
  })
```

### Ce que tu gagnes

✔ Lecture linéaire
✔ Chaque colonne est auto-documentée
✔ Facile à modifier sans effet domino

---

## 📋 TableColumnConfig : règles claires

### Ce qu'une colonne peut dire

```javascript
col
  .header(label, { icon?, helper? })
  .sortable()
  .searchable()
  .filterable(type, options?)
  .visibleFrom('sm') // xs | sm | md | lg | xl | never
  .display({
    xs: 'icon',
    md: 'badge',
    xl: 'text'
  })
  .customComponent(component, props?)  // Optionnel : composant personnalisé
```

### Ce qu'elle ne fait jamais

❌ Appeler un formatter
❌ Accéder à `entity`
❌ Faire du rendu
❌ Contenir de la logique métier

---

## 📝 FormConfig : édition simple

### Structure saine

```javascript
FormConfig.create()
  .group('Général', group => {
    group
      .field('name', f => f
        .type('text')
        .required()
        .label('Nom')
      )

      .field('rarity', f => f
        .type('select')
        .options(RarityFormatter.OPTIONS)  // Référence aux constantes, pas au formatter
        .label('Rareté')
      )

      .field('level', f => f
        .type('number')
        .min(0)
        .max(200)
        .label('Niveau')
      )
  })
  .group('Métadonnées', group => {
    // Autres champs
  })
```

### Règles

✔ Le form décrit les champs
✔ Le modèle transforme les valeurs
✔ Le mapper prépare le payload
✔ Référence aux constantes (ex: `RarityFormatter.OPTIONS`), pas aux méthodes

---

## 🔧 BulkConfig : quickedit maîtrisé

```javascript
BulkConfig.create()
  .group('Général', group => {
    group
      .field('rarity', f => f
        .enabled()
        .nullable()
      )

      .field('level', f => f
        .enabled()
        .default(null)
      )

      .field('isVisible', f => f
        .enabled()
        .default(true)
      )
  })
```

👉 Le bulk **n'est pas un form bis**, c'est un outil chirurgical.

**Règles :**
- ✅ Seulement les champs qui ont du sens en bulk
- ✅ Pas de validation complexe
- ✅ Pas de relations complexes

---

## 🚫 Ce que le descriptor ne doit JAMAIS contenir

### Champs pour les vues Large / Compact / Minimal / Text
❌ Ces vues sont manuelles, le descriptor ne les décrit pas

### Ordre d'affichage visuel
❌ Le descriptor ne décrit pas le layout des vues

### Helpers UI
❌ Pas de fonctions de formatage dans le descriptor

### Appels à des modèles
❌ Le descriptor ne manipule pas d'instances de modèles

### Formatters concrets
❌ Le descriptor peut référencer des constantes (ex: `RarityFormatter.OPTIONS`), mais pas appeler des méthodes de formatage

### Logique métier
❌ Pas de conditions complexes, pas de calculs

### État
❌ Pas de variables mutables, pas d'effet de bord

---

## ✅ Exemple complet (condensé)

```javascript
export class ResourceDescriptor extends EntityDescriptor {
  static entity = 'resource'

  static table(ctx) {
    return TableConfig.create()
      .withPermissions({ 
        read: true,
        quickEdit: ctx.capabilities?.updateAny ?? false 
      })
      .withColumns(c => {
        c.add('image', col => col
          .header('Image')
          .visibleFrom('md')
          .display({ xs: 'icon', md: 'thumb' })
        )

        c.add('name', col => col
          .header('Nom')
          .sortable()
          .searchable()
          .display('route')
        )

        c.add('rarity', col => col
          .header('Rareté')
          .sortable()
          .filterable('select')
          .display({ xs: 'icon', sm: 'badge' })
        )

        c.add('level', col => col
          .header('Niveau')
          .sortable()
          .display('badge')
        )
      })
  }

  static form(ctx) {
    return FormConfig.create()
      .group('Général', g => {
        g.field('name', f => f
          .type('text')
          .required()
          .label('Nom')
        )

        g.field('rarity', f => f
          .type('select')
          .options(RarityFormatter.OPTIONS)  // Constante, pas méthode
          .label('Rareté')
        )

        g.field('level', f => f
          .type('number')
          .min(0)
          .max(200)
          .label('Niveau')
        )
      })
  }

  static bulk(ctx) {
    return BulkConfig.create()
      .group('Général', g => {
        g.field('rarity', f => f
          .enabled()
          .nullable()
        )

        g.field('level', f => f
          .enabled()
        )

        g.field('isVisible', f => f
          .enabled()
          .default(true)
        )
      })
  }
}
```

---

## 🎯 Signal que ton descriptor est "bon"

✅ Tu peux le lire sans connaître l'entité
✅ Tu peux le modifier sans toucher aux vues
✅ Tu peux ajouter un champ sans peur
✅ Il ne dépasse jamais 200 lignes
✅ Il est déterministe (même contexte = même résultat)
✅ Il ne contient aucune logique métier
✅ Il ne décrit pas les vues manuelles

Quand tu arrives à ça, ton système est **sain** 🪴

---

## 🔍 Linter mental : détecter un mauvais descriptor

### Questions à se poser

1. **Y a-t-il des `if` ou des conditions complexes ?**
   - ❌ Si oui → Déplacer vers le modèle ou le formatter

2. **Y a-t-il des appels à des méthodes de formatage ?**
   - ❌ Si oui → Utiliser des constantes à la place

3. **Y a-t-il des références aux vues Large/Compact/Minimal/Text ?**
   - ❌ Si oui → Supprimer, ces vues sont manuelles

4. **Y a-t-il des accès à des instances de modèles ?**
   - ❌ Si oui → Le descriptor ne doit pas manipuler d'instances

5. **Y a-t-il des calculs ou des transformations de données ?**
   - ❌ Si oui → Déplacer vers le modèle ou le mapper

6. **Y a-t-il des effets de bord ou de l'état mutable ?**
   - ❌ Si oui → Le descriptor doit être pur et déterministe

---

## 📚 Références

- [ARCHITECTURE_ENTITY_SYSTEM.md](./ARCHITECTURE_ENTITY_SYSTEM.md) — Vue d'ensemble de l'architecture
- [ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md](./ARCHITECTURE_ENTITIES_ATOMIC_DESIGN.md) — Structure des fichiers
