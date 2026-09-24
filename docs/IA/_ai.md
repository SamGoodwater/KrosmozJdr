# IA générative — carte IA

> Cadrage LLM métier + pipeline d’appel. Lis ce nœud avant d’ajouter un type ou un second client LLM.

## Quand lire

- Améliorer des données Dofus pour le JDR (objets, sorts, monstres, PNJ).
- Générer une rencontre ou un PNJ à la demande.
- Choisir entre algo, LLM, fine-tuning, RAG, agent avec outils.

## Décisions retenues

- **Pas de modèle maison / fine-tuning** au départ. LLM du commerce + prompt + schéma JSON + validateurs PHP.
- **Laravel assemble le contexte** ; l’IA ne « browse » pas l’API en batch.
- L’IA **propose**, jamais `playable`. État `auto` (UI « Auto »). Ability `generate` = `isAdmin()` (rôle ≥ 4). HTTP + page admin : `password.confirm`. CLI : `--user` admin obligatoire. Publication `auto` → `playable` : ability `publish`. `example_ids` : fiches `playable` (`official_id` / nom) via le sélecteur admin (`api.tables.*`, défaut jouable) ; pool vide refusé (`FewShotExamplePool`).
- **Noyau** : `GenerativeAiClient` (HTTP Anthropic, outil `submit_json`, cache prompt **explicite**), `ContextAssembler`, `AllowlistWriter`, `ConvertPacketJob` (`dispatchSync` UI). Import manuel : `ConversionPipeline::runFromPayload` + `ManualJsonPayloadSanitizer` (strip HTML, taille/profondeur). Modèle run : `manual-json`, `prompt_version=manual-v1`. Clé `ANTHROPIC_API_KEY` (LLM seulement). Tests : `Http::fake` ; Feature inject sur les **5** actions (`IaInjectJsonTest`) ; Vitest onglet JSON + `submitAiInject`.
- **Specs** : `spell`, `encounter` (monstre + gabarit 5.1.2 en `extraContext`), `npc` (`NpcKitCatalog` : objets, classes/spés, `spells_by_breed` + `NpcEquipmentSlotValidator` + gabarit), `item`, `consumable`. Persistés en `auto` via le même pipeline.
- **Objets** : grille algo `ia:equipment-grid`. Rapport ; `--write` = trous `draft`.
- **Fiches Création** : `CreationGuideCatalog` injecté dans la couche tâche.
- **UI** : un modal `EntitySourceModal` (onglets Conversion DofusDB | Conversion IA | **JSON**). Après scrap, conversion ou injection : tableau **avant / après** — clic cellule ou en-tête pour garder l’ancienne ou la nouvelle valeur ; **Enregistrer** applique le mix (`update-diff/apply`) ; **Rétablir** / fermer annule (`restore`). Volet DofusDB : cases contenu + **Récupérer l’image**. Volets IA / JSON : admin+ **et** `password.confirm`. Solde : tokens du mois (`ai_generation_runs`) + crédit Anthropic s’il est connu, dans la page admin **et** le modal. HTTP `ia-convert` / `ia-inject` / `ia/status` / `ia/schema/{type}` = `role:admin` + `password.confirm`. Clé absente = 422 pour `ia-convert` seulement ; `ia-inject` n’appelle pas Anthropic. `action` calée sur le type d’URL ; `playable`/`archived` exigent `force`. **Création** : `CreateEntityModal` — à la main, Conversion IA, ou JSON (stub `store` puis `ia-inject`).
- **Admin** `/admin/content/ia-generation` : lecture et mutations derrière `password.confirm`. Superviseur, **modèle** (Haiku / Sonnet / Opus), **cache prompt** (défaut on), prompts de tâche, étalons (recherche de fiches playable), panoplies or (objets, recherche playable), gel, tokens du mois + solde Anthropic, estimés (`CostEstimator`). Types d’entité en **onglets colonne** (`SidebarNav`, comme caractéristiques) : un panneau visible. Carte d’entrée sur `/admin/content`.
- **L’IA ne réécrit pas l’identité** ni, par défaut, les **caractéristiques** d’une fiche Dofus. Liste éditable admin / `resources/ia/generation.json`.
- **Monstres** : génération **à la demande**, paquet `{ monster, spells: [2-3] }` → `auto`. Commande `ia:convert encounter` (`ia:convert-encounter` en alias).
- **Sorts / PNJ / objets / conso** : même pipeline (`ia:convert {spell|npc|item|consumable}`). Sort = `effect` seulement ; PNJ = kit `NpcKitCatalog` (classes/spés, `spells_by_breed`).

## Fichiers

| Fichier | Contenu |
| --- | --- |
| [README](./README.md) | Problème, principes, ordre de livraison. |
| [CHAMPS](./CHAMPS.md) | Figé vs généré : admin `/admin/content/ia-generation`, JSON de repli. |
| [ARCHITECTURE](./ARCHITECTURE.md) | Pipeline, état, prompts, validateurs, code existant. |
| [CATALOGUE](./CATALOGUE.md) | Objets, grille `ia:equipment-grid`, pré-filtre. |
| Config grille | `resources/ia/equipment-grid.json` |
| [CREATION](./CREATION.md) | Fiches de bonne pratique (prompt conversion). |
| [RENCONTRES](./RENCONTRES.md) | Monstre + sorts, PNJ. |
| [COUTS](./COUTS.md) | Modèles, tokens, ordres de grandeur. |

## Liens

- Scrapping (conversion Dofus, normes objets) : [../features/scrapping/_ai.md](../features/scrapping/_ai.md)
- Entités / états : [../features/entities/_ai.md](../features/entities/_ai.md)
- Caractéristiques : [../features/characteristics/_ai.md](../features/characteristics/_ai.md)
- Gabarits MJ : `private/game/rules/5-Ressources-et-equilibrage/5.1-ressources-mj/5.1.2-creation-de-pnj-et-monstres.md`
