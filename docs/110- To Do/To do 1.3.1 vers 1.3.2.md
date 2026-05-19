# Feuille de route 1.3.1 → 1.3.2 (première mise en prod)

**Décisions validées** : [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md) · **Plan d’exécution** : [`PLAN-EXECUTION-release-1.3.2.md`](./PLAN-EXECUTION-release-1.3.2.md) · **Suivi à cocher** : [`CHECKLIST-release-1.3.2.md`](./CHECKLIST-release-1.3.2.md)

Comme le projet n’est pas en prod, **pas d’aliases** ni code obsolète forcé pour rétrocompatibilité.

---

## Sommaire

1. [Résumé des décisions (Q1–Q27)](#1-résumé-des-décisions-q1q27)
2. [Vues entité & édition](#2-vues-entité--édition)
3. [Droits, invités & page « Gérer l’affichage »](#3-droits-invités--page-gérer-laffichage)
4. [Sécurité zone sensible (mot de passe, 1 h, cadenas)](#4-sécurité-zone-sensible-mot-de-passe-1-h-cadenas)
5. [Interfaces : accueil, admin, compte](#5-interfaces--accueil-admin-compte)
6. [Légal, cookies, RGPD, changelog (fichiers)](#6-légal-cookies-rgpd-changelog-fichiers)
7. [Recherche globale — bloquante 1.3.2](#7-recherche-globale--bloquante-132)
8. [Sections TipTap & mention `@`](#8-sections-tiptap--mention-)
9. [Entités — exigences par type](#9-entités--exigences-par-type)
10. [Commandes projet & note `project:refresh`](#10-commandes-projet--note-projectrefresh)
11. [Fonctionnalités transverses & polish (renvoi)](#11-fonctionnalités-transverses--polish-renvoi)
12. [Caractéristiques (calibrage détaillé)](#caractéristiques)

---

## 1. Résumé des décisions (Q1–Q27)

Table de synthèse — **référence complète et réponses longues** : [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md).

| Thème | Décision |
| --- | --- |
| **Q1 Menus** | **Gestion du contenu** : **game_master** et au-dessus (admin, super_admin). **Espace administration** : **admin** et **super_admin**. Ajouter le **planning / cron** dans l’admin. |
| **Q2 Zone sensible** | Déblocage **1 h** après mot de passe ; **cadenas vert** près de l’avatar ; re-verrouillage si **aucune** modification sur pages sensibles pendant 1 h ; exigence **sécurité prod**. |
| **Q3 Dashboards** | **Vue d’ensemble** : camemberts entités × statuts + **pages** + **sections**. **Récap admin** : courbe **utilisateurs** + camembert **par rôle**. (*Confirmé.*) |
| **Q4 Guests** | Par défaut **états stables** si pas d’autre configuration. |
| **Q5 Affichage** | Intégré aux **Policies** ; matrice **état × rôle** par entité. |
| **Q6 Pages/sections** | Par défaut : admin, super_admin, créateur, MJ — sauf réglage spécifique page/section. |
| **Q7 Vues** | **minimal**, **line**, **texte**, **full**, **edit** ; doc et code alignés sur ce modèle. |
| **Q8 Raccourcis** | macOS : **Ctrl** et **⌘** pour ouvrir en page ; sans droit édition → **notification** sans ouvrir de vue. |
| **Q9 Création** | Champs **simples obligatoires** (nom, type/race, description, niveau typiquement) ; **liste exacte par type d’entité** fixée à l’implémentation. |
| **Q10 Variantes** | 1 à 4 sorts « normaux » ; pas de contrainte stricte DB ; UX ≤ 4 ; même sort dans **plusieurs** variantes possible. |
| **Q11 Champs classe** | Pas de migration legacy : contenu **reconstruit** avec **sections** à l’init. |
| **Q12 Biblio** | Aligné **existant** : page + **sous-pages** par classe et par spécialisation. |
| **Q13–Q14 TipTap** | Chargement **léger** ; aperçu **~10 lignes**, fin de paragraphe ; limite **`@` : globale**. |
| **Q15–Q16 Sorts** | IDs = **exemples** ; mapper ou texte + **icône dans le HTML** ; éléments : doc **`400- Jeu`**. |
| **Q17–Q19 Recherche** | Technique libre, perf ; périmètre **entités + pages + sections** ; résultats **titre + extrait** uniquement. |
| **Q20–Q21 Fichiers** | Légal **mono-langue**, noms **clairs** ; changelog sous **`storage/app/public/changelog/{version}.md`**, pas dans `docs/`. |
| **Q22–Q24 Commandes** | **Refresh** : fresh + seed puis **`project:init`** — **aligner le code** (état au 2026-05-17 : voir Décisions). Seed / cron : **revérifier le dépôt**. |
| **Q25 Accueil** | Ton / cadre : [`docs/400- Jeu/420- Règles`](../../400-%20Jeu/420-%20Règles). |
| **Q26 Panoplies** | Liste **équipements** + effets bonus ; minimal quasi final : **effet par défaut** + équipements en **vue Texte** au **hover**. |
| **Q27** | Recherche **bloquante** pour **1.3.2**. |

---

## 2. Vues entité & édition

| Vue | Rôle |
| --- | --- |
| **minimal** | Listes, cartes |
| **line** | Dérivée de **minimal** (ligne enrichie) |
| **texte** | Dans un paragraphe ; détail via **popover** |
| **full** | Remplace **compact** et **large** — tout le contenu, **page ou modal** |
| **edit** | Édition **page ou modal**, même gabarit |

Harmoniser **tooltips / popovers** (ex. nature Physique / Wakfu).

---

## 3. Droits, invités & page « Gérer l’affichage »

- La majorité du contenu (pages, règles, entités, etc.) reste lisible par les **invités** ; exceptions par **rôle** et **auteur**.
- **Invités** : par défaut, visibilité des entités en **états stables** (ex. jouable), tant qu’il n’existe pas de règle contraire dans **Gérer l’affichage**.
- Cette page permet de définir, **par type d’entité et état**, **quels rôles** peuvent **lire** — implémenté dans les **Policies Laravel** (pas de surcouche parallèle sans justification sécurité).

---

## 4. Sécurité zone sensible (mot de passe, 1 h, cadenas)

- Les zones **Gestion du contenu** et **Espace administration** (y compris Récapitulatif) passent par une **confirmation du mot de passe** qui **débloque** l’accès **1 h**.
- Indicateur : **cadenas vert** à côté de l’**avatar** lorsque le mode débloqué est actif.
- Après **1 h sans modification** sur les **pages sensibles**, retour au mode verrouillé.
- Traiter comme partie **critique** de la sécurité : fiabilité, tests, robustesse.

---

## 5. Interfaces — accueil, admin, compte

### Accueil

- Présenter le **projet** et le **jeu** ; caler le **ton** sur le corpus des règles : [`docs/400- Jeu/420- Règles`](../../400-%20Jeu/420-%20Règles).

### Gestion du contenu (game_master, admin, super_admin)

- **Vue d’ensemble** — camemberts **par type d’entité** avec statuts (brut, brouillon, actif / jouable, archivé) ; **nombre de pages** et **sections**.
- **Caractéristiques** — derrière **zone sensible** (§4).
- **Langues**, **Effect**, **Sous-effet**.

### Espace administration (admin, super_admin)

Toutes les entrées ci-dessous derrière **zone sensible** (§4) :

- **Récapitulatif** — historique **nombre d’utilisateurs** ; camembert **par rôle**.
- Gestion des **utilisateur·ices**.
- **Scrapping** ; mapping scrapping ; **mapping effets DofusDB**.
- **Sauvegarde** ; **sync données** ; **mise à jour stack**.
- **Gérer l’affichage** (§3).
- **Planning / cron** — planification des tâches (**ne pas oublier** dans l’UI).

**Menu compte** : entrées **Gestion du contenu** et **Espace administration** selon les droits.

### Mon compte

- Raccourcis **entités** (icônes + couleurs) + **règles** ; blocs **« mes créations »**.
- **Paramètres notifications** à valider fonctionnellement.
- **Édition profil** : design system.
- **Données personnelles** : **export** + **suppression** ; liens vers **CGU** et **confidentialité / cookies** (routes nommées).

---

## 6. Légal, cookies, RGPD, changelog (fichiers)

- Rendu **markdown** pour CGU, confidentialité, cookies depuis **`storage/app/public/legal/*.md`** — **une langue**, noms de fichiers **alignés** sur les routes du site.
- **Changelog utilisateur** : **`storage/app/public/changelog/`** — **un fichier par version** (ex. `1.3.2.md`), **section par version**, intro, navigation entre versions ; contenu pas dans `/docs/` pour la publication versionnée.
- Après stabilisation des fonctionnalités : rédiger le changelog ; structure **Contenu** (prioritaire) + **Technique** (bref).

---

## 7. Recherche globale — bloquante 1.3.2

- UI header (focus, agrandissement, **overlay**, filtres type **Filter** DaisyUI : entités, couleurs, **pages**, **sections**).
- Filtres d’**état** cohérents **entités + CMS** (pages, sections).
- Résultats : **titre + extrait** ; groupement ; **droits lecture** respectés.
- **Livraison obligatoire** avec la 1.3.2.

---

## 8. Sections TipTap & mention `@`

- Chaîne complète liens **caractéristiques / entités / sections** à fiabiliser : popover **section** (card, début de contenu, **~10 lignes**, fin de paragraphe), scroll vertical, pas de scroll horizontal sauf PDF, etc.
- Suggestion `@` : ordre **caractéristiques → sections → entités** ; liste sous la ligne ; **plafond global** + scroll si hauteur.

---

## 9. Entités — exigences par type

### Tableaux (général)

- Retirer la ligne de **densité** tableau inutilisée (compact / normal / dense).
- **Création** : modal **champs minimal obligatoires** → puis **édition complète** (§1 Q9).
- **Raccourcis** : sélection ; double-clic affichage modal ; **Ctrl ou ⌘** → page ; **Alt** → édition modal ; clic droit → menu ; Alt **sans** droit → **notification** uniquement (§1 Q8).

### Spécialisations

- Page **Bibliothèque** + sous-pages ; mise en page **page** ; blocs par niveau (sorts, capacités, ressources, équipements, panoplies) puis **sans niveau** puis **sections** ; édition alignée ; legacy HTML → **imports d’entité**.

### Classes (Breed)

- Même logique bibliothèque ; **variantes de sorts** (§1 Q10) ; sorts hors variantes ; suppression future des champs **evolution, specifity, life** une fois sections en place (**pas** de migration manuelle du vieux HTML — §1 Q11).

### Items / Ressources / Consommables

- Stats en **box** (comme créatures) ; **état** depuis actions ; sorts objet en **popover** ; **panoplie** en vue Texte ; **recette** full ; corriger navigation full/edit et bugs état.

### Monstres

- Langues : **badges** + tooltip ; box stats **flex** ; édition **alignée** à l’affichage ; **barre d’actions fixe** (sauvegarder + hover reset/annuler).

### Sorts

- Sous-effets / éléments (§1 Q15–Q16) ; bug **changement d’état depuis vue minimal**.

### Capacités

- Épinglage **extended** ; corriger route **Ziggy** `capability`.

### États / Traits

- Réparer les pages bibliothèque prévues.

### Panoplies (§1 Q26)

- Données : liste d’**équipements** + un ou plusieurs **bonus** (effets / sous-effets).
- **Minimal** : **effet par défaut** + liste équipements en **vue Texte** au **hover** ; extended : description, équipements en popover ; bonus via **render caractéristiques** ; vérifier **scrapping**.

---

## 10. Commandes projet & note `project:refresh`

**Décision produit** : enchaîner **`migrate:fresh`** et le **pipeline `project:init`** pour données complètes (`DatabaseSeeder` Laravel optionnel en théorie ; en pratique **`project:init`** remplace et étend ce socle).

**Implémentation** : `project:refresh` exécute le ménage local puis **`project:init --fresh`** puis les clears (voir [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md) § Q22). Options utiles : `--without-seed` → `--skip-seeders`, `--skip-scrapping`, `--noimage`, `--skip-types`, `--force`.

Ensemencement « tout sauf scrapping » : **`project:init`** avec **`--skip-scrapping`** (aucune commande `project:seed` dédiée repérée au dernier audit).

**`project:cron`** : comportement **sans action par défaut** si aucun flag — acceptable pour un cron vide ; vérifier les besoins prod (clear, backup, etc.).

*Le détail des phases (`project:init`, `project:update`, `project:clear`, …) figure dans la suite du document (sections **Optimisation**, **Commandes**).*

---

## 11. Fonctionnalités transverses & polish (renvoi)

Améliorations listées plus bas dans ce fichier : **caractéristiques** (aide MJ), **scrapping**, **conversion**, **retour utilisateur** (mail récap optionnel connectés), **optimisation** (dossiers, doc, commandes), **UI/UX** (responsive, accessibilité, bandeaux, notifications).

---

# Caractéristiques

Ce bloc sert de **cadre** pour calibrer conversion DofusDB → Krosmoz et les **normes** (grilles + conditions). Source de vérité applicative : les JSON sous [`database/seeders/data/characteristic-definitions/`](../../database/seeders/data/characteristic-definitions/) (`entities["*"]` sauf overrides futurs par entité).

**Travail attendu avant prod** : revue **ligne par ligne** (une clé = une ligne du tableau = un fichier JSON). C’est là que l’écart DofusDB ↔ besoin Krosmoz est le plus fort. Les colonnes « Normes » / « Régulateurs » du tableau peuvent renvoyer au seed ; le détail métier se complète dans le JSON (`norms_description`, `norms_conditions`, `norms_grid`).

## Comment lire ces tableaux

- **Une ligne = une caractéristique** (`characteristic_key`) et **un fichier seed**.
- **Famille A (descripteur)** : valeur sur la fiche (PA, PO, résolution…). Souvent **pas de vraie conversion** (identité + clamp) ; **normes** utiles pour la création manuelle.
- **Famille B (effet / bonus)** : sous-effet de sort ou bonus d’objet. Conversion souvent **compressée** ; normes + parfois **dés** (`convertToDice`).
- **Hiérarchie de vérité** : (1) `docs/400-…` / `docs/410-…` ; (2) intention ligne par ligne dans ce document ; (3) JSON actuel du dépôt ; (4) ordres de grandeur DofusDB (**approximatifs**). En cas de conflit (1) vs (3), statut **`écart à traiter`**.

### Colonne « Statut » (ne pas confondre avec « aligné dépôt »)

| Statut | Signification |
| --- | --- |
| `aligné dépôt` | Fichier JSON présent ; **pas encore validé** métier ni en jeu. |
| `à revoir — conversion` | Import ou valeurs jugées trop hautes / mauvaise courbe ; ajuster `conversion_formula` / dés. |
| `à revoir — normes` | Conversion OK ou absente, mais grille / régulateurs / texte à refaire. |
| `sans conversion — normes seules` | Pas de calibrage Dofus (`[d]` inutile ou pass-through) ; **normes obligatoires** pour le MJ. |
| `ok` | Validé (doc 400 + test import ou création manuelle). |

## Détecter ce qui n’est pas à jour : auto ou manuel ?

| Approche | Ce qu’elle donne | Limite |
| --- | --- | --- |
| **Automatique (partiel)** | Lister les clés (CSV), comparer min/max/formule du seed, repérer l’absence de `norms_grid`, utiliser l’admin (suggest-conversion-formula, graphiques échantillons). | Ne dit pas si le résultat **en jeu** est trop fort. |
| **Manuel (obligatoire pour valider)** | Importer ou ouvrir quelques entités représentatives ; comparer aux plafonds doc 400 ; noter la clé en `à revoir — conversion`. | Seule preuve que « ce n’est plus trop haut ». |
| **Ligne par ligne (ce document)** | Pour chaque clé : sémantique Dofus, cible Krosmoz, avec/sans conversion, avec/sans dés, normes. | Travail éditorial ; c’est la référence avant prod. |

**Conclusion** : on **ne peut pas tout déduire automatiquement**. Le CSV et les tableaux **accélèrent** ; la validation reste **manuelle** (échantillon d’entités + doc 400), puis mise à jour du JSON et du statut.
## Conversion sans échantillons
Les `conversion_dofus_sample` / `conversion_krosmoz_sample` sont **optionnels** (aide graphiques et `ConversionFormulaGenerator`). Beaucoup de lignes n’en auront pas.
- **Pas d’échantillon ≠ pas de travail** : il faut quand même décider **pass-through**, **formule**, **fixe**, ou **dés** (`convertToDice`), et les **normes**.
- **Pas de conversion** : laisser `conversion_formula` vide ou `[d]` + clamp si Dofus ≈ Krosmoz ; documenter en **sans conversion — normes seules**.
## Règle Dofus : fourchette, fixe, et dés (`ndX`)

Côté DofusDB, une valeur est en général :

- une **fourchette** min–max (effet variable selon niveau / tirage) ;
- plus rarement une **valeur fixe**.

Après conversion vers l’échelle Krosmoz, on choisit :
### 1. Valeur fixe Krosmoz

Si Dofus est **fixe** (ou fourchette très étroite) : **pas de dé** ; une valeur entière (ou table) dans les bornes Krosmoz (`min` / `max`, `conversion_formula`, clamp).
### 2. Fourchette → choix du dé

Estimer l’écart sur la **valeur Dofus** (min/max du grade ou de l’effet), ou sur la fourchette **après** une première conversion :

`écart_% = (max - min) / min × 100` (si `min > 0` ; sinon traiter au cas par cas).

| Situation | Interprétation | Forme Krosmoz visée |
| --- | --- | --- |
| **écart_% > 20** | Grande dispersion Dofus | **`ndX` avec n petit, X grand** (ex. `1d12`, `2d10`) — on **maximise l’aléatoire**. |
| **écart_% ≤ 20** | Peu de dispersion | **`ndX` avec n grand, X petit** (ex. `4d4`, `6d3`) — résultat plus **stable**. |
| Valeur fixe Dofus | — | Entier Krosmoz, **sans dé** ; convertir pour rester dans les plafonds JDR. |

**Implémentation** : utiliser `conversion_function` = `convertToDice` quand le résultat stocké / affiché doit être une **notation de dés** (voir sorts `dommages_spell`, `soin_spell`). La formule sur `[d]` compresse d’abord l’ordre de grandeur ; la fonction peut formater en dés.

**Référence technique** : [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](../50-Fonctionnalités/Characteristics-DB/PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md), [FORMULES_CONVERSION_SORTS_REGLES.md](../50-Fonctionnalités/Scrapping/FORMULES_CONVERSION_SORTS_REGLES.md).

## Ordre de travail recommandé (par caractéristique)

Pour **chaque ligne** du tableau (ou chaque clé du CSV), dans cet ordre :

1. **Lire** la doc jeu : [MATRICE_ROLES_CARACTERISTIQUES_SPELL.md](../400-%20Jeu/410-%20Ressources/MATRICE_ROLES_CARACTERISTIQUES_SPELL.md) (sorts), [PROPOSITIONS_FORMULES_ET_PROPRIETES.md](../400-%20Jeu/410-%20Ressources/PROPOSITIONS_FORMULES_ET_PROPRIETES.md) (objets), règles créature / monstre dans `docs/400-…`.
2. **Classer** : famille A ou B ; conversion **oui / non** ; résultat en **fixe** ou **dés** (règle 20 % ci-dessus).
3. **Renseigner la sémantique** : ce que vaut `d` à l’import ; fourchette Dofus typique (min–max) ou fixe.
4. **Fixer la cible Krosmoz** : min/max après conversion ; ancres texte `(d_dofus → k_krosmoz)` si utile (pas besoin d’échantillons JSON).
5. **Conversion** (si applicable) : `conversion_formula` et éventuellement `conversion_function` ; sinon pass-through / vide + statut **sans conversion — normes seules**.
6. **Normes (souvent obligatoires)** : `norms_grid` (5×20), `norms_conditions`, `norms_description` — même sans conversion.
7. **Écrire** le fichier `characteristic-definitions/<groupe>/*.json`.
8. **Vérifier** : re-seed caractéristiques ; test sur 1–3 entités importées ou créées à la main ; passer le statut à `ok` ou `écart à traiter`.

### Priorité suggérée (ordre des groupes)

1. **Créature** : PV, PA, PM, stats, mods, résistances % (impact monstres / PNJ).
2. **Objet** : bonus équipement (stats, dommages fixes, résistances).
3. **Sort Type 2** : `dommages_spell`, `soin_spell`, variations stats, déplacements.
4. **Sort Type 1** : descripteurs (souvent normes + pass-through).
5. Compétences / passifs / champs texte (normes optionnelles ou N/A).

## Lexique des régulateurs (`norms_conditions`)

Le lecteur de normes ([`useNormsReader`](../../resources/js/Composables/characteristic/useNormsReader.js)) applique des **décalages additifs** sur la grille 5 puissances × 20 niveaux :

| Notation parlante | JSON | Effet |
| --- | --- | --- |
| `+1p` / `-1p` | `"target": "power", "modifier": ±1` | Décale la lecture d’**une ligne de puissance** (very_weak → very_strong). |
| `+1n` / `-1n` | `"target": "level", "modifier": ±1` | Décale la lecture d’**une colonne de niveau** (1–20). |

Toute ancienne mention « cellule » (`c`) doit être **traduite** en `power` ou `level`. Chaque condition référence une `characteristic_key` existante (ex. `action_points_spell`, `area_spell`).

## Sortie attendue pour une IA (mise à jour des seeders)

Fichier cible : `database/seeders/data/characteristic-definitions/<groupe>/<fichier>.json`.

Champs sous `entities["*"]` : `min`, `max`, `conversion_formula`, `conversion_function` (`identity`, `convertToDice` — [registry](../../app/Services/Characteristic/Conversion/ConversionFunctionRegistry.php)), échantillons **optionnels**, **`norms_grid`**, **`norms_conditions`**, **`norms_description`**, etc.

## Mapping colonnes du tableau → chemins JSON

| Colonne du tableau | Où écrire dans le seed |
| --- | --- |
| Libellé, Type | `characteristic.name`, `characteristic.type`, `helper`, … |
| Colonne SQL | `entities["*"].db_column` |
| Sémantique Dofus & d | Intention métier ; si agrégat DofusDB : fourchette observée + écart % (voir § Données DofusDB empiriques) |
| Ancres/échantillons | Texte, `conversion_*_sample`, ou ancres Dofus/Krosmoz issues de l’agrégat |
| Min/max | `entities["*"].min`, `entities["*"].max` |
| Conversion | `conversion_formula` ; si dés : `convertToDice` |
| Normes / Régulateurs | `norms_grid`, `norms_conditions`, `norms_description` |
| Statut | Suivi éditorial (voir tableau ci-dessus) ; pas un champ JSON |

### Données DofusDB empiriques (agrégats)

Colonnes ajoutées au **CSV** (`characteristic_definitions_index.csv`) et texte injecté dans **Sémantique Dofus & d** / **Ancres/échantillons** pour les lignes créature et objet disposant d’un agrégat :

| Colonne CSV | Signification |
| --- | --- |
| `dofus_source` | `creature_monster_grades` (≈2500 monstres, tous les grades) ou `object_equipment_effects` (équipements) |
| `dofus_field` | Champ grade Dofus (`lifePoints`, …) ou `characteristic_id=N` (objets) |
| `dofus_global_min` / `dofus_global_max` | Min / max sur toutes les tranches de niveau de l’agrégat |
| `dofus_anchors` | Médianes aux niveaux 1, 40, 80, 120, 160, 200 |
| `dofus_spread_pct_1_200` | `(valeur@200 − valeur@1) / valeur@1 × 100` — indicatif pour la règle des dés |
| `krosmoz_anchors` | Cibles JDR quand présentes dans l’agrégat (règles 2.2.x) |
| `dofus_extracted_at` | Date du fichier `storage/app/characteristics_*_samples.json` |

**Régénération** : `python3 scripts/enrich-characteristic-dofusdb.py` (après mise à jour des JSON d’agrégat ou des seeders).

**Limite** : ordres de grandeur **indicatifs** ; la validation métier reste manuelle. Sorts : pas d’agrégat automatique dans cette passe.


## CSV vs JSON — rôles différents

| | **JSON** (`characteristic-definitions/**/*.json`) | **CSV** (`characteristic_definitions_index.csv`) |
| --- | --- | --- |
| **Rôle** | **Source de vérité** chargée en base (seeders). | **Index / inventaire** régénérable pour tri, filtres, prompts IA. |
| **Contenu** | Tout : normes complètes, formules, textes d’aide. | Résumé seed + colonnes **DofusDB empiriques** (`dofus_anchors`, `dofus_spread_pct_1_200`, …) quand un agrégat existe. |
| **Édition** | C’est **ici** qu’on corrige conversion et normes. | Ne pas éditer à la main pour « corriger » le jeu — regénérer après changement JSON. |
| **Intérêt** | Exécuté par l’app. | Trouver une clé, lister « sans normes », script, tableur ; **pas** de remplacement du JSON. |

**Mieux que le JSON ?** Non pour l’application. Le CSV est **meilleur pour naviguer** 282 lignes ; le JSON reste **indispensable** pour la prod.

Régénération du CSV : `python3 scripts/enrich-characteristic-dofusdb.py --csv-only` (seeders + fusion des agrégats `storage/app/characteristics_*_samples.json`).

---

## Groupes spells

Réf. architecture : [ARCHITECTURE_CARACTERISTIQUES_SPELL.md](../50-Fonctionnalités/Characteristics-DB/ARCHITECTURE_CARACTERISTIQUES_SPELL.md) ; formules scrapping sorts : [FORMULES_CONVERSION_SORTS_REGLES.md](../50-Fonctionnalités/Scrapping/FORMULES_CONVERSION_SORTS_REGLES.md).

> **Chemins** : `…/spell/` = `../../database/seeders/data/characteristic-definitions/spell/`. Les lignes encore en **`aligné dépôt`** n’ont pas été relues métier : à traiter avec l’ordre de travail ci-dessus (sémantique + statut).

### 3.1 Descripteurs du sort (Type 1 — famille A)

Métadonnée du sort (`spells` / `effects` selon la clé). En général : **sans conversion** (pass-through + clamp) ; **normes** à renseigner pour la création MJ. Colonnes « Normes » / « Régulateurs » : détail dans le JSON seed.

| N° | Libellé (FR) | Clé BDD | Famille | Type | Colonne SQL | Sémantique Dofus & d | Min/max | Conversion (extrait) | conversion_function | Ancres/échantillons | Normes | Régulateurs | Cas limites | Doc réf. | Fichier seed | Statut |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | Coût en PA | action_points_spell | A | int | pa | Dofus : 2–5 typ. (1–8 extrêmes). Krosmoz : 1–12, souvent 2–4. Fixe, pas de dé. | 1 / 12 | round(min(12,max(1,[d]))) | — | Pas d’échantillon requis | Faible=2, moyen=3, fort=4 | Zone ou multicible : +1p ; portée longue : +1n ; effet fort : +1p | Effet du sort (dgt/retrait/boost) | 5.2.3 ; ARCHITECTURE §2 | …/spell/action_points-spell-definition.json | à revoir — normes |
| 2 | Utilisable en réaction | allows_reaction_spell | A | bool | allows_reaction | Pas DofusDB. Krosmoz : 0/1, rare. | 0 / 1 | min(1,max(0,round([d]))) | — | — | Sort peu puissant, hors zone | — | — | ARCHITECTURE §2 | …/spell/allows_reaction-spell-definition.json | sans conversion — normes seules |
| 3 | Zone | area_spell | A | int | area | Dofus : souvent >1 case. Krosmoz : 1 courant ; 2–4 moyen ; 5+ fort (rare). | 0 / 10 | [d] | — | — | 1=faible courant ; 2–4=moyen ; 5+=fort | — | En zone : cast_per_target souvent N/A | ARCHITECTURE §2 | …/spell/area-spell-definition.json | à revoir — normes |
| 4 | Caractéristique d’attaque | attack_characteristic_key_spell | A | string | attack_characteristic_key | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | "" / "" | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/attack_characteristic_key-spell-definition.json | aligné dépôt |
| 5 | Réussite auto si cible consentante | auto_success_if_willing_target_spell | A | bool | auto_success_if_willing_target | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 1 | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/auto_success_if_willing_target-spell-definition.json | aligné dépôt |
| 6 | Lancers par cible | cast_per_target_spell | A | int | cast_per_target | Dofus : 1–4. Krosmoz : 1 (parfois 2), max 4. Inutile si sort en zone. | 0 / 99 | [d] | — | — | 1=normal ; 2=moyen ; 3+=exception | Effet fort : -1p ; faible : +1p ; effets nombreux : -1p | Sorts puissants : baisser | 5.2.3 | …/spell/cast_per_target-spell-definition.json | à revoir — normes |
| 7 | Lancers par tour | cast_per_turn_spell | A | int | cast_per_turn | Dofus : 1–4. Krosmoz : 1–2 courant, max 6. | 0 / 99 | [d] | — | — | 1=faible ; 2=moyen ; 3+=fort | Effet fort : -1p ; zone : -1p | — | 5.2.3 | …/spell/cast_per_turn-spell-definition.json | à revoir — normes |
| 8 | Temps d'incantation | casting_time_spell | A | string | casting_time | Pas DofusDB. Krosmoz : null souvent ; combat 1 tour ; hors combat s/min/h. | "" / "" | [d] | — | — | Contexte combat / hors combat | Effets très importants | Spécifique hors combat | 5.2.3 | …/spell/casting_time-spell-definition.json | sans conversion — normes seules |
| 9 | Catégorie | category_spell | A | int | category | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 3 | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/category-spell-definition.json | aligné dépôt |
| 10 | Durée | duration_spell | A | string | duration | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | "" / "" | [d] | — | voir seed | oui | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/duration-spell-definition.json | aligné dépôt |
| 11 | Élément | element_spell | A | int | element | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 127 | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/element-spell-definition.json | aligné dépôt |
| 12 | Physique ou Wakfu | is_magic_spell | A | bool | is_magic | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 1 | min(1,max(0,round([d]))) | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/is_magic-spell-definition.json | aligné dépôt |
| 13 | Niveau du sort | level_spell | A | int | level | Niveau sort Dofus 1–200 ; table / floor(d/10) vers 1–20 | 1 / 200 | {"1":"floor([d]/10)","characteristic":"d"} | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/level-spell-definition.json | aligné dépôt |
| 14 | Délai entre deux lancers | number_between_two_cast_spell | A | int | number_between_two_cast | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 63 | [d] | — | voir seed | oui | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/number_between_two_cast-spell-definition.json | aligné dépôt |
| 15 | Puissance | power_spell | A | int | powerful | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 30 | round(min(30, max(1, 2 + 14 * pow(max(1,[d])/250, 0.5)))) | — | voir seed | oui | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/power-spell-definition.json | aligné dépôt |
| 16 | Portée modifiable | range_editable_spell | A | bool | po_editable | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 1 | min(1,max(0,round([d]))) | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/range_editable-spell-definition.json | aligné dépôt |
| 17 | Portée | range_spell | A | int | po | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 8 | round(min(8,max(0,[d]))) | — | voir seed | oui | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/range-spell-definition.json | aligné dépôt |
| 18 | Mode de résolution | resolution_mode_spell | A | string | resolution_mode | Pas DofusDB ; liste interne attack_roll / saving_throw / auto_success | "" / "" | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/resolution_mode-spell-definition.json | aligné dépôt |
| 19 | Rituel disponible | ritual_available_spell | A | bool | ritual_available | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 1 | min(1,max(0,round([d]))) | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/ritual_available-spell-definition.json | aligné dépôt |
| 20 | Caractéristique de sauvegarde | save_characteristic_key_spell | A | string | save_characteristic_key | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | "" / "" | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/save_characteristic_key-spell-definition.json | aligné dépôt |
| 21 | Formule du DD | save_dc_formula_spell | A | string | save_dc_formula | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | "" / "" | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/save_dc_formula-spell-definition.json | aligné dépôt |
| 22 | Effet si sauvegarde réussie | save_success_note_spell | A | string | save_success_note | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | "" / "" | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/save_success_note-spell-definition.json | aligné dépôt |
| 23 | Ligne de vue | sight_line_spell | A | bool | sight_line | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 1 | min(1,max(0,round([d]))) | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/sight_line-spell-definition.json | aligné dépôt |
| 24 | Portée max | spell_range_max_spell | A | int | po_max | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 63 | [d] | — | voir seed | oui | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/spell_range_max-spell-definition.json | aligné dépôt |
| 25 | Portée min | spell_range_min_spell | A | int | po_min | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | 0 / 63 | [d] | — | voir seed | oui | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/spell_range_min-spell-definition.json | aligné dépôt |
| 26 | Type de sort | spell_type_spell | A | string | spell_type | Valeur Dofus globale sort / effet → `[d]` (voir mapping scrapping `spell`) | "" / "" | — | — | voir seed | — | voir seed | `d` vide ; bool 0/1 | ARCHITECTURE §2 | ../../database/seeders/data/characteristic-definitions/spell/spell_type-spell-definition.json | aligné dépôt |

### 3.2 Effets et variations (Type 2 — famille B)

Conversion des **sous-effets** (boost, retrait, dommages, soins, déplacements, etc.).

| N° | Libellé (FR) | Clé BDD | Famille | Type | Colonne SQL | Sémantique Dofus & d | Min/max | Conversion (extrait) | conversion_function | Ancres/échantillons | Normes | Régulateurs | Cas limites | Doc réf. | Fichier seed | Statut |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | Variation de PA | action_points_variation_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 3 | round(min(3,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/action_points_variation-spell-definition.json | aligné dépôt |
| 2 | Agilité | agi_spell | B | int | agi | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/agi-spell-definition.json | aligné dépôt |
| 3 | CA | armor_class_spell | B | int | ca | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.45*pow(max(1,abs([d])),0.78)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/armor_class-spell-definition.json | aligné dépôt |
| 4 | Bouclier | bouclier_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 32 | round(min(32, max(1, 1.00 * pow(max(1,[d]), 0.475)))) | convertToDice | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/bouclier-spell-definition.json | aligné dépôt |
| 5 | Chance | chance_spell | B | int | chance | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/chance-spell-definition.json | aligné dépôt |
| 6 | Résistance critiques | critical_damage_reduction_spell | B | int | res_crit | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.65*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/critical_damage_reduction-spell-definition.json | aligné dépôt |
| 7 | Critiques | critical_spell | B | int | critical | Montant / carac ciblée dans effet Dofus → `d` | 0 / 2 | {"characteristic":"level","1":0,"7":1,"14":2} | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/critical-spell-definition.json | aligné dépôt |
| 8 | Dommages fixes multi | do_fixe_multiple_spell | B | int | do_fixe_multiple | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.58*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/do_fixe_multiple-spell-definition.json | aligné dépôt |
| 9 | Esquive PA | dodge_action_points_spell | B | int | esquive_pa | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.65*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/dodge_action_points-spell-definition.json | aligné dépôt |
| 10 | Esquive PM | dodge_movement_points_spell | B | int | esquive_pm | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.65*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/dodge_movement_points-spell-definition.json | aligné dépôt |
| 11 | Fuite | dodge_spell | B | int | fuite | Montant / carac ciblée dans effet Dofus → `d` | 0 / 10 | round(min(10,max(0,0.55*pow(max(1,[d]),0.45)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/dodge-spell-definition.json | aligné dépôt |
| 12 | Dommages | dommages_spell | B | int | — | Dofus : 5–600+ (fourchette large). Krosmoz : plafond ~45 ; **dés** 1d2→6d12 selon écart % (règle 20 %). Ancres : 1d4–6d8 distance, 1d6–6d10 moyen, 1d6–6d12 cac. | 0 / 45 | pow + convertToDice | convertToDice | Écart % Dofus → choix ndX | Privilégier dgt modérés ; sort multi-lance moins fort | PA 2:-1p ; PA4:+1p ; zone : +1n/case | Pas >150 dgt équivalent lvl 20 | 5.2.3 ; FORMULES_SORTS | …/spell/dommages-spell-definition.json | à revoir — conversion |
| 13 | Dégâts fixe Air | fixed_damage_air_spell | B | int | do_air | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_air-spell-definition.json | aligné dépôt |
| 14 | Dégâts fixe Terre | fixed_damage_earth_spell | B | int | do_terre | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_earth-spell-definition.json | aligné dépôt |
| 15 | Dégâts fixe Feu | fixed_damage_fire_spell | B | int | do_feu | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_fire-spell-definition.json | aligné dépôt |
| 16 | Dégâts fixe Neutre | fixed_damage_neutral_spell | B | int | do_neutre | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_neutral-spell-definition.json | aligné dépôt |
| 17 | Dégâts fixes Sagesse (sort) | fixed_damage_sagesse_spell | B | int | do_sagesse | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_sagesse-spell-definition.json | aligné dépôt |
| 18 | Dégâts fixes Vitalité (sort) | fixed_damage_vitalite_spell | B | int | do_vitalite | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_vitalite-spell-definition.json | aligné dépôt |
| 19 | Dégâts fixe Eau | fixed_damage_water_spell | B | int | do_eau | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.28*pow(max(1,[d]),0.5)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_damage_water-spell-definition.json | aligné dépôt |
| 20 | Résistance fixe Air | fixed_resistance_air_spell | B | int | res_fixe_air | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.72*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_resistance_air-spell-definition.json | aligné dépôt |
| 21 | Résistance fixe Eau | fixed_resistance_eau_spell | B | int | res_fixe_eau | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.72*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_resistance_eau-spell-definition.json | aligné dépôt |
| 22 | Résistance fixe Feu | fixed_resistance_feu_spell | B | int | res_fixe_feu | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.72*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_resistance_feu-spell-definition.json | aligné dépôt |
| 23 | Résistance fixe Neutre | fixed_resistance_neutre_spell | B | int | res_fixe_neutre | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.72*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_resistance_neutre-spell-definition.json | aligné dépôt |
| 24 | Résistance fixe Terre | fixed_resistance_terre_spell | B | int | res_fixe_terre | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.72*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/fixed_resistance_terre-spell-definition.json | aligné dépôt |
| 25 | Bonus de soin | heal_bonus_spell | B | int | heal_bonus | Montant / carac ciblée dans effet Dofus → `d` | 0 / 7 | round(min(7,max(0,0.5*pow(max(1,[d]),0.82)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/heal_bonus-spell-definition.json | aligné dépôt |
| 26 | Bonus de touche | hit_bonus_spell | B | int | touch | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.45*pow(max(1,abs([d])),0.78)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/hit_bonus-spell-definition.json | aligné dépôt |
| 27 | Initiative | initiative_spell | B | int | ini | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,0.38*pow(max(1,[d]),0.92)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/initiative-spell-definition.json | aligné dépôt |
| 28 | Intelligence | intel_spell | B | int | intel | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/intel-spell-definition.json | aligné dépôt |
| 29 | Passif | is_passive_spell | B | bool | is_passive | Montant / carac ciblée dans effet Dofus → `d` | 0 / 1 | min(1,max(0,round([d]))) | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/is_passive-spell-definition.json | aligné dépôt |
| 30 | Distance de saut | jump_distance_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/jump_distance-spell-definition.json | aligné dépôt |
| 31 | Bonus maîtrise | mastery_bonus_spell | B | int | mastery_bonus | Montant / carac ciblée dans effet Dofus → `d` | 0 / 6 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/mastery_bonus-spell-definition.json | aligné dépôt |
| 32 | Distance de déplacement | movement_distance_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/movement_distance-spell-definition.json | aligné dépôt |
| 33 | PM | movement_points_spell | B | int | pm | Montant / carac ciblée dans effet Dofus → `d` | 0 / 4 | round(min(4,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/movement_points-spell-definition.json | aligné dépôt |
| 34 | Distance d’attirance | pull_distance_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/pull_distance-spell-definition.json | aligné dépôt |
| 35 | Résistance poussée | push_damage_reduction_spell | B | int | res_push | Montant / carac ciblée dans effet Dofus → `d` | 0 / 20 | round(min(20,max(0,0.65*pow(max(1,[d]),0.48)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/push_damage_reduction-spell-definition.json | aligné dépôt |
| 36 | Distance de repousse | push_distance_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 5 | round(min(5,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/push_distance-spell-definition.json | aligné dépôt |
| 37 | Résistance Air | res_air_spell | B | int | res_air | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_air-spell-definition.json | aligné dépôt |
| 38 | Résistance Eau | res_eau_spell | B | int | res_eau | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_eau-spell-definition.json | aligné dépôt |
| 39 | Résistance Feu | res_feu_spell | B | int | res_feu | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_feu-spell-definition.json | aligné dépôt |
| 40 | Résistance Neutre | res_neutre_spell | B | int | res_neutre | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_neutre-spell-definition.json | aligné dépôt |
| 41 | Résistance Sagesse (élément) | res_sagesse_spell | B | int | res_sagesse | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_sagesse-spell-definition.json | aligné dépôt |
| 42 | Résistance Terre | res_terre_spell | B | int | res_terre | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_terre-spell-definition.json | aligné dépôt |
| 43 | Résistance Vitalité (élément) | res_vitalite_spell | B | int | res_vitalite | Montant / carac ciblée dans effet Dofus → `d` | 0 / 50 | round(min(50,max(0,0.55*pow(max(1,[d]),0.52)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/res_vitalite-spell-definition.json | aligné dépôt |
| 44 | Sagesse | sagesse_spell | B | int | sagesse | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/sagesse-spell-definition.json | aligné dépôt |
| 45 | Sauvegarde Agilité | save_agility_spell | B | int | save_agility | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/save_agility-spell-definition.json | aligné dépôt |
| 46 | Sauvegarde Chance | save_chance_spell | B | int | save_chance | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/save_chance-spell-definition.json | aligné dépôt |
| 47 | Sauvegarde Intelligence | save_intelligence_spell | B | int | save_intelligence | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/save_intelligence-spell-definition.json | aligné dépôt |
| 48 | Sauvegarde Force | save_strength_spell | B | int | save_strength | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/save_strength-spell-definition.json | aligné dépôt |
| 49 | Sauvegarde Vitalité | save_vitality_spell | B | int | save_vitality | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/save_vitality-spell-definition.json | aligné dépôt |
| 50 | Sauvegarde Sagesse | save_wisdom_spell | B | int | save_wisdom | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/save_wisdom-spell-definition.json | aligné dépôt |
| 51 | Soin | soin_spell | B | int | — | Dofus : 5–100 ou % PV. Krosmoz : 1d4–6d6 ; **dés** selon écart %. Ancres : 1d4–4d4 distance, 1d6–4d6 moyen, 1d6–6d6 cac. | 0 / 32 | pow + convertToDice | convertToDice | — | Soins modérés ; max 2 lancers/cible/cycle | Idem dommages (PA, zone) | Limiter inflation soins | 5.2.3 ; FORMULES_SORTS | …/spell/soin-spell-definition.json | à revoir — conversion |
| 52 | Force | strong_spell | B | int | strong | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/strong-spell-definition.json | aligné dépôt |
| 53 | Invocations | summoning_spell | B | int | invocation | Montant / carac ciblée dans effet Dofus → `d` | 0 / 3 | round(min(3,max(0,0.55*pow(max(1,[d]),0.78)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/summoning-spell-definition.json | aligné dépôt |
| 54 | Tacle | tackle_spell | B | int | tacle | Montant / carac ciblée dans effet Dofus → `d` | 0 / 10 | round(min(10,max(0,0.55*pow(max(1,[d]),0.45)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/tackle-spell-definition.json | aligné dépôt |
| 55 | Distance de téléportation | teleport_distance_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 12 | round(min(12,max(0,[d]))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/teleport_distance-spell-definition.json | aligné dépôt |
| 56 | Vitalité | vitality_spell | B | int | vitality | Montant / carac ciblée dans effet Dofus → `d` | 0 / 8 | round(min(8,max(0,0.55*pow(max(1,[d]),0.42)))) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/vitality-spell-definition.json | aligné dépôt |
| 57 | Vol de vie | vol_vie_spell | B | int | — | Montant / carac ciblée dans effet Dofus → `d` | 0 / 24 | round(min(24, max(1, 0.75 * pow(max(1,[d]), 0.475)))) | convertToDice | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/vol_vie-spell-definition.json | aligné dépôt |
| 58 | Réserve Wakfu | wakfu_reserve_spell | B | int | wakfu_reserve | Montant / carac ciblée dans effet Dofus → `d` | 0 / 6 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; malus/vol | ARCHITECTURE §3 ; FORMULES_SORTS | ../../database/seeders/data/characteristic-definitions/spell/wakfu_reserve-spell-definition.json | aligné dépôt |

## Groupes objets (famille B — bonus équipement / consommable / ressource / panoplie)

Les bonus peuvent être des **malus** (valeurs négatives). Un fichier par caractéristique dans `characteristic-definitions/object/`.

**À faire ligne par ligne** : voir [PROPOSITIONS_FORMULES_ET_PROPRIETES.md](../400-%20Jeu/410-%20Ressources/PROPOSITIONS_FORMULES_ET_PROPRIETES.md) (fourchettes par niveau, max forgemagie). Beaucoup de bonus : fourchette Dofus → conversion compressée ; si écart % > 20 → dés ; sinon fixe ou `ndX` stable. **Normes** même sans `conversion_*_sample`.

| N° | Libellé (FR) | Clé BDD | Famille | Type | Colonne SQL | Sémantique Dofus & d | Min/max | Conversion (extrait) | conversion_function | Ancres/échantillons | Normes | Régulateurs | Cas limites | Doc réf. | Fichier seed | Statut |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | Bonus d'Acrobaties | acrobatics_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/acrobatics-object-definition.json | aligné dépôt |
| 2 | Bonus passif (Acrobaties) | acrobatics_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/acrobatics_passive-object-definition.json | aligné dépôt |
| 3 | Bonus de points d'action | action_points_object | B | int | — | Agrégat DofusDB (effets équipement (n≈157), characteristic_id=1). obs. 0–1. écart 1→200: 0.0%. → `d` sur l’item. | 0 / 5 | [d] | — | Dofus: 1→1, 40→1, 80→1, 120→1, 160→1, 200→1 · K: 1→0 · (2.2.2 : PA base 6, max 12, équipement +6 max → bonus équipement 0 à 6) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/action_points-object-definition.json | aligné dépôt |
| 4 | Bonus d'agilité | agility_object | B | int | — | Agrégat DofusDB (effets équipement (n≈678), characteristic_id=14). obs. 8–46. écart 1→200: 475.0%. → `d` sur l’item. | 0 / 8 | floor(0.0408 * pow([d], 0.9412)) | — | Dofus: 1→8, 40→15, 80→30, 120→36, 160→42, 200→46 · K: 1→0 · (2.2.1 : jusqu'à +4 par objet) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/agility-object-definition.json | aligné dépôt |
| 5 | Bonus de Dressage | animal_handling_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/animal_handling-object-definition.json | aligné dépôt |
| 6 | Bonus passif (Dressage) | animal_handling_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/animal_handling_passive-object-definition.json | aligné dépôt |
| 7 | Bonus d'Arcanes | arcana_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/arcana-object-definition.json | aligné dépôt |
| 8 | Bonus passif (Arcanes) | arcana_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/arcana_passive-object-definition.json | aligné dépôt |
| 9 | Bonus de classe d'armure | armor_class_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/armor_class-object-definition.json | aligné dépôt |
| 10 | Bonus d'Athlétisme | athletics_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/athletics-object-definition.json | aligné dépôt |
| 11 | Bonus passif (Athlétisme) | athletics_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/athletics_passive-object-definition.json | aligné dépôt |
| 12 | Bonus de chance | chance_object | B | int | — | Agrégat DofusDB (effets équipement (n≈613), characteristic_id=13). obs. 7–50. écart 1→200: 614.3%. → `d` sur l’item. | 0 / 8 | floor(0.0408 * pow([d], 0.9412)) | — | Dofus: 1→7, 40→15, 80→29, 120→38, 160→43, 200→50 · K: 1→0 · (2.2.1 : jusqu'à +4 par objet) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/chance-object-definition.json | aligné dépôt |
| 13 | Bonus de coup critique | critical_hit_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | floor(0.0802 * [d] + 0.0445) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/critical_hit-object-definition.json | aligné dépôt |
| 14 | Bonus de Supercherie | deception_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/deception-object-definition.json | aligné dépôt |
| 15 | Bonus passif (Supercherie) | deception_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/deception_passive-object-definition.json | aligné dépôt |
| 16 | Description | description_object | B | string | description | Bonus item DofusDB → `d` | None / None | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/description-object-definition.json | aligné dépôt |
| 17 | Bonus de fuite | dodge_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(1.1 + 2* pow(([d]-1)/12, 0.6)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/dodge-object-definition.json | aligné dépôt |
| 18 | Bonus d'esquive aux points d'action | dodge_action_points_object | B | int | — | Agrégat DofusDB (effets équipement (n≈125), characteristic_id=27). obs. -2–23. écart 1→200: -69.6%. → `d` sur l’item. | 0 / 3 | floor(0.1202 * [d] + 0.5109) | — | Dofus: 1→23, 40→3, 80→4, 120→2, 160→7, 200→7 · K: 1→0 · (2.2.2 : +5 max équipement) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/dodge_action_points-object-definition.json | aligné dépôt |
| 19 | Bonus d'esquive aux points de mouvement | dodge_movement_points_object | B | int | — | Agrégat DofusDB (effets équipement (n≈102), characteristic_id=28). obs. -5–24. écart 1→200: -50.0%. → `d` sur l’item. | 0 / 3 | floor(0.1202 * [d] + 0.5109) | — | Dofus: 1→2, 40→4, 80→4, 120→6, 160→5, 200→1 · K: 1→0 · (2.2.2 : +5 max équipement) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/dodge_movement_points-object-definition.json | aligné dépôt |
| 20 | Bonus d'échec critique | failure_hit_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | floor(0.1244 * [d] + 0.4425) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/failure_hit-object-definition.json | aligné dépôt |
| 21 | Bonus de dégâts fixes (Air) | fixed_damage_air_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_damage_air-object-definition.json | aligné dépôt |
| 22 | Bonus de dégâts fixes (Terre) | fixed_damage_earth_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_damage_earth-object-definition.json | aligné dépôt |
| 23 | Bonus de dégâts fixes (Feu) | fixed_damage_fire_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_damage_fire-object-definition.json | aligné dépôt |
| 24 | Bonus de dégâts fixes (multi-éléments) | fixed_damage_multiple_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_damage_multiple-object-definition.json | aligné dépôt |
| 25 | Bonus de dégâts fixes (Neutre) | fixed_damage_neutral_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_damage_neutral-object-definition.json | aligné dépôt |
| 26 | Bonus de dégâts fixes (Eau) | fixed_damage_water_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(-0.1 + 1.78* pow(([d]-1)/4, 0.7)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_damage_water-object-definition.json | aligné dépôt |
| 27 | Bonus de résistance fixe (Air) | fixed_resistance_air_object | B | int | — | Agrégat DofusDB (effets équipement (n≈127), characteristic_id=57). obs. 3–26. écart 1→200: -42.3%. → `d` sur l’item. | 0 / 10 | floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6)) | — | Dofus: 1→26, 40→3, 80→5, 120→10, 160→9, 200→15 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_resistance_air-object-definition.json | aligné dépôt |
| 28 | Bonus de résistance fixe (Terre) | fixed_resistance_earth_object | B | int | — | Agrégat DofusDB (effets équipement (n≈117), characteristic_id=54). obs. 3–26. écart 1→200: -46.2%. → `d` sur l’item. | 0 / 10 | floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6)) | — | Dofus: 1→26, 40→3, 80→5, 120→10, 160→8, 200→14 · K: 1→0 · (2.2.2 : résistance fixe 0 à 10, équipement +10 max) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_resistance_earth-object-definition.json | aligné dépôt |
| 29 | Bonus de résistance fixe (Feu) | fixed_resistance_fire_object | B | int | — | Agrégat DofusDB (effets équipement (n≈127), characteristic_id=55). obs. 3–26. écart 1→200: -42.3%. → `d` sur l’item. | 0 / 10 | floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6)) | — | Dofus: 1→26, 40→3, 80→5, 120→11, 160→8, 200→15 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_resistance_fire-object-definition.json | aligné dépôt |
| 30 | Bonus de résistance fixe (Neutre) | fixed_resistance_neutral_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_resistance_neutral-object-definition.json | aligné dépôt |
| 31 | Bonus de résistance fixe (Eau) | fixed_resistance_water_object | B | int | — | Agrégat DofusDB (effets équipement (n≈120), characteristic_id=56). obs. 3–26. écart 1→200: -38.5%. → `d` sur l’item. | 0 / 10 | floor(1.1361 + 3.5* pow(([d]-1)/9, 0.6)) | — | Dofus: 1→26, 40→3, 80→5, 120→10, 160→8, 200→16 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/fixed_resistance_water-object-definition.json | aligné dépôt |
| 32 | Bonus de soins | heal_bonus_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | floor(0.2 * [d]) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/heal_bonus-object-definition.json | aligné dépôt |
| 33 | Bonus d'Histoire | history_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/history-object-definition.json | aligné dépôt |
| 34 | Bonus passif (Histoire) | history_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/history_passive-object-definition.json | aligné dépôt |
| 35 | Bonus de touche | hit_bonus_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/hit_bonus-object-definition.json | aligné dépôt |
| 36 | Bonus d'initiative | initiative_object | B | int | — | Agrégat DofusDB (effets équipement (n≈399), characteristic_id=44). obs. 12–386. écart 1→200: 1550.0%. → `d` sur l’item. | 0 / ∞ | floor(0.1643 * pow([d], 0.5457)) | — | Dofus: 1→12, 40→88, 80→107, 120→240, 160→232, 200→198 · K: 1→0 · (2.2.2 : modifiable par équipement (illimité) ; convention ordre de grandeur) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/initiative-object-definition.json | aligné dépôt |
| 37 | Bonus de Perspicacité | insight_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/insight-object-definition.json | aligné dépôt |
| 38 | Bonus passif (Perspicacité) | insight_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/insight_passive-object-definition.json | aligné dépôt |
| 39 | Bonus d'intelligence | intelligence_object | B | int | — | Agrégat DofusDB (effets équipement (n≈636), characteristic_id=15). obs. 7–44. écart 1→200: 528.6%. → `d` sur l’item. | 0 / 8 | floor(0.0408 * pow([d], 0.9412)) | — | Dofus: 1→7, 40→15, 80→29, 120→38, 160→40, 200→44 · K: 1→0 · (2.2.1 : jusqu'à +4 par objet) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/intelligence-object-definition.json | aligné dépôt |
| 40 | Bonus d'Intimidation | intimidation_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/intimidation-object-definition.json | aligné dépôt |
| 41 | Bonus passif (Intimidation) | intimidation_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/intimidation_passive-object-definition.json | aligné dépôt |
| 42 | Bonus d'Investigation | investigation_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/investigation-object-definition.json | aligné dépôt |
| 43 | Bonus passif (Investigation) | investigation_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/investigation_passive-object-definition.json | aligné dépôt |
| 44 | Niveau | level_object | B | string | level | Agrégat DofusDB (effets équipement, characteristic_id=5). obs. 3–200. écart 1→200: 6566.7%. → `d` sur l’item. | 1 / 20 | floor([d]/10) | — | Dofus: 1→3, 40→43, 80→83, 120→125, 160→164, 200→200 · K: 1→1 · (Niveau JDR = identité (échelle 1–20)) | — | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/level-object-definition.json | aligné dépôt |
| 45 | Bonus de points de vie maximum | life_points_max_object | B | int | — | Agrégat DofusDB (effets équipement (n≈579), characteristic_id=0). obs. -75–7680. écart 1→200: 1466.7%. → `d` sur l’item. | 0 / 30 | floor(-0.4+ 13.8587 * pow(([d]-10)/340, 0.8)) | — | Dofus: 1→3, 40→7108, 80→7626, 120→15, 160→15, 200→47 · K: 1→0 · (2.2.2 : modifiable par équipement (illimité) ; convention ordre de grandeur bonus PV) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/life_points_max-object-definition.json | aligné dépôt |
| 46 | Bonus de Médecine | medicine_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/medicine-object-definition.json | aligné dépôt |
| 47 | Bonus passif (Médecine) | medicine_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/medicine_passive-object-definition.json | aligné dépôt |
| 48 | Bonus de points de mouvement | movement_points_object | B | int | — | Agrégat DofusDB (effets équipement (n≈182), characteristic_id=23). obs. 0–1. → `d` sur l’item. | 0 / 3 | [d] | — | Dofus: 1→0, 40→1, 80→1, 120→1, 160→1, 200→1 · K: 1→0 · (2.2.2 : PM base 3, max 6, équipement +3 max) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/movement_points-object-definition.json | aligné dépôt |
| 49 | Nom | name_object | B | string | name | Bonus item DofusDB → `d` | None / None | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/name-object-definition.json | aligné dépôt |
| 50 | Bonus de Nature | nature_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/nature-object-definition.json | aligné dépôt |
| 51 | Bonus passif (Nature) | nature_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/nature_passive-object-definition.json | aligné dépôt |
| 52 | Bonus de Perception | perception_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/perception-object-definition.json | aligné dépôt |
| 53 | Bonus passif (Perception) | perception_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/perception_passive-object-definition.json | aligné dépôt |
| 54 | Bonus de Représentation | performance_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/performance-object-definition.json | aligné dépôt |
| 55 | Bonus passif (Représentation) | performance_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/performance_passive-object-definition.json | aligné dépôt |
| 56 | Bonus de Persuasion | persuasion_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/persuasion-object-definition.json | aligné dépôt |
| 57 | Bonus passif (Persuasion) | persuasion_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/persuasion_passive-object-definition.json | aligné dépôt |
| 58 | Prix | price_object | B | string | price | Bonus item DofusDB → `d` | 0 / None | [d] | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/price-object-definition.json | aligné dépôt |
| 59 | Bonus de portée | range_object | B | int | — | Bonus item DofusDB → `d` | 0 / 6 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/range-object-definition.json | aligné dépôt |
| 60 | Rareté | rarity_object | B | int | rarity | Bonus item DofusDB → `d` | 0 / 5 | {"0":"0","2":"0","7":"1","15":"2","20":"3","characteristic":"level"} | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/rarity-object-definition.json | aligné dépôt |
| 61 | Bonus de Religion | religion_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/religion-object-definition.json | aligné dépôt |
| 62 | Bonus passif (Religion) | religion_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/religion_passive-object-definition.json | aligné dépôt |
| 63 | Bonus de résistance en % (Air, palier) | resistance_percent_tier_air_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | {"characteristic":"d","0":0,"80":1,"95":2} | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/resistance_percent_tier_air-object-definition.json | aligné dépôt |
| 64 | Bonus de résistance en % (Terre, palier) | resistance_percent_tier_earth_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | {"characteristic":"d","0":0,"80":1,"95":2} | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/resistance_percent_tier_earth-object-definition.json | aligné dépôt |
| 65 | Bonus de résistance en % (Feu, palier) | resistance_percent_tier_fire_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | {"characteristic":"d","0":0,"80":1,"95":2} | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/resistance_percent_tier_fire-object-definition.json | aligné dépôt |
| 66 | Bonus de résistance en % (Neutre, palier) | resistance_percent_tier_neutral_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | {"characteristic":"d","0":0,"80":1,"95":2} | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/resistance_percent_tier_neutral-object-definition.json | aligné dépôt |
| 67 | Bonus de résistance en % (Eau, palier) | resistance_percent_tier_water_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | {"characteristic":"d","0":0,"80":1,"95":2} | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/resistance_percent_tier_water-object-definition.json | aligné dépôt |
| 68 | Bonus de sauvegarde (Agilité) | save_agility_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/save_agility-object-definition.json | aligné dépôt |
| 69 | Bonus de sauvegarde (Chance) | save_chance_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/save_chance-object-definition.json | aligné dépôt |
| 70 | Bonus de sauvegarde (Intelligence) | save_intelligence_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/save_intelligence-object-definition.json | aligné dépôt |
| 71 | Bonus de sauvegarde (Force) | save_strength_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/save_strength-object-definition.json | aligné dépôt |
| 72 | Bonus de sauvegarde (Vitalité) | save_vitality_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/save_vitality-object-definition.json | aligné dépôt |
| 73 | Bonus de sauvegarde (Sagesse) | save_wisdom_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/save_wisdom-object-definition.json | aligné dépôt |
| 74 | Bonus d'Escamotage | sleight_of_hand_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/sleight_of_hand-object-definition.json | aligné dépôt |
| 75 | Bonus passif (Escamotage) | sleight_of_hand_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/sleight_of_hand_passive-object-definition.json | aligné dépôt |
| 76 | Bonus de Discrétion | stealth_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/stealth-object-definition.json | aligné dépôt |
| 77 | Bonus passif (Discrétion) | stealth_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/stealth_passive-object-definition.json | aligné dépôt |
| 78 | Bonus de force | strength_object | B | int | — | Agrégat DofusDB (effets équipement (n≈644), characteristic_id=10). obs. 7–48. écart 1→200: 585.7%. → `d` sur l’item. | 0 / 8 | floor(0.0408 * pow([d], 0.9412)) | — | Dofus: 1→7, 40→16, 80→24, 120→38, 160→42, 200→48 · K: 1→0 · (2.2.1 : jusqu'à +4 par objet sur une caractéristique) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/strength-object-definition.json | aligné dépôt |
| 79 | Bonus au nombre d'invocations | summoning_object | B | int | — | Agrégat DofusDB (effets équipement (n≈283), characteristic_id=26). obs. 1–2. écart 1→200: 0.0%. → `d` sur l’item. | 0 / 5 | [d] | — | Dofus: 1→1, 40→1, 80→1, 120→1, 160→1, 200→1 · K: 1→0 · (2.2.2 : +5 max équipement) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/summoning-object-definition.json | aligné dépôt |
| 80 | Bonus de Survie | survival_object | B | int | — | Bonus item DofusDB → `d` | 0 / 5 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/survival-object-definition.json | aligné dépôt |
| 81 | Bonus passif (Survie) | survival_passive_object | B | int | — | Bonus item DofusDB → `d` | 0 / 2 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/survival_passive-object-definition.json | aligné dépôt |
| 82 | Bonus de tacle | tackle_object | B | int | — | Bonus item DofusDB → `d` | 0 / 10 | floor(1.1 + 2* pow(([d]-1)/12, 0.6)) | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/tackle-object-definition.json | aligné dépôt |
| 83 | Bonus de vitalité | vitality_object | B | int | — | Agrégat DofusDB (effets équipement (n≈1598), characteristic_id=11). obs. 16–307. écart 1→200: 1818.8%. → `d` sur l’item. | 0 / 6 | floor(0.0408 * pow([d], 0.9412)) | — | Dofus: 1→16, 40→31, 80→61, 120→136, 160→200, 200→307 · K: 1→0 · (2.2.1 : jusqu'à +4 par objet) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/vitality-object-definition.json | aligné dépôt |
| 84 | Bonus de recharge de réserve Wakfu | wakfu_recharge_object | B | int | — | Bonus item DofusDB → `d` | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/wakfu_recharge-object-definition.json | aligné dépôt |
| 85 | Poids | weight_object | B | int | weight | Agrégat DofusDB (effets équipement (n≈61), characteristic_id=40). obs. 114–698. écart 1→200: 338.6%. → `d` sur l’item. | 0 / None | floor([d] / 2) | — | Dofus: 1→114, 40→151, 80→233, 120→389, 160→500, 200→500 | — | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/weight-object-definition.json | aligné dépôt |
| 86 | Bonus de sagesse | wisdom_object | B | int | — | Agrégat DofusDB (effets équipement (n≈1031), characteristic_id=12). obs. 5–36. écart 1→200: 66.7%. → `d` sur l’item. | 0 / 6 | floor(0.0408 * pow([d], 0.9412)) | — | Dofus: 1→21, 40→11, 80→19, 120→27, 160→33, 200→35 · K: 1→0 · (2.2.1 : jusqu'à +4 par objet) | grille 5×20 | norms_conditions dans seed | d=0 ; rareté / FM | doc 400 ; PROPRIETES_CONVERSION | ../../database/seeders/data/characteristic-definitions/object/wisdom-object-definition.json | aligné dépôt |
## Groupes monsters (créature — stats de fiche)

Hors bonus de calcul composite (ex. formules `life` liées à plusieurs champs) : une ligne par caractéristique `characteristic-definitions/creature/`.

**À faire ligne par ligne** : priorité PV, PA, PM, stats primaires, résistances % — c’est le cœur des écarts Dofus (centaines de points) → Krosmoz (JDR). Pas d’échantillons obligatoires ; **normes** + conversion/formule à valider manuellement sur quelques monstres tests.

| N° | Libellé (FR) | Clé BDD | Famille | Type | Colonne SQL | Sémantique Dofus & d | Min/max | Conversion (extrait) | conversion_function | Ancres/échantillons | Normes | Régulateurs | Cas limites | Doc réf. | Fichier seed | Statut |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | Acrobaties | acrobatics_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/acrobatics-creature-definition.json | aligné dépôt |
| 2 | Acrobaties (palier maîtrise) | acrobatics_mastery_creature | A/B | int | acrobatie_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/acrobatics_mastery-creature-definition.json | aligné dépôt |
| 3 | Acrobaties (passif) | acrobatics_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/acrobatics_passive-creature-definition.json | aligné dépôt |
| 4 | Points d'action | action_points_creature | A | int | pa | Agrégat DofusDB (grades monstres (n≈13821), actionPoints). obs. 3–12. écart 1→200: 266.7%. → `d` au grade. | 6 / 12 | [d] | — | Dofus: 1→3, 40→7, 80→8, 120→9, 160→11, 200→11 · K: 1→6 · (2.2.2 : PA base 6, max 12) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/action_points-creature-definition.json | aligné dépôt |
| 5 | Agilité | agility_creature | A | int | agi | Agrégat DofusDB (grades monstres (n≈13821), agility). obs. 44–641. écart 1→200: 1313.6%. → `d` au grade. | 6 / 24 | [d] | — | Dofus: 1→44, 40→231, 80→260, 120→449, 160→641, 200→622 · K: 1→8 · (2.2.1 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/agility-creature-definition.json | aligné dépôt |
| 6 | Dressage | animal_handling_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/animal_handling-creature-definition.json | aligné dépôt |
| 7 | Dressage (palier maîtrise) | animal_handling_mastery_creature | A/B | int | dressage_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/animal_handling_mastery-creature-definition.json | aligné dépôt |
| 8 | Dressage (passif) | animal_handling_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/animal_handling_passive-creature-definition.json | aligné dépôt |
| 9 | Arcanes | arcana_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/arcana-creature-definition.json | aligné dépôt |
| 10 | Arcanes (palier maîtrise) | arcana_mastery_creature | A/B | int | arcane_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/arcana_mastery-creature-definition.json | aligné dépôt |
| 11 | Arcanes (passif) | arcana_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/arcana_passive-creature-definition.json | aligné dépôt |
| 12 | Classe d'armure | armor_class_creature | A | int | ca | Agrégat DofusDB (grades monstres, ca_creature). → `d` au grade. | 0 / 22 | 10+floor(([d]-10)/2) | — | K: 1→10 · (2.2.2.2 : 10 + mod. Vitalité + bonus bouclier (max 21+5)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/armor_class-creature-definition.json | aligné dépôt |
| 13 | Athlétisme | athletics_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/athletics-creature-definition.json | aligné dépôt |
| 14 | Athlétisme (palier maîtrise) | athletics_mastery_creature | A/B | int | athletisme_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/athletics_mastery-creature-definition.json | aligné dépôt |
| 15 | Athlétisme (passif) | athletics_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/athletics_passive-creature-definition.json | aligné dépôt |
| 16 | Chance | chance_creature | A | int | chance | Agrégat DofusDB (grades monstres (n≈13821), chance). obs. 34–717. écart 1→200: 1685.3%. → `d` au grade. | 6 / 24 | [d] | — | Dofus: 1→34, 40→222, 80→192, 120→361, 160→717, 200→607 · K: 1→8 · (2.2.1 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/chance-creature-definition.json | aligné dépôt |
| 17 | Bonus de critique | critical_hit_creature | A | int | critical_hit | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 3 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/critical_hit-creature-definition.json | aligné dépôt |
| 18 | Supercherie | deception_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/deception-creature-definition.json | aligné dépôt |
| 19 | Supercherie (palier maîtrise) | deception_mastery_creature | A/B | int | supercherie_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/deception_mastery-creature-definition.json | aligné dépôt |
| 20 | Supercherie (passif) | deception_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/deception_passive-creature-definition.json | aligné dépôt |
| 21 | Fuite | dodge_creature | A | int | fuite | Agrégat DofusDB (grades monstres (n≈13821), bonusCharacteristics.tackleEvade). obs. 0–0. → `d` au grade. | 0 / 17 | [d] | — | Dofus: 1→0, 40→0, 80→0, 120→0, 160→0, 200→0 · K: 1→0 · (Fuite (Dofus tackleEvade)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/dodge-creature-definition.json | aligné dépôt |
| 22 | Esquive PA | dodge_action_points_creature | A | int | dodge_pa | Agrégat DofusDB (grades monstres (n≈13821), paDodge). obs. 0–15. écart 1→200: 66.7%. → `d` au grade. | 0 / 20 | [d] | — | Dofus: 1→9, 40→4, 80→5, 120→4, 160→2, 200→15 · K: 1→7 · (2.2.2.3 : 8 + mod. Sagesse (max 19+5 équip.)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/dodge_action_points-creature-definition.json | aligné dépôt |
| 23 | Esquive PM | dodge_movement_points_creature | A | int | dodge_pm | Agrégat DofusDB (grades monstres (n≈13821), pmDodge). obs. 2–9. écart 1→200: 80.0%. → `d` au grade. | 0 / 20 | [d] | — | Dofus: 1→5, 40→5, 80→6, 120→5, 160→4, 200→9 · K: 1→7 · (2.2.2.3 : 8 + mod. Sagesse (max 19+5 équip.)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/dodge_movement_points-creature-definition.json | aligné dépôt |
| 24 | Dommage fixe Air | fixed_damage_air_creature | A | int | do_fixe_air | Agrégat DofusDB (grades monstres (n≈13821), bonusCharacteristics.bonusAirDamage). obs. 0–4. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→4, 40→0, 80→0, 120→0, 160→0, 200→0 · K: 1→0 · (Dégât fixe air) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_air-creature-definition.json | aligné dépôt |
| 25 | Dommage fixe Terre | fixed_damage_earth_creature | A | int | do_fixe_terre | Agrégat DofusDB (grades monstres (n≈13821), bonusCharacteristics.bonusEarthDamage). obs. 0–4. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→4, 40→0, 80→0, 120→0, 160→0, 200→0 · K: 1→0 · (Dégât fixe terre (Dofus bonusEarthDamage)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_earth-creature-definition.json | aligné dépôt |
| 26 | Dommage fixe Feu | fixed_damage_fire_creature | A | int | do_fixe_feu | Agrégat DofusDB (grades monstres (n≈13821), bonusCharacteristics.bonusFireDamage). obs. 0–4. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→4, 40→0, 80→0, 120→0, 160→0, 200→0 · K: 1→0 · (Dégât fixe feu) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_fire-creature-definition.json | aligné dépôt |
| 27 | Dommage fixe Multiples | fixed_damage_multiple_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 10 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_multiple-creature-definition.json | aligné dépôt |
| 28 | Dommage fixe Neutre | fixed_damage_neutral_creature | A | int | do_fixe_neutre | Agrégat DofusDB (grades monstres, do_fixe_neutre_creature). → `d` au grade. | 0 / 10 | -0.1704 + 8.418 * pow(([d]-0)/40, 1.3) | — | K: 1→0 · (Dégât fixe neutre) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_neutral-creature-definition.json | aligné dépôt |
| 29 | Dégâts fixes Sagesse | fixed_damage_sagesse_creature | A | int | do_sagesse | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 10 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_sagesse-creature-definition.json | aligné dépôt |
| 30 | Dégâts fixes Vitalité | fixed_damage_vitalite_creature | A | int | do_vitalite | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 10 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_vitalite-creature-definition.json | aligné dépôt |
| 31 | Dommage fixe Eau | fixed_damage_water_creature | A | int | do_fixe_eau | Agrégat DofusDB (grades monstres (n≈13821), bonusCharacteristics.bonusWaterDamage). obs. 0–4. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→4, 40→0, 80→0, 120→0, 160→0, 200→0 · K: 1→0 · (Dégât fixe eau) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_damage_water-creature-definition.json | aligné dépôt |
| 32 | Résistance fixe Air | fixed_resistance_air_creature | A | int | res_fixe_air | Agrégat DofusDB (grades monstres (n≈13821), airResistance). obs. 5–16. écart 1→200: 220.0%. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→5, 40→12, 80→14, 120→13, 160→16, 200→16 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_resistance_air-creature-definition.json | aligné dépôt |
| 33 | Résistance fixe Terre | fixed_resistance_earth_creature | A | int | res_fixe_terre | Agrégat DofusDB (grades monstres (n≈13821), earthResistance). obs. 6–19. écart 1→200: 150.0%. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→6, 40→12, 80→15, 120→15, 160→17, 200→15 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_resistance_earth-creature-definition.json | aligné dépôt |
| 34 | Résistance fixe Feu | fixed_resistance_fire_creature | A | int | res_fixe_feu | Agrégat DofusDB (grades monstres (n≈13821), fireResistance). obs. 5–19. écart 1→200: 220.0%. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→5, 40→11, 80→14, 120→15, 160→19, 200→16 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_resistance_fire-creature-definition.json | aligné dépôt |
| 35 | Résistance fixe Neutre | fixed_resistance_neutral_creature | A | int | res_fixe_neutre | Agrégat DofusDB (grades monstres (n≈13821), neutralResistance). obs. 10–25. écart 1→200: 90.0%. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→10, 40→20, 80→20, 120→21, 160→25, 200→19 · K: 1→0 · (2.2.2 : résistance fixe 0 à 10) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_resistance_neutral-creature-definition.json | aligné dépôt |
| 36 | Résistance fixe Eau | fixed_resistance_water_creature | A | int | res_fixe_eau | Agrégat DofusDB (grades monstres (n≈13821), waterResistance). obs. 6–20. écart 1→200: 166.7%. → `d` au grade. | 0 / 10 | [d] | — | Dofus: 1→6, 40→13, 80→10, 120→13, 160→20, 200→16 · K: 1→0 · (2.2.2 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/fixed_resistance_water-creature-definition.json | aligné dépôt |
| 37 | Bonus de soin | heal_bonus_creature | A | int | heal_bonus | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 7 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/heal_bonus-creature-definition.json | aligné dépôt |
| 38 | Histoire | history_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/history-creature-definition.json | aligné dépôt |
| 39 | Histoire (palier maîtrise) | history_mastery_creature | A/B | int | histoire_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/history_mastery-creature-definition.json | aligné dépôt |
| 40 | Histoire (passif) | history_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/history_passive-creature-definition.json | aligné dépôt |
| 41 | Bonus de touche | hit_bonus_creature | A | int | touch | Agrégat DofusDB (grades monstres, touch_creature). → `d` au grade. | 0 / 12 | [d] | — | K: 1→0 · (Portée 0 à 16 ; convention) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/hit_bonus-creature-definition.json | aligné dépôt |
| 42 | Dés de vie | hit_dice_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 10 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/hit_dice-creature-definition.json | aligné dépôt |
| 43 | Hostilité | hostility_creature | A | int | hostility | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 4 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/hostility-creature-definition.json | aligné dépôt |
| 44 | Initiative | initiative_creature | A | int | ini | Agrégat DofusDB (grades monstres, ini_creature). → `d` au grade. | 0 / None | [d] | — | K: 1→0 · (2.2.2.1 : 1d20 + mod. Intelligence + bonus équipement) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/initiative-creature-definition.json | aligné dépôt |
| 45 | Perspicacité | insight_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/insight-creature-definition.json | aligné dépôt |
| 46 | Perspicacité (palier maîtrise) | insight_mastery_creature | A/B | int | perspicacite_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/insight_mastery-creature-definition.json | aligné dépôt |
| 47 | Perspicacité (passif) | insight_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/insight_passive-creature-definition.json | aligné dépôt |
| 48 | Intelligence | intelligence_creature | A | int | intel | Agrégat DofusDB (grades monstres (n≈13821), intelligence). obs. 38–610. écart 1→200: 1492.1%. → `d` au grade. | 6 / 24 | [d] | — | Dofus: 1→38, 40→227, 80→219, 120→417, 160→610, 200→605 · K: 1→8 · (2.2.1 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/intelligence-creature-definition.json | aligné dépôt |
| 49 | Intimidation | intimidation_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/intimidation-creature-definition.json | aligné dépôt |
| 50 | Intimidation (palier maîtrise) | intimidation_mastery_creature | A/B | int | intimidation_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/intimidation_mastery-creature-definition.json | aligné dépôt |
| 51 | Intimidation (passif) | intimidation_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/intimidation_passive-creature-definition.json | aligné dépôt |
| 52 | Investigation | investigation_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/investigation-creature-definition.json | aligné dépôt |
| 53 | Investigation (palier maîtrise) | investigation_mastery_creature | A/B | int | investigation_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/investigation_mastery-creature-definition.json | aligné dépôt |
| 54 | Investigation (passif) | investigation_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/investigation_passive-creature-definition.json | aligné dépôt |
| 55 | Niveau | level_creature | A | int | level | Agrégat DofusDB (grades monstres (n≈13821), level). obs. 3–247. écart 1→200: 8133.3%. → `d` au grade. | 1 / 20 | {"1":"floor([d]/10)","characteristic":"d"} | — | Dofus: 1→3, 40→44, 80→83, 120→122, 160→162, 200→247 · K: 1→1 · (Niveau JDR = identité (échelle 1–20)) | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/level-creature-definition.json | aligné dépôt |
| 56 | Dé de vie | life_dice_creature | A | string | life_dice | Agrégat DofusDB (grades monstres, de_vie_creature). → `d` au grade. | None / None | — | — | K: 1→0 · (2.2.2.4 : floor(niveau/2), max 10) | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/life_dice-creature-definition.json | aligné dépôt |
| 57 | Points de vie | life_points_creature | A | int | life | Agrégat DofusDB (grades monstres (n≈13821), lifePoints). obs. 137–9528. écart 1→200: 3636.5%. → `d` au grade. | [hit_dice_creature] / ∞ | floor([d]/200)+[level]*5 | — | Dofus: 1→255, 40→681, 80→1906, 120→3533, 160→5542, 200→9528 · K: 1→20 · (2.2.2 : PV fonction de la classe / créature ; convention ordre de grandeur) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/life_points-creature-definition.json | aligné dépôt |
| 58 | Bonus de maîtrise | mastery_bonus_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 6 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/mastery_bonus-creature-definition.json | aligné dépôt |
| 59 | Médecine | medicine_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/medicine-creature-definition.json | aligné dépôt |
| 60 | Médecine (palier maîtrise) | medicine_mastery_creature | A/B | int | medecine_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/medicine_mastery-creature-definition.json | aligné dépôt |
| 61 | Médecine (passif) | medicine_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/medicine_passive-creature-definition.json | aligné dépôt |
| 62 | Modificateur d'Agilité | modifier_agility_creature | A | int | — | Agrégat DofusDB (grades monstres, modificateur_agi_creature). → `d` au grade. | -2 / 7 | [d] | — | K: 1→0 · (Modificateur d'agilité (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/modifier_agility-creature-definition.json | aligné dépôt |
| 63 | Modificateur de Chance | modifier_chance_creature | A | int | — | Agrégat DofusDB (grades monstres, modificateur_chance_creature). → `d` au grade. | -2 / 7 | [d] | — | K: 1→0 · (Modificateur de chance (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/modifier_chance-creature-definition.json | aligné dépôt |
| 64 | Modificateur d'Intelligence | modifier_intelligence_creature | A | int | — | Agrégat DofusDB (grades monstres, modificateur_intel_creature). → `d` au grade. | -2 / 7 | [d] | — | K: 1→0 · (Modificateur d'intelligence (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/modifier_intelligence-creature-definition.json | aligné dépôt |
| 65 | Modificateur de Force | modifier_strength_creature | A | int | — | Agrégat DofusDB (grades monstres, modificateur_force_creature). → `d` au grade. | -2 / 7 | [d] | — | K: 1→0 · (Modificateur de force (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/modifier_strength-creature-definition.json | aligné dépôt |
| 66 | Modificateur de Vitalité | modifier_vitality_creature | A | int | — | Agrégat DofusDB (grades monstres, modificateur_vitality_creature). → `d` au grade. | -2 / 7 | [d] | — | K: 1→0 · (Modificateur de vitalité (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/modifier_vitality-creature-definition.json | aligné dépôt |
| 67 | Modificateur de Sagesse | modifier_wisdom_creature | A | int | — | Agrégat DofusDB (grades monstres, modificateur_sagesse_creature). → `d` au grade. | -2 / 7 | [d] | — | K: 1→0 · (Modificateur de sagesse (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/modifier_wisdom-creature-definition.json | aligné dépôt |
| 68 | Points de mouvement | movement_points_creature | A | int | pm | Agrégat DofusDB (grades monstres (n≈13821), movementPoints). obs. 2–6. écart 1→200: 150.0%. → `d` au grade. | 3 / 6 | [d] | — | Dofus: 1→2, 40→5, 80→4, 120→5, 160→5, 200→5 · K: 1→3 · (2.2.2 : PM base 3, max 6) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/movement_points-creature-definition.json | aligné dépôt |
| 69 | Nature | nature_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/nature-creature-definition.json | aligné dépôt |
| 70 | Nature (palier maîtrise) | nature_mastery_creature | A/B | int | nature_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/nature_mastery-creature-definition.json | aligné dépôt |
| 71 | Nature (passif) | nature_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/nature_passive-creature-definition.json | aligné dépôt |
| 72 | Perception | perception_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/perception-creature-definition.json | aligné dépôt |
| 73 | Perception (palier maîtrise) | perception_mastery_creature | A/B | int | perception_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/perception_mastery-creature-definition.json | aligné dépôt |
| 74 | Perception (passif) | perception_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/perception_passive-creature-definition.json | aligné dépôt |
| 75 | Représentation | performance_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/performance-creature-definition.json | aligné dépôt |
| 76 | Représentation (palier maîtrise) | performance_mastery_creature | A/B | int | representation_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/performance_mastery-creature-definition.json | aligné dépôt |
| 77 | Représentation (passif) | performance_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/performance_passive-creature-definition.json | aligné dépôt |
| 78 | Persuasion | persuasion_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/persuasion-creature-definition.json | aligné dépôt |
| 79 | Persuasion (palier maîtrise) | persuasion_mastery_creature | A/B | int | persuasion_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/persuasion_mastery-creature-definition.json | aligné dépôt |
| 80 | Persuasion (passif) | persuasion_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/persuasion_passive-creature-definition.json | aligné dépôt |
| 81 | Portée | range_creature | A | int | po | Agrégat DofusDB (grades monstres (n≈13821), bonusRange). obs. 0–1. → `d` au grade. | 0 / 6 | [d] | — | Dofus: 1→0, 40→0, 80→0, 120→0, 160→1, 200→0 · K: 1→0 · (2.2.2 : PO 0 à 6) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/range-creature-definition.json | aligné dépôt |
| 82 | Religion | religion_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/religion-creature-definition.json | aligné dépôt |
| 83 | Religion (palier maîtrise) | religion_mastery_creature | A/B | int | religion_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/religion_mastery-creature-definition.json | aligné dépôt |
| 84 | Religion (passif) | religion_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/religion_passive-creature-definition.json | aligné dépôt |
| 85 | Résistance Air % | resistance_air_creature | A | int | res_air | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_air-creature-definition.json | aligné dépôt |
| 86 | Résistance Terre % | resistance_earth_creature | A | int | res_terre | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_earth-creature-definition.json | aligné dépôt |
| 87 | Résistance Feu % | resistance_fire_creature | A | int | res_feu | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_fire-creature-definition.json | aligné dépôt |
| 88 | Résistance Neutre % | resistance_neutral_creature | A | int | res_neutre | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_neutral-creature-definition.json | aligné dépôt |
| 89 | Résistance Sagesse % | resistance_sagesse_creature | A | int | res_sagesse | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_sagesse-creature-definition.json | aligné dépôt |
| 90 | Résistance Vitalité % | resistance_vitalite_creature | A | int | res_vitalite | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_vitalite-creature-definition.json | aligné dépôt |
| 91 | Résistance Eau % | resistance_water_creature | A | int | res_eau | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 100 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/resistance_water-creature-definition.json | aligné dépôt |
| 92 | Bonus jet de sauvegarde Agilité | save_agility_creature | A | int | — | Agrégat DofusDB (grades monstres, save_agi_creature). → `d` au grade. | -1 / 16 | [d] | — | K: 1→0 · (Bonus jet de sauvegarde Agilité (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/save_agility-creature-definition.json | aligné dépôt |
| 93 | Bonus jet de sauvegarde Chance | save_chance_creature | A | int | — | Agrégat DofusDB (grades monstres, save_chance_creature). → `d` au grade. | -1 / 16 | [d] | — | K: 1→0 · (Bonus jet de sauvegarde Chance (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/save_chance-creature-definition.json | aligné dépôt |
| 94 | Bonus jet de sauvegarde Intelligence | save_intelligence_creature | A | int | — | Agrégat DofusDB (grades monstres, save_intel_creature). → `d` au grade. | -1 / 16 | [d] | — | K: 1→0 · (Bonus jet de sauvegarde Intelligence (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/save_intelligence-creature-definition.json | aligné dépôt |
| 95 | Bonus jet de sauvegarde Force | save_strength_creature | A | int | — | Agrégat DofusDB (grades monstres, save_force_creature). → `d` au grade. | -1 / 16 | [d] | — | K: 1→0 · (Bonus jet de sauvegarde Force (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/save_strength-creature-definition.json | aligné dépôt |
| 96 | Bonus jet de sauvegarde Vitalité | save_vitality_creature | A | int | — | Agrégat DofusDB (grades monstres, save_vitality_creature). → `d` au grade. | -1 / 16 | [d] | — | K: 1→0 · (Bonus jet de sauvegarde Vitalité (Krosmoz only)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/save_vitality-creature-definition.json | aligné dépôt |
| 97 | Bonus jet de sauvegarde Sagesse | save_wisdom_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -1 / 16 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/save_wisdom-creature-definition.json | aligné dépôt |
| 98 | Escamotage | sleight_of_hand_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/sleight_of_hand-creature-definition.json | aligné dépôt |
| 99 | Escamotage (palier maîtrise) | sleight_of_hand_mastery_creature | A/B | int | escamotage_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/sleight_of_hand_mastery-creature-definition.json | aligné dépôt |
| 100 | Escamotage (passif) | sleight_of_hand_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/sleight_of_hand_passive-creature-definition.json | aligné dépôt |
| 101 | Discrétion | stealth_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/stealth-creature-definition.json | aligné dépôt |
| 102 | Discrétion (palier maîtrise) | stealth_mastery_creature | A/B | int | discretion_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/stealth_mastery-creature-definition.json | aligné dépôt |
| 103 | Discrétion (passif) | stealth_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/stealth_passive-creature-definition.json | aligné dépôt |
| 104 | Force | strength_creature | A | int | strong | Agrégat DofusDB (grades monstres (n≈13821), strength). obs. 36–636. écart 1→200: 1636.1%. → `d` au grade. | 6 / 24 | [d] | — | Dofus: 1→36, 40→255, 80→235, 120→414, 160→636, 200→625 · K: 1→8 · (2.2.1 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/strength-creature-definition.json | aligné dépôt |
| 105 | Nombre d'invocations | summoning_creature | A | int | invocation | Agrégat DofusDB (grades monstres, invocation_creature). → `d` au grade. | 1 / 6 | [d] | — | K: 1→1 · (Invocation 1 à 6) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/summoning-creature-definition.json | aligné dépôt |
| 106 | Survie | survival_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | -2 / 24 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/survival-creature-definition.json | aligné dépôt |
| 107 | Survie (palier maîtrise) | survival_mastery_creature | A/B | int | survie_mastery | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 2 | — | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/survival_mastery-creature-definition.json | aligné dépôt |
| 108 | Survie (passif) | survival_passive_creature | A/B | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 8 / 34 | — | — | conversion_*_sample dans seed | — | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/survival_passive-creature-definition.json | aligné dépôt |
| 109 | Tacle | tackle_creature | A | int | tacle | Agrégat DofusDB (grades monstres (n≈13821), bonusCharacteristics.tackleBlock). obs. 0–0. → `d` au grade. | 0 / 17 | [d] | — | Dofus: 1→0, 40→0, 80→0, 120→0, 160→0, 200→0 · K: 1→0 · (Tacle (Dofus tackleBlock)) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/tackle-creature-definition.json | aligné dépôt |
| 110 | Vitalité | vitality_creature | A | int | vitality | Agrégat DofusDB (grades monstres (n≈13821), vitality). obs. 0–19. → `d` au grade. | 6 / 24 | [d] | — | Dofus: 1→0, 40→2, 80→11, 120→11, 160→15, 200→19 · K: 1→8 · (2.2.1 : score caractéristique 6–31 par niveau) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/vitality-creature-definition.json | aligné dépôt |
| 111 | Réserve de Wakfu | wakfu_reserve_creature | A | int | — | Stat monstre DofusDB → `d` (+ niveau si formule) | 0 / 9 | [d] | — | conversion_*_sample dans seed | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/wakfu_reserve-creature-definition.json | aligné dépôt |
| 112 | Sagesse | wisdom_creature | A | int | sagesse | Agrégat DofusDB (grades monstres (n≈13821), wisdom). obs. 59–654. écart 1→200: 1008.5%. → `d` au grade. | 6 / 24 | [d] | — | Dofus: 1→59, 40→146, 80→180, 120→330, 160→515, 200→654 · K: 1→8 · (2.2.1 : idem) | grille 5×20 | norms_conditions dans seed | d=0 ; formules liées | doc 400 ; creature | ../../database/seeders/data/characteristic-definitions/creature/wisdom-creature-definition.json | aligné dépôt |
# Fonctionnalités
## Améliorations
### Caractéristiques 
- Au niveau de l'édition d'une caractéristique dans la vue edit d'un monstre par exemple, il serai bien d'avoir une icone aide à coté de l'imput. Cette icone permet d'ouvrir un popover avec le tableau ou graph des nomes et les quelques conseils associés. Cela permet d'y accèder là où il y en a besoin.
### Scrapping
Vérifier la pipeline et les tests ( il faut ça fonctionne et que ça soit robuste et limiter le nombre de requête)
### Conversion
Les tableaux sont ci-dessus pour améliorer les conversions. Il est important de se rapprocher le plus possible de ce que seront les valeurs Krosmoz pour nous faire gagner du temps lors de la relecture manuel.
### Retour utilisateur
Dans le système de retour "Signaler un problème ou faire une suggestion" : pour les utilisateurs connectés, il faut cocher un check input pour recevoir un mail récapitulatif. False par defaut.
## Nouveautés
### Moteur de recherche
Il existe différent moteur de recherche dans des pages d'édition pour créer les liens.
Mais il n'y a pas encore de moteur de recherche principal. Je souhaite qu'il recherche dans toutes les sections, pages et entités où le user a les droit de read.
Concernant l'UI et l'UX de ce moteur, l'input existe déjà dans le layout header. J'aimerai que lorsqu'on prenne le focus dedans (avec la souris ou via le raccourci clavier) ; 
- que l'input grandissent pour prendre plus de place 
- qu'il y ait les filtres qui s'affichent en dessous de l'input. L'idée est d'avoir toutes les entités en formats style Filter (https://daisyui.com/components/filter/). On met dans une sorte de bage filter l'icone de l'entité avec son nom. Si il a sa couleur de fond alors on recherche dans cette entité sinon on le laisse en noir et blanc. Pas besoin de croix pour supprimer. En plus des entités à rechercher il faut rajouter les pages et les sections. 
  Dans les filtres je souhaite aussi qu'on puisse choisir l'état, qu'on puisse choisir un état ou tout les états (ou mixte).
  J'aimerai aussi qu'on assombrisse et floute le restant de la page pour donner un effet un peu modal.
  Puis on fait une liste avec des vues Texte des différentes entités. On peut classer les résultats par types d'entités en mettant un badge avec la couleur de l'entité pour les séparés. Il faut respecter le design du site et utiliser les classes déjà existante.
# Optimisation
## Répertoires et Fichiers
Le projet a beaucoup évoluer et pleins de fichiers ont été créé. Il serai bien de créer des répertoires là où il y a bcp de fichier pour avoir une architecture plus logique et moins bordélique. Il y a aussi des endroits où il serai intéressant de déplacer des fichiers d'un répertoire à l'autre ou de créer des nouveaux sous dossier pour mieux organiser le code.
## Documentation
La documentation est un gros morceaux qui s'est alourdi au fur et à mesure du projet. Il serai bien de nettoyé toutes la doc, en reclassifiant si besoin, enlever tout ce qui est obsolère, mettre à jour ce qui a besoin et enlever les parties qui commentent l'évolution d'une fonctionnalité (pas besoin de décrire l'évolution d'une fonctionnalité mais surtout décrire les fonctionnalités comme elles sont aujourd'hui).
## Commandes
Les commandes pourraient être optimisé car il y a des doublons entre certaines commande au lieu de les appeler entre elles.
Voici les principales commandes du projet : 
### Project:seed 
Cette commande n'existe pas mais j'aimerai une commande pour lancer tout les seeds même ceux qui ne le sont pas vraiment (capacité, specialisation, essentiel, rules). On ne lance pas le scrapping via cette commande.
### Project:review
Vérifier si la review est complète et si il ne manque rien pour une review complète.

1. Tests backend (`test_back`) : `php artisan test` (timeout 7200 s).
2. Tests frontend (`test_front`) : `pnpm run test:run` (3600 s).
3. Bloc « Qualité » (si au moins une sous-étape qualité) :
    - PHPStan : `vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress` (900 s).
    - Pint : `vendor/bin/pint --test` [+ `--dirty` si demandé], avec repli par lots sur `app`, `routes`, `config`, `database`, `tests`, `bootstrap`, `lang` si timeout et pas `--no-pint-batches`.
    - ESLint : `pnpm run lint` (300 s).
4. Pint « apply » : si `--fix-pint` et Pint dans le plan → second passage Pint sans `--test` (même stratégie timeout / lots).
5. Sécurité : `composer audit --no-interaction` (120 s).
6. Documentation : JSON `docs/docs.index.json` valide ; présence `docs/README.md` ; présence `docs/DOCUMENTATION_GUIDE.md` (absence = échec de l’étape doc) ; note sur `pnpm run update:docs`.

Sorties : intégrées au rapport en blocs `text` avec statut OK / échec.
### Project:init
Vérifier l'ordre des exécutions, Vérifier aussi que toutes les données sont bien importé (en théorie).
Faire un script qui vérifie qu'il y ait toutes les choses de base.
#### Optionnel — phase 0
- Mise à jour : `--deps` : enchaîne `project:deps` (composer, pnpm, `project:optimize`).
#### Phase 1 — base de données
- `migrate` ou, avec `--fresh`, `migrate:fresh --force`, sauf `--skip-migrate`.
- `storage:link` (lien symbolique public → storage
#### Phase 2 — seeders (sauf `--skip-seeders`)
1. `scrapping:setup` avec `--skip-migrate` (les migrations viennent d’être faites). Il exécute dans l’ordre ces seeders :
    - `TypeSeeder` → à son tour : `ItemTypeSeeder`, `ConsumableTypeSeeder`, `MonsterRaceSeeder`, `ResourceTypeSeeder`, `SpellTypeSeeder`
    - `CharacteristicSeeder`
    - `CreatureCharacteristicSeeder`
    - `ObjectCharacteristicSeeder`
    - `DofusdbCharacteristicIdSeeder`
    - `SpellCharacteristicSeeder`
    - `SpellEffectTypeSeeder`
    - `DofusdbEffectMappingSeeder`
    - `ScrappingEntityMappingSeeder`
    - `ScrappingEntityMappingCharacteristicSeeder`
2. Puis, en `db:seed --class=...` dans cet ordre :
    - `UserSeeder` — utilisateurs de base ; après succès, invite à créer le premier super_admin (sauf `--skip-super-admin-prompt`, utile CI).
    - `CriticalPagesSeeder`
    - `NavMenuSeeder`
    - `PageSeeder`
    - `SectionSeeder`
    - `SubEffectSeeder`
    - `LanguageSeeder`
    - `ConditionSeeder`
    - `CreatureTraitSeeder`
    - `CreationPagesSeeder`
3. `SpecializationSeeder` — sauf `--skip-specializations` : import legacy depuis des fichiers HTML sous `database/seeders/data/legacy-specializations/` s’ils existent.
#### Phase 2b — règles CMS (dépend des pages/sections seedées)
- `project:data:import-rules-toc` : import de la table des matières (référence `TABLE_DES_MATIERES.md`) vers les pages règles CMS. Ignoré si `--skip-seeders`.
#### Phase 3 — capabilities (local, pas DofusDB) — sauf `--skip-capabilities`
- Si le fichier existe : `capabilities:import-legacy` sur `database/seeders/data/capability.json`. Sinon le message indique que l’import est ignoré.
#### Phase 4 — types DofusDB (API) — sauf `--skip-types`
- `scrapping:types:seed` — types d’objets API (ressources, consommables, équipements, etc., tel que documenté dans le projet).
- `scrapping:races:seed` — races monstres depuis l’API DofusDB (`/monster-races`).
- `SpellTypeSeeder` (référentiel métier ; peut faire écho à ce qui a déjà tourné dans `TypeSeeder` lors du setup).
Les options `--skip-cache` sont propagées aux commandes de types qui les supportent.
#### Phase 5 — scrapping entités (API, le plus long) — sauf `--skip-scrapping`
- Vidage de la queue avant scrapping : `queue:clear` + `queue:flush` (sauf queue `sync` ou `--skip-clear-queue`).
- Entités par défaut, dans l’ordre : `class` (breeds), `spell`, `monster`, `resource`, `consumable`, `item`, `panoply`.  
    Filtrage possible avec `--entity=breed|class,spell,...` (voir la signature ; `breed`/`class` pour les classes).
- Appel `scrapping:run` avec notamment : `--max-items`, `--limit` (100), `--max-pages` (0), `--update-mode` (défaut `ignore`), `--skip-existing`, et selon les flags `--noimage`, `--simulate`, `--skip-cache`.
- `resource` : en plus, `--resource-types=allowed`.
- `monster` : boucle par tranches de niveau 1–250, pas de `50` niveaux (`MONSTER_LEVEL_CHUNK`), un `scrapping:run` par tranche pour limiter les timeouts.
- `DB::reconnect()` entre entités / tranches monstres.
#### Phase 6 — scheduler — seulement `--init-scheduler`
- Affiche la ligne crontab pour `schedule:run`, rappelle `PROJECT_UPDATE_AUTO_ENABLED` / `PROJECT_UPDATE_CRON`, et exécute `schedule:list`.
#### Fin
- `printInitSummary` : récap par phase.
- `NotificationService::notifyProjectMaintenance('init', ...)` vers admin/super_admin, sauf `--skip-notify`.
### Project:update
1. `set_time_limit(0)`, titre « Mise à jour des données (auto_update) ».
2. File d’attente (sauf `--skip-clear-queue` et sauf queue `sync`) :  
    `queue:clear` + `queue:flush` (comme pour `project:init`).
3. `effects:rebuild-signatures` avant les mises à jour — sauf si `--dry-run` (alors cette phase est sautée).
4. Pour chaque entité (filtrée ou toutes) :
    - Lecture des IDs à traiter : modèle Eloquent associé, `auto_update = true`, `dofusdb_id` renseigné et > 0 (voir `getAutoUpdateIds`).
    - Si la table n’a pas la colonne `auto_update`, message d’avertissement et aucun ID (ex. mention dans le code pour panoplies si migration manquante).
    - Sinon, paquets de 100 IDs (`IDS_CHUNK_SIZE`) : pour chaque chunk, appel `scrapping:run` avec :
        - `--entity` = alias (`class`, `spell`, `monster`, …),
        - `--ids` = liste d’IDs DofusDB du chunk,
        - `--update-mode=auto_update`,
        - `--skip-existing=true`,
        - et selon options : `--noimage`, `--skip-cache`, `--simulate` (dry-run).
    - `DB::reconnect()` après chaque chunk.
5. `effects:rebuild-signatures` après les mises à jour — encore une fois sauf `--dry-run`.
6. Notification `NotificationService::notifyProjectMaintenance('update', ...)` avec un résumé erreurs / entités traitées — sauf `--skip-notify`.
7. Code de sortie : échec si au moins un chunk a renvoyé un code non nul.
### Project:clear
Son but est de nettoyer caches et artefacts du projet via `ProjectRunService` (pas de seed ni de migration).
Y a t'il d'autres choses à nettoyer comme des fichiers ou répertoires temporaire (review, backup obsolète) ?
Il faut que cette commande ne soit pas destructrice. Elle sera appeler régulièrement via cron.
### Projeck:refresh
Manque t'il des choses dans le project:refresh ? Il devrait servir à remettre tout à zéro puis relancer une initiation.

1. Confirmation (sauf `--force`) : danger `migrate:fresh`.
2. Si `--hard` : `setup --refresh` — en cas d’échec, arrêt avec le code retour de `setup`.
3. `migrate:fresh --force` et, par défaut, `--seed` (sauf si `--without-seed`) → lance le seeder global Laravel (`DatabaseSeeder`), pas le pipeline complet `project:init` (pas de scrapping DofusDB, etc.).
4. `project:clear --all` via `ProjectRunService` avec la clé `clear:all` (même logique que `project:clear --all`).
5. Message de succès.

### Project:cron
Cette commande est l'entrée pour les tâches crons. On insére l'option que l'on souhaite comme --clear, --update, etc
# UI / UX
## Sections texte
Dans les sections textes, il y a possibilité d'implémenter des liens vers des caractéristiques, des sections et des entités grâce au / ou en utilisant le bouton de l'interface. Cela ajoute dans le texte une sorte de balise pour intégrer ces éléments sous forme de vue Text et un popover pour ouvrir la vue minimal (c'est le principal de la vue Text : image - nom avec popover vue minimal).
Pour les caractéristiques ça marche très bien, mais avec les sections, j'ai bien son nom mais ça n'arrive pas à la charger dans un tooltips pour voir le début d'une section avec un scroll vertical.
C'est la même chose avec les entités : j'aimerai qu'on affiche la vue minimal dans une popover.
## Responsive
Faire une passe sur tout le code front pour voir si le responsive est bien pensé et implémenté partout.
## Accessibilité 
Pour l'accessibilité, il est peut être interessant d'utiliser un plugin pour faire un rapport automatique.
- Faire une passe sur tout le code front pour voir si les bonnes pratiques du web lié à l'accessibilité sont bien respectés. Sinon ajouter, corrigerer cela (ajout de balise, de meta, etc).
- Vérifier les contrastes et la bonne lecture du texte
## Bandeaux d'affichages
Les bandeaux Alert qui permettent de mettre des informations ont un contraste déplorable. Il faut les mettre avec intensité de couleur foncé et un look glass (peu d'arrondi, flou avec un background pas tout à faire opaque, etc), et la font doit être claire (c'est le cas actuellement).
## Notifications
Les notifications temporaires ont un délai avant de se fermer. Pendant une partie du temps elles sont déplié puis elle se repli pour prendre moins de place. Il faudrai allonger leur durée de vie car aujourd'hui c'est trop court.
