# Données des seeders de caractéristiques

La source versionnée est le répertoire **`database/seeders/data/characteristic-definitions/{creature,object,spell}/`** : un fichier JSON par caractéristique (`{stem}-{groupe}-definition.json`), avec un bloc `characteristic` (table `characteristics`) et un objet `entities` (lignes pivot par entité).

Pour **régénérer** ces fichiers depuis la BDD après modification en admin : `php artisan scrapping:seeders:export --characteristics` (alias : `db:export-seeder-data`). Voir aussi `database/seeders/data/README.md` et [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md) § 6.

---

## 1. Vue d’ensemble

| Emplacement | Rôle |
|-------------|------|
| **`characteristic-definitions/*/ *-definition.json`** | Bloc `characteristic` : clé, libellés, type, groupe, visuels, `linked_to_key`. Bloc `entities` : min/max, formules, conversion, normes, spécificités objet (`forgemagie_max`, `item_type_ids`, etc.) par clé d’entité (`*`, `monster`, `spell`, …). |

---

## 3. Groupe object : bonus des équipements

Les **bonus que peuvent donner les équipements** (armes, chapeaux, capes, amulettes, bottes, anneaux, ceintures, boucliers) sont définis comme caractéristiques du groupe **object**, d’après le document **Equipements et forgemagie.pdf** (docs/110- To Do).

Exemples : bonus de touche, dommages fixes (neutre, terre, feu, air, eau, multiple), PV max, Vitalité, Sagesse, PA, PM, Esquive PA/PM, Tacle, Fuite, CA, résistances fixes, résistance 50 %, invulnérabilité 100 %, etc.

- **Limites (min/max)** et **forgemagie** (`forgemagie_max`, etc.) sont renseignées dans les entités JSON du groupe **object** selon les tableaux du PDF.
- **Prix par unité** (`base_price_per_unit`) : prix de base par point de bonus pour la création d’équipement (kamas).
- **Prix rune par unité** (`rune_price_per_unit`) : prix de la rune de forgemagie par unité (brisage / forgemagie). Null si pas de rune (ex. bonus de touche, CA, résistance 50 %, invulnérabilité).
- Le **stockage** côté item : le modèle `Item` possède un champ `bonus` (texte/JSON). Les caractéristiques bonus ont `db_column` à null ; une évolution peut prévoir un mapping clé caractéristique → clé dans le JSON `bonus` pour lecture/écriture.
- **Panoplies** : le bonus de panoplie (champ `bonus` sur `panoplies`) est converti via le même formatter `itemEffectsToKrosmozBonus` que les items, avec `entityType` = `panoply`, donc formules et limites des définitions **object** (entité panoply ou *) s’appliquent. Les règles de mapping panoply sont en BDD (`scrapping_entity_mappings`) et liées aux caractéristiques via la table pivot `scrapping_entity_mapping_characteristic` (voir `ScrappingEntityMappingCharacteristicSeeder`).
- **Consommables et ressources** : la règle de mapping « effect » (entity item) cible aussi `resources.effect` et `consumables.effect`. Lors du scrapping, selon le type d’item (équipement, ressource ou consommable), le bonus converti est écrit dans `items.effect`, `resources.effect` ou `consumables.effect`, en utilisant les formules et limites du groupe object.

---

## 4. Groupe spell : propriétés du sort vs effets du sort

Les **caractéristiques du groupe spell** décrivent les **propriétés du sort** stockées sur le modèle **Spell** : niveau, coût en PA, portée (PO), zone (area), élément, puissance, lancers par tour / par cible, ligne de vue, délai entre deux lancers, catégorie, sort magique (booléen).

Les **effets que peut infliger un sort** (dégâts, soins, retrait PA/PM, bouclier, états, placement, invocation, etc.) ne sont **pas** des caractéristiques au sens des seeders. Ils sont gérés par :

- **SpellEffectType** : référentiel des types d’effets (catégories : damage, heal, ap, pm, shield, state, etc.).
- **SpellEffect** : instances d’effets liées à un sort (valeurs min/max, dés, durée, cible, etc.).

Voir [Spell-Effects/TAXONOMIE_EFFETS_SORTS.md](../Spell-Effects/TAXONOMIE_EFFETS_SORTS.md) pour la taxonomie complète des effets (dommages, soins, retrait PA/PM, protection, états, etc.).
