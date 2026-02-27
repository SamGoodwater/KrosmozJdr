# Vérification du moteur de recherche d'entités

**Date** : 2026-02-27

## 1. Cohérence

### Backend (contrat API)

- **Contrat commun** : tous les `*TableController` utilisés par le moteur exposent `format=entities`, `search`, `filters`, `limit`, `sort`, `order` ; la plupart supportent `whitelist` / `blacklist` (ResourceType, ItemType, ConsumableType, MonsterRace, Monster, etc.).
- **Campaign / Scenario** : visibilité cohérente — `is_public`, `created_by`, ou présence dans la table pivot `*_user` (campaign_user, scenario_user) ; `read_level` appliqué dans tous les cas.
- **Policies** : chaque contrôleur table appelle `$this->authorize('viewAny', Model::class)` ; les réponses sont donc filtrées par les droits existants.

### Frontend

- **useEntitySearch** : appelle `api.tables.{entityType}` avec les paramètres du contrat ; `buildParams()` fusionne filtres, whitelist, blacklist ; l’override dans `search(override)` permet de forcer `filters[id]` et `search` au chargement initial (picker avec valeur existante).
- **EntityPickerCore** : synchronise les props (filters, sort, order, whitelist, blacklist) vers le composable via `watch` ; `selectedLabel` utilise `entity.name` ou `entity.creature?.name` (monstres) ; émet `update:modelValue` et `update:selectedEntities`.

### Points d’attention

- **Pages / Sections** : pas d’endpoints `api.tables.pages` ni `api.tables.sections` pour l’instant. Si ajout ultérieur, appliquer le scope `readableFor($user)` (read_level + table pivot users) comme pour Campaign/Scenario.
- **selectedEntities** : après une sélection, si l’utilisateur modifie la recherche, `results` peut ne plus contenir l’entité sélectionnée ; `fullSelected` émis reste alors partiel. Comportement acceptable pour le picker (affichage du libellé via `selectedLabel` et valeur via `modelValue`).

---

## 2. Optimisation

- **Debounce** : présent dans `useEntitySearch` (délai configurable), évite les appels en rafale.
- **Limite** : `limit` par requête (défaut 20 dans le picker, 5000 côté backend) ; adapté à l’usage.
- **Pas d’AbortController** : les requêtes précédentes ne sont pas annulées à une nouvelle frappe ; acceptable pour un usage formulaire ; peut être ajouté plus tard si besoin (recherche globale header).

---

## 3. Simplicité

- Un seul composable de recherche (`useEntitySearch`) et un seul composant cœur (`EntityPickerCore`) ; pas de duplication de logique d’appel API.
- Contrôleurs table : structure identique (format, search, filters, sort, whitelist/blacklist, réponse entities) ; duplication raisonnable pour garder chaque contrôleur lisible et autonome.
- **entityRouteRegistry** : centralise les noms de routes et paramètres ; à étendre pour la recherche globale (campaigns, scenarios, spells, etc.) pour les liens « show ».

---

## 4. Conclusion

Le code est **cohérent**, **suffisamment optimisé** et **simple**. Aucun correctif bloquant ; la recherche globale dans le header peut s’appuyer sur le même contrat API et sur un nouveau composable `useGlobalSearch` (appels parallèles à plusieurs `api.tables.*`).
