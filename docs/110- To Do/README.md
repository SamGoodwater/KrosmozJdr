# 110 – To Do (caractéristiques, formules, équipements)

Ce dossier rassemble les **sources métier** (PDF) et la **proposition d’intégration** pour les caractéristiques, formules de calcul, forgemagie et équipements.

---

## Contenu du dossier

| Fichier | Description |
|--------|-------------|
| **PROPOSITION_CHARACTERISTICS_EQUIPMENT_IMPLEMENTATION.md** | Proposition détaillée : formula_display, forgemagie, monstres ±50 %, équipements par slot, tableau vs formule. |
| **Caractéristiques.pdf** | Règles métier des caractéristiques. |
| **Equipements et forgemagie.pdf** | Règles équipements et forgemagie. |
| **Généralités Classes.pdf** | Contexte classes. |
| **Creation sort.pdf** | Création de sorts. |
| **Système de soin.pdf** | Système de soins. |

---

## Checklist (proposition vs implémenté)

D’après *PROPOSITION_CHARACTERISTICS_EQUIPMENT_IMPLEMENTATION.md*, section 5 « Récap à valider » :

| Point | Statut | Détail |
|-------|--------|--------|
| **1. characteristics.php** | ✅ | `formula_display` et `forgemagie` (allowed, max) présents en config ; CharacteristicConfigSeeder importe vers BDD (characteristics + characteristic_entities). |
| **2. Monstres** | ✅ | Min/max explicites dans `entities.monster` ; règle ±50 % documentée en tête de `config/characteristics.php`. |
| **3. Équipements** | ✅ | Fichier **config/equipment_characteristics.php** par slot (weapon, hat, cape, amulet, boots, ring, belt, shield) avec characteristic_id, bracket_max, forgemagie_max. EquipmentCharacteristicConfigSeeder importe vers equipment_slots et equipment_slot_characteristics. |
| **4. Niveau (max par niveau)** | ✅ | Tableau **bracket_max** (paliers 1–2, 3–4, …, 19–20) utilisé dans la config ; formule optionnelle possible plus tard. |
| **5. Formules de conversion Dofus → JDR** | ✅ | En BDD : `dofusdb_conversion_formulas` (level, life, attributes, ini, **res_neutre + handler**). Handlers (ex. résistances) configurables via admin. Voir `docs/50-Fonctionnalités/Characteristics-DB/`. |

---

## Seeders et préremplissage

Les seeders suivants préremplissent les données à partir des configs (et d’un bloc dédié pour les résistances).

### Ordre d’exécution recommandé

1. **CharacteristicConfigSeeder**  
   Prérequis pour les caractéristiques (ids utilisés par les autres seeders).

2. **DofusdbConversionFormulaSeeder**  
   Dépend des `characteristic_id` existants (level, life, ini, res_neutre, etc.).

3. **EquipmentCharacteristicConfigSeeder**  
   Dépend des `characteristic_id` existants (life, pa, ca, compétences, etc.).

### Détail des seeders

| Seeder | Source | Données préremplies |
|--------|--------|---------------------|
| **CharacteristicConfigSeeder** | `config/characteristics.php` → `characteristics.characteristics` | Toutes les caractéristiques (id, name, type, entities, forgemagie, etc.) + characteristic_entities (min, max, formula, formula_display, forgemagie_allowed/max pour item). |
| **DofusdbConversionFormulaSeeder** | `config/dofusdb_conversion.php` → `formulas` + bloc dédié | **level** : k = d / 10 (monster, class, item). **life** : k = d/200 + level×5 (monster, class). **strength, intelligence, chance, agility** : formules sqrt par entité. **ini** : ratio initiative (monster, class). **res_neutre** : handler `resistance_dofus_to_krosmoz` pour monster, class, item (paramètres plafonds : max_invulnerable, max_resistant, max_weak, max_vulnerable). |
| **EquipmentCharacteristicConfigSeeder** | `config/equipment_characteristics.php` → `slots` | Slots (weapon, hat, cape, amulet, boots, ring, belt, shield) + pour chaque slot les characteristic_id avec bracket_max et forgemagie_max. |

### Exécution

```bash
php artisan db:seed --class=CharacteristicConfigSeeder
php artisan db:seed --class=DofusdbConversionFormulaSeeder
php artisan db:seed --class=EquipmentCharacteristicConfigSeeder
```

Pour inclure ces seeders dans un seed global, les ajouter dans `database/seeders/DatabaseSeeder.php` dans l’ordre ci‑dessus (après les types / pages si besoin).

---

## À faire / évolutions possibles

- **Formula_display** : vérifier que toutes les caractéristiques concernées ont une formule d’affichage cohérente avec les PDF.
- **Formule optionnelle** pour max par niveau : si une loi commune est identifiée (ex. `floor((level+1)/4)`), ajouter une clé `formula` en complément du tableau dans la config.
- **Tests** : couvrir DofusdbConversionFormulaSeeder (notamment res_neutre + handler) et EquipmentCharacteristicConfigSeeder.
- **Documentation** : mettre à jour les PDF ou les annexes si les règles métier évoluent.
