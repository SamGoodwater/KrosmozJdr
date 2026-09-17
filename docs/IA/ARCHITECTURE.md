# Architecture de la génération

Cadrage. Config des champs figés : page admin `/admin/content/ia-generation` (table `ia_generation_settings`) avec repli `resources/ia/generation.json`. Grille objets : `ia:equipment-grid`. Pipeline LLM : `app/Services/GenerativeAi/` (Anthropic, défaut Haiku 4.5).

## Partage des responsabilités

```
Brief MJ  ou  entité raw (scrap)
        │
        ▼
Laravel — assembleur de contexte
  • Config IA (admin ou JSON de repli : frozen / writable / étalons)
  • JSON Schema du type (clés `writable` seulement)
  • champs et caracs figés recopiés hors LLM
  • 3–8 fiches or playable (`example_ids`)
  • panoplies or : noms `playable` dans `entities.item.few_shot_panoplies`
  • catalogue pré-filtré (ids, noms, bonus) si besoin
  • normes / gabarit de niveau
  • fiches Création du type (`CreationGuideCatalog`, `resources/ia/creation-guides/`)
  • PNJ : brief MJ et/ou extrait d’une page de site
        │
        ▼
LLM → JSON strict (clés `writable` seulement, sauf PNJ / unique)
        │
        ▼
Validateurs PHP (limites, ids, cohérence)
        │
   erreurs ? ──► 1 à 2 retries avec la liste d’erreurs
        │
        ▼
        Enregistrement en auto  (+ métadonnées modèle / prompt)
        │
        ▼
Relecture humaine → playable  (les meilleures fiches enrichissent les étalons)
```

Règle d’or : **Laravel interroge le catalogue, pas le LLM.** Un agent avec outils n’est envisagé que pour un PNJ ponctuel très exploratoire, pas pour un batch.

## État `auto`

Les états de publication sont `raw` / `draft` / `auto` / `playable` / `archived` (`app/Enums/EntityState.php`, policies, Form Requests, recherche, stats admin).

| Code | UI | Rôle |
| --- | --- | --- |
| `raw` | Brut | Import Dofus, pas encore « JDR ». |
| `draft` | Brouillon | Travail humain (création ou retravail). |
| `auto` | Auto | Proposition construite par IA ou script ; file d’attente de publication. |
| `playable` | Jouable | Seul état que le modèle a le droit d’utiliser comme exemple. |
| `archived` | Archivé | Retiré. |

Visibilité de `auto` : **comme `raw` / `draft`** (éditeurs seulement, `rôle ≥ write_level`).

Éviter le libellé « Brouillon amélioré » : trop proche de `draft`.

Métadonnées utiles (champs, pas un état) : `ai_generated_at`, identifiant du modèle, version de prompt, rapport du validateur. Permet de régénérer sans casser le cycle de vie.

L’état `auto` est **dans le code**. Le pipeline LLM (assembleur, JSON Schema writable-only, retries, writer allowlist) est branché. Persist : rencontre, sort (`effect`), PNJ (kit `NpcKitCatalog`), objet unique, consommable (`effect`). Ability `generate` = admin. Tokens réels : table `ai_generation_runs` (`model`, `input_tokens`, `output_tokens`, `ai_generated_at`).

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

Sur une fiche **sourcée Dofus**, le schéma n’expose que les clés `writable` du JSON. Laravel recopie le reste. Sur un **PNJ** (et un objet unique sans source), le schéma inclut l’identité. Contrat : [CHAMPS.md](./CHAMPS.md).

## Validateurs (à étendre, déjà amorcés)

| Déjà là | À ajouter pour l’IA |
| --- | --- |
| `CharacteristicLimitService` (min/max) | Cohérence élément ↔ caractéristique d’attaque / build |
| `NormsResolver`, `NormAwareEntityProcessor` | Budget PA du kit de sorts |
| `CharacteristicCompatibilityService` (`allowed_item_type_ids`) | 1 objet par slot ; stuff dans la voie |
| `DuplicateEquipmentSignatureChecker` | Ids du catalogue uniquement ; sorts d’une classe donnée |
| Gabarits règles 5.1.2 / 5.2.4 | Max 3 effets par sort JDR ; trop de sorts sur un monstre |

Un JSON « dans les normes » mais idiot (sorts Terre, Force 0) doit **échouer**. Les bornes numériques ne suffisent pas.

## Code branché

- Client : `GenerativeAiClient` (Laravel HTTP, Messages API, outil forcé `submit_json`). Cache **explicite** : `cache_control` ephemeral sur l’outil + le préfixe user stable (tâche, gel, few-shot). Le suffixe (fiche source, brief, retries) n’est pas caché. Pas de cache automatique top-level (le dernier bloc change à chaque fiche). Modèle : allowlist admin (`generation.model`, défaut `claude-haiku-4-5`). Toggle `generation.prompt_cache` (défaut on). Pas de SDK.
- Assembleur : `ContextAssembler` — superviseur + `task_prompt` / fiche Création + few-shot compact (**préfixe cache**) + schéma writable-only. Fiche source, brief MJ, kit PNJ : **suffixe dynamique** (hors cache).
- Writer : `AllowlistWriter` — jamais `unguard` du JSON LLM ; `state=auto` via `EntityStateGate::assertAutomatedWriterMaySet` ; `auto_update=false`.
- Job : `ConvertPacketJob`. L’UI HTTP l’exécute en `dispatchSync` (file `database` sans worker = faux succès). Retries validateur = `generation.max_retries`.
- Specs : `app/Services/GenerativeAi/Specializations/` (`spell`, `encounter`, `npc`, `item`, `consumable`).
- HTTP : `POST /api/entities/{type}/{id}/ia-convert` (`role:admin`, throttle 12/min). Types : `monsters`, `spells`, `npcs`, `items`, `consumables`. Exécution **synchrone** (`dispatchSync`) pour l’UI ; réponse `queued` n’est plus un succès. Statut : `GET /api/ia/status` (tokens locaux du mois + estimés + crédit Anthropic s’il est lisible). Instantané : `POST /api/entities/{type}/{id}/update-diff/restore`.
- CLI : `php artisan ia:convert {spell|encounter|npc|item|consumable}` (`ia:convert-encounter` reste un alias).
- UI : une icône « Sources » → `EntitySourceModal` (DofusDB | Conversion IA). Après écriture : tableau avant/après, Enregistrer / Rétablir. Volet IA si admin.
- Tests : `Http::fake` — aucun appel LLM réel en CI (`ANTHROPIC_API_KEY` vide dans `phpunit.xml`).

## Code et docs existants à réutiliser

- Pipeline scrap : `app/Services/Scrapping/` — Collecte → Conversion → Validation → Intégration. L’IA s’insère **après** la conversion, sur du `raw`.
- Config gel / étalons : page admin `/admin/content/ia-generation` (`ExamplePicker` via `api.tables.*`, filtre `state=playable`), `GenerationConfigStore`, `resources/ia/generation.json`.
- Fiches de création (conversion) : `CreationGuideCatalog`, `resources/ia/creation-guides/`, commande `ia:creation-guides`. Détail : [CREATION.md](./CREATION.md).
- Création intelligente objets v1 (preview, pas d’écriture auto) : `NormAwareEntityProcessor`, `ItemEffectsToBonusConverter`.
- PNJ : `app/Models/Entity/Npc.php` (`creature_id`, `breed_id`, `specialization_id`, story, panoplies). Stats sur `Creature`.
- Sorts d’une créature : pivot `creature_spell` (`Creature::spells()`).
- Caractéristiques : `routes/api/characteristics.php` (index, normes, table de référence). Prévoir une **vue compacte** pour le prompt (limites, `norms_grid`, élément ↔ carac).
- Tables admin existantes : trop lourdes (session, colonnes UI). Un catalogue IA serait une **API compacte** consommée par Laravel, `state=playable` uniquement.

## Appels LLM : 1 paquet = 1 requête

Le prompt cache facture le préfixe stable une fois (écriture 1,25×, puis ~10 % sur les hits, TTL 5 min). Seuil : 1 024 tokens (Sonnet 5) / 4 096 (Haiku 4.5). Le superviseur seul est trop court : on cache aussi schéma + étalons. Enchaîner des appels unitaires du même type reste cheap.

Mettre 8 monstres dans **une** réponse : qualité en baisse, un JSON cassé fait tout rater, relecture impossible à l’unité, sortie (la partie chère) trop grosse.

Exceptions : 3–5 **objets du même type** dans un appel si l’IA s’en mêle ; Batch API fournisseur (−50 %) pour un run de nuit, **toujours 1 paquet par requête**.
