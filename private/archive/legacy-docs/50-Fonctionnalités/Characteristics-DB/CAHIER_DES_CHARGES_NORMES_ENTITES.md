# Cahier des charges — Aide à la création d’entités (normes / chartes)

**Date** : 2026-04-15  
**Statut** : spécification produit + recensement de l’existant technique  
**Objectif** : cadrer la fonctionnalité « lecture des normes pour concevoir des entités » (MJ / contributeurs), en cohérence avec ce qui est déjà implémenté dans KrosmozJDR.

---

## 1. Contexte et problème utilisateur

Les **maîtres de jeu** et contributeurs ont besoin d’un **référentiel lisible** pour **calibrer** les propriétés des entités de jeu (monstres, sorts, ressources, consommables, équipements, capacités, etc.) en fonction :

- du **niveau** (1–20) ;
- d’une notion de **puissance** sur une échelle discrète (du très faible au très fort) ;
- de **variantes** exprimées par d’autres caractéristiques (portée, zone, coût en PA, type d’effet, etc.).

Sans cet outil, la création de contenu reste subjective ; avec lui, on **norme** les ordres de grandeur attendus et on réduit les écarts entre fiches.

---

## 2. Modèle métier cible (vision produit)

### 2.1 Grille de référence

Pour **chaque caractéristique concernée** :

- Un **tableau** à deux dimensions :
  - **Axe colonnes** : niveau de 1 à 20.
  - **Axe lignes** : **puissance** (échelle qualitative de référence).

- **Point de départ de lecture** : la ligne dite **« neutre »** (puissance de référence médiane), puis la colonne correspondant au **niveau** choisi.

- **Cellule** : valeur recommandée (nombre, ou valeur discrète selon la caractéristique).

### 2.2 Régulateurs (R) — règles conditionnelles

Pour tenir compte d’autres propriétés (PO, zone, PA, type d’effet, etc.) :

- On définit des **règles** du type :  
  **SI** la caractéristique **C** est *<, >, ≤, ≥, =* à une valeur **N**, **ALORS** on applique un **décalage de lecture** sur la grille.

- Le régulateur peut agir sur :
  - **l’axe puissance** (ex. « −1 ligne », « +2 lignes ») — notations parlantes du type **−1p / +2p** ;
  - **l’axe niveau** (ex. « −1 colonne », « +2 colonnes ») — **−1n / +2n**.

- Des **règles métier libres** restent possibles (ex. « si le sort inflige l’entrave, R = −1p »), matérialisées comme des entrées de même nature (décalage sur puissance ou niveau), éventuellement sans prédicat sur une autre caractéristique numérique.

### 2.3 Périmètre entités

Les groupes de référence dans le projet sont :

| Besoin métier | Groupe technique dans le projet | Tables pivot normes |
|---------------|----------------------------------|----------------------|
| Monstre (et créatures assimilées) | `creature` | `characteristic_creature` |
| Équipement, consommable, ressource, panoplie | `object` | `characteristic_object` |
| Sort | `spell` | `characteristic_spell` |
| **Capacité** | *Pas de groupe dédié aujourd’hui* | Les capacités partagent une structure proche du sort ; les normes applicables sont à trancher (réutilisation du groupe `spell`, ou évolution du modèle — voir § 6). |

---

## 3. État de l’art dans le dépôt (existant à prendre en compte)

### 3.1 Données — schéma et seeders

- **Migration** `2026_04_12_100609_add_norms_to_characteristic_pivot_tables` : ajoute sur `characteristic_creature`, `characteristic_object`, `characteristic_spell` :
  - `norms_grid` (JSON) : grille **5 × 20** ;
  - `norms_conditions` (JSON) : liste de conditions de lecture ;
  - `norms_description` (texte) : description libre de la norme.

- **Fichiers de données** (seeders) déjà fournis et commentés :
  - `database/seeders/data/characteristic_spell_norms.php`
  - `database/seeders/data/characteristic_creature_norms.php`
  - `database/seeders/data/characteristic_object_norms.php`

Ils calibrent les valeurs selon les sections de règles internes (ex. 5.2.3 sorts, 5.2.4 équipements, courbes PV / stats créature, etc.).

### 3.2 Modèle conceptuel implémenté vs formulation « 7 niveaux de puissance »

- **Vision produit (énoncé initial)** : parfois décrite comme une échelle de **7** niveaux de puissance pour 20 niveaux de personnage.
- **Implémentation actuelle** : **5** lignes de puissance nommées  
  `very_weak`, `weak`, `neutral`, `strong`, `very_strong`  
  (libellés FR dans `resources/js/Utils/Characteristic/normsConstants.js`).

La ligne **« neutre »** correspond à l’indice **2** (`NEUTRAL_INDEX = 2`) sur 5 lignes.

**Décision à trancher plus tard** : conserver 5 lignes, étendre à 7, ou documenter l’équivalence (ex. regroupement de niveaux) — le cahier des charges doit noter cet **écart** pour le plan d’implémentation.

### 3.3 Format JSON des conditions (régulateurs)

Chaque élément de `norms_conditions` suit la structure utilisée en base et dans les seeders, du type :

```json
{
  "characteristic_key": "action_points_spell",
  "operator": ">=",
  "value": 5,
  "target": "power",
  "modifier": 1,
  "comment": "Texte libre pour l’admin / le MJ"
}
```

- `target` : `power` (décalage sur les **lignes**) ou `level` (décalage sur les **colonnes**).
- `modifier` : entier signé (agrégation additive dans le lecteur interactif).

Les opérateurs reconnus côté UI sont listés dans `normsConstants.js` (`CONDITION_OPERATORS`).

### 3.4 API publique de lecture

- **Endpoint** : `GET /api/characteristics/{key}/norms/{entity?}`  
  Contrôleur : `App\Http\Controllers\Api\CharacteristicNormsController`.

- **Comportement** : résout la ligne pivot (`characteristic_creature` / `object` / `spell`) pour la caractéristique et l’entité (`*` par défaut), renvoie `grid`, `conditions`, `description`, métadonnées (`power_levels`, `max_level` = 20), et les noms des caractéristiques référencées dans les conditions.

- **Tests** : `tests/Feature/Api/CharacteristicNormsControllerTest.php`.

### 3.5 Interface d’administration

- Édition des normes dans l’admin caractéristiques : composant  
  `resources/js/Pages/Admin/characteristics/NormsPanel.vue`  
  (grille éditable, conditions, description).

- Le contrôleur admin `CharacteristicController` gère la persistance avec le reste des champs pivot.

### 3.6 Visualisation interactive (déjà réutilisable)

| Composant | Rôle |
|-----------|------|
| `NormsViewer.vue` | Assemble tableau, graphique, conditions, aide |
| `NormsTable.vue` | Tableau 5×20 cliquable (sélection du niveau en colonne) |
| `NormsChart.vue` | Courbes par ligne de puissance + repères du niveau / puissance effectifs |
| `NormsConditionSelector.vue` | Chips **activables / désactivables** pour appliquer des décalages |
| `useNormsReader.js` | Calcule puissance effective, index de niveau effectif, valeur résolue |

**Comportement** : l’utilisateur choisit une **colonne (niveau)** ; la **ligne neutre** sert de base ; les **conditions** cochées **additionnent** leurs `modifier` sur `power` et/ou `level`, avec **clamp** sur les bornes du tableau.

### 3.7 Intégration CMS (sections)

- Template de section : `characteristic_norms`  
  Config : `resources/js/Pages/Organismes/section/templates/characteristic_norms/config.js`  
  Rendu : `SectionCharacteristicNormsRead.vue` (appel API + `NormsViewer`).

- Paramètres de section : `characteristic_key`, `group`, `entity`.  
  **Point d’attention** : l’URL d’API actuelle ne prend que `key` et `entity` ; la résolution backend parcourt **creature → object → spell**. En pratique les clés sont suffixées (`*_creature`, `*_spell`, …), ce qui évite l’ambiguïté.

---

## 4. Écart entre la vision « règles SI… ALORS… » et l’UI actuelle

| Aspect | Vision documentaire | Comportement actuel |
|--------|---------------------|----------------------|
| Évaluation des prédicats | Une règle **SI** `C op N` est **vraie ou fausse** selon les valeurs d’une entité en cours de création | Les conditions sont des **aides manuelles** : l’utilisateur **active** les chips qui correspondent à sa situation |
| Combinaison | Non spécifié (priorités, exclusivités) | **Somme** des `modifier` pour les conditions activées, avec plafonnement |
| Règles « sans prédicat » (ex. entrave) | Possible via règle métier | Peut être une **condition** avec `comment` explicite ; l’activation reste manuelle sauf évolution |

Ce point doit être tranché dans une phase d’implémentation : **conserver le mode pédagogique manuel**, **ajouter un mode « simulation »** avec saisie des valeurs de C et évaluation automatique des prédicats, ou les **deux**.

---

## 5. Exigences fonctionnelles (à valider)

### 5.1 Accès et emplacement

- **Public cible** : utilisateurs avec rôle **game_master** (au minimum).
- **Livrable manquant** : une **page (ou parcours) dédiée** dans l’espace « contributions » (ou équivalent) listant **toutes** les caractéristiques pertinentes par type d’entité, avec accès rapide aux tableaux — aujourd’hui la brique existe **par section CMS** ; il manque l’**assemblage** et la **découverte** (navigation, filtres, regroupement par monstre / sort / objet / …).

### 5.2 Contenu par écran

Pour chaque caractéristique (ou groupe logique) :

- Affichage du **tableau graphique** (déjà couvert par `NormsViewer`).
- Liste des **modificateurs** / conditions avec libellés clairs (déjà couvert ; amélioration possible des libellés à partir de `comment`).
- **Interactivité** : sélection de niveau, activation des conditions, **valeur de référence** affichée (déjà couvert).

### 5.3 Couverture des entités

- **Sort** : données seedées volumineuses — référence actuelle la plus complète.
- **Créature / monstre** : seed présent (`characteristic_creature_norms.php`).
- **Objet** (équipement, consommable, ressource, panoplie) : seed présent (`characteristic_object_norms.php`).
- **Capacité** : à définir (réutilisation des normes `spell` si les champs alignés, ou hors périmètre normes jusqu’à modèle dédié).

### 5.4 Édition (admin)

- Déjà possible via **NormsPanel** + sauvegarde admin.
- Exigences complémentaires possibles : historisation, validation, export/import, alignement sur les règles PDF — **hors scope minimal** du présent cahier des charges fonctionnel.

---

## 6. Exigences non fonctionnelles

- **Performance** : chargement d’une norme = un GET API léger (JSON déjà préparé).
- **Accessibilité** : tableaux denses — prévoir contrastes et titres (partiellement assuré par DaisyUI).
- **Cohérence** : une seule source de vérité (BDD + seeders) ; pas de duplication de grilles côté front.
- **Internationalisation** : libellés des lignes de puissance en français dans `POWER_LABELS` ; prévoir clés i18n si la page « contributions » est multilingue.

---

## 7. Risques et dépendances

- **Échelle 5 vs 7** : risque de friction avec la documentation papier / habitudes de table ; nécessite décision de design.
- **Conditions manuelles vs auto** : risque que les MJ attendent une **évaluation automatique** des SI ; l’UI actuelle ne le fait pas.
- **Capacités** : absence de pivot dédié ; risque de contournement fragile si on force des clés `spell` sans modèle clair.
- **Endpoint API** : résolution par ordre de tables ; si une clé ambiguë apparaissait, il faudrait passer un **groupe** explicite (évolution API).

---

## 8. Critères d’acceptation (produit)

Pour considérer la fonctionnalité « prête » côté MJ :

1. Un game_master peut **ouvrir une page unique** (ou parcours clair) depuis les contributions et **atteindre** les normes des caractéristiques des entités ciblées (monstre, sort, objet, etc.) sans connaître les clés techniques.
2. Pour une caractéristique donnée, il peut **lire** la grille, **sélectionner un niveau**, **appliquer** les régulateurs pertinents et **voir la valeur** recommandée.
3. Les textes d’aide expliquent le mode de lecture (déjà amorcé dans `NormsViewer` — aide repliable).
4. Les données affichées sont **celles de la base** (éditables par l’admin).

---

## 9. Pistes pour le plan d’implémentation (hors scope immédiat)

Non contractuelles, mais dérivées du présent document :

- Page Inertia « Normes / aide à la création » : agrégation des `characteristic_key` par groupe + liens ou sections réutilisant `NormsViewer` ou des iframes CMS.
- Route dédiée + middleware `role:game_master`.
- Option : endpoint listant toutes les caractéristiques ayant un `norms_grid` non nul (évite un inventaire statique).
- Évolution : évaluation automatique des conditions à partir de champs saisis (formulaire « simuler un sort »).
- Alignement échelle 5/7 et documentation utilisateur.

---

## 10. Références code et doc internes

| Élément | Emplacement |
|---------|-------------|
| Migration normes | `database/migrations/2026_04_12_100609_add_norms_to_characteristic_pivot_tables.php` |
| Modèles pivot | `CharacteristicSpell`, `CharacteristicCreature`, `CharacteristicObject` |
| Seeders normes | `database/seeders/data/characteristic_*_norms.php` |
| API | `routes/api/characteristics.php`, `CharacteristicNormsController`, `CharacteristicNormsCatalogController` |
| Constantes UI | `resources/js/Utils/Characteristic/normsConstants.js` |
| Lecteur | `resources/js/Composables/characteristic/useNormsReader.js` |
| Architecture sorts | [ARCHITECTURE_CARACTERISTIQUES_SPELL.md](./ARCHITECTURE_CARACTERISTIQUES_SPELL.md) |

---

## 11. Synthèse

Le projet dispose déjà du **cœur métier** (stockage, API, composants interactifs, admin, seeders volumineux pour spell/creature/object). Le travail restant pour répondre pleinement au besoin exprimé est surtout **produit et navigation** : **page contributions** unifiant l’accès, **clarification du mode d’emploi** des régulateurs (manuel vs futur automatique), **décision** sur l’échelle de puissance et le cas **capacités**, et **éventuelles évolutions** d’API si l’on veut filtrer par groupe explicitement.


---

## 12. Implémentations livrées (2026-04)

- **Template de section** `characteristic_norms_catalog` : catalogue interactif (accordéon, chargement du détail à l’ouverture). Paramètres : `group`, `entity`, `characteristic_keys` (filtre optionnel). Combinable avec des sections **texte** sur la même page pour intros et conseils.
- **API** `GET /api/characteristics/norms-catalog/{group}/{entity?}` : liste des caractéristiques ayant une `norms_grid` ; query `keys=` pour filtrer.
- **Seeder** `CreationPagesSeeder` : page parente **Création** (`slug` `creation`) et **trois** sous-pages alignées sur les **groupes** `spell`, `creature`, `object` (`creation-sorts`, `creation-creatures`, `creation-objets`) — un catalogue par groupe. La sous-page `creation-sorts` est libellée **« Sorts et capacités »** (même groupe technique `spell`) ; la sous-page `creation-objets` couvre équipement/consommables/ressources. Les anciennes sous-pages redondantes sont archivées au re-seed. Visibilité **MJ** (`read_level` = game_master).
