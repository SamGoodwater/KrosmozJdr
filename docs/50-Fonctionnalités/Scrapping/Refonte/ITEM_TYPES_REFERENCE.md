# Référence item-types DofusDB

Ce document décrit comment les **232 item-types** de l’API DofusDB sont pris en compte dans la config KrosmozJDR.

## Fichiers concernés

| Fichier | Rôle |
|--------|------|
| **resources/scrapping/v2/sources/dofusdb/item-types.json** | Catalogue de référence : liste des 232 item-types (id, superTypeId, categoryId, nameFr). Utilisé pour validation et pour s’assurer qu’aucun type n’est oublié. |
| **resources/scrapping/sources/dofusdb/item-super-types.json** | Mapping métier : superTypeId → catégorie KrosmozJDR (equipment / resource / consumable / excluded). Contient **superTypesReference** (tous les superTypes présents dans l’API) et les **groups** (resource, consumable, equipment). |

## API DofusDB

- **Endpoint** : `GET https://api.dofusdb.fr/item-types?$skip=0&$limit=50&lang=fr`
- **Pagination** : 5 pages (skip=0, 50, 100, 150, 200) pour couvrir les 232 types.
- **Réponse** : `{ total, limit, skip, data: [ { id, superTypeId, categoryId, name: { fr, en, ... } }, ... ] }`

## superTypesReference (item-super-types.json)

Tous les **superTypeId** renvoyés par l’API sont documentés dans `superTypesReference` avec :

- **id** : superTypeId DofusDB
- **nameFr** : libellé français
- **krosmozCategory** : `equipment` | `resource` | `consumable` | `excluded`

| superTypeId | nameFr (résumé) | krosmozCategory |
|-------------|----------------|-----------------|
| 1 | Amulette | equipment |
| 2 | Arme | equipment |
| 3 | Anneau | equipment |
| 4 | Ceinture | equipment |
| 5 | Bottes | equipment |
| 6 | Consommable | consumable |
| 7 | Bouclier | equipment |
| 9 | Ressource | resource |
| 10 | Chapeau | equipment |
| 11 | Cape | equipment |
| 12 | Familier | equipment |
| 13 | Dofus / Trophée / Prysmaradite | equipment |
| 14 | Objet de quête | excluded |
| 15–20, 22–27, 69 | Mutation, Nourriture, Cosmétiques, etc. | excluded |
| 70 | Consommables de combat | consumable |

## Groups (resource / consumable / equipment)

- **resource** : `include` superTypeIds [9].
- **consumable** : `include` superTypeIds [6, 70].
- **equipment** : `exclude` superTypeIds [6, 9, 14, 15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 69, 70] (tout le reste = équipement « jouable »).

Les services (`DofusDbItemSuperTypeMappingService`, `ItemEntityTypeFilterService`) utilisent ces groups pour dériver les listes de typeId (allowlist / exclusions) et les registries DB.

## Rafraîchir le catalogue item-types.json

Pour resynchroniser la liste des 232 item-types depuis l’API :

```bash
php -r '
$base = "https://api.dofusdb.fr/item-types";
$all = [];
for ($skip = 0; $skip < 250; $skip += 50) {
    $url = $base . "?\$skip=$skip&\$limit=50&lang=fr";
    $j = @file_get_contents($url);
    if (!$j) break;
    $d = json_decode($j, true);
    if (!isset($d["data"]) || !is_array($d["data"])) break;
    foreach ($d["data"] as $row) {
        $all[] = [
            "id" => (int)($row["id"] ?? 0),
            "superTypeId" => (int)($row["superTypeId"] ?? 0),
            "categoryId" => (int)($row["categoryId"] ?? 0),
            "nameFr" => $row["name"]["fr"] ?? "",
        ];
    }
    if (count($d["data"]) < 50) break;
}
$out = [
    "metadata" => [
        "source" => "dofusdb",
        "endpoint" => "/item-types",
        "total" => count($all),
        "description" => "Référence des item-types DofusDB (API /item-types).",
        "lastSync" => date("c"),
    ],
    "itemTypes" => $all,
];
file_put_contents("resources/scrapping/v2/sources/dofusdb/item-types.json", json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Written " . count($all) . " item-types.\n";
'
```

Après un rafraîchissement, vérifier si de nouveaux **superTypeId** apparaissent et, le cas échéant, les ajouter dans **item-super-types.json** (`superTypesReference` et, si besoin, dans les `groups`).
