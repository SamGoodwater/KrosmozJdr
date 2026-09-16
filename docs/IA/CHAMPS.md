# Champs figés vs champs générés

Source de vérité **effective** : une ligne en base (`ia_generation_settings`) si un admin a enregistré la page **Gestion du contenu → IA métier** (`/admin/content/ia-generation`). Sinon le fichier **`resources/ia/generation.json`**.

L’UI n’écrit pas le JSON sur le disque (déploiement / git). Admin uniquement, enregistrement et reset protégés par `password.confirm`. Chargeur : `GenerationConfigStore` + `GenerationConfigLoader`.

L’IA **ne réécrit pas une fiche Dofus entière**. Laravel recopie ce qui est figé ; le modèle ne reçoit que les clés `writable`. **Exception : les PNJ** (création complète, éventuellement à partir d’une page de site).

## Comment éditer

Quatre types historiques plus le consommable : `item`, `spell`, `monster`, `npc`, `consumable`.

| Clé | Rôle |
| --- | --- |
| `frozen_fields` | Colonnes figées. `"*"` = toutes. |
| `writable_fields` | Exceptions : **gagne** sur `frozen_*`. |
| `frozen_characteristics` | Clés `characteristics.key` (ex. `intelligence_object`). `"*"` = toutes. |
| `writable_characteristics` | Caracs que l’IA **peut** toucher malgré le joker (libellé admin : « Caracs que l’IA peut modifier »). |
| `example_ids` | `official_id` ou nom d’une fiche `playable` (pas un id SQL portable). Dans l’admin, un sélecteur cherche via `api.tables.{type}` (défaut `state=playable`). Pool vide refusé à l’assembleur. |
| `few_shot_panoplies` (objets, extra) | Noms des panoplies `playable` que l’IA doit imiter. Portable entre bases. Détail : [CATALOGUE](./CATALOGUE.md#liste-few-shot-panoplies-ce-que-lia-doit-imiter). |
| `has_dofus_source` | `true` : recopier l’identité depuis la fiche `raw`. |
| `generation.*` | Variables globales (`max_retries`, `few_shot_count`, …). |

On peut ajouter **n’importe quelle clé** (`tone`, `prompt_pack`, …). `$loader->get('chemin.pointé')` la lit ; sur une entité, elle atterrit dans `extra`. Les clés `_…` sont de la doc, ignorées.

Défiger une seule carac d’objet sans tout lister :

```json
"frozen_characteristics": "*",
"writable_characteristics": ["intelligence_object"]
```

Les caracs déjà dans les normes et **figées** ne passent pas par le LLM. L’**algo** de scrap (filtre type, `norms_grid`) peut toujours les aligner **avant**, ce n’est pas l’IA.

Objet unique de scénario (sans source Dofus) : on ignore le gel, comme un PNJ.

## Défauts actuels du JSON

| Type | Figé | IA |
| --- | --- | --- |
| **Objets** | Tous les champs et toutes les caracs | Rien (uniques de scénario hors gel) |
| **Sorts** | Tout sauf `effect` ; toutes les caracs (PA, portée…) | Texte d’effets (1 + 0–2 secondaires) |
| **Monstres** | Tous les champs et toutes les caracs | 2–3 sorts-créature (paquet, pas des colonnes) |
| **PNJ** | Rien | Toute la fiche ; kit = ids `playable` |
| **Consommables** | Tous les champs et toutes les caracs sauf `effect` | Texte d’effet lisible à table |

**Source optionnelle PNJ (site)** : page encyclopédie / wiki / DofusDB. Laravel extrait nom / portrait / lore. Pas de scrap de masse, pas de `dofusdb_id` sur `Npc`.

## Consigne pour le JSON Schema

- Entité sourcée : le modèle **renvoie seulement les clés `writable`**. Laravel recopie le reste depuis `raw`.
- PNJ (et objet unique sans source) : le modèle remplit l’identité **et** le kit ; le validateur refuse les ids hors liste.
