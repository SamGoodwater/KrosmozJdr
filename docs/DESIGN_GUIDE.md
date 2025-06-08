# Guide de Design Krosmoz-JDR

## 1. Introduction

Le design du site s'appuie sur les principes d'Atomic Design, le glassmorphism et une forte accessibilité. L'interface est responsive et moderne.

## 2. Principes de design

### 2.1. Atomic Design

- Structure le frontend en atomes, molécules, organismes, templates et pages.
- Favorise la réutilisabilité et la clarté du code.
- Le projet suit strictement le principe Atomic Design pour l'organisation des composants Vue 3 (atoms, molecules, organisms, templates, pages).

### 2.2. Accessibilité

- Respecter les standards WCAG (contraste, taille de police, navigation clavier).
- Utiliser les attributs ARIA sur les composants interactifs.
- Vérifier la navigation clavier sur tous les layouts et composants.
- Accessibilité : contraste, adaptation automatique, écriture inclusive.

### 2.3. Glassmorphism

- Utilisation de panneaux semi-transparents avec un fort flou d'arrière-plan (`backdrop-filter`).
- Angles arrondis généreux (`border-radius`), ombres subtiles (`box-shadow`).
- Contours semi-opaques pour un contraste doux.
- Le site utilise des panneaux superposés, dont l'arrière-plan est flouté pour recréer un effet de « verre dépoli ».
- Chacun de ces panneaux présente des angles arrondis et une ombre portée subtile pour renforcer la sensation de profondeur et de flottement.
- De fins contours semi-opaques délimitent chaque bloc, tout en laissant transparaître le flou d'arrière-plan pour un contraste doux entre les calques.

### 2.4. Atomic Design – Structure des composants

Le design system est structuré selon la méthodologie Atomic Design :

- **Atoms** : éléments UI de base, réutilisables partout.
- **Molecules** : combinaisons simples d'atoms, unités fonctionnelles.
- **Organisms** : blocs complexes, assemblages de molecules et d'atoms.

## 3. Thèmes

- Deux thèmes principaux : dark (par défaut) et light.
- Variables CSS pour la gestion des couleurs, radius, ombres, etc.
- Utilisation de DaisyUI pour la gestion et la personnalisation des thèmes (voir config Tailwind/DaisyUI).
- Définition complète des thèmes DaisyUI :

### 3.1. Dark (défaut)

```css
name: "dark";
default: true;
prefersdark: true;
color-scheme: "dark";
--color-base-100: "#1f2937";
--color-base-200: "#19222e";
--color-base-300: "#141c26";
--color-base-content: "#cdd0d3";
--color-primary: "#60a5fa";
--color-primary-content: "#1f2937";
--color-secondary: "#bae6fd";
--color-secondary-content: "#1f2937";
--color-accent: "#d8b4fe";
--color-accent-content: "#110c16";
--color-neutral: "#a8a29e";
--color-neutral-content: "#0a0a09";
--color-info: "#2dd4bf";
--color-info-content: "#01100d";
--color-success: "#bef264";
--color-success-content: "0d1403";
--color-warning: "#fde047";
--color-warning-content: "#161202";
--color-error: "#f87171";
--color-error-content: "#150404";
--radius-selector: 1rem;
--radius-field: 0.5rem;
--radius-box: 1rem;
--size-selector: 0.25rem;
--size-field: 0.25rem;
--border: 1px;
--depth: 1;
--noise: 0;
```

### 3.2. Light

```css
name: "light";
color-scheme: "light";
--color-base-100: "#f3f4f6";
--color-base-200: "#d3d4d6";
--color-base-300: "#b4b5b7";
--color-base-content: "#cdd0d3";
--color-primary: "#3b82f6";
--color-primary-content: "#010615";
--color-secondary: "#38bdf8";
--color-secondary-content: "#010d15";
--color-accent: "#c084fc";
--color-accent-content: "#0e0616";
--color-neutral: "#d6d3d1";
--color-neutral-content: "#0a0a09";
--color-info: "#14b8a6";
--color-info-content: "#01100d";
--color-success: "#84cc16";
--color-success-content: "#0d1403";
--color-warning: "#facc15";
--color-warning-content: "#161202";
--color-error: "#f87171";
--color-error-content: "#150404";
--radius-selector: 1rem;
--radius-field: 0.5rem;
--radius-box: 1rem;
--size-selector: 0.25rem;
--size-field: 0.25rem;
--border: 1px;
--depth: 1;
--noise: 0;
```

## 4. Style général

- Icônes FontAwesome et icônes Dofus spécifiques (stockées dans Laravel storage).
- Images compressées et converties en webp si possible.

### Structure détaillée des assets

- **Logos** :
  - `storage/images/Logos/logo.webp`, `.svg`, `.png`, `.ico`
- **Aires des sorts** :
  - `storage/images/Icones/zones/X.svg` (X = nom de la zone)
- **Caractéristiques** :
  - `storage/images/Icones/caracteristiques/X.png` (X = nom de la caractéristique)
- **Fichiers uploadés** :
  - Pour une entité : `storage/entities/nom_de_l_entite/`
  - Pour une page : `storage/pages/`
  - Pour les autres fichiers : `storage/files/`

## 5. Animations

- Utiliser des animations douces pour les transitions (hover, focus, ouverture de menu, etc.).
- Privilégier les transitions CSS natives (`transition`, `transform`, `opacity`).
- Exemples :
  - Hover sur bouton : légère élévation et ombre.
  - Ouverture de menu : fade + slide.

## 6. Structure du layout

### 6.1. Aside

- Logo, nom du projet, menu dynamique et menu statique.
- Pages statiques : contribution, outils, page à définir.
- Responsive :
  - Menu rétractable (icône seule)
  - Sur tablette : menu flottant
  - Sur mobile : menu fullscreen, caché par défaut

### 6.2. Header

- De gauche à droite : titre de la page, zone de recherche (extensible avec filtres), zone de gestion du compte (connexion, pseudo, menu déroulant).
- Fixed en haut sur desktop, en bas sous forme d'icônes sur mobile.
- Responsive :
  - Icône menu aside
  - Icône recherche
  - Icône gestion du compte

### 6.3. Footer

- Footer classique, sobre.

### 6.4. Notifications

- Les notifications sont gérées de façon centralisée via l'organism `NotificationContainer`.
- Il n'y a plus de slots de notifications dans le layout principal (`Main.vue`).
- `NotificationContainer` gère l'affichage, la pile, la transition, l'accessibilité et le positionnement (top-end, top-start, bottom-end, bottom-start) de toutes les notifications de l'application.
- Pour afficher une notification, il suffit d'utiliser le store/composable dédié (voir la doc du composable Pinia ou maison).
- Ce pattern garantit la cohérence, la maintenabilité et l'accessibilité des notifications sur tout le site.

## 7. Outils et technologies design

- Utilisation de **Tailwindcss** pour la gestion des utilitaires CSS ([doc](https://v3.tailwindcss.com/docs/installation)).
- Utilisation du plugin **DaisyUI** pour la gestion et la personnalisation des thèmes ([doc](https://daisyui.com/docs/install/)).
- Design général basé sur le **glassmorphism** (voir [css.glass](https://css.glass/) et [cluely.com](https://cluely.com/?_bhlid=de8aa2acd6ff066978222f60217ea27a9d49f2c2)).

## 8. Liens et ressources

- [Tailwind CSS](https://v3.tailwindcss.com/docs/installation)
- [DaisyUI](https://daisyui.com/docs/install/)
- [css.glass](https://css.glass/)
- [cluely.com](https://cluely.com/?_bhlid=de8aa2acd6ff066978222f60217ea27a9d49f2c2)

## 9. Atomic Design – Atoms de base

### Philosophie de création des atoms

- Les composants de base (atoms) sont construits à partir des composants DaisyUI ([voir la doc officielle](https://daisyui.com/components/button/)).
- **Chaque atom expose uniquement :**
  - Les paramètres natifs DaisyUI (mêmes noms et valeurs que la doc DaisyUI)
  - Les paramètres d'accessibilité (aria-*, role, etc.)
  - Les paramètres HTML natifs nécessaires (type, href, etc.)
- **Les classes CSS doivent être écrites en toutes lettres dans le code (pas de calcul dynamique)** pour garantir la détection par Tailwind lors du build.
- **Chaque atom intègre nativement un composant Tooltip** (voir DaisyUI Tooltip), avec les props :
  - `tooltip` (contenu simple ou complexe)
  - `tooltip_placement` (et non `location`, pour respecter la doc DaisyUI)
- **Gestion flexible des props/template :**
  - Pour chaque prop pouvant recevoir du contenu complexe (ex : tooltip, label, icon, etc.), accepter à la fois une valeur simple (string) et un slot/template nommé (ex : `#content`, `#label`).
  - Privilégier les noms logiques pour les slots : `content`, `label`, etc.
  - Les props communes à tous les atoms sont incluses grâce à commonProps.js
- **Props de position :**
  - Privilégier l'utilisation de `vertical` et `horizontal` comme props pour la gestion des positions, quand c'est possible (ex : placement d'un élément, alignement, etc.).
- **Utilitaires custom :**
  - N'utiliser `getCustomUtilityProps()` et `getCustomUtilityClasses()` que si l'atom le nécessite (ex : ombre, backdrop, opacity, etc.), **pas systématiquement** (inutile sur un Checkbox, par exemple).
- **Pattern de composition des classes :**
  - Utiliser `mergeClasses` pour composer les classes :
    - 1er argument : toutes les classes DaisyUI explicites (jamais de concaténation dynamique non couverte par Tailwind).
    - 2e argument : `getCustomUtilityClasses(props)` **si pertinent**.
    - 3e argument : `props.class` pour la personnalisation utilisateur.
- **Accessibilité :**
  - Utiliser `getCommonAttrs(props)` pour générer les attributs HTML/accessibilité à appliquer à l'élément principal.
- **Événements natifs :**
  - Toujours utiliser `v-on="$attrs"` sur l'élément principal pour garantir la transmission des événements natifs (`@click`, etc.).
  - Toujours utiliser `defineOptions({ inheritAttrs: false })` dans le script setup.
- **Tooltip :**
  - Tous les atoms (sauf Tooltip lui-même) doivent intégrer le composant `Tooltip` avec les props et slot appropriés.
- **DocBlock :**
  - Toujours documenter le composant, ses props, slots, et donner un exemple d'utilisation.

**Exemple de structure d'atom à jour :**

```vue
<script setup>
defineOptions({ inheritAttrs: false });
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
const props = defineProps({
  ...getCommonProps(),
  color: { type: String, default: '', validator: v => ['', 'primary', 'secondary', ...].includes(v) },
  vertical: { type: String, default: '', validator: v => ['', 'top', 'middle', 'bottom'].includes(v) },
  horizontal: { type: String, default: '', validator: v => ['', 'start', 'center', 'end'].includes(v) },
  // ...getCustomUtilityProps() UNIQUEMENT si pertinent
});
const atomClasses = computed(() =>
  mergeClasses(
    [
      'btn',
      props.color === 'primary' && 'btn-primary',
      // ...autres classes DaisyUI explicites
    ].filter(Boolean),
    // getCustomUtilityClasses(props) SI pertinent
    props.class
  )
);
const attrs = computed(() => getCommonAttrs(props));
</script>
<template>
  <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
    <button :class="atomClasses" v-bind="attrs" v-on="$attrs">
      <slot name="content" />
    </button>
    <template v-if="typeof props.tooltip === 'object'" #tooltip>
      <slot name="tooltip" />
    </template>
  </Tooltip>
</template>
```

**Checklist des bonnes pratiques (mise à jour) :**
- Pas de concaténation dynamique de classes DaisyUI/Tailwind non couverte par Tailwind.
- mergeClasses obligatoire, avec classes DaisyUI explicites, utilitaires custom si pertinent, puis props.class.
- getCustomUtilityProps() et getCustomUtilityClasses() uniquement si pertinent.
- Props de position : privilégier vertical/horizontal.
- Slots : privilégier les noms logiques (content, label, etc.).
- v-on="$attrs" sur l'élément principal, defineOptions({ inheritAttrs: false }) dans le script setup.
- Tooltip intégré partout sauf Tooltip lui-même.
- DocBlock, accessibilité, pattern DRY, etc. toujours présents.

### Système atomManager, InputLabel et Validator

- **atomManager** centralise toutes les props communes, utilitaires (shadow, backdrop, opacity), et helpers pour les Atoms d'input (getInputProps, getInputAttrs, etc.).
- Tous les inputs utilisent **InputLabel** pour le rendu du label (accessibilité, cohérence, DaisyUI) et **Validator** pour l'affichage des messages d'aide/erreur/validation.
- Ce pattern garantit :
  - DRY (aucune duplication de props ou de logique)
  - Accessibilité native (labels reliés, aria, etc.)
  - Cohérence visuelle et API claire sur tous les champs
- **Schéma du flux** :

  atomManager → InputField (ou autre input atom) → InputLabel / Validator

- **Exemple minimal** :

```vue
<InputField label="Nom" v-model="name" :validator="form.errors.name" help="Votre nom complet" />
```

> Tous les nouveaux inputs doivent suivre ce schéma pour garantir la maintenabilité et la cohérence du design system.

### Edition réactive des inputs (pattern DRY, UX moderne)

- **Tous les atoms d'input** (InputField, Textarea, NumberInput, etc.) peuvent activer un mode "édition réactive" via la prop `useFieldComposable`.
- Ce mode permet :
  - **Debounce** automatique sur la saisie (évite les requêtes ou updates trop fréquents)
  - **Bouton reset** qui apparaît si la valeur a été modifiée (UX moderne)
  - **Gestion centralisée de la valeur, du reset, de l'état modifié, etc.**
  - **Callback onUpdate** pour brancher le v-model natif ou une logique custom
- Toute la logique métier (debounce, reset, update, isModified, etc.) est **centralisée dans le composable** `useEditableField` (voir `/Composables/form/useEditableField.js`).
- Le composant atomique ne gère que l'UI et branche les handlers du composable.
- **Exemple d'utilisation** :

```vue
<!-- InputField avec édition réactive, debounce 300ms, bouton reset -->
<InputField v-model="user.name" useFieldComposable :debounceTime="300" />
```

- **Avantages** :
  - DRY : aucune duplication de logique dans les composants
  - Réutilisable : le composable peut être utilisé dans tous les inputs, molécules, etc.
  - Cohérence UX : tous les champs réactifs ont le même comportement (reset, debounce, etc.)
  - Facile à tester et à faire évoluer

> **Note** : Ce pattern est recommandé pour tous les formulaires dynamiques, les champs éditables inline, ou les usages où l'utilisateur doit pouvoir annuler ses modifications facilement.

### Exemple de structure de composant atom (bouton)

```vue
<Tooltip :tooltip="tooltip" :tooltip-placement="tooltip_placement">
  <button
    type="button"
    class="btn btn-primary btn-lg"
    aria-label="..."
    @click="onClick"
    ...
  >
    <span v-if="label">{{ label }}</span>
    <slot v-else />
  </button>
  <template v-if="typeof tooltip === 'object'" #content>
    <slot name="content" />
  </template>
</Tooltip>
```

- Ici, `tooltip` peut être une string ou un template complexe via le slot `#content`.
- Les classes sont écrites en toutes lettres (ex : `btn btn-primary btn-lg`).
- Les props DaisyUI sont exposées telles quelles (ex : `color`, `size`, `variant`, ...).
- Les props d'accessibilité et HTML natif sont ajoutées si besoin.

**Respecter ces règles pour tous les nouveaux Atoms.**

### Liste initiale des atoms (exemples)

- **Button** : tous les variants DaisyUI (primary, secondary, accent, outline, ghost, etc.), toutes les tailles, tous les modifiers (block, wide, square, circle, etc.), tooltip intégré.
- **Input** : supporte placeholder, type, value, disabled, etc. + tooltip.
- **Checkbox** : paramètres DaisyUI + tooltip.
- **Radio** : idem.
- **Select** : idem.
- **Textarea** : idem.
- **Badge** : couleurs, variants DaisyUI + tooltip.
- **Avatar** : image, alt, size, shape + tooltip.
- **Icon** : FontAwesome ou Dofus, nom, taille, couleur + tooltip.
- **Label** : for, text, color + tooltip.
- **Switch/Toggle** : paramètres DaisyUI + tooltip.
- **Tooltip** : composant atom dédié, utilisé partout.
- **Kbd** : raccourci clavier, style DaisyUI + tooltip.
- **Progress** : valeur, max, couleur + tooltip.
- **Divider** : orientation, style DaisyUI + tooltip.
- **Paragraphe** : Permet d'avoir les styles de base du thème DaisyUI.

> Cette liste est évolutive et sera complétée au fil des besoins.

### Pattern de composition des classes : mergeClasses

Tous les composants atomiques (atoms) doivent utiliser la fonction utilitaire `mergeClasses` pour composer leurs classes CSS :

- **Le premier argument** liste explicitement toutes les classes DaisyUI (et variantes) nécessaires, sans aucune génération dynamique de noms de classes (pour garantir la détection par Tailwind).
- **Le second argument** fusionne les utilitaires custom (shadow, backdrop, opacity, etc.) via `getCustomUtilityClasses(props)`.
- **Le dernier argument** fusionne les classes personnalisées passées via `props.class` (priorité à l'utilisateur).

**Exemple :**
```js
const atomClasses = computed(() =>
  mergeClasses(
    [
      'btn',
      props.color === 'primary' && 'btn-primary',
      props.size === 'lg' && 'btn-lg',
      // ...autres variantes DaisyUI explicites
    ].filter(Boolean),
    getCustomUtilityClasses(props),
    props.class
  )
);
```

> Ce pattern est **obligatoire** pour tous les atoms du design system. Il garantit la compatibilité avec Tailwind (aucune classe manquante au build), la maintenabilité, la cohérence et la prévisibilité du rendu sur l'ensemble du projet.

**Toute nouvelle contribution ou refactorisation d'atom doit suivre ce schéma.**

## Atomic Design – Helpers et organisation des utilitaires

### 1. Centralisation des helpers

- **Tous les helpers universels** (props communes, accessibilité, mergeClasses, utilitaires custom, etc.) sont centralisés dans `uiHelper.js` (dossier `/Utils/atomic-design/`).
- **Les helpers spécifiques aux inputs** (`getInputProps`, `getInputAttrs`) sont dans `atomManager.js` (même dossier).
- **Des managers spécifiques** (`moleculeManager.js`, `organismManager.js`) peuvent être créés si des besoins propres à ces niveaux apparaissent, mais ils doivent réutiliser les helpers de `uiHelper.js`.
- **Aucune duplication** : factoriser tout helper réutilisable dans `uiHelper.js`.

### 2. Utilisation dans les composants

- **Atoms** :
  - Importent `getCommonProps`, `getCommonAttrs`, `mergeClasses`, `getCustomUtilityProps`, `getCustomUtilityClasses` depuis `uiHelper.js`.
  - Les atoms de type input importent aussi `getInputProps`, `getInputAttrs` depuis `atomManager.js`.
  - Exemple :
    ```js
    import { getCommonProps, getCommonAttrs, mergeClasses, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atomic-design/uiHelper';
    import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/atomManager';
    ```

- **Molecules** :
  - Importent les helpers de `uiHelper.js`.
  - Peuvent importer des helpers de `moleculeManager.js` si besoin spécifique.
  - Certaines molecules sont des composants DaisyUI (ex : Fieldset, FileInput, AvatarGroup, Modal, etc.) et doivent suivre la même rigueur d'API et de documentation que les atoms.
  - Exemple :
    ```js
    import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
    ```

- **Organisms** :
  - Importent les helpers de `uiHelper.js`.
  - Peuvent importer des helpers de `organismManager.js` si besoin spécifique.

### 3. Philosophie DRY et factorisation

- **Un helper ne doit jamais être dupliqué** : s'il est utile à plusieurs niveaux, il va dans `uiHelper.js`.
- **Les managers spécifiques** (atomManager, moleculeManager, organismManager) ne doivent contenir que des helpers propres à leur niveau.
- **La documentation de chaque composant** doit préciser l'origine des helpers utilisés.

### 4. DaisyUI et molecules

- Certaines molecules sont des wrappers ou des compositions directes de composants DaisyUI (ex : Fieldset, FileInput, AvatarGroup, Modal, etc.).
- Elles doivent respecter la même rigueur d'API, de slots, de props et de documentation que les atoms.
- Leur docBlock doit inclure un lien vers la documentation DaisyUI correspondante si applicable.

### 5. Exemple d'import pour chaque niveau

- **Atom** :
  ```js
  import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
  import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/atomManager';
  ```
- **Molecule** :
  ```js
  import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
  ```
- **Organism** :
  ```js
  import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
  ```

---

**En résumé** :
- Tous les composants UI (atoms, molecules, organisms) partagent les mêmes helpers universels via `uiHelper.js`.
- Les helpers spécifiques (inputs, contextes, etc.) sont dans des managers dédiés.
- Les molecules DaisyUI sont documentées et rigoureusement typées comme les atoms.

## 10. Atomic Design – Molecules principaux

La liste des molecules est indicative et sera affinée au fil du développement. Certaines molecules sont conçues pour être utilisées dans le système de sections dynamiques (PageSection).

- **InputGroup** : Champ de saisie avec label, icône, aide ou validation.
- **SearchInput** : Input + bouton ou icône de recherche, éventuellement avec suggestions.
- **FormField** : Label + input/select/textarea + message d'erreur.
- **EntityPreview** : Mini-carte ou résumé d'entité (image, titre, actions rapides).
- **SectionTitle** : Titre de section avec options (icône, actions, aide).
- **SectionActions** : Groupe de boutons/actions contextuelles pour une section.
- **FileInput** : Input de fichier avec preview et validation.
- **AvatarWithName** : Avatar + nom d'utilisateur ou d'entité.
- **BadgeList** : Liste de badges (ex : tags, statuts).
- **TableRowActions** : Groupe d'actions pour une ligne de tableau (éditer, supprimer, etc.).
- **TabsNav** : Barre de navigation d'onglets.
- **PaginationControls** : Contrôles de pagination simples.
- **FilterGroup** : Groupe de filtres (selects, checkboxes, etc.).
- **StatDisplay** : Affichage d'une statistique ou d'un score (icône, valeur, label).

> Cette liste est volontairement non exhaustive et évoluera selon les besoins du projet et du système de sections dynamiques. Chaque molecule doit être pensée pour être réutilisable dans différents organisms ou sections.

## 11. Atomic Design – Organisms principaux

Voici la liste initiale des organisms, avec une courte description pour chacun :

- **Header** : Barre supérieure contenant le titre de la page, la barre de recherche (avec filtres), la gestion du compte (avatar, menu utilisateur), le switcher de langue, les notifications. Responsive (fixé en haut sur desktop, en bas sur mobile).
- **Aside / Sidebar** : Menu latéral dynamique pour la navigation principale, liens statiques, logo, version, gestion du menu rétractable, responsive, sous-menus.
- **Footer** : Pied de page sobre, liens utiles, copyright, mentions légales.
- **NotificationContainer** : Organism centralisé pour l'affichage, la pile, la transition et l'accessibilité des notifications (voir section 6.4). À utiliser dans le layout principal.
- **Card** : Bloc d'affichage d'entité (classe, monstre, objet, sort, etc.), affiche image, titre, description, actions (voir, éditer, supprimer…).
- **EntityTable** : Tableau dynamique pour lister les entités (tri, filtres, pagination, actions). Colonnes configurables selon l'entité.
- **Formulaire dynamique** : Formulaire de création/édition d'entité, généré à partir d'un schéma ou d'une config. Gestion des validations, messages d'erreur, champs dynamiques.
- **PageSection** : Bloc de section de page dynamique (texte riche, tableau, entité, fichier, etc.), utilisé dans le système de pages/sections.
- **Modal** : Fenêtre modale pour confirmation, édition rapide, affichage de détails, etc.
- **NotificationList** : Liste des notifications utilisateur (icônes, actions, marquage comme lu).
- **UserMenu** : Menu déroulant utilisateur (profil, paramètres, déconnexion, etc.).
- **SearchBar** : Barre de recherche avancée, avec suggestions, filtres, intégration dans le header.
- **EntityDetails** : Affichage détaillé d'une entité (fiche complète, tabs, actions contextuelles).
- **Tabs** : Système d'onglets pour naviguer entre différentes vues ou sections d'une page.
- **Breadcrumbs** : Fil d'Ariane pour la navigation contextuelle.
- **FileUploader** : Composant d'upload de fichiers (images, PDF), drag & drop, preview, validation.
- **WYSIWYGEditor** : Éditeur de texte riche (Tiptap), intégré dans les formulaires ou sections dynamiques.
- **ThemeSwitcher** : Sélecteur de thème (dark/light/custom), intégré dans le header ou le menu utilisateur.
- **LanguageSwitcher** : Sélecteur de langue, intégré dans le header ou le menu utilisateur.
- **Pagination** : Contrôles de pagination pour les listes et tableaux.
- **EntityFilter** : Filtres avancés pour les listes d'entités (par type, rareté, etc.).

> Cette liste est évolutive et pourra être complétée selon les besoins du projet.

## 9.1. Référence des atoms (API, props, slots)

> **La liste officielle des atoms, leur API, props, slots, liens DaisyUI et chemin de fichier source est centralisée dans le fichier :**
>
>    `resources/js/Pages/Atoms/atoms.index.json`
>
> Ce fichier JSON fait foi pour :
> - Générer la documentation automatique
> - Construire un styleguide ou une doc interactive
> - Vérifier la cohérence de l'API des atoms
> - Lister tous les composants atomiques du design system
>
> **À chaque ajout ou modification d'un atom, il faut mettre à jour ce fichier pour garantir la cohérence du design system.**
