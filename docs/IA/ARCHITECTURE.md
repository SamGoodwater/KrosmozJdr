# Architecture de la génération

Cadrage, **pas encore de code dédié**. S’appuie sur le scrapping, les états d’entité et les validateurs déjà en place.

## Partage des responsabilités

```
Brief MJ  ou  entité raw (scrap)
        │
        ▼
Laravel — assembleur de contexte
  • JSON Schema du type
  • contraintes machine (extrait)
  • 3–8 fiches or playable du même type
  • catalogue pré-filtré (ids, noms, bonus) si besoin
  • normes / gabarit de niveau
        │
        ▼
LLM → JSON strict (structured output)
        │
        ▼
Validateurs PHP (limites, ids, cohérence)
        │
   erreurs ? ──► 1 à 2 retries avec la liste d’erreurs
        │
        ▼
Enregistrement en ai_review  (+ métadonnées modèle / prompt)
        │
        ▼
Relecture humaine → playable  (les meilleures fiches enrichissent les étalons)
```

Règle d’or : **Laravel interroge le catalogue, pas le LLM.** Un agent avec outils n’est envisagé que pour un PNJ ponctuel très exploratoire, pas pour un batch.

## État `ai_review`

Les états actuels sont `raw` / `draft` / `playable` / `archived` (`EntityStateController`, `EntityDisplayVisibilityService`, Form Requests).

État prévu :

| Code | UI | Rôle |
| --- | --- | --- |
| `raw` | Brut | Import Dofus, pas encore « JDR ». |
| `draft` | Brouillon | Travail humain (création ou retravail). |
| `ai_review` | À relire / Proposition IA | L’IA a proposé ; file d’attente de publication. |
| `playable` | Jouable | Seul état que le modèle a le droit d’utiliser comme exemple. |
| `archived` | Archivé | Retiré. |

Visibilité de `ai_review` : **comme `raw` / `draft`** (éditeurs seulement).

Éviter le libellé « Brouillon amélioré » : trop proche de `draft`.

Métadonnées utiles (champs, pas un état) : `ai_generated_at`, identifiant du modèle, version de prompt, rapport du validateur. Permet de régénérer sans casser le cycle de vie.

Impact transversal connu : policies, `EntityStateController::STATES`, `GlobalSearchService::ALLOWED_STATES`, Form Requests, filtres de tables, stats admin (`AdminOverviewStatsService`). Tant que ce n’est pas branché, le code ne connaît que les 4 états actuels.

## Pourquoi pas un modèle « à nous »

Le fine-tuning (ou un LLM local entraîné sur le JDR) n’est pas le premier investissement :

- il faut des **centaines** d’exemples parfaits par type ;
- une règle qui change (PA, normes, effets) **périme** le modèle ;
- il n’empêche pas les aberrations (Iop Terre / Force 0) : ça, c’est le validateur.

Le « renforcement » utile :

1. contraintes machine écrites pour un programme ;
2. 10–30 fiches or par type ;
3. validateur déterministe ;
4. boucle de correction (erreurs renvoyées au LLM, 1–2 fois).

Un fine-tuning ne se discute **que** si l’on a 300+ fiches or **et** que prompt + outils saturent.

RAG : oui, mais **ciblé** (extraits de règles + étalons + tranche de catalogue), pas les 150 fichiers de lore.

## Prompts et format

Trois couches, pas un pavé unique :

1. **Superviseur** — court, stable, mis en cache : rôle, interdits, « tu ne publies pas », sortie JSON uniquement.
2. **Tâche par type** + **JSON Schema** (structured output). Le format n’est pas une fiche prose que le modèle « essaie » de suivre.
3. **Contrat d’API catalogue** — pour les développeurs Laravel. Version courte « outils » seulement si un agent PNJ existe un jour.

L’IA n’invente pas de types d’effets hors whitelist, ni d’ids d’objets/sorts hors liste fournie.

## Validateurs (à étendre, déjà amorcés)

| Déjà là | À ajouter pour l’IA |
| --- | --- |
| `CharacteristicLimitService` (min/max) | Cohérence élément ↔ caractéristique d’attaque / build |
| `NormsResolver`, `NormAwareEntityProcessor` | Budget PA du kit de sorts |
| `CharacteristicCompatibilityService` (`allowed_item_type_ids`) | 1 objet par slot ; stuff dans la voie |
| `DuplicateEquipmentSignatureChecker` | Ids du catalogue uniquement ; sorts d’une classe donnée |
| Gabarits règles 5.1.2 / 5.2.4 | Max 3 effets par sort JDR ; trop de sorts sur un monstre |

Un JSON « dans les normes » mais idiot (sorts Terre, Force 0) doit **échouer**. Les bornes numériques ne suffisent pas.

## Code et docs existants à réutiliser

- Pipeline scrap : `app/Services/Scrapping/` — Collecte → Conversion → Validation → Intégration. L’IA s’insère **après** la conversion, sur du `raw`.
- Création intelligente objets v1 (preview, pas d’écriture auto) : `NormAwareEntityProcessor`, `ItemEffectsToBonusConverter`.
- PNJ : `app/Models/Entity/Npc.php` (`creature_id`, `breed_id`, `specialization_id`, story, panoplies). Stats sur `Creature`.
- Sorts d’une créature : pivot `creature_spell` (`Creature::spells()`).
- Caractéristiques : `routes/api/characteristics.php` (index, normes, table de référence). Prévoir une **vue compacte** pour le prompt (limites, `norms_grid`, élément ↔ carac).
- Tables admin existantes : trop lourdes (session, colonnes UI). Un catalogue IA serait une **API compacte** consommée par Laravel, `state=playable` uniquement.

## Appels LLM : 1 paquet = 1 requête

Le prompt cache facture déjà le préfixe stable une fois (puis ~10 % sur les hits). Enchaîner des appels unitaires est donc peu cher.

Mettre 8 monstres dans **une** réponse : qualité en baisse, un JSON cassé fait tout rater, relecture impossible à l’unité, sortie (la partie chère) trop grosse.

Exceptions : 3–5 **objets du même type** dans un appel si l’IA s’en mêle ; Batch API fournisseur (−50 %) pour un run de nuit, **toujours 1 paquet par requête**.
