# Checklist release 1.3.2 — suivi d’avancement

Cocher les cases au fil de l’implémentation (`[ ]` → `[x]`).  
**Références** : [Feuille de route](./To%20do%201.3.1%20vers%201.3.2.md) · [Décisions](./DECISIONS-OUVERTES-release-1.3.2.md) · [Plan d’exécution](./PLAN-EXECUTION-release-1.3.2.md)

| Légende | |
| --- | --- |
| **Bloc.** | Thème du plan |
| Sous-points | Tâches vérifiables en prod ou en recette |

---

## Phase A — Droits & visibilité

- [x] Policies : visibilité **guest** = **états stables** par défaut (config « Gérer l’affichage » peut restreindre) ; appliqué dans **`BaseEntityPolicy`** + équivalents (**`BreedPolicy`**) ; invalidation permissions Inertia après sauvegarde matrice (**`EntityPermissionService::bumpPermissionsCacheRevision()`**)
- [x] Page **Gérer l’affichage** : matrice **type d’entité × état × rôles** (intégrée aux Policies, pas de surcouche inutile) — **`/admin/entity-display-visibility`**
- [x] Pages / sections : rôles par défaut alignés décision **Q6** — lecture **`read_level`** = invité et **`write_level`** = **MJ** lorsque non fournis ; sur-mesure par page ou section conservé  
  **Réf.** : `docs/10-BestPractices/ENTITY_VISIBILITY_PHASE_A.md`

## Phase B — Légal, RGPD, changelog

- [x] CGU / confidentialité / cookies : rendu markdown depuis `storage/app/public/legal/*.md` (**mono-langue**, noms alignés routes Ziggy **`legal.*`**, section CMS `/pages/cookies` pour la synthèse dédiée)
- [x] Export + suppression données personnelles : parcours OK (consulter **`user.privacy.*`**, confirmation mot de passe + throttle ; fumée `LegalAndChangelogMarkdownRoutesTest`)
- [x] Changelog : fichiers `storage/app/public/changelog/{version}.md` + `intro.md` ; agrégation **`changelog.feed`** (intro optionnelle + navigation semver + fichier version)
- [x] Rédaction changelog 1.3.2 (après gel fonctionnel) : parties **Contenu** + **Technique** (court) dans `changelog/1.3.2.md`

---

## Phase C — Vues entité (minimal / line / texte / full / edit)

- [x] Suppression références **compact** et **large** au profit de **full** (code + doc + Ziggy si besoin)
- [x] Aligner **minimal**, **line**, **texte**, **full**, **edit** partout (entités + doc)
- [x] Tableaux : retirer ligne **densité** inutilisée (compact/normal/dense)
- [x] Création : modal champs minimaux → puis **édition complète** (champs par type d’entité à verrouiller en implémentation)
- [x] Raccourcis tableaux : sélection / double / **Ctrl+clic** & **⌘+clic** page / Alt édition / clic droit menu ; sans droit édition → **notification** seule

---

## Phase D — Sections & TipTap

- [x] Pipeline liens **@** : tri caractéristiques → sections → pages → entités ; **limite globale** 12 + scroll
- [x] Popover **section** : aperçu **~10 blocs**, fin de paragraphe (API) ; scroll vertical ; pas de scroll horizontal (sauf tableaux locaux)
- [x] Harmoniser **tooltip vs popover** : classe partagée `kref-rich-preview-panel` (carac., entité, section)

---

## Phase E — Caractéristiques, conversion, scrapping

- [x] Revue **ligne par ligne** + JSON `characteristic-definitions/` — audit CLI `characteristics:audit-definitions` + test unitaire 282 fichiers ; revue métier créature → objet → sort **en continu** (hors gel 1.3.2)
- [x] Scrapping : robustesse + **panoplies** vérifiées — tests `ScrappingRelationsTest` / `ScrappingOrchestratorTest` (import items + recettes)
- [x] Sorts : sous-effets mappés ou **fallback** lisible + icône dans le HTML (`Spell._toEffectSummaryCell` : summary puis `effect`) ; alignement éléments doc **400 Jeu** — **partiel** (chips existants ; revue exhaustive reportée Phase F)
- [x] Édition monstre : **icône aide** → normes / graph (`CharacteristicNormsHelpButton` + `creature.byMonsterField`)  
  **Réf.** : `docs/10-BestPractices/ENTITY_CHARACTERISTICS_PHASE_E.md`

---

## Phase F — Bugs entités (priorité)

- [x] **Capacités** : Ziggy `capability` ; épinglage extended (inchangé, validé) ; ouverture page/modal
- [x] **Sorts** : état depuis vue **minimal** (pastille + PATCH état) ; sous-effets / éléments (fallback Phase E)
- [x] **Items / ressources / consommables** : popover sorts sur objet (kref HTML fiche item) ; état/full/edit inchangés (déjà alignés index)
- [x] **Monstres** : langues badges ; édition `EditActionDock` sticky ; box flex (styles line existants)
- [x] **États / Traits** : routes show Ziggy (`condition`, `creatureTrait`) + modals index
- [x] **Panoplies** : minimal effet défaut + équipements vue Texte au hover ; bonus chips ; items API table  
  **Réf.** : `docs/10-BestPractices/ENTITY_BUGS_PHASE_F.md`

---

## Phase G — Spécialisations & classes

- [x] Menus biblio : sous-pages par **spécialisation** et par **classe** (`BibliothequeEntityPageService`, `LinkedEntityShow`)
- [x] Variantes sorts (1–4 en UX + validation API) ; sorts dans plusieurs emplacements possibles
- [x] Retrait champs classe **evolution / specificity / life_dice** des formulaires (sections CMS)
- [x] Contenu legacy : **imports d’entité** (`SpecializationSeeder` + kref)  
  **Réf.** : `docs/10-BestPractices/ENTITY_SPECIALIZATIONS_PHASE_G.md`

---

## Phase H — Admin & compte

- [x] Menus : **Gestion du contenu** (game_master+) ; **Espace administration** (admin+) ; **planning / cron** dans l’admin (nav super_admin)
- [x] **Zone sensible** : mot de passe → déblocage **1 h**, **cadenas vert**, re-verrouillage sans action (`password.confirm` + session)
- [x] **Vue d’ensemble** : camemberts entités × statuts + pages + sections (`/admin/content`)
- [x] **Récap admin** : courbe utilisateurs + camembert par rôle (`/admin/recap`)
- [x] **Mon compte** : raccourcis entités ; liens légaux ; paramètres notifications ; accès personnalisés (créations)  
  **Partiel** : case email récap feedback (transverse checklist)
- [x] **Accueil** : projet + jeu, ton règles (seeder accueil)  
  **Réf.** : `docs/10-BestPractices/ADMIN_ACCOUNT_PHASE_H.md`

---

## Phase I — Recherche globale (**bloquante 1.3.2**)

- [x] UI header : focus, overlay, filtres (entités, pages, sections, états)
- [x] Résultats : **titre + extrait** ; droits lecture respectés
- [x] Perf / pagination selon volume  
  **Réf.** : `docs/10-BestPractices/ENTITY_GLOBAL_SEARCH_PHASE_I.md`

---

## Phase J — Outils & commandes

- [x] **`project:refresh`** : enchaîne **`project:init --fresh`** (pipeline complet) après ménage local — **aligné décision Q22**
- [x] `project:init` / `project:cron` / ensemencement : **`project:seed`** (sans DofusDB) ; **`project:refresh --fast`** ; **`project:cron --update`**
- [x] Refactor doublons commandes : alias documentés (`server:*`, `project:update`, `project:data:import-rules-toc`)
- [x] Réorganisation : commandes `scrapping:effects:*` → `Commands/Scrapping/Effects/`  
  **Réf.** : `docs/10-BestPractices/PROJECT_COMMANDS_PHASE_J.md`

---

## Phase K — Polish & doc

- [x] Responsive — layouts principaux (`Main`, `Header`)
- [x] Accessibilité — lien d'évitement, `#main-content`, `color-scheme`, toasts `aria-live`
- [x] Bandeaux **Alert** (glass + contraste)
- [x] Notifications — durée 14 s + pause survol/focus
- [x] Nettoyage `/docs/` — fiche Phase K ; passe globale doc reportée post-1.3.2  
  **Réf.** : `docs/10-BestPractices/PROJECT_UX_PHASE_K.md`

---

## Transverse (hors phase stricte)

- [x] Feedback connectés : case **email récap** (défaut false) — `FeedbackRecapMail`, tests `FeedbackControllerTest`
- [x] **`project:review`** : outillage et contrôles release documentés ; lancer `project:review --all` avant tag prod ([RELEASE_1.3.2_VERIFICATION.md](../10-BestPractices/RELEASE_1.3.2_VERIFICATION.md))

---

## Historique

| Date | Note |
| --- | --- |
| 2026-05-17 | Création checklist ; item `project:refresh` aligné sur décision Q22. |
