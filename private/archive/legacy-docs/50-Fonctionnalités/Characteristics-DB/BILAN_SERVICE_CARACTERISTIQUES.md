# Bilan : service de caractéristiques — fonctionnel, propre, à finaliser

**Date :** 2026-02  
**Contexte :** Évaluation des 4 sous-services et conclusion sur l’état du système.

---

## 1. Les 4 sous-services sont-ils fonctionnels ?

| Service | Rôle | État | Utilisation |
|--------|------|------|-------------|
| **Getter** | Définitions par clé/entité, limites, formules, conversion, type, value_available, lien maître/liée. | ✅ Fonctionnel | Admin (CharacteristicController, DofusConversionFormulaController), Form Requests (HasCharacteristicValidation), Limit, Conversion, FormatterApplicator, ScrappingSeedersExportCommand, Orchestrator. |
| **Limit** | Validation par type (boolean, list, min/max), validation globale, clamp. | ✅ Fonctionnel | Orchestrator (validation des données converties), DofusConversionService (clamp après conversion), Form Requests via characteristicRules / characteristicMinMaxRules. |
| **Formula** | Évaluation sécurisée des formules (variables, fonctions, tables). | ✅ Fonctionnel | DofusConversionService, CharacteristicController (formula-preview), Getter (résolution min/max formule/table). |
| **Conversion** | Dofus → Krosmoz (niveau, vie, attributs, initiative, rareté). | ✅ Fonctionnel | FormatterApplicator, Orchestrator (ConversionService), DofusConversionFormulaController. |

**Conclusion :** Les quatre sous-services sont **fonctionnels** et utilisés en conditions réelles (admin, scrapping, validation des entités).

---

## 2. Optimisations

- **Singleton :** Getter, Limit, Formula, Conversion sont enregistrés en singleton dans `AppServiceProvider` — pas de duplication d’instances.
- **Getter :** Pas de cache (méthode `clearCache()` vide). Chaque `getDefinition()` fait 1 requête sur `characteristics` + 1 sur la table de groupe. En contexte admin ou validation unitaire, c’est acceptable.
- **Limit::validate() :** Pour chaque champ du payload, appel à `getDefinitionByField()` puis `validateSingle()`. Donc N appels au Getter pour N champs. En scrapping, le nombre de champs par entité reste limité (typ. &lt; 30) ; en cas de pics (très gros payloads), une optimisation possible serait de **précharger toutes les définitions pour l’entité** en une ou deux requêtes, puis valider en mémoire. Non critique pour l’instant.
- **Rareté :** Plus de config fantôme ; la caractéristique `rarity_object` (type list) et le Getter/Conversion sont la source de vérité.

**Conclusion :** Le système est **suffisamment optimisé** pour l’usage actuel. Une optimisation ciblée (batch des définitions dans `validate()`) reste possible si les volumes augmentent.

---

## 3. Le service est-il « fini » et « propre » ?

### Fini (fonctionnellement)

- **Oui** pour le cœur métier : définitions en BDD, lien/copie, validation par type, conversion Dofus → Krosmoz, admin complète, Form Requests alignés sur les types, scrapping qui s’appuie sur les caractéristiques.
- **Restant identifié (audit) :** L’**export seeder** n’inclut pas encore `value_available` (object/spell) ni la pivot **characteristic_object_item_type**. Donc après un `db:export-seeder-data --characteristics`, une réinjection complète (listes de valeurs + restrictions par type d’item) n’est pas restituée. C’est le **seul point « non fini »** documenté.

### Propre

- **Oui** : pas de config obsolète (characteristics_rarity supprimée), pas de doublon entre les deux endpoints formula-preview (rôles distincts), responsabilités claires (Getter = lecture, Limit = validation, Formula = calcul, Conversion = Dofus → Krosmoz), trait HasCharacteristicValidation unifié (characteristicRules + characteristicMinMaxRules).
- **Documentation** : ARCHITECTURE_SOUS_SERVICES.md décrit encore l’ancien état du Limit (« à prévoir » pour boolean/list) ; à mettre à jour manuellement pour refléter `validateSingle` et la validation par type.

---

## 4. Synthèse

| Question | Réponse |
|----------|---------|
| Les sous-services sont-ils fonctionnels ? | **Oui.** Les 4 sont utilisés et couvrent définition, validation, formules et conversion. |
| Sont-ils optimisés ? | **Oui** pour l’usage actuel (singletons, pas de N+1 bloquant). Optimisation possible en batch des définitions dans `validate()` si besoin. |
| Le service de caractéristiques est-il fini ? | **Oui** pour la config et l’usage en production. **À finaliser** : export seeder (value_available + pivot item_type). |
| Est-il propre ? | **Oui** : code cohérent, pas de config fantôme, responsabilités claires. Doc d’architecture à aligner sur l’état actuel du Limit. |

**En une phrase :** Le service de caractéristiques est **fonctionnel, propre et utilisable en l’état** ; la seule brique à compléter pour une boucle « config → export → seed » complète est l’export seeder (value_available + characteristic_object_item_type).
