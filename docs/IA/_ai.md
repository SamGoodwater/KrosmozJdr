# IA générative — carte IA

> Cadrage LLM métier. Config des champs figés en JSON. Pipeline d’appel **non branché**. Lis ce nœud avant d’ajouter un appel LLM.

## Quand lire

- Améliorer des données Dofus pour le JDR (objets, sorts, monstres, PNJ).
- Générer une rencontre ou un PNJ à la demande.
- Choisir entre algo, LLM, fine-tuning, RAG, agent avec outils.

## Décisions retenues

- **Pas de modèle maison / fine-tuning** au départ. LLM du commerce + prompt + schéma JSON + validateurs PHP.
- **Laravel assemble le contexte** ; l’IA ne « browse » pas l’API en batch.
- L’IA **propose**, jamais `playable`. État `auto` (UI « Auto ») : déjà dans le code. Pipeline LLM non branché.
- **Objets** : catalogue réduit par **algorithme** (grille niveau × slot × voie), pas tout Dofus.
- **L’IA ne réécrit pas l’identité** ni, par défaut, les **caractéristiques** d’une fiche Dofus. Liste éditable : `resources/ia/generation.json`. **PNJ** : création complète. Détail : [CHAMPS.md](./CHAMPS.md).
- **Monstres / PNJ / sorts de créature** : génération **à la demande**, paquet cohérent.
- Exemples few-shot : uniquement des fiches `playable` (une vingtaine par type quand l’IA s’en mêle).

## Fichiers

| Fichier | Contenu |
| --- | --- |
| [README](./README.md) | Problème, principes, ordre de livraison. |
| [CHAMPS](./CHAMPS.md) | Figé vs généré : JSON `resources/ia/generation.json`. |
| [ARCHITECTURE](./ARCHITECTURE.md) | Pipeline, état, prompts, validateurs, code existant. |
| [CATALOGUE](./CATALOGUE.md) | Objets, pré-filtre, API caractéristiques. |
| [RENCONTRES](./RENCONTRES.md) | Monstre + sorts, PNJ. |
| [COUTS](./COUTS.md) | Modèles, tokens, ordres de grandeur. |

## Liens

- Scrapping (conversion Dofus, normes objets) : [../features/scrapping/_ai.md](../features/scrapping/_ai.md)
- Entités / états : [../features/entities/_ai.md](../features/entities/_ai.md)
- Caractéristiques : [../features/characteristics/_ai.md](../features/characteristics/_ai.md)
- Gabarits MJ : `private/game/rules/5-Ressources-et-equilibrage/5.1-ressources-mj/5.1.2-creation-de-pnj-et-monstres.md`
