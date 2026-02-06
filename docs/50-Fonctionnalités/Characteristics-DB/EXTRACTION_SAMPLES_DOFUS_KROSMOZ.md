# Extraction des samples Dofus et Krosmoz — Faisabilité

Ce document analyse si l’on peut **automatiser** l’extraction des échantillons **conversion_dofus_sample** et **conversion_krosmoz_sample** (niveau → valeur) en s’appuyant sur le **service de scrapping** et sur la **documentation** (règles + To Do).

**Contexte :** [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](./PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md) (échantillons par niveau, formules de conversion).

---

## 1. Objectif des samples

- **conversion_dofus_sample** : pour des niveaux donnés (ex. 1, 40, 80, 120, 160, 200), la **valeur de la caractéristique côté Dofus** (ex. PV, Force, PA).
- **conversion_krosmoz_sample** : pour les **mêmes niveaux** (ou une échelle JDR 1–20), la **valeur cible côté JDR**.

Ces paires permettent de proposer une formule de conversion (linéaire, carré, table) dans l’admin caractéristiques.

---

## 2. Côté Dofus — Service de scrapping

### 2.1 Source : API DofusDB (monstres)

- Le scrapping utilise **DofusDbClient** et la config **monster** (`resources/scrapping/config/sources/dofusdb/entities/monster.json`).
- Chaque monstre exposé par l’API a un tableau **`grades`** : chaque grade = un niveau du monstre avec ses stats (niveau, PV, Force, Intelligence, Agilité, Sagesse, Chance, Vitalité, PA, PM, résistances, etc.).
- Aujourd’hui le mapping ne prend que **`grades.0`** (premier grade). Donc une seule paire (niveau, valeur) par monstre.

**Extraction possible :**

1. **Par monstre** : pour chaque monstre récupéré, lire **tous** les grades (`grades[0]`, `grades[1]`, …) au lieu de seulement `grades.0`.
2. Pour chaque caractéristique (ex. `lifePoints`, `strength`, `agility`, …), collecter les paires **(niveau du grade, valeur)**.
3. **Agrégation** : sur un échantillon de monstres (ex. filtrés par `levelMin` / `levelMax`), agréger par niveau (médiane ou moyenne des valeurs à ce niveau) pour obtenir une courbe **niveau Dofus → valeur Dofus**.
4. Résultat : un objet du type `{"1": …, "40": …, "80": …, "120": …, "160": …, "200": …}` = **conversion_dofus_sample** pour cette caractéristique et cette entité (ex. `monster`).

**Limites :**

- **Classes (breeds)** : la config indique « pas de level, life ni attributs » côté DofusDB ; pas de grades. Donc pas d’extraction automatique de samples pour les classes depuis l’API.
- **Objets (items)** : à vérifier si l’API équipement expose un niveau et des stats par niveau ; si oui, même principe (niveau → valeur) pour le groupe **object**.

**Verdict Dofus :** **Oui, automatisable** pour les **monstres** (et éventuellement les équipements si l’API le permet), en étendant le flux de scrapping pour parcourir tous les `grades` et agréger par niveau.

---

## 3. Côté JDR (Krosmoz) — Règles et To Do

### 3.1 Documentation structurée (400- Règles, Markdown)

- **2.2.1 Caractéristiques principales** : tableau **Niveau | Mod max base | Score max base** pour les niveaux 1 à 20 (mod max = ⌊Niveau/2⌋ + 1, score max dérivé).
- On peut **parser ce Markdown** (ou un export structuré) pour obtenir une table niveau → valeur pour les caractéristiques qui suivent ce plafond (ex. score de caractéristique max par niveau).
- **2.2.2 Caractéristiques secondaires** : PA (6 base, 12 max), PM (3 base, 6 max), PV (« Fonction de la classe »), etc. Pas toujours une courbe niveau → valeur explicite ; souvent des formules ou des plafonds fixes.
- **4.2.2 Caractéristiques et statistiques (créatures)** : exemples de fourchettes (créature faible/moyenne/puissante) mais pas de table niveau → valeur exhaustive.

**Extraction possible :**

- Pour les caractéristiques dont la règle donne une **table niveau → valeur** (ex. mod max / score max par niveau), un script peut extraire ces tables depuis les fichiers Markdown (regex ou parsing léger) et produire **conversion_krosmoz_sample** (ex. niveau 1→1, 2→2, …, 20→20 pour une échelle linéaire, ou les scores max par niveau).
- Pour les autres (PV, PA, PM, etc.), les règles donnent des **formules** (ex. « PV = Vitalité × 10 + dés de vie ») : on ne déduit pas directement une table niveau → valeur sans fixer d’autres paramètres (classe, vitalité, etc.). On peut toutefois **définir des conventions** (ex. niveau 1→X, 20→Y) et les utiliser comme samples par défaut.

**Verdict règles Markdown :** **Partiellement automatisable** : extraction des tables explicites (niveau → mod/score) ; pour le reste, convention ou formule dérivée.

### 3.2 Documents To Do (110- To Do) — PDF

- **Caractéristiques.pdf**, **Equipements et forgemagie.pdf** : d’après [ANALYSE_PDF_CARACTERISTIQUES_EQUIPEMENTS.md](./ANALYSE_PDF_CARACTERISTIQUES_EQUIPEMENTS.md), ils contiennent des **tableaux** (valeur de base, valeur limite, bonus en fonction du niveau 1–2, 3–4, …, 19–20).
- Ces tableaux sont une source directe pour **conversion_krosmoz_sample** (niveau → valeur JDR) pour les caractéristiques concernées.

**Extraction possible :**

- **PDF → données** : nécessite un **parsing PDF** (pdftotext, ou librairie type TCPDF / PyMuPDF / tabula) pour extraire les cellules des tableaux.
- Une fois les tableaux extraits, un script peut mapper colonnes « niveau » (ou tranches 1–2, 3–4, …) vers valeurs et produire un objet niveau → valeur = **conversion_krosmoz_sample**.

**Verdict PDF :** **Faisable** avec un pipeline dédié (extraction PDF + règles de mapping colonnes → niveaux). Pas dans le scrapping actuel ; à prévoir comme tâche séparée (commande Artisan ou script).

---

## 4. Synthèse

| Source | Dofus sample | Krosmoz sample | Automatisable |
|--------|----------------|----------------|---------------|
| **DofusDB API (monstres)** | Oui : tous les `grades` → agrégation par niveau | — | Oui (extension du flux scrapping) |
| **DofusDB API (breeds)** | Non (pas de level/stats) | — | Non |
| **DofusDB API (items)** | À vérifier (niveau équipement) | — | Possible si niveau exposé |
| **Règles (400-, .md)** | — | Tables explicites (ex. mod/score par niveau) | Oui (parse Markdown) |
| **Règles (formules seules)** | — | Conventions niveau → valeur | Partiel (conventions) |
| **To Do (PDF)** | — | Tableaux bonus / niveau | Oui avec parsing PDF |

**Réponse directe :**

- **Oui**, on peut **automatiser** une partie importante :
  - **Dofus** : extraction des samples à partir des **monstres** DofusDB en utilisant tous les **grades** (niveau + stats) et une agrégation par niveau ; le service de scrapping et DofusDbClient sont les briques adaptées.
  - **JDR** : extraction depuis les **règles** (tables en Markdown) et, avec un outil d’extraction PDF, depuis les **PDF** du To Do (Caractéristiques, Equipements et forgemagie).

Aucune modification du seeder n’a été faite ici ; ce document sert à valider la **faisabilité** avant d’implémenter une commande ou un script d’extraction et, plus tard, d’alimenter les seeders ou l’admin.

---

## 5. Pistes d’implémentation (pour plus tard)

1. **Commande Artisan** du type `php artisan characteristics:extract-dofus-samples` :
   - Appel DofusDB (monstres), récupération de tous les grades par monstre.
   - Agrégation par niveau (médiane / moyenne) pour chaque caractéristique creature (life, strength, …).
   - Sortie : JSON ou mise à jour des `conversion_dofus_sample` (en base ou dans des fichiers de data).

2. **Script ou commande** pour les règles Markdown :
   - Parser les fichiers dans `docs/400- Règles/` pour extraire les tableaux niveau → valeur.
   - Produire des fichiers ou structures **conversion_krosmoz_sample** par caractéristique.

3. **Pipeline PDF** (optionnel) :
   - Extraire les tableaux des PDF (110- To Do) puis appliquer les mêmes règles de mapping que ci‑dessus pour **conversion_krosmoz_sample**.

4. **Alignement des niveaux** : Dofus 1–200 vs JDR 1–20. Soit on garde deux échelles (conversion_sample_rows avec dofus_level et krosmoz_level), soit on dérive le niveau JDR à partir du niveau Dofus (ex. formule level_creature) pour n’avoir qu’une série de paires (d, k) par niveau logique.

---

## 6. Implémentation — Caractéristiques object (équipements uniquement)

Une **commande Artisan** agrège les infos dans un **fichier JSON** avant toute incorporation en base.

### 6.1 Commande

```bash
php artisan characteristics:extract-object-samples [--output=...] [--max-items=50000] [--skip-cache] [--chunk=500]
```

- **Périmètre** : uniquement les **équipements** DofusDB (superTypeIds 1, 2, 3, 4, 5, 7, 10, 11, 12, 13), en excluant les `excludedTypeIds` définis dans `resources/scrapping/config/sources/dofusdb/item-super-types.json`.
- **Source** : API DofusDB `/items` avec filtre `typeId[$in][]` (chunks de 500 typeIds max).
- **Données utilisées** : pour chaque item, `level` et `effects[]` (champs DofusDB : `effectId`, `from`, `to` ; la valeur utilisée est la moyenne de `from` et `to`).

### 6.2 Fichier JSON de sortie

**Emplacement par défaut :** `storage/app/characteristics_object_samples.json`.

**Structure :**

- **source** : `"dofusdb"`, **entity** : `"item"`, **scope** : `"equipment"`.
- **meta** : `extracted_at`, `equipment_super_type_ids`, `equipment_type_ids_count`, `excluded_type_ids`, `item_count`, `level_buckets`, `level_object_sample_levels`.
- **by_characteristic_key** : pour l’instant uniquement **level_object** avec `conversion_dofus_sample` (niveau Dofus → niveau) et `raw_level_counts` (nombre d’items par bucket de niveau).
- **by_effect_id** : pour chaque `effectId` DofusDB présent sur les équipements, `conversion_dofus_sample` (niveau bucket → valeur médiane), `item_count`, `level_buckets`.
- **effect_id_to_characteristic_key** : objet vide ; à remplir manuellement ou par un mapping ultérieur (effectId → clé caractéristique object, ex. pa_object, pm_object).

Aucune modification du seeder ni de la BDD n’est effectuée ; ce fichier sert à **réunir toutes les infos** avant décision d’incorporation en base (samples par caractéristique object).

**Structure détaillée du JSON :** voir [STRUCTURE_JSON_OBJECT_SAMPLES.md](./STRUCTURE_JSON_OBJECT_SAMPLES.md) pour une explication de chaque section (`meta`, `by_characteristic_key`, `by_effect_id`, `effect_id_to_characteristic_key`).

**Samples Krosmoz :** la commande charge en plus le fichier `resources/scrapping/config/sources/krosmoz/object_krosmoz_samples.json` (valeurs cibles dérivées des règles 2.2.1 / 2.2.2) et fusionne **conversion_krosmoz_sample** et **conversion_krosmoz_sample_reference** (niveaux 1, 4, 8, 12, 16, 20) dans **by_characteristic_key**. Voir [SOURCE_SAMPLES_KROSMOZ.md](./SOURCE_SAMPLES_KROSMOZ.md).
