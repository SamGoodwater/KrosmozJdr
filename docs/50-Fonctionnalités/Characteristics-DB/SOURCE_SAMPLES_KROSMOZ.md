# Source des samples Krosmoz (conversion object)

Les **samples Krosmoz** (`conversion_krosmoz_sample` / `conversion_krosmoz_sample_reference`) sont des valeurs **cibles** pour la conversion Dofus → Krosmoz. Ils sont chargés depuis un fichier JSON dérivé des règles JDR et fusionnés dans le fichier produit par `php artisan characteristics:extract-object-samples`.

---

## Fichier source

**Emplacement :** `resources/scrapping/config/sources/krosmoz/object_krosmoz_samples.json`

**Contenu :**

- **reference_levels_krosmoz** : `[1, 4, 8, 12, 16, 20]` — niveaux JDR utilisés dans l’admin (alignés sur `getDefaultConversionSampleRows()`).
- **by_characteristic_key** : pour chaque `characteristic_key` (ex. `pa_object`, `strong_object`), un objet avec :
  - **conversion_krosmoz_sample** : `{ "1": valeur, "4": valeur, "8": valeur, "12": valeur, "16": valeur, "20": valeur }`
  - **source_rule** (optionnel) : référence à la règle (ex. « 2.2.2 : PA base 6, max 12, équipement +6 max »).

---

## Origine des valeurs (règles)

Les valeurs sont dérivées des règles Markdown :

- **2.2.1 Caractéristiques principales** (`docs/400- Règles/2-Creer-un-personnage/2.2-les-caracteristiques/2.2.1-caracteristiques-principales.md`) : mod max base, score max base par niveau ; « jusqu’à +4 par objet » pour les caractéristiques principales.
- **2.2.2 Caractéristiques secondaires** (`docs/400- Règles/2-Creer-un-personnage/2.2-les-caracteristiques/2.2.2-caracteristiques-secondaires.md`) : PA (6 base, 12 max, équipement +6), PM (3 base, 6 max, équipement +3), invocation +5 max, esquive PA/PM +5 max, résistances fixes 0–10 (+10 max équipement), etc.

Conventions utilisées dans le JSON :

- **level_object** : identité (niveau 1→1, 4→4, …, 20→20).
- **pa_object** / **pm_object** : bonus équipement typique par niveau (0 à +6 pour PA, 0 à +3 pour PM).
- **strong_object**, **vitality_object**, etc. : plafond +4 par objet → progression 0 à 4 sur les niveaux de référence.
- **invocation_object**, **esquive_pa_object**, **esquive_pm_object** : plafond +5.
- **res_fixe_*_object** : 0 à 10 (équipement +10 max).
- **weight_object** : pas de table dans les règles → `conversion_krosmoz_sample` vide.

Une évolution ultérieure peut s’appuyer sur les **tableaux PDF** (Equipements et forgemagie) pour affiner les courbes par niveau ; voir [ANALYSE_PDF_CARACTERISTIQUES_EQUIPEMENTS.md](./ANALYSE_PDF_CARACTERISTIQUES_EQUIPEMENTS.md).

---

## Intégration dans le flux d’extraction

1. La commande `characteristics:extract-object-samples` agrège les samples **Dofus** depuis l’API (équipements).
2. Elle charge **object_krosmoz_samples.json** et fusionne, pour chaque `characteristic_key` présent dans ce fichier, les champs **conversion_krosmoz_sample** et **conversion_krosmoz_sample_reference** (et optionnellement **conversion_krosmoz_source_rule**) dans **by_characteristic_key**.
3. Le fichier de sortie (`storage/app/characteristics_object_samples.json`) contient donc à la fois les samples Dofus (niveaux 1–200) et les samples Krosmoz (niveaux 1, 4, 8, 12, 16, 20).

---

## Utilisation

- **Admin caractéristiques** : utiliser **by_characteristic_key[clé].conversion_krosmoz_sample_reference** pour pré-remplir les lignes « Krosmoz » du tableau de conversion (niveaux 1, 4, 8, 12, 16, 20).
- Ces données sont **fiables** car elles représentent l’objectif à atteindre pour la conversion (règles JDR), avec moins de points que Dofus mais cohérentes avec le système de jeu.
