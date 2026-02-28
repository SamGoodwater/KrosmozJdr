# Caractéristiques des effets de sort — Classification par action

**Contexte** : Ce document formalise le récapitulatif des besoins pour la conversion des effets de sorts DofusDB → KrosmozJDR : mapping des effectId, propriétés déjà converties, et **tri des caractéristiques par action** (quelle conversion appliquer selon l’action).

Références : [PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md](./PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md), [DOFUSDB_EFFECTS_CONVERSION.md](./DOFUSDB_EFFECTS_CONVERSION.md), [DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md](../Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md).

---

## 1. Récapitulatif des besoins

### 1.1 Mapping effectId DofusDB → action KrosmozJDR

- **Objectif** : mapper chaque effectId DofusDB vers une **action** (sous-effet) KrosmozJDR (frapper, soigner, booster, retirer, etc.).
- **Extensibilité** : pouvoir **en ajouter au fil du scrapping** sans redéploiement (table BDD + UI admin recommandée).
- Les effectId non mappés tombent dans le sous-effet **« autre »** (valeur seule, pas de caractéristique).

### 1.2 Propriétés des sorts déjà converties

Les **propriétés de niveau de sort** (spell-level) suivantes sont déjà gérées par la conversion / les caractéristiques :

| Propriété | Conversion / source |
|-----------|----------------------|
| Coût en PA | `levels.0.apCost` → pa (clampToCharacteristic) |
| Portée min / max | `levels.0.range` (min/max) → po, spell_po_min, spell_po_max |
| Ligne de vue | `levels.0.needLineOfSight` → sight_line |
| Portée modifiable | `levels.0.rangeEditable` → po_editable |
| Durée avant relance | `levels.0.minCastInterval` → number_between_two_cast |
| Nombre de cibles par tour | `levels.0.maxCastPerTurn` → cast_per_turn |
| Nombre de lancers sur une même cible en 1 tour | `levels.0.maxCastPerTarget` → cast_per_target |
| **Durée de l’effet** | Propriété disponible ; conversion existante côté caractéristiques / effets |

Ces champs sont mappés dans `resources/scrapping/config/sources/dofusdb/entities/spell.json` (et mapping « sort » des caractéristiques).

### 1.3 Tri des caractéristiques par action

Pour chaque **action** (sous-effet), on détermine :

- **Quelles caractéristiques** sont concernées (élément, stat, ressource, etc.).
- **Quelle règle de conversion** appliquer : une seule règle globale pour l’action, une règle par caractéristique, ou aucune conversion.

---

## 2. Classification par action

### 2.1 Une seule règle de conversion pour l’action (dommages / vie)

| Action | Caractéristique(s) | Conversion |
|--------|--------------------|------------|
| **frapper** | Élément (neutre, feu, eau, terre, air) = dommages | **1 règle** : conversion « dommages » DofusDB → Krosmoz (quel que soit l’élément, même formule ou par élément selon config). |
| **soigner** | Vie / soin | **1 règle** : conversion soin (valeurs DofusDB → PV Krosmoz). |
| **voler-vie** | Vie / élément | **1 règle** : comme dommages/soin (vol de PV). |
| **protéger** | Vie supplémentaire ou bouclier | **1 règle** : conversion bouclier / absorption (PV temporaires ou bouclier). |

Pour **frapper**, **soigner**, **voler-vie**, **protéger** : la valeur convertie ne dépend que du **type** (dommages, soin, bouclier), pas d’une caractéristique métier multiple. La formule de conversion peut être partagée au niveau de l’action ou de la caractéristique « sort » (ex. dommages_spell).

### 2.2 Pas de conversion (données déjà ailleurs ou N/A)

| Action | Raison |
|--------|--------|
| **déplacer** | Portée du déplacement = **portée du sort** ; déjà convertie (po, spell_po_min/max). Aucune conversion de valeur d’effet à faire. |
| **invoquer** | Invocation : pas de valeur numérique à convertir (lien monstre, nombre éventuel). **Aucune conversion** prévue. |

### 2.3 Conversion dépendante de la caractéristique (boost / retrait / vol)

Pour ces actions, **chaque caractéristique** peut avoir sa **propre formule** de conversion DofusDB → Krosmoz (car les échelles diffèrent : PA, PM, force, résistance, etc.).

| Action | Type de caractéristiques | Conversion |
|--------|--------------------------|------------|
| **booster** | Stats, ressources, éléments | **Par caractéristique** : une formule (ou borne) par characteristic_key (pa_spell, pm_spell, strong_creature, res_terre, etc.). |
| **retirer** | Stats, ressources (PA, PM, etc.) | **Par caractéristique** : idem (retrait PA, retrait PM, etc.). |
| **voler-caracteristiques** | Stats, ressources (PA, PM, vie, etc.) | **Par caractéristique** : idem. |

Le service de caractéristiques (groupe « sort » / creature selon le contexte) fournit déjà les formules par characteristic_key ; la conversion des effets doit **s’appuyer sur cette formule** selon la caractéristique cible du sous-effet.

### 2.4 Synthèse

| Action | Conversion | Remarque |
|--------|------------|----------|
| frapper | 1 règle (dommages) | Élément = type de dommage ; une formule dommages suffit (ou une par élément). |
| soigner | 1 règle (soin) | |
| voler-vie | 1 règle (vol PV) | |
| protéger | 1 règle (bouclier / PV temp) | |
| déplacer | Aucune | Utiliser la portée du sort. |
| invoquer | Aucune | |
| booster | Par caractéristique | Réutiliser les formules du service caractéristiques (spell/creature). |
| retirer | Par caractéristique | Idem. |
| voler-caracteristiques | Par caractéristique | Idem. |
| autre | — | Valeur / description uniquement. |

---

## 3. Caractéristiques de base à supporter en priorité

Liste des caractéristiques **convertibles** à prendre en charge en premier pour les actions booster / retirer / voler (et pour les actions à 1 règle quand applicable). Les clés Krosmoz sont celles du groupe **spell** ou **creature** selon le contexte (characteristic_spell / characteristic_creature). Les **id** sont ceux de l’API DofusDB (`/characteristics`, `item.effects[].characteristic`, définitions `/effects/{id}`).

### 3.1 Attributs de base (stats)

| DofusDB id | keyword | Nom | characteristic_key (Krosmoz, à confirmer groupe spell) |
|------------|---------|-----|--------------------------------------------------------|
| 10 | strength | Force | strong |
| 13 | chance | Chance | chance |
| 14 | agility | Agilité | agi |
| 12 | wisdom | Sagesse | sagesse |
| 11 | vitality | Vitalité | vitality |
| 15 | intelligence | Intelligence | intel |

### 3.2 Tacle, fuite

| DofusDB id | keyword | Nom | characteristic_key |
|------------|---------|-----|--------------------|
| 79 | tackleBlock | Tacle | tacle |
| 78 | tackleEvade | Fuite | fuite |

### 3.3 Résistances (%)

| DofusDB id | keyword | Nom | characteristic_key |
|------------|---------|-----|--------------------|
| 37 | neutralElementResistPercent | Neutre (%) | res_neutre ou équivalent |
| 34 | fireElementResistPercent | Feu (%) | res_feu |
| 35 | waterElementResistPercent | Eau (%) | res_eau |
| 33 | earthElementResistPercent | Terre (%) | res_terre |
| 36 | airElementResistPercent | Air (%) | res_air |

### 3.4 Dommages (fixes / bonus)

| DofusDB id | keyword | Nom | characteristic_key |
|------------|---------|-----|--------------------|
| 103 | weaponPower | Dommages multiples | do_fixe_multiple ou équivalent |
| 92 | neutralDamageBonus | Neutre (dommages) | do_neutre / do_fixe_neutre |
| 88 | earthDamageBonus | Terre (dommages) | do_terre / do_fixe_terre |
| 89 | fireDamageBonus | Feu (dommages) | do_feu / do_fixe_feu |
| 91 | airDamageBonus | Air (dommages) | do_air / do_fixe_air |
| 90 | waterDamageBonus | Eau (dommages) | do_eau / do_fixe_eau |

### 3.5 Retrait PA / PM

| DofusDB id | keyword | Nom | characteristic_key |
|------------|---------|-----|--------------------|
| 82 | apReduction | Retrait PA | retrait_pa ou équivalent |
| 83 | mpReduction | Retrait PM | retrait_pm ou équivalent |

### 3.6 Ressources de combat (PA, PM, PO, initiative, invocations)

| DofusDB id | keyword | Nom | characteristic_key |
|------------|---------|-----|--------------------|
| 19 | range | Portée (PO) | po |
| 1 | actionPoints | PA | pa |
| 23 | movementPoints | PM | pm |
| 44 | initiative | Initiative | ini |
| 26 | maxSummonedCreaturesBoost | Invocation | invocation |

### 3.7 Non présentes dans DofusDB (pas de conversion)

- **Bonus de touche** : pas d’équivalent DofusDB dans les effets ; pas de conversion.
- **Compétences** : idem.
- **Classe d’armure** : idem.

Ces caractéristiques restent gérées côté Krosmoz (fiches, règles) sans être alimentées par le scrapping des effets.

---

## 4. Suite à donner

1. **Mapping effectId → action** : mettre en place la table + UI (voir [PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md](./PLAN_MAPPING_EFFETS_DOFUSDB_KROSMOZ.md)).
2. **Conversion par action** : dans le service de conversion des effets, selon l’action (et la caractéristique si booster/retirer/voler), appeler la **bonne formule** du service de caractéristiques (groupe sort / creature).
3. **Caractéristiques de base** : s’assurer que les characteristic_key listées ci-dessus existent bien dans les groupes spell/creature et possèdent les formules de conversion DofusDB → Krosmoz nécessaires ; compléter les seeders ou la config si besoin.
4. **Amélioration continue** : au fil du scrapping, ajouter des mappings effectId → action et des formules pour les caractéristiques manquantes.
