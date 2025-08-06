# Documentation du code (docbloc) — KrosmozJDR

## 1. Principes généraux
- Toujours documenter les composants, fonctions, classes, méthodes publiques.
- Docbloc synthétique : description, props/params, retour, exemple minimal.
- Pas d’historique, pas de commentaires inutiles.
- Lien vers la documentation projet si pattern ou API factorisée.

---

## 2. Frontend (Vue 3, JS)

- Utiliser le format JSDoc pour les composants, props, slots, events.
- Donne une description @description
- Exemples d’utilisation dans le docbloc si pertinent.
- Préciser les types, les valeurs par défaut, les slots, les events.
- Pour les atoms dérivant d'un composant DaisyUI ajouter dans le docBloc :
  - la lien vers le composant DaisyUI : @see https://daisyui.com/components/xxx
  - la version de DaisyUi utilisé : @version DaisyUI v5.x

### Exemple (composant Vue)
```js
/**
 * InputField — Champ de saisie complet (molecule)
 * @props {String|Object} label — Label du champ (positions multiples)
 * @props {Object|String|Boolean} validation — Validation factorisée
 * @event update:modelValue — Événement d’édition
 * @slot labelTop, labelBottom, overStart, overEnd
 * @see docs/40-UI/INPUTS.md
 * @example
 * <InputField label="Email" v-model="email" :validation="{ state: 'error', message: 'Email invalide' }" />
 */
```

---

## 3. Backend (PHP, Laravel)

- Utiliser le format PHPDoc pour les classes, méthodes, services, jobs, etc.
- Préciser les types, les retours, les exceptions, les usages importants.
- Exemples d’utilisation si pertinent.

### Exemple (méthode PHP)
```php
/**
 * Récupère l’utilisateur courant.
 *
 * @return User|null
 * @throws AuthException
 * @see docs/20-Entities/ENTITIES_OVERVIEW.md
 * @example
 * $user = $this->getCurrentUser();
 */
```

---

## 4. Conseils
- Un docbloc = une intention claire, pas de blabla.
- Toujours référencer la doc projet si le composant suit un pattern factorisé.
- Exemples courts, pas de duplication.
- Pour les conventions globales, voir aussi BEST_PRACTICES.md. 