# Décisions — release 1.3.2

**Statut : validé** (réponses du produit en date du 2026-05-17).  
Fichiers liés : [`To do 1.3.1 vers 1.3.2.md`](./To%20do%201.3.1%20vers%201.3.2.md) (spec exhaustive), [`PLAN-EXECUTION-release-1.3.2.md`](./PLAN-EXECUTION-release-1.3.2.md) (phases).

| ID | Statut |
| --- | --- |
| Q1–Q27 | Validé |

---

## A — Administration, rôles, ré-authentification

### Q1 — Menu compte : qui voit quoi ?

**Réponse :**

- **Gestion du contenu** : à partir du rôle **game_master** et au-dessus (**admin**, **super_admin** inclus).
- **Espace administration** : à partir du rôle **admin** (**super_admin** inclus).
- À prévoir dans l’espace administration : **planning / cron** (ne pas oublier dans le menu ou la page dédiée).
- Un utilisateur peut cumuler les droits (ex. super_admin voit les deux zones).

---

### Q2 — Zone sensible et mot de passe

**Réponse :**

- Le système **débloque** l’accès aux parties sensibles pendant **1 heure** après confirmation du mot de passe.
- **Indicateur UI** : petit **cadenas vert** à côté de l’avatar lorsque le compte est en mode débloqué.
- **Re-blocage automatique** : après **1 h** sans modification sur les **pages sensibles**, le compte repasse verrouillé pour ces zones.
- Exigence non négociable : **fiabilité** et **robustesse** (sécurité de production).

---

### Q3 — Vue d’ensemble (contenu) vs Récapitulatif (admin)

**Réponse :** **Oui**, les deux vues sont distinctes comme prévu dans la spec initiale :

- **Vue d’ensemble** : camemberts par **type d’entité** × statuts (brut, brouillon, actif/jouable, archivé) + **nombre de pages** et de **sections**.
- **Récapitulatif** : historique / graph du **nombre d’utilisateurs** + camembert **utilisateurs par rôle**. (Granularité temporelle et détail RGPD à affiner à l’implémentation si besoin — données agrégées pour les graphiques.)

---

## B — Droits, invités et « Gérer l’affichage »

### Q4 — Invités (sans compte)

**Réponse :** Par défaut, pour les **guests**, n’exposer que les états **stables** (ex. **jouable** / équivalent « publiable ») pour autant qu’**aucune autre configuration** n’impose autre chose dans « Gérer l’affichage ».

---

### Q5 — Intégration des règles de visibilité

**Réponse :**

- Intégrer dans les **Policies Laravel existantes** : **pas de surcouche parallèle**, sauf si une analyse de sécurité montre qu’un complément est nécessaire (à documenter alors).
- Besoin métier exprimé : par **entité**, savoir **quel état** du workflow est **visible par quel rôle**.

---

### Q6 — Lecteurs par défaut des pages / sections (hors réglage fin)

**Réponse :** Sauf configuration spécifique d’une page ou section : **admin**, **super_admin**, **créateur**, **MJ** conservent les accès attendus ; les détails par page/section peuvent restreindre autrement si paramétré.

---

## C — Entités : vues, tableaux, création

### Q7 — Remplacement de `compact` et `large`

**Réponse :**

- Supprimer **compact** et **large** au profit d’une seule vue détail **`full`**.
- Mettre à jour la **documentation** et **toutes les entités** du projet pour réduire la surface de maintenance.
- **Modèle de vues officiel** :

  | Vue | Usage |
  | --- | --- |
  | **minimal** | Listes, cartes compactes |
  | **line** | Dérivée de minimal (ligne enrichie) |
  | **texte** | Intégration dans un paragraphe, avec accès plus poussé via **popover** |
  | **full** | Contenu complet en **page** ou **modal** |
  | **edit** | Édition en **page** ou **modal** |

---

### Q8 — Raccourcis dans les tableaux (dont macOS)

**Réponse :**

- Pour **ouvrir en page pleine** : **Ctrl + clic** et **⌘ + clic** (les deux).
- Si l’utilisateur **n’a pas** le droit d’**édition** et déclenche l’action d’édition (ex. Alt + clic) : **notification uniquement**, **sans** ouvrir de vue.

---

### Q9 — Champs minimaux à la création (modal avant édition complète)

**Réponse initiale :** la question n’était pas claire côté produit.

**Décision retenue pour l’implémentation :**

- Principe inchangé du spec : modal avec **uniquement des propriétés simples**, **obligatoires**, avec droits **read + write** ; typiquement **nom**, **race/type**, **description**, **niveau** — ou **peu plus** selon l’entité.
- Le **détail exact des champs** peut **varier par type d’entité** (monstre, sort, objet…). Il sera aligné sur les **modèles**, **validations** et **seeders** existants pour chaque type (décision de développement par entité, cohérente avec cette règle).

---

## D — Spécialisations & classes (Breed)

### Q10 — Variantes de sorts

**Réponse :**

- Nombre « normal » **1 à 4** sorts par variante ; **pas d’obligation** de contrainte stricte en base.
- L’**UX** doit rester prévue pour **ne pas dépasser 4** sorts dans l’affichage standard.
- Le **même sort** peut apparaître dans **plusieurs** variantes (acceptable même si rare en usage).

---

### Q11 — Champs `evolution`, `specifity`, `life` (classes)

**Réponse :** **Pas de migration manuelle du contenu** — le contenu sera **reconstruit** avec le système de **sections** lors de l’**init** (initiation / pipeline projet).

---

### Q12 — Navigation Bibliothèque

**Réponse :** Aligné sur **l’existant** : une **page Classes** avec **sous-pages par classe** ; idem pour **Spécialisations** (sous-pages par spécialisation).

---

## E — Sections, TipTap, mention `@`

### Q13 — Chargement des sections dans le popover

**Réponse :** Suivre les **bonnes pratiques** perf : parcours **léger et fluide** ; ce flux peut être lourd donc privilégier chargement **paresseux** / **optimiste** selon ce qui est le plus stable dans le stack (décision d’implémentation documentée dans le code).

**Troncature d’aperçu :** environ **dix lignes**, en essayant de **terminer à la fin d’un paragraphe**.

---

### Q14 — Limite des résultats `@`

**Réponse :** **Budget global** (une limite totale pour la liste), pas une quota séparée par type.

---

## F — Sorts

### Q15 — Sous-effets non mappés (exemples d’IDs)

**Réponse :**

- Les IDs cités sont des **exemples**, pas une liste exhaustive.
- **Priorité** : **mapper** quand c’est possible ; sinon texte **compréhensible pour un humain**.
- **Fallback** : peut inclure une **icône dans le texte** rendu pour signaler succès / échec de résolution (**pas** de stockage séparé pour cette icône).

---

### Q16 — Éléments (airs, etc.) sur les sorts

**Réponse :** **Oui** — s’appuyer sur la **documentation de référence** (`docs/400-…`) pour la liste des valeurs légitimes et valider la conversion depuis les sous-effets.

---

## G — Recherche globale

### Q17 — Technique et performance

**Réponse :** Performance acceptable notamment via **nombre de résultats** et pagination. **Technique libre** : bonnes pratiques et outils performants (choix d’implémentation).

---

### Q18 — Périmètre du filtre « état » et du contenu

**Réponse :** Filtre et résultats tenant compte des **entités** **et** du **CMS** (**pages** et **sections**), avec cohérence sur les états selon le modèle choisi pour chaque type de ressource.

---

### Q19 — Contenu des sections dans l’index de recherche

**Réponse :** **Titre** + **extrait** uniquement (pas le corps HTML complet dans les résultats).

---

## H — Légal et changelog (fichiers)

### Q20 — Fichiers markdown légaux

**Réponse :** **Mono-langue** ; **noms de fichiers précis** alignés sur les pages du site (détail des noms dans l’implémentation, cohérent avec les routes CMS).

---

### Q21 — Où rédiger la « doc changelog utilisateur »

**Réponse :** **Pas** dans `/docs/` pour le contenu publié version par version.

- **Emplacement** : `storage/app/public/changelog/` — **un fichier par version** (ex. `1.3.2.md`).
- Structure : **section d’introduction** ; **une section par version** ; navigation possible **d’une version à l’autre** sans fichiers trop lourds.

---

## I — Commandes Artisan

### Q22 — `project:refresh`

**Réponse (produit) :** Enchaînement : **`migrate:fresh` + `DatabaseSeeder`**, puis **pipeline équivalent à `project:init`** (données complètes après réinit).

**Implémentation (2026-05-17)** : `project:refresh` appelle désormais **`project:init --fresh`** après le ménage local. `project:init --fresh` exécute **`migrate:fresh`** puis le pipeline complet (seeders Krosmoz, règles, capacités…). L’étape **`DatabaseSeeder`** Laravel (`migrate:fresh --seed`) n’est **pas** dupliquée : les seeders de `project:init` couvrent et **étendent** le socle. Options transmis depuis `project:refresh` : `--without-seed` → `--skip-seeders`, `--skip-scrapping`, `--noimage`, `--skip-types` ; avec `--force` ou mode non interactif : `--skip-super-admin-prompt`.

---

### Q23 — Commande d’ensemencement type `project:seed`

**Réponse :** Le produit considère que la commande a été **refaite** ; **vérifier le dépôt** avant de modifier à nouveau.

**Note code (audit 2026-05-17)** : aucune signature `project:seed` trouvée dans le codebase. L’équivalent attendu reste typiquement **`project:init`** avec options pour **sauter le scrapping** (`--skip-scrapping`, etc.). À valider lors de l’implémentation.

---

### Q24 — `project:cron`

**Réponse :** Idem — **vérifier** que le comportement est satisfaisant ; **a priori pas de changement**.

**Note code (audit 2026-05-17)** : `project:cron` **n’exécute rien** si aucune option (`--clear`, `--backup`, …) : message d’avertissement et code d’échec — comportement **sûr** pour un cron « vide ».

---

## J — Priorités release

### Q25 — Page d’accueil : référence « cf rule »

**Réponse :** Contenu / ton à caler sur les **règles de jeu** documentées sous [`docs/400- Jeu/420- Règles`](../../400-%20Jeu/420-%20Règles) (notamment ton & intentions dans l’introduction).

---

### Q26 — Panoplies (périmètre 1.3.2)

**Réponse :**

- Entité **simple** : liste d’**équipements** + un ou plusieurs **effets** en bonus (effets / sous-effets de sorts).
- **Vue minimal** : afficher l’**effet par défaut** + liste des équipements en **vue Texte** au **hover**.
- Le minimal est **presque** la version finale pour cette entité.

---

### Q27 — Recherche globale

**Réponse :** **Bloquante** pour la **1.3.2** — doit sortir avec cette version.

---

## Historique des mises à jour de ce fichier

| Date | Changement |
| --- | --- |
| 2026-05-17 | Création : questions Q1–Q27. |
| 2026-05-17 | Validation : réponses produit intégrées + notes d’audit code (`project:refresh`, `project:cron`, absence `project:seed`). |
