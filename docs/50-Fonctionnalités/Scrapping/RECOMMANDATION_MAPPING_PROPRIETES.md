# Recommandation : mapping propriétés Krosmoz ↔ DofusDB

## Question

Faut-il un fichier JSON (ou équivalent) répertoriant toutes les propriétés des entités scrappables, avec pour chaque propriété le chemin côté API DofusDB ?

## Réponse courte

**Non, pas un nouveau fichier dédié.** La source de vérité existe déjà : les **fichiers de config d’entité** dans `resources/scrapping/config/sources/dofusdb/entities/` (ex. `monster.json`, `spell.json`). Chaque fichier contient un tableau **`mapping`** qui fait exactement ce lien : propriété Krosmoz → chemin DofusDB (`from.path`) → formatters → champs cibles (`to[].model` / `to[].field`).

---

## 1. Ce qui existe déjà

| Fichier | Rôle |
|--------|------|
| `entities/monster.json` | Mapping complet : `key` (ex. `level`, `name`), `from.path` (ex. `grades.0.level`, `name`), `to[].field`, `formatters`. |
| `entities/spell.json` | Idem pour les sorts. |
| `entities/breed.json` | Idem pour les classes. |
| `entities/item.json`, `panoply.json`, etc. | Idem pour items, panoplies. |
| `dofusdb_characteristic_to_krosmoz.json` | Mapping spécifique : id caractéristique DofusDB → `characteristic_key` Krosmoz (effets d’objets). |

Exemple dans `monster.json` :

```json
{
  "key": "level",
  "from": { "path": "grades.0.level" },
  "to": [{ "model": "creatures", "field": "level" }],
  "formatters": [{ "name": "dofusdb_level", "args": {} }]
}
```

Donc : **propriété Krosmoz** = `key` (et champ cible `to[].field`), **chemin DofusDB** = `from.path`. C’est déjà la liste “propriétés utiles + chemin API”.

---

## 2. Ne pas intégrer au système de caractéristiques

Le **système de caractéristiques** (modèles `Characteristic`, `CharacteristicCreature`, etc.) sert à :

- définir les règles métier (formules, limites, dés de vie, etc.) ;
- lier caractéristiques aux entités (créature, objet, sort).

Le **lien “où lire la valeur dans l’API DofusDB”** relève de l’**intégration scrapping**, pas du métier JDR. Si on mettait les chemins DofusDB dans les caractéristiques :

- le domaine métier dépendrait d’une API externe ;
- toute évolution DofusDB (renommage, structure) impacterait le cœur du modèle.

**Recommandation :** garder les caractéristiques pour le métier ; garder le mapping DofusDB dans la config scrapping (fichiers entité).

---

## 3. Approche recommandée

### 3.1 Une seule source de vérité : les JSON d’entité

- **Backend (conversion, prévisualisation)**  
  Utiliser les `mapping` des fichiers `entities/{entity}.json` pour :
  - savoir quels chemins lire dans le brut DofusDB ;
  - savoir quelles propriétés Krosmoz exposer (clés du mapping).

- **Frontend (tableau de comparaison “Brut / Converti / Krosmoz”)**  
  Au lieu de dupliquer une whitelist en dur dans le Vue, on peut :
  - soit exposer une API du type `GET /api/scrapping/config/property-keys?entity=monster` qui lit le `mapping` du JSON et renvoie la liste des `key` (et optionnellement `from.path`) ;
  - soit charger ces configs au build et générer un module (ex. liste des clés autorisées par entité) pour le front.

Ainsi, **toutes les propriétés affichées** et **tous les chemins DofusDB** viennent du même endroit : les JSON d’entité.

### 3.2 Fichier JSON dédié “property_mapping” ?

- **Option A – Nouveau JSON global** (ex. `property_mapping.json` listant toutes les entités et toutes les propriétés) : **à éviter** car cela duplique ce qui est déjà dans `entities/*.json`. Double maintenance et risque de désynchronisation.

- **Option B – Rester sur les JSON d’entité** : **recommandé**. Une entité = un fichier ; le mapping propriété ↔ chemin DofusDB est dans ce fichier. Pour avoir “toutes les propriétés de toutes les entités”, le backend ou un script peut **agréger** les `mapping` de tous les `entities/*.json` (lecture de répertoire + merge des clés par entité).

### 3.3 Résumé

| Besoin | Où le faire |
|--------|--------------|
| Liste des propriétés par entité scrappable | Lire le tableau `mapping` des `entities/{entity}.json`. |
| Chemin DofusDB pour une propriété | `mapping[].from.path` dans le même fichier. |
| Affichage “propriétés utiles” dans l’UI | Dériver la whitelist depuis ces mapping (API ou build), plutôt qu’une liste en dur dans le front. |
| Caractéristiques métier (formules, limites) | Rester dans le système de caractéristiques, sans y mettre les chemins DofusDB. |

---

## 4. Suite possible

1. **Documenter** dans le projet (par ex. dans ce fichier ou dans `SCHEMA_CONFIG.md`) que les **propriétés utiles Krosmoz** et les **chemins DofusDB** sont définis dans `resources/scrapping/config/sources/dofusdb/entities/*.json`.
2. **Exposer** (si utile) un endpoint ou une méthode qui retourne, par type d’entité, la liste des clés de mapping (et optionnellement les chemins), pour alimenter le tableau de comparaison sans dupliquer la whitelist en front.
3. **Vérifier** que chaque entité scrappable (monster, spell, breed, item, resource, consumable, panoply) a bien un fichier `entities/{entity}.json` avec un `mapping` à jour.

Ainsi, on évite un fichier JSON supplémentaire redondant et on ne mélange pas le système de caractéristiques avec l’intégration DofusDB.
