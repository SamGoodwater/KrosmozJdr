# Plan : Composant Entity Label (icône + label entité)

**Contexte** : Ajout d’un composant d’UI pour afficher le type d’entité (icône fine + couleur de thème), avec plusieurs variantes d’affichage et un fond en trapèze aux bords diffus. Les entités et couleurs sont définies dans `resources/scss/themes/_theme-entities.scss` ; les icônes sont dans `storage/app/public/images/icons/entities/`.

---

## 1. Objectifs

- **Composant réutilisable** : afficher “quelle entité” pour chaque ligne/contexte (tableaux, cartes, filtres).
- **Discret** : icônes fines, fond coloré en trapèze avec dégradé diffus en début/fin.
- **Trois variantes** : icône seule ; icône + label rectangulaire ; icône + label carré.
- **Source de vérité partagée** : un fichier JS exposant URL d’icône et couleur (nom de token DaisyUI) par entité, utilisable avec ou sans le composant.
- **Conformité** : Atomic Design, design system (Tailwind/DaisyUI, pas de classes dynamiques), accessibilité.

---

## 2. Analyse du design system et de l’existant

### 2.1 Thème entités (SCSS)

- **Fichier** : `resources/scss/themes/_theme-entities.scss`
- **Map** : `$theme-entities` — clés = type d’entité, valeurs = nom de couleur DaisyUI (zinc, green, indigo, emerald, amber, etc.).
- **Clés actuelles** : `section`, `page`, `npc`, `item`, `creature`, `shop`, `campaign`, `resource`, `mob`, `specialization`, `spell`, `user`, `attribute`, `capitalize`, `classe`, `consumable`, `scenario`, `condition`.
- **Classes générées** (ex. `_color.scss`, `_box.scss`, `_border.scss`) :  
  `bg-color-{entityKey}-{intensity}`, `color-{entityKey}-{intensity}`, `border-glass-{entityKey}-*`, etc.  
  **Contrainte** : pas de construction dynamique de noms de classes ; utiliser un mapping explicite entité → classe complète dans le composant (comme `entityBgClasses` dans `resources/js/Pages/Admin/characteristics/Index.vue`).

### 2.2 Icônes entités

- **Stockage** : `storage/app/public/images/icons/entities/`
- **URL publique** : `/storage/images/icons/entities/{nom}.{ext}` (après `php artisan storage:link`).
- **Convention** : nom de fichier = clé d’entité (ex. `npc.svg`, `item.svg`, `spell.svg`). Format à confirmer (svg recommandé pour finesse).

### 2.3 Atomic Design

- **Atoms** : plus petite unité (Icon, Badge, Image, EntityUsableDot, etc.) — une responsabilité.
- **Molecules** : combinaison d’atoms (EntityCard avec `type: { name, color }`, champs, etc.).
- **Index** : `atoms.index.json` et `molecules.index.json` à mettre à jour pour le nouveau composant.
- **Référence** : `EntityUsableDot` (atom discret avec couleur + tooltip) et `Badge` (couleur, size, pas de classes dynamiques).

### 2.4 Règles UI

- **DESIGN_GUIDE.md** : Tailwind + DaisyUI ; couleurs/espaces via tokens ; glassmorphism pour thème entités.
- **ui-design-system.mdc** : pas de classes dynamiques ; accessibilité (contraste, aria, clavier).
- **entity-views.mdc** : descriptors (label, icon) ; pattern “icône + label” pour les metas.

---

## 3. Fichier JS de constantes entités (config)

**Objectif** : une seule source pour “entité → icône + couleur (+ label optionnel)”, utilisable par le composant et par tout autre code (filtres, exports, analytics, etc.).

- **Emplacement proposé** : `resources/js/config/entities.js` (ou `resources/js/constants/entityConfig.js` selon conventions du projet).
- **Contenu** :
  - Liste des clés d’entité alignées sur `$theme-entities` (au moins celles utilisées en UI).
  - Pour chaque entité :
    - **iconPath** ou **iconUrl** : chemin relatif ou URL (ex. `icons/entities/npc.svg` → URL `/storage/images/icons/entities/npc.svg`). Préférer une fonction `getEntityIconUrl(entityKey)` qui préfixe la base.
    - **color** : nom du token DaisyUI (ex. `green`, `indigo`) — même valeur que dans `_theme-entities.scss`.
    - **label** (optionnel) : libellé d’affichage en français (ex. `PNJ`, `Sort`), pour cohérence avec i18n plus tard.
- **API proposée** :
  - `ENTITY_KEYS` : tableau des clés reconnues.
  - `getEntityConfig(entityKey)` : `{ iconUrl, color, label }` (ou valeurs par défaut si inconnu).
  - `getEntityIconUrl(entityKey)` : URL complète de l’icône.
  - `getEntityColor(entityKey)` : nom de couleur (pour usage hors composant, ex. filtre ou style inline si nécessaire).
- **Alignement** : les clés et couleurs doivent rester synchronisées avec `_theme-entities.scss` (documenter la synchro dans le fichier ou dans ce plan).

---

## 4. Composant UI : niveau Atom ou Molecule ?

- **Atom** si le composant = “une seule chose” : “bloc entité” (icône + fond trapèze + optionnellement label).  
- **Molecule** si on considère que c’est une composition (Image/Icon + Badge/span + forme trapèze).  

**Recommandation** : **Atom** nommé **EntityLabel** (ou **EntityTypeBadge**), car il a une responsabilité unique (“afficher le type d’entité”) et une API simple (entity, variant, size). La forme trapèze + dégradé fait partie du rendu de cet atom.

- **Emplacement** : `resources/js/Pages/Atoms/data-display/EntityLabel.vue` (ou `EntityTypeIcon.vue` si on privilégie “icône” dans le nom).
- **Index** : ajout dans `resources/js/Pages/Atoms/atoms.index.json` avec description, props, slots.

---

## 5. Spécification du composant EntityLabel

### 5.1 Props (obligatoires + optionnelles)

| Prop       | Type   | Obligatoire | Description |
|-----------|--------|-------------|-------------|
| `entity`  | String | Oui         | Clé d’entité (ex. `npc`, `item`, `spell`). Doit correspondre aux clés de `entities.js` / `$theme-entities`. |
| `variant` | String | Non         | `'icon-only'` \| `'icon-rect'` \| `'icon-square'`. Défaut : `'icon-only'`. |
| `size`    | String | Non         | Taille : `'xs'` \| `'sm'` \| `'md'` \| `'lg'` \| `'xl'`. Défaut : `'md'`. |

- **entity** : nécessaire pour icône + couleur.
- **variant** :  
  - **icon-only** : trapèze + icône uniquement (le plus discret).  
  - **icon-rect** : trapèze + icône + label en rectangle (texte à côté).  
  - **icon-square** : trapèze + icône + label dans une forme carrée (ex. badge carré à côté ou en dessous selon maquette).
- **size** : pilote la taille de l’icône et éventuellement du label / du trapèze (mapping explicite vers des classes Tailwind, pas de concaténation dynamique).

Autres props utiles (optionnel) :

- **label** : string ; override du label affiché (sinon usage de `getEntityConfig(entity).label`).
- **tag** : élément racine (`span`, `div`) pour sémantique.
- **ariaLabel** / **id** : pour accessibilité (héritage de commonProps si le projet les utilise).

### 5.2 Slots (optionnel)

- **default** : remplace le label en variantes rect/square (pour custom texte ou contenu).
- Aucun slot obligatoire.

### 5.3 Comportement visuel

- **Fond** : forme en trapèze qui entoure uniquement la zone icône (et label si présent). Les bords du trapèze “commencent et terminent de façon diffuse” (dégradé doux en début/fin, pas de bord net).  
  Implémentation possible :  
  - `clip-path: polygon(...)` pour le trapèze + `mask-image` avec dégradé linéaire (transparent → opaque → transparent) pour adoucir les bords, ou  
  - fond avec `linear-gradient` et forme trapèze en masque.
- **Couleur de fond** : celle de l’entité (via classe `bg-color-{entity}-*`). Toutes les combinaisons utilisées doivent être listées en dur dans le composant (map entity → classe), pour respecter la règle “pas de classes dynamiques”.
- **Icône** : image via `<img>` ou atom `Image` ; `src` = `getEntityIconUrl(entity)` ; `alt` = label entité ou `ariaLabel` pour accessibilité.
- **Tailles** : mapping `size` → classes Tailwind (ex. `w-4 h-4` pour xs, `w-5 h-5` pour sm, etc.) et éventuellement `text-xs` / `text-sm` pour le label.

### 5.4 Accessibilité

- Texte alternatif pour l’icône (attribut `alt` ou `aria-label` sur le conteneur).
- Si le bloc est purement décoratif dans un tableau, possibilité de `aria-hidden="true"` et de fournir le type d’entité dans une cellule accessible (texte ou aria-label sur la ligne).

---

## 6. Plan d’implémentation (ordre suggéré)

1. **Créer `resources/js/config/entities.js`**  
   - Aligner les clés sur `_theme-entities.scss`.  
   - Exposer `ENTITY_KEYS`, `getEntityConfig(entityKey)`, `getEntityIconUrl(entityKey)`, `getEntityColor(entityKey)`.  
   - Utiliser une base d’URL pour les icônes (ex. `/storage/images/icons/entities/`) et un suffixe par défaut (ex. `.svg`).

2. **Créer l’atom EntityLabel**  
   - Fichier : `resources/js/Pages/Atoms/data-display/EntityLabel.vue`.  
   - Importer le config entités ; props `entity` (required), `variant`, `size` ; mapping explicite entity → classe de fond (toutes les clés du thème).  
   - Implémenter le trapèze + dégradé diffus (CSS : clip-path + mask ou gradient).  
   - Trois variantes de template (icon-only, icon-rect, icon-square).  
   - Respecter Tailwind/DaisyUI sans construire de noms de classes dynamiquement.

3. **Tester visuellement**  
   - Une page ou Storybook : afficher EntityLabel pour quelques entités (npc, item, spell, resource, etc.) en chaque variant et size.  
   - Vérifier contraste (fond coloré + icône fine) et lisibilité.

4. **Documenter et indexer**  
   - Mettre à jour `atoms.index.json` (nom, description, props, slots, chemin).  
   - Référencer ce plan dans `docs.index.json` et, si besoin, ajouter une section dans `DESIGN_GUIDE.md` ou `ENTITY_VIEWS.md` pour le pattern “type d’entité en ligne”.

5. **Intégration**  
   - Remplacer ou compléter les endroits qui affichent déjà le type d’entité (ex. tableaux de résultats, EntityCard, listes) pour utiliser EntityLabel quand c’est pertinent.

---

## 7. Fichiers impactés (résumé)

| Fichier | Action |
|--------|--------|
| `resources/js/config/entities.js` | Créer (constantes + getEntityIconUrl, getEntityConfig, getEntityColor) |
| `resources/js/Pages/Atoms/data-display/EntityLabel.vue` | Créer (atom) |
| `resources/js/Pages/Atoms/atoms.index.json` | Ajouter entrée EntityLabel |
| `resources/scss/themes/_theme-entities.scss` | Aucune modification ; rester la source de vérité des couleurs (JS en miroir) |
| `docs/docs.index.json` | Ajouter entrée vers ce plan |
| `docs/30-UI/DESIGN_GUIDE.md` ou `ENTITY_VIEWS.md` | Optionnel : court paragraphe sur le composant EntityLabel |

---

## 8. Notes techniques

- **Fallback** : si `entity` inconnu ou icône manquante, afficher une icône générique et une classe de couleur neutre (ex. `bg-base-300`).
- **i18n** : les labels dans `entities.js` peuvent être des clés i18n plus tard (ex. `$t('entities.npc')` dans le composant).
- **Extensions** : ajout d’une nouvelle entité = ajout dans `_theme-entities.scss` + dans `entities.js` + éventuellement une nouvelle icône dans `storage/.../icons/entities/` + une entrée dans le mapping des classes de fond dans EntityLabel.

Ce plan peut servir de base pour l’implémentation et les revues de code. Les noms de composant (EntityLabel vs EntityTypeBadge) et le chemin exact du config (config vs constants) peuvent être ajustés selon les conventions du projet.
