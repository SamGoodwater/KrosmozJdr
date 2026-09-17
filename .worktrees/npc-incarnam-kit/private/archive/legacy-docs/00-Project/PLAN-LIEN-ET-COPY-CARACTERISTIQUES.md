# Plan : Lien et copie de caractéristiques

## Objectifs

1. **Lien** : Une caractéristique peut être « liée » à une caractéristique maître. La liée n’a pas de paramètres propres : elle affiche et utilise partout la config de la maître (nom, couleur, formules, conversion, etc.). Une seule source de vérité pour des caractéristiques identiques dans plusieurs groupes (ex. `level_creature`, `level_object`, `level_spell` → une maître + deux liées).

2. **Copie** : Lors de la création, proposer de « copier depuis » une caractéristique existante pour pré-remplir le formulaire (nouvelle caractéristique autonome, pas un lien).

---

## 1. Modèle de données

### 1.1 Migration : table `characteristics`

- **`linked_to_characteristic_id`** (nullable, FK vers `characteristics.id`)  
  Si renseigné : cette ligne est une **liée** ; toute la config (nom, couleur, entités, conversion) est résolue via la maître.

- **`group`** (nullable, string : `creature` | `object` | `spell`)  
  Groupe d’affichage (liste à gauche). Pour les lignes existantes : remplir par rétrocalcul à partir de `inferPrimaryGroup` (présence de lignes dans creature/object/spell). Pour les **liées** : obligatoire (groupe dans lequel la liée apparaît). Une liée **n’a pas** de lignes dans les tables de groupe (creature/object/spell) ; le groupe est donc porté par `group`.

**Contraintes :**

- Une caractéristique liée ne peut pas avoir elle-même de `linked_to_characteristic_id` (maître non liée).
- Une maître ne doit pas être supprimée tant qu’il existe des liées (ou politique de conversion en copie / suppression en cascade à définir).

### 1.2 Rétrocompatibilité

- `group` nullable : si `null`, on continue d’utiliser `inferPrimaryGroup()` comme aujourd’hui.
- Pas de lignes dans les tables de groupe pour une liée : le getter et l’admin doivent traiter le cas `linked_to_characteristic_id` non null.

---

## 2. Modèle Eloquent

- **`Characteristic`**
  - `linked_to_characteristic_id` dans `$fillable` (ou pas, si on ne le set qu’en create).
  - Relation `masterCharacteristic()` : `belongsTo(Characteristic::class, 'linked_to_characteristic_id')`.
  - Relation `linkedCharacteristics()` : `hasMany(Characteristic::class, 'linked_to_characteristic_id')`.
  - Accesseur ou scope `isLinked()` : `linked_to_characteristic_id !== null`.
  - Accesseur `effectiveCharacteristic()` : si liée, retourne la maître ; sinon `$this`. À utiliser partout où on lit la config (nom, couleur, entités, etc.) pour l’affichage et l’édition.

---

## 3. Service Getter (`CharacteristicGetterService`)

- Dans **`getDefinition($characteristicKey, $entity)`** :
  - Charger la caractéristique par `key`.
  - Si `linked_to_characteristic_id` est set : prendre la **maître**, récupérer sa ligne de base (entity `'*'`) dans **sa** table de groupe (creature/object/spell selon la maître), puis construire la définition avec `mergeDefinition(maître, baseMaître, null, $entity)` et **forcer** `result['key'] = $characteristicKey` (clé de la liée) pour que les formules qui référencent la liée gardent la bonne clé.
  - Sinon : comportement actuel (findGroupRows sur cette caractéristique, mergeDefinition comme aujourd’hui).
- S’assurer que **`resolveFieldToKey`** et les autres méthodes qui résolvent par clé/entité continuent de trouver les caractéristiques liées (recherche par `key` ; la liée a bien sa propre `key`, ex. `level_object`).

---

## 4. Contrôleur admin (`CharacteristicController`)

### 4.1 Liste et affichage

- **`buildCharacteristicsByGroup()`** : utiliser la colonne `group` quand elle est renseignée ; sinon garder `inferPrimaryGroup($c)` pour les anciennes lignes. Les liées ont `group` renseigné, donc elles apparaissent dans le bon groupe.
- **`show($key)`** : si la caractéristique est liée, soit rediriger vers l’édition de la maître avec un message, soit afficher une vue en lecture seule avec un bandeau « Cette caractéristique est liée à X » et un bouton « Modifier la caractéristique maître ». Les données affichées (entités, conversion, etc.) viennent de la maître (via `effectiveCharacteristic()` + même logique que pour le getter).

### 4.2 Création

- **`create()`** : fournir à la vue la liste des caractéristiques existantes (ou au moins id, key, name, group) pour :
  - **Lier à une existante** : choix de la maître + choix du groupe de la liée (creature/object/spell). Création d’une ligne `characteristics` avec `key` normalisé pour ce groupe, `linked_to_characteristic_id = maître->id`, `group` = groupe choisi. **Aucune** ligne dans les tables creature/object/spell pour cette liée.
  - **Copier depuis une existante** : choix de la caractéristique source. Création d’une **nouvelle** caractéristique (sans lien) avec le même nom, couleur, type, etc. et **copie des lignes** du groupe source (creature/object/spell) vers le nouveau groupe choisi (même structure que store actuel). L’utilisateur peut ensuite modifier la clé et les champs avant enregistrement si on fait un écran de pré-remplissage, ou on enregistre directement une copie (à trancher en UX).
- **`store()`** : accepter un paramètre `mode` (ou champs dédiés) : `new` | `link` | `copy`.
  - **`link`** : validation `linked_to_characteristic_id`, `group`, `key` (optionnel, par défaut dérivé de la maître + suffixe du groupe). Créer une seule ligne `characteristics`, pas d’appel à `updateGroupRow`.
  - **`copy`** : validation comme aujourd’hui (group, entities, etc.) ; les données par défaut viennent de la caractéristique source (pré-remplissage côté front ou re-validation côté back avec `source_characteristic_id`).

### 4.3 Mise à jour

- **`update()`** : si la caractéristique est liée, refuser la modification (retour 403 ou redirect avec message) ou rediriger vers l’édition de la maître. Seule la maître est éditée.

### 4.4 Suppression

- **`destroy()`** :
  - Si la caractéristique est **maître** et qu’il existe des liées : soit interdire la suppression, soit proposer (côté front) « Supprimer et convertir les liées en copies autonomes » ou « Supprimer la maître et toutes les liées ». À implémenter selon le choix métier.
  - Si la caractéristique est **liée** : suppression uniquement de la liée (une ligne `characteristics`), pas de touche aux tables de groupe ni à la maître.

---

## 5. Frontend (Vue Admin caractéristiques)

### 5.1 Création

- Au lieu d’un seul formulaire « Nouvelle caractéristique », proposer trois modes (onglets ou boutons radio) :
  - **Nouvelle** : comportement actuel (formulaire vide, choix du groupe, etc.).
  - **Lier à une existante** : liste déroulante (ou recherche) des caractéristiques **non liées** (maîtres ou autonomes), choix du **groupe** où la liée doit apparaître, génération de la clé (ex. `level_object`) si la maître est `level_creature`. Enregistrement → appel store avec `mode: 'link'`.
  - **Copier depuis une existante** : liste déroulante des caractéristiques, choix du groupe de la **nouvelle** caractéristique, pré-remplissage du formulaire (nom, couleur, type, unit, sort_order, entities) à partir de la source. L’utilisateur peut modifier la clé et tous les champs puis enregistrer → store avec `mode: 'copy'` (ou store classique avec données pré-remplies).
- Validation : pour « Lier », ne pas afficher les champs entités / conversion / etc. (ou les afficher en lecture seule avec les valeurs de la maître pour info).

### 5.2 Édition

- Si `selected.linked_to_characteristic_id` (ou `selected.is_linked`) : afficher un bandeau « Caractéristique liée à [nom maître]. Les paramètres sont gérés sur la caractéristique maître. » et un bouton « Modifier la caractéristique maître » (lien vers `show(maître.key)`). Soit tout le formulaire en lecture seule, soit redirection directe vers la maître à l’ouverture.
- Liste à gauche : afficher les liées comme aujourd’hui (même libellé/nom que la maître, ou avec une petite icône « lien » pour les distinguer).

---

## 6. Ordre d’implémentation suggéré

1. **Migration** : ajout de `linked_to_characteristic_id` et `group` à `characteristics`, backfill de `group` pour les lignes existantes.
2. **Modèle** : relations, accesseurs `isLinked`, `effectiveCharacteristic`, et éventuellement un scope `whereLinkedTo(null)` pour exclure les liées dans les listes « à lier ».
3. **Getter** : dans `getDefinition`, prise en charge des caractéristiques liées (résolution via maître, clé de retour = clé de la liée).
4. **Controller – liste / show** : utilisation de `group` dans `buildCharacteristicsByGroup`, et pour `show` d’une liée affichage lecture seule + lien vers maître (ou redirection).
5. **Controller – store** : modes `link` et `copy` (création liée sans lignes de groupe ; copie avec duplication des lignes du groupe source).
6. **Controller – update / destroy** : interdiction d’édition des liées ; règles de suppression (maître avec liées, liée seule).
7. **Frontend – création** : choix du mode (nouvelle / lier / copier), formulaires et appels API associés.
8. **Frontend – édition** : bandeau + lecture seule ou redirection pour les liées.
9. **Tests** : création liée, copie, getter pour une entité du groupe lié, suppression maître/liée, édition liée refusée.
10. **Documentation** : mise à jour de la doc projet (par ex. `docs/00-Project/` ou doc admin) pour décrire le lien et la copie.

---

## 7. Points à trancher (optionnel)

- **Clé des liées** : toujours dérivée de la maître (ex. maître `level_creature` → liée `level_object`) ou autoriser une clé personnalisée (risque de confusion dans les formules).
- **Suppression de la maître** : interdire tant qu’il y a des liées, ou proposer « convertir les liées en copies » puis supprimer la maître.
- **Délier** : bouton « Délier » sur une liée pour en faire une copie autonome (duplication des données de la maître à cet instant, puis `linked_to_characteristic_id = null`).

Ce plan peut servir de base pour l’implémentation par étapes et les revues de code.
