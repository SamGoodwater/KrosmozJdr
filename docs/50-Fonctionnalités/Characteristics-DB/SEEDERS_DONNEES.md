# Données des seeders de caractéristiques

Ce document décrit le contenu des fichiers de données des seeders (`database/seeders/data/characteristics.php`, `characteristic_creature.php`, `characteristic_object.php`, `characteristic_spell.php`) et le lien avec les autres modules (équipements, effets de sorts). Pour **régénérer** ces fichiers à partir de la BDD : `php artisan db:export-seeder-data` (option `--characteristics`). Fichiers produits : `database/seeders/data/characteristics.php`, `characteristic_creature.php`, `characteristic_object.php`, `characteristic_spell.php`. Voir [ARCHITECTURE_SOUS_SERVICES.md](./ARCHITECTURE_SOUS_SERVICES.md) § 6.

---

## 1. Vue d’ensemble

| Fichier | Rôle |
|---------|------|
| **characteristics.php** | Définitions globales : clé, nom, short_name, helper, type, sort_order. Une ligne par caractéristique (suffixe _creature, _object, _spell). |
| **characteristic_creature.php** | Groupe créature (monster, class, npc) : db_column, min, max, default_value, conversion_formula, etc. |
| **characteristic_object.php** | Groupe objet (item, consumable, resource, panoply) : idem + forgemagie_allowed, forgemagie_max pour les bonus équipements. |
| **characteristic_spell.php** | Groupe sort : propriétés du modèle Spell (level, pa, po, area, element, cast_per_turn, etc.). |

---

## 3. Groupe object : bonus des équipements

Les **bonus que peuvent donner les équipements** (armes, chapeaux, capes, amulettes, bottes, anneaux, ceintures, boucliers) sont définis comme caractéristiques du groupe **object**, d’après le document **Equipements et forgemagie.pdf** (docs/110- To Do).

Exemples : bonus de touche, dommages fixes (neutre, terre, feu, air, eau, multiple), PV max, Vitalité, Sagesse, PA, PM, Esquive PA/PM, Tacle, Fuite, CA, résistances fixes, résistance 50 %, invulnérabilité 100 %, etc.

- **Limites (min/max)** et **forgemagie** (`forgemagie_allowed`, `forgemagie_max`) sont renseignées dans `characteristic_object` selon les tableaux du PDF.
- **Prix par unité** (`base_price_per_unit`) : prix de base par point de bonus pour la création d’équipement (kamas).
- **Prix rune par unité** (`rune_price_per_unit`) : prix de la rune de forgemagie par unité (brisage / forgemagie). Null si pas de rune (ex. bonus de touche, CA, résistance 50 %, invulnérabilité).
- Le **stockage** côté item : le modèle `Item` possède un champ `bonus` (texte/JSON). Les caractéristiques bonus ont `db_column` à null ; une évolution peut prévoir un mapping clé caractéristique → clé dans le JSON `bonus` pour lecture/écriture.

---

## 4. Groupe spell : propriétés du sort vs effets du sort

Les **caractéristiques du groupe spell** décrivent les **propriétés du sort** stockées sur le modèle **Spell** : niveau, coût en PA, portée (PO), zone (area), élément, puissance, lancers par tour / par cible, ligne de vue, délai entre deux lancers, catégorie, sort magique (booléen).

Les **effets que peut infliger un sort** (dégâts, soins, retrait PA/PM, bouclier, états, placement, invocation, etc.) ne sont **pas** des caractéristiques au sens des seeders. Ils sont gérés par :

- **SpellEffectType** : référentiel des types d’effets (catégories : damage, heal, ap, pm, shield, state, etc.).
- **SpellEffect** : instances d’effets liées à un sort (valeurs min/max, dés, durée, cible, etc.).

Voir [Spell-Effects/TAXONOMIE_EFFETS_SORTS.md](../Spell-Effects/TAXONOMIE_EFFETS_SORTS.md) pour la taxonomie complète des effets (dommages, soins, retrait PA/PM, protection, états, etc.).
