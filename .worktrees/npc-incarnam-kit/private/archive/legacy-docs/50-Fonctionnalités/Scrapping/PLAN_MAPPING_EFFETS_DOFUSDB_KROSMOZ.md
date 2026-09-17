# Plan : Mapping des actions d’effets DofusDB → KrosmozJDR

**Contexte** : Aujourd’hui le mapping effectId DofusDB → sous-effet Krosmoz est figé dans `DofusDbEffectMapping` (constante PHP). Les effectId non mappés tombent dans le sous-effet **« autre »** (valeur seule, pas de caractéristique). Pour gérer le mapping de façon évolutive et claire, plusieurs options sont possibles. Les caractéristiques possèdent déjà un groupe « sort » avec des caractéristiques (ex. dommages) et des formules de conversion DofusDB → Krosmoz ; ce plan se concentre sur le **mapping des actions** (effectId → sub_effect_slug + règle characteristic).

---

## 0. Récapitulatif des besoins

- **Mapping effectId → action** : mapper les ids DofusDB à nos actions (frapper, soigner, booster, etc.) et **prévoir d’en ajouter au fil du scrapping** (table BDD + UI recommandée).
- **Propriétés des sorts déjà converties** : coût PA, portée min/max, ligne de vue, portée modifiable, durée avant relance, nombre de cibles par tour, nombre de lancers sur une cible, durée de l’effet — la conversion existe déjà côté config/caractéristiques.
- **Tri des caractéristiques par action** : selon l’action, la conversion est soit **une seule règle** (frapper = dommages, soigner = soin, voler-vie, protéger = bouclier/PV), soit **aucune** (déplacer = portée du sort, invocation), soit **par caractéristique** (booster, retirer, voler-caracteristiques — chaque stat/ressistance a sa formule).

Le détail de la **classification par action** et la **liste des caractéristiques de base** à supporter (force, chance, agilité, sagesse, vitalité, intelligence, tacle, fuite, résistances, dommages, retrait PA/PM, PO, PA, PM, initiative, invocations ; bonus touche, compétences, classe d’armure hors DofusDB) est dans [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md).

---

## 1. Objectif

Permettre de **mapper clairement les actions DofusDB aux actions KrosmozJDR** (sous-effets), de préférence via une **UI admin** (comme pour les caractéristiques), afin de :

- ne plus dépendre d’une constante PHP pour ajouter/modifier des correspondances ;
- afficher et éditer les mappings (effectId, nom DofusDB, sub_effect_slug Krosmoz, source de caractéristique) ;
- réutiliser la logique existante (DofusDbEffectCatalog, formules de conversion des caractéristiques) sans dupliquer.

---

## 2. Options envisagées

### Option A — UI admin dédiée (recommandée à moyen terme)

**Principe** : une section admin « Mapping effets DofusDB » (ou intégrée aux Effets / Caractéristiques) qui liste les effectId connus (via catalogue ou table de référence) et permet d’associer à chacun un sous-effet Krosmoz + une règle de caractéristique (element, characteristic, none).

**Avantages** :

- Alignement avec l’existant (caractéristiques, formules) : même philosophie « config en BDD + UI ».
- Pas besoin de redéployer pour ajouter un mapping.
- Possibilité d’afficher le nom/description DofusDB (GET /effects/{id}) à côté de chaque ligne pour faciliter le choix.

**Inconvénients** :

- Nécessite une table (ex. `dofusdb_effect_mappings` : effect_id, sub_effect_slug, characteristic_source, optionnellement characteristic_key ou lien vers characteristic_id) et des écrans (liste, édition).
- Dépendance à une liste d’effectId de référence (synchronisée depuis l’API ou maintenue manuellement).

**Implémentation type** :

- Table `dofusdb_effect_mappings` : `id`, `dofusdb_effect_id` (unique), `sub_effect_slug`, `characteristic_source` (enum : element, characteristic, none), `characteristic_key` (nullable, si source = characteristic).
- Service de conversion : au lieu de `DofusDbEffectMapping::getSubEffectForEffectId()`, lire en BDD (avec cache) et fallback sur la constante actuelle si aucune ligne.
- UI : liste des mappings + formulaire (sélection sous-effet, source de caractéristique, caractéristique si besoin) ; optionnellement bouton « Charger les effectId depuis l’API » pour pré-remplir les effectId non encore mappés.

---

### Option B — Fichier de config (JSON / PHP) éditable

**Principe** : déplacer le mapping actuel vers un fichier (ex. `resources/scrapping/config/dofusdb_effect_mapping.json` ou tableau PHP dans `config/`) et le charger au runtime. Éventuellement prévoir un écran admin en lecture seule qui affiche ce fichier, ou un formulaire qui écrit le fichier (moins standard en Laravel).

**Avantages** :

- Pas de migration BDD ; déploiement = mise à jour du fichier.
- Versionnable (git).

**Inconvénients** :

- Pas d’UI d’édition naturelle (sauf à construire un écran qui écrit le fichier, ce qui pose des questions de droits et de concurrence).
- Moins aligné avec le reste du projet (caractéristiques et formules en BDD).

---

### Option C — Table BDD sans UI (seeders + édition manuelle en BDD)

**Principe** : une table `dofusdb_effect_mappings` comme en A, mais remplie par seeders ou migrations ; pas d’écran admin. Les modifications se font en base (php artisan tinker, SQL, ou seeder dédié).

**Avantages** :

- Une seule source de vérité en BDD ; le code lit la table (avec cache).
- On peut préparer le terrain pour une UI plus tard.

**Inconvénients** :

- Pas de « mapping clair » côté utilisateur sans UI ; moins pratique pour les non-développeurs.

---

### Option D — Hybride : config par défaut + overrides en BDD

**Principe** : garder un fichier (ou la constante actuelle) comme **défaut** ; une table `dofusdb_effect_mappings` stocke les **surcharges**. À la résolution : d’abord BDD, si rien alors fichier/constante. Plus tard, ajout d’une UI pour éditer les lignes BDD.

**Avantages** :

- Pas de perte du comportement actuel ; extension progressive.
- UI possible uniquement pour les overrides au début.

**Inconvénients** :

- Deux sources à maintenir (défaut vs BDD) ; il faut documenter la priorité.

---

## 3. Recommandation

- **Court terme** : garder le comportement actuel (constante PHP + sous-effet « autre ») ; il est déjà en place.
- **Moyen terme** : viser **Option A (UI admin)** pour être cohérent avec les caractéristiques et les formules de conversion, en prévoyant :
  1. Une table `dofusdb_effect_mappings` (effect_id, sub_effect_slug, characteristic_source, characteristic_key nullable).
  2. Un service (ou une évolution de `DofusDbEffectMapping`) qui lit la BDD avec cache et fallback sur la constante actuelle.
  3. Une page admin « Mapping effets DofusDB » (liste + édition), avec possibilité d’afficher le libellé/description DofusDB (catalogue) à côté de chaque effect_id.
- **Lien avec les caractéristiques** : le groupe « sort » et les formules de conversion (ex. dommages DofusDB → dommages Krosmoz) restent gérés côté caractéristiques ; le mapping dont on parle ici est **action DofusDB → action Krosmoz** (sub_effect_slug). Les deux peuvent coexister : une fois l’action mappée (ex. effectId 98 → frapper + element), la caractéristique (élément) et sa formule sont déjà gérées par le système de caractéristiques. Une amélioration ultérieure pourra consister à lier plus finement les effectId aux caractéristiques « sort » (dommages, soin, etc.) pour réutiliser les mêmes formules ; à traiter après l’ajout du sous-effet « autre » et la stabilisation du mapping des actions.

---

## 4. Plan d’implémentation (phases)

Un [plan d’implémentation par phases](./PLAN_IMPLEMENTATION_MAPPING_EFFETS.md) découpe le travail en tenant compte de l’**harmonisation des noms** (BDD, config, seeders) en cours : Phase 1 (table + service + fallback), Phase 2 (données + UI admin), Phase 3 (conversion par action).

---

## 5. Références

- [PLAN_IMPLEMENTATION_MAPPING_EFFETS.md](./PLAN_IMPLEMENTATION_MAPPING_EFFETS.md) — plan d’implémentation (phases, dépendances à l’harmonisation)
- [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) — classification par action (1 règle / aucune / par caractéristique) et liste des caractéristiques de base
- [DOFUSDB_EFFECTS_CONVERSION.md](./DOFUSDB_EFFECTS_CONVERSION.md) — architecture et sous-effet « autre »
- [VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md](../VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md) — vision globale mapping + caractéristiques
- [DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md](../Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md) — id DofusDB ↔ characteristic_key Krosmoz
- `App\Services\Scrapping\Core\Conversion\SpellEffects\DofusDbEffectMapping` — implémentation actuelle
