# IA générative — carte IA

> Cadrage LLM métier. Config des champs figés (admin + JSON). Pipeline d’appel **non branché**. Lis ce nœud avant d’ajouter un appel LLM.

## Quand lire

- Améliorer des données Dofus pour le JDR (objets, sorts, monstres, PNJ).
- Générer une rencontre ou un PNJ à la demande.
- Choisir entre algo, LLM, fine-tuning, RAG, agent avec outils.

## Décisions retenues

- **Pas de modèle maison / fine-tuning** au départ. LLM du commerce + prompt + schéma JSON + validateurs PHP.
- **Laravel assemble le contexte** ; l’IA ne « browse » pas l’API en batch.
- L’IA **propose**, jamais `playable`. État `auto` (UI « Auto ») : déjà dans le code. Pipeline LLM non branché.
- **Objets** : grille algo `ia:equipment-grid` (`resources/ia/equipment-grid.json`). Rapport ; `--write` = trous `draft`. Pas tout Dofus. Pipeline LLM non branché.
- **L’IA ne réécrit pas l’identité** ni, par défaut, les **caractéristiques** d’une fiche Dofus. Liste éditable : page admin **IA métier** (`/admin/content/ia-generation`), fichier `resources/ia/generation.json` en repli. **PNJ** : création complète. Détail : [CHAMPS.md](./CHAMPS.md).
- **Monstres / PNJ / sorts de créature** : génération **à la demande**, paquet cohérent.
- Exemples few-shot : uniquement des fiches `playable`. **Panoplies or** : liste `entities.item.few_shot_panoplies` dans `generation.json` + [CATALOGUE](./CATALOGUE.md#liste-few-shot-panoplies-ce-que-lia-doit-imiter). ~54 sets (Piou, Bouftou For+Int au complet, Blop, Gelax, Craqueleur, Pandala, etc.).
- **Étalons objets** : 32 items niveau 8 `playable` (4 éléments × 4 raretés), capes + armes seulement. L’élément n’existe que sur cape (For/Int/Cha/Agi) et armes (dégâts fixes) — pivot `characteristic_object_item_type`. Valeurs = `norms_grid` écrêtées par `formula`. Bonus JDR à écrire dans **`bonus`** (le front le fait gagner sur `effect`) + `auto_update = false`. Détail : [CATALOGUE](./CATALOGUE.md#kit-détalons-niveau-8-en-base-à-relire).
- **Socle rejouable** : `database/seeders/data/entities/items/*-item.json` (1 fichier/item) + `database/seeders/data/entities/panoplies/*-panoply.json` + `database/seeders/data/entities/consumables/healing-out-of-combat.json` + `characteristic-respec-scrolls.json`. `items:seeder-export` / `items:seeder-import` ; `Entity\ItemSeeder` puis `Entity\ResourceSeeder` (recettes → `playable`, plancher 1 kama) puis `Entity\ConsumableSeeder` puis `Entity\PanoplySeeder` dans `project:seed`. Boutons super_admin items sur `/admin/content/ia-generation`.
- **Sets Dofus playable** : ~54 panoplies or (Piou, Bouftou For+Int au complet, Blop, Gelax, Craqueleur, Mulou, Koalak, Pandala, etc.). En général **+1 compétence** de thème (pièce vide ou palier complet) ; Piou Rose / Violet sans. Liste few-shot : [CATALOGUE](./CATALOGUE.md#liste-few-shot-panoplies-ce-que-lia-doit-imiter).

## Fichiers

| Fichier | Contenu |
| --- | --- |
| [README](./README.md) | Problème, principes, ordre de livraison. |
| [CHAMPS](./CHAMPS.md) | Figé vs généré : admin `/admin/content/ia-generation`, JSON de repli. |
| [ARCHITECTURE](./ARCHITECTURE.md) | Pipeline, état, prompts, validateurs, code existant. |
| [CATALOGUE](./CATALOGUE.md) | Objets, grille `ia:equipment-grid`, pré-filtre. |
| Config grille | `resources/ia/equipment-grid.json` |
| [RENCONTRES](./RENCONTRES.md) | Monstre + sorts, PNJ. |
| [COUTS](./COUTS.md) | Modèles, tokens, ordres de grandeur. |

## Liens

- Scrapping (conversion Dofus, normes objets) : [../features/scrapping/_ai.md](../features/scrapping/_ai.md)
- Entités / états : [../features/entities/_ai.md](../features/entities/_ai.md)
- Caractéristiques : [../features/characteristics/_ai.md](../features/characteristics/_ai.md)
- Gabarits MJ : `private/game/rules/5-Ressources-et-equilibrage/5.1-ressources-mj/5.1.2-creation-de-pnj-et-monstres.md`
