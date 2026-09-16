# IA générative — carte IA

> Cadrage LLM métier + pipeline d’appel. Lis ce nœud avant d’ajouter un type ou un second client LLM.

## Quand lire

- Améliorer des données Dofus pour le JDR (objets, sorts, monstres, PNJ).
- Générer une rencontre ou un PNJ à la demande.
- Choisir entre algo, LLM, fine-tuning, RAG, agent avec outils.

## Décisions retenues

- **Pas de modèle maison / fine-tuning** au départ. LLM du commerce + prompt + schéma JSON + validateurs PHP.
- **Laravel assemble le contexte** ; l’IA ne « browse » pas l’API en batch.
- L’IA **propose**, jamais `playable`. État `auto` (UI « Auto »). Ability `generate` = `isAdmin()` (rôle ≥ 4). Publication `auto` → `playable` : ability `publish`. `example_ids` : fiches `playable` (`official_id` / nom) via le sélecteur admin (`api.tables.*`, défaut jouable) ; pool vide refusé (`FewShotExamplePool`).
- **Noyau** : `GenerativeAiClient` (HTTP Anthropic, outil `submit_json`, cache prompt), `ContextAssembler`, `AllowlistWriter` (jamais d’unguard JSON), `ConvertPacketJob` (1 paquet = 1 requête, retries `generation.max_retries`). Clé `ANTHROPIC_API_KEY`. Tests : `Http::fake`.
- **Specs** : `spell`, `encounter` (monstre), `npc` (`NpcKitCatalog`), `item`, `consumable`. Persistés en `auto` via le même pipeline.
- **Objets** : grille algo `ia:equipment-grid`. Rapport ; `--write` = trous `draft`.
- **Fiches Création** : `CreationGuideCatalog` injecté dans la couche tâche.
- **UI** : un modal `EntitySourceModal` (DofusDB | IA), une icône « Sources ». Volet IA admin only.
- **Admin** `/admin/content/ia-generation` : superviseur, prompts de tâche, étalons (recherche de fiches playable), gel, solde Anthropic, estimés (`CostEstimator`).
- **L’IA ne réécrit pas l’identité** ni, par défaut, les **caractéristiques** d’une fiche Dofus. Liste éditable admin / `resources/ia/generation.json`.
- **Monstres** : génération **à la demande**, paquet `{ monster, spells: [2-3] }` → `auto`. Commande `ia:convert encounter` (`ia:convert-encounter` en alias).
- **Sorts / PNJ / objets / conso** : même pipeline (`ia:convert {spell|npc|item|consumable}`). Sort = `effect` seulement ; PNJ = kit `NpcKitCatalog`.

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
