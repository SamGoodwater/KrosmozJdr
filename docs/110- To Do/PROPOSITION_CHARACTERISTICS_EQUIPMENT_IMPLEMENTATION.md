# Proposition : intégration formules, forgemagie et équipements dans la config characteristics

Références : `Caractéristiques.pdf`, `Equipements et forgemagie.pdf`.

---

## 1. Objectifs

- **Formules de calcul final** : ex. CA = 10 + Mod. Vitalité + équipement + forgemagie.
- **Valeurs max** pour classe, item et forgemagie (déjà partiellement présentes).
- **Indication forgemagie** : autorisée ou non, et bonus max par caractéristique.
- **Monstres** : même formules mais sans équipement ni forgemagie → min/max à ±50 % par rapport à la classe.
- **Équipements** : quel type d’équipement peut donner quelle caractéristique, et bonus max selon le niveau (tableau ou formule).

---

## 2. Proposition A — Tout dans `config/characteristics.php`

### 2.1 Par caractéristique : champs ajoutés

Pour chaque entrée de `characteristics` (et compétences si pertinent) :

| Clé | Type | Description |
|-----|------|-------------|
| **formula_display** | `string \| null` | Formule affichée pour le calcul final (ex. `"10 + Mod. Vitalité + équipement + forgemagie"`). Utilisée pour l’UI / la doc. |
| **forgemagie** | `array` | `allowed` (bool), `max` (int) : bonus max ajouté par forgemagie sur un objet (ex. PA +1, compétences +3). |

Exemple pour la CA :

```php
'ca' => [
    // ... champs existants (name, type, entities, etc.) ...
    'formula_display' => '10 + Mod. Vitalité + équipement + forgemagie',
    'forgemagie' => ['allowed' => false, 'max' => 0],
],
```

Exemple pour les PA (forgemagie +1) :

```php
'pa' => [
    // ...
    'formula_display' => '6 (base) + équipement + forgemagie',
    'forgemagie' => ['allowed' => true, 'max' => 1],
],
```

### 2.2 Monstres : règle ±50 %

- **Option 1 (recommandée)** : garder les min/max **explicites** dans `entities.monster` (comme aujourd’hui) et documenter en tête de fichier que la règle métier est : *pour les monstres (sans équipement ni forgemagie), les bornes sont environ 50 % plus basses (min) et 50 % plus hautes (max) que pour la classe*.
- **Option 2** : ne stocker que les bornes **class** et calculer en PHP les bornes monster : `monster_min = floor(class_min * 0.5)`, `monster_max = ceil(class_max * 1.5)` (à utiliser dans un helper ou un service). La config ne contient alors que class (et item).

On peut partir sur l’option 1 pour rester lisible et éviter de casser l’existant.

### 2.3 Équipements : quel slot donne quelle caractéristique

Deux sous-options :

**A1 — Par caractéristique : `equipment_slots`**

Dans chaque caractéristique, ajouter :

```php
'equipment_slots' => [
    'shield' => [
        'max_by_level' => [1=>0, 2=>1, 3=>1, 4=>2, 5=>2, 6=>3, 7=>3, 8=>4, 9=>4, 10=>5, 11=>5, 12=>5, 13=>5, 14=>5, 15=>5, 16=>5, 17=>5, 18=>5, 19=>5, 20=>5],
        'forgemagie_max' => null,
    ],
],
```

- **max_by_level** : tableau `niveau (1..20) => bonus max` pour ce slot sur cette caractéristique. Fidèle au PDF (paliers par niveau).
- Si une formule simple existe (ex. `min(5, floor((level+1)/4))`), on peut ajouter une clé **formula** à la place de **max_by_level** et l’utiliser dans le code pour calculer le max au niveau N.

**A2 — Fichier séparé `config/equipment_characteristics.php` (par slot)**

Un fichier dédié, structuré **par type d’équipement** :

```php
return [
    'slots' => [
        'weapon' => [
            'name' => 'Arme',
            'characteristics' => [
                'touch' => ['max_by_level' => [...], 'forgemagie_max' => null],
                'do_fixe_neutre' => ['max_by_level' => [...], 'forgemagie_max' => 5],
                'do_fixe_terre' => ['max_by_level' => [...], 'forgemagie_max' => 5],
                // ...
            ],
        ],
        'hat' => [
            'name' => 'Chapeau',
            'characteristics' => [
                'athletisme' => ['max_by_level' => [...], 'forgemagie_max' => 3], // compétences
                'life' => ['max_by_level' => [...], 'forgemagie_max' => 20],
                'vitality' => ['max_by_level' => [...], 'forgemagie_max' => 2],
                'wisdom' => ['max_by_level' => [...], 'forgemagie_max' => 2],
                // ...
            ],
        ],
        'amulet' => ['name' => 'Amulette', 'characteristics' => ['life' => [...], 'pa' => [...], 'dodge_pa' => [...]]],
        'boots' => ['name' => 'Bottes', 'characteristics' => ['life' => [...], 'pm' => [...], 'dodge_pm' => [...]]],
        'ring' => ['name' => 'Anneau', 'characteristics' => ['invocation' => [...], 'po' => [...], 'life' => [...]]],
        'belt' => ['name' => 'Ceinture', 'characteristics' => ['tacle' => [...], 'fuite' => [...], 'master_bonus' => [...]]],
        'shield' => ['name' => 'Bouclier', 'characteristics' => ['ca' => [...], 'res_fixe_neutre' => [...], ...]],
        'cape' => ['name' => 'Cape', 'characteristics' => ['ini' => [...], 'life' => [...], ...]],
    ],
];
```

- Avantage : un seul endroit par slot, facile à maintenir avec le PDF « Equipements et forgemagie ».
- Inconvénient : les infos « quel slot donne quelle carac » sont dans un fichier différent de `characteristics.php`.

---

## 3. Proposition B — Hybride (recommandé)

- **Dans `config/characteristics.php`** (par caractéristique) :
  - **formula_display** (optionnel).
  - **forgemagie** : `allowed`, `max`.
  - Pas de `equipment_slots` ici pour éviter de dupliquer les infos forgemagie et de surcharger le fichier.

- **Fichier `config/equipment_characteristics.php`** (ou `config/equipment_slots.php`) :
  - Définition **par slot** (weapon, hat, cape, amulet, boots, ring, belt, shield).
  - Pour chaque slot : liste des caractéristiques qu’il peut donner + **max_by_level** (tableau 1..20) et **forgemagie_max** par caractéristique.
  - Référence aux ids de `characteristics` (ex. `pa`, `life`, `ca`).

- **Monstres** : garder min/max explicites dans `entities.monster` + doc en tête de fichier (règle ±50 %).

Résumé :

- **characteristics.php** : formules affichées, min/max class + monster + item, forgemagie (allowed + max) par caractéristique.
- **equipment_characteristics.php** : qui donne quoi (slot → caractéristiques) et progression par niveau (+ forgemagie max par slot/carac si on veut la détailler par slot).

---

## 4. Montée par niveau : tableau ou formule

- **Tableau** : `max_by_level` = `[1=>v1, 2=>v2, ..., 20=>v20]`. Fidèle au PDF, facile à relire, un peu verbeux.
- **Formule** : quand une règle simple existe (ex. bonus = `floor((level+1)/4)` pour certains cas), on peut ajouter une clé **formula** du type `"floor(([level]+1)/4)"` et l’évaluer dans un helper. Sinon, rester sur le tableau.

Proposition : **tableau par défaut** pour coller au PDF ; ajouter une **formula** optionnelle plus tard si on identifie des lois communes.

---

## 5. Récap à valider

1. **characteristics.php**  
   - Ajouter pour chaque caractéristique (et compétences si vous le souhaitez) :
     - **formula_display** (string, optionnel).
     - **forgemagie** : `['allowed' => bool, 'max' => int]`.

2. **Monstres**  
   - Garder les min/max explicites dans `entities.monster`.  
   - Documenter en tête de `characteristics.php` la règle : *monstres sans équipement ni forgemagie, bornes environ ±50 % par rapport à la classe*.

3. **Équipements**  
   - Nouveau fichier **config/equipment_characteristics.php** (ou nom à votre goût).  
   - Structure par **slot** (weapon, hat, cape, amulet, boots, ring, belt, shield).  
   - Pour chaque slot : liste de **characteristic_id** avec **max_by_level** (tableau 1..20) et **forgemagie_max**.  
   - Réutiliser les ids de `config/characteristics.php`.

4. **Niveau**  
   - Utiliser un **tableau** `max_by_level` pour l’instant ; formule optionnelle plus tard si besoin.

Dès que vous validez ce schéma (ou les variantes que vous préférez, ex. tout dans characteristics avec `equipment_slots`), on peut détailler les exemples concrets (CA, PA, PM, compétences, etc.) et les implémenter dans le projet.
