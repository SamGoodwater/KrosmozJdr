# Simplifications possibles — Service de scrapping

**Contexte :** le pipeline (Collecte → Conversion → Validation → Intégration) et les mappings ont grandi par ajouts successifs. Ce document identifie ce qui alourdit le tout et propose des pistes pour simplifier **sans tout réécrire**.

---

## 1. Ce qui alourdit aujourd’hui

### 1.1 Plusieurs sources de vérité pour le « mapping »

| Donnée | Où c’est défini | Conséquence |
|--------|------------------|-------------|
| Règles « chemin → cible » (level, name, etc.) | **BDD** (scrapping_entity_mappings) **ou** **JSON** (entities/*.json `mapping`) | ConfigLoader fusionne : si BDD a des règles → on prend BDD, sinon JSON. Deux formats à maintenir, comportement « tout l’un ou tout l’autre » par entité. |
| Id DofusDB → caractéristique (bonus items) | **BDD** (dofusdb_characteristic_id sur characteristic_object) **+ fallback JSON** (dofusdb_characteristic_to_krosmoz.json) | Une seule logique métier mais deux chemins de chargement (getter + fichier). |
| EffectId → sous-effet (sorts) | **BDD** (dofusdb_effect_mappings) **+ fallback** constante PHP (DofusDbEffectMapping) | Idem : deux chemins. |

Résultat : pour comprendre « d’où vient le mapping », il faut suivre plusieurs chemins (BDD, JSON, constante) selon l’entité et le type de donnée.

### 1.2 FormatterApplicator : une grosse boîte à tout faire

- **~15 formatters** dans un seul registry (toString, pickLang, dofusdb_level, itemEffectsToKrosmozBonus, zoneDescrToNotation, recipeToResourceRecipe, etc.).
- Mélange de :
  - formatters **génériques** (toInt, truncate, toJson),
  - formatters **Dofus** (dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini) qui délèguent à DofusConversionService,
  - formatters **métier lourd** (itemEffectsToKrosmozBonus, recipeToResourceRecipe, zoneDescrToNotation) qui contiennent beaucoup de logique.
- Pour lire ou modifier un comportement, il faut ouvrir un gros fichier et comprendre le contexte (raw, context, mappingRule, _resolvedCharacteristicKey).

Conséquence : une seule classe « fourre-tout », difficile à tester unitairement par domaine (items vs sorts vs monstres).

### 1.3 Deux façons de traiter les données « profondes »

- **Chemin standard :** règle de mapping (from_path + formatters + to). Ex. `grades.0.level` → formatter dofusdb_level → `creatures.level`.
- **Chemin dédié :** un formatter unique qui prend tout un bloc (effects[], levels[]) et fait tout (résolution id → key, conversion, agrégation). Ex. itemEffectsToKrosmozBonus, SpellEffectsConversionService pour les effets de sort.

Conséquence : le modèle mental n’est pas « tout est une règle + formatters », mais « parfois règle, parfois formatter qui fait tout ». Les nouveaux ven ne savent pas où ajouter une règle (mapping BDD/JSON vs nouveau formatter).

### 1.4 Orchestrator et construction manuelle

- `Orchestrator::default()` construit à la main : ConfigLoader, CollectService, ConversionService, FormatterApplicator, LimitService, IntegrationService, SpellEffectsConversionService, SpellGlobalNormalizer, puis RelationResolutionService.
- Les dépendances (qui appelle qui) ne sont pas évidentes sans lire le code.

Conséquence : un seul point d’entrée « par défaut » mais la structure du pipeline n’est pas lisible (pas de schéma unique « pipeline »).

### 1.5 Vocabulaire et concepts qui se chevauchent

- **mapping_key**, **from_path**, **to** (model, field), **characteristic_id**, **characteristic_key**, **mappingRule**, **_resolvedCharacteristicKey**, **entityType**, **targetModel**…
- Beaucoup de noms pour des idées proches (règle de mapping, cible, caractéristique liée).

Conséquence : la doc et le code demandent un effort pour savoir quel concept utiliser où.

---

## 2. Pistes de simplification (par impact / effort)

### 2.1 Réduire les sources de vérité (priorité haute, effort moyen)

**Objectif :** une seule source « officielle » par type de mapping, le fallback restant uniquement pour compat / tests.

| Action | Bénéfice |
|--------|----------|
| **Décider une source par défaut pour les règles « chemin → cible »** | Ex. « BDD = source de vérité ; JSON = template initial / seed ». Supprimer la logique « si BDD vide alors JSON » au runtime et plutôt **remplir la BDD depuis le JSON** au seed ou à l’import. En run, on lit toujours la BDD. |
| **Supprimer le fallback JSON pour les bonus items** | Une fois DofusdbCharacteristicIdSeeder et BDD en place, ne plus charger dofusdb_characteristic_to_krosmoz.json dans le formatter. Si un id n’est pas en BDD, on ignore l’effet (ou on log). Réduit chemins et fichiers à maintenir. |
| **Documenter clairement** « Où modifier le mapping ? » (une page : règles par entité = BDD ; id → caractéristique = BDD characteristic_* ; effectId = BDD dofusdb_effect_mappings). | Moins de « où c’est défini ? » à chaque changement. |

### 2.2 Alléger FormatterApplicator (priorité haute, effort moyen)

**Objectif :** garder un seul point d’entrée (registry) mais rendre le contenu plus lisible et testable.

| Action | Bénéfice |
|--------|----------|
| **Extraire les formatters « bloc métier » dans des classes dédiées** | Ex. `ItemEffectsToBonusConverter` (au lieu de méthode privée + closure dans le registry). Le registry ne fait qu’appeler `$this->itemEffectsConverter->convert($value, $raw, $context)`. Même idée pour recipe / zone si ça grossit. |
| **Grouper les formatters dans des namespaces ou sous-classes** | Ex. FormatterApplicator + `Formatters\Generic`, `Formatters\Dofus`, `Formatters\Item` (ou des classes *Handler). Pas obligé de tout casser : on peut garder le registry actuel et déléguer à des petits services par domaine. |
| **Réduire le nombre de formatters « one-shot »** | Certains (storeScrappedImage, truncate, mapSizeToKrosmoz) pourraient être des formatters par défaut avec args, ou fusionnés (ex. « string » : truncate + option storeImage). À évaluer au cas par cas. |

### 2.3 Clarifier le pipeline (priorité moyenne, effort faible)

| Action | Bénéfice |
|--------|----------|
| **Un schéma texte ou diagramme** (dans la doc) : Collecte → Normalisation (si spell) → Conversion (mapping + formatters) → Validation (limites) → Intégration. Avec la liste des services et « qui lit BDD / JSON ». | On voit d’un coup le flux et les responsabilités. |
| **Centraliser la construction du pipeline** | Ex. un `ScrappingPipelineFactory` ou un provider qui construit l’Orchestrator avec ses dépendances, au lieu de tout dans `Orchestrator::default()`. Les tests peuvent surcharger une étape (ex. ConversionService mock). |

### 2.4 Unifier un peu le vocabulaire (priorité basse, effort faible)

| Action | Bénéfice |
|--------|----------|
| **Glossaire dans la doc** : mapping rule, from_path, target (model + field), characteristic_key, entityType, etc. | Moins d’ambiguïté dans les discussions et la doc. |
| **Éviter d’ajouter de nouveaux synonymes** | Pour les prochaines évolutions, réutiliser les termes existants (characteristic_key, mapping rule) plutôt qu’introduire de nouveaux noms. |

---

## 3. Ce qu’il est raisonnable de ne pas toucher (pour l’instant)

- **Collecte / Intégration / Relations** : structure déjà séparée (CollectService, IntegrationService, RelationResolutionService). Les simplifications ci-dessus ne les imposent pas.
- **Validation (CharacteristicLimitService)** : pilotée par les caractéristiques ; la logique « merged models + limites » est claire.
- **Tables BDD existantes** : pas de proposition de fusion de tables (scrapping_entity_mappings, dofusdb_effect_mappings, characteristic_*) ; on simplifie l’usage (une source par type) plutôt que le schéma.

---

## 4. Ordre suggéré

1. **Court terme** : Documenter le flux (schéma + « où modifier le mapping ») et supprimer le fallback JSON des bonus items une fois la BDD validée.
2. **Moyen terme** : Extraire 1–2 formatters lourds (itemEffectsToKrosmozBonus, éventuellement recipe) en convertisseurs dédiés ; garder le registry comme façade.
3. **Si besoin** : Décider « BDD uniquement » pour les règles d’entité et alimenter la BDD depuis les JSON au seed / import au lieu de fallback au runtime.

Cela réduit la « usine à gaz » sans refonte complète : moins de chemins de données, moins de logique dans une seule classe, et une doc qui fixe les responsabilités.

---

## 5. Appliqué (2026-03-03)

- **Convertisseur dédié** : `ItemEffectsToBonusConverter` extrait de FormatterApplicator. Le formatter `itemEffectsToKrosmozBonus` délègue à cette classe. Source de vérité **BDD uniquement** (plus de fallback JSON pour les bonus items).
- **Suppression du fallback JSON** : `loadDofusdbCharacteristicToKrosmozJsonMapping()` et la propriété de cache JSON supprimées. Les id DofusDB non présents en BDD (characteristic_object.dofusdb_characteristic_id) sont ignorés.
- **Construction centralisée** : `ScrappingPipelineFactory::createDefault()` construit l’Orchestrator et toutes ses dépendances (ConfigLoader, CollectService, ConversionService, FormatterApplicator, ItemEffectsToBonusConverter, etc.). `Orchestrator::default()` délègue à la factory.
- **Documentation** : [PIPELINE_ET_MAPPING.md](./PIPELINE_ET_MAPPING.md) — schéma du pipeline, tableau « Où modifier le mapping ? », glossaire (mapping rule, from_path, characteristic_key, entityType, etc.).
- **Suite** : RecipeToResourceRecipeConverter (extraction recettes), executePipelineForOneRaw dans Orchestrator (runOne/runOneWithRaw), tests ItemEffectsToBonusConverter.
- **Bootstrap lancement** : commande `scrapping:setup` (migrate + seeders scrapping ordonnés) pour garantir la création des caractéristiques et mappings au démarrage.

---

## 6. Prochaines améliorations possibles

Idées classées par priorité / effort pour continuer à simplifier ou améliorer le service.

### 6.1 Priorité haute, effort faible à moyen

| Action | Bénéfice | Effort |
|--------|----------|--------|
| **Alimenter la BDD depuis le JSON au seed** | Avoir un seeder (ou commande) qui lit les `mapping` des JSON d’entité et crée/met à jour les règles dans `scrapping_entity_mappings`. Ensuite, optionnel : en runtime, ne plus faire le fallback JSON dans ConfigLoader (toujours BDD). Si BDD vide → mapping vide ou erreur explicite. | Moyen (seeder + décision de supprimer le fallback). |
| **Extraire un convertisseur « recettes »** | `recipeToResourceRecipe` et `recipeIdsToResourceRecipe` contiennent la logique pivot resource_recipe. Les déplacer dans une classe du type `RecipeToResourceRecipeConverter` (comme ItemEffectsToBonusConverter) allège FormatterApplicator et rend la logique testable seule. | Faible. |
| **Tests unitaires pour ItemEffectsToBonusConverter** | Couvrir le convertisseur (map BDD, conversion par DofusConversionService, agrégation par shortKey) sans passer par tout le pipeline. | Faible. |

### 6.2 Priorité moyenne, effort moyen

| Action | Bénéfice | Effort |
|--------|----------|--------|
| **Extraire la logique « run un item » de l’Orchestrator** | runOne et runMany dupliquent une grosse séquence (enrichRaw, spell_global, convert, spell_effects, entityType, validate, integrate, relations). Une méthode privée `runPipelineForOneRaw(string $source, string $entity, array $raw, array $context, array $options)` retournant (converted, validationResult?, integrationResult?) réduirait la duplication et rendrait le flux lisible. | Moyen. |
| **Grouper les formatters Dofus dans un handler** | Créer une classe `DofusFormattersHandler` (dofusdb_level, dofusdb_life, dofusdb_attribute, dofusdb_ini) qui reçoit DofusConversionService et expose une méthode par formatter. FormatterApplicator délègue à ce handler. Même idée possible pour les formatters « génériques » (toString, toInt, truncate, toJson) si on veut séparer par domaine. | Moyen. |
| **Migrer les usages restants du JSON dofusdb_characteristic_to_krosmoz** | ScrappingController (keywords pour l’UI) et ExtractObjectConversionSamplesCommand lisent encore ce fichier. Les faire s’appuyer sur CharacteristicGetterService + characteristic_object (ou un export JSON généré depuis la BDD) pour n’avoir qu’une source de vérité. | Moyen. |

### 6.3 Priorité basse, effort variable

| Action | Bénéfice | Effort |
|--------|----------|--------|
| **Supprimer la constante PHP DofusDbEffectMapping** | Une fois que la table dofusdb_effect_mappings est toujours peuplée (seed systématique ou import), supprimer la classe DofusDbEffectMapping et faire en sorte que DofusdbEffectMappingService retourne un sous-effet « autre » si effectId inconnu, sans fallback PHP. | Faible (après vérif seed/import). |
| **Documenter les options de l’Orchestrator** | Un tableau en tête d’Orchestrator (ou dans PIPELINE_ET_MAPPING.md) listant toutes les options de runOne/runMany (convert, validate, integrate, dry_run, force_update, include_relations, exclude_from_update, etc.) avec leur effet. | Faible. |
| **Cache invalidation explicite** | Quand un admin modifie une caractéristique (dofusdb_characteristic_id) ou un mapping effectId, appeler CharacteristicGetterService::clearCache() et DofusdbEffectMappingService::clearCache() pour éviter de servir des données obsolètes. | Faible (appels dans les contrôleurs ou observers). |

### 6.4 Ce qu’il vaut mieux ne pas faire (pour l’instant)

- **Fusionner les tables de mapping** (scrapping_entity_mappings, dofusdb_effect_mappings, characteristic_*) : le schéma actuel est clair ; simplifier l’usage (une source par type) suffit.
- **Réécrire le pipeline en « tout événement »** : refonte trop lourde sans gain immédiat.
- **Tout mettre en JSON et supprimer la BDD** : la BDD permet l’UI admin et l’édition sans redéploiement ; c’est la cible.
