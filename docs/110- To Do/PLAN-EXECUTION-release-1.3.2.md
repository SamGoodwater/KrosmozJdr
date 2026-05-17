# Plan d'exécution — release 1.3.2 (prod)

Document de synthèse pour ordonner le travail avant la **première mise en production**.  
**Référence détaillée** : [`To do 1.3.1 vers 1.3.2.md`](./To%20do%201.3.1%20vers%201.3.2.md) (liste complète, grilles caractéristiques, pipelines commandes).

**Décisions métier avant code** — Toute réponse officielle doit figurer dans [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md) (**27 questions**, statut tableau en en-tête de ce fichier). Le §2 ci-dessous reste une **traduction plausible** jusqu'à validation.

**Contexte** : pas d'obligation de rétrocompatibilité ni d'aliases pour code obsolète — on peut simplifier.

---

## 1. Lecture du document source — ce qu'il contient

| Bloc dans le fichier source | Rôle |
| --- | --- |
| Interfaces → Entités → Caractéristiques | UX, CMS, données, calibrage DofusDB → Krosmoz |
| Fonctionnalités | Améliorations transverses + moteur de recherche global |
| Optimisation | Structure dépôt, documentation, refactor commandes Artisan |
| UI / UX (fin du fichier) | Sections texte, responsive, accessibilité, alertes, notifications |

Les **énormes tableaux** du chapitre *Caractéristiques* (sorts / objets / monstres…) restent dans le fichier source ; ce plan indique **quand** et **comment** les utiliser (revue ligne par ligne + mise à jour des JSON sous `database/seeders/data/characteristic-definitions/`).

---

## 2. Points réécrits pour lever les ambiguïtés

Interprétation explicite de passages flous ou typo dans la spec d'origine :

1. **Admin — mot de passe** : *« protégé via la redemande du mot de passe »* → re-authentification (ex. middleware / modal « confirmez votre mot de passe ») pour **Gestion du contenu** (sous-parties listées) et pour **tout l'espace Administration** y compris le futur Récapitulatif.
2. **Deux menus compte** : entrées dropdown **« Gestion du contenu »** et **« Espace administration »** réservées aux rôles concernés (ex. game master vs admin).
3. **Récap admin vs données contenu** : le fichier demande aussi un **Récap utilisateurs** (graph évolution + camembert par rôle), distinct des camemberts « entités par statut » de la vue d'ensemble contenu.
4. **Liens légaux** : éviter URLs `127.0.0.1` en prod → liens **Ziggy** ou routes nommées vers pages CGU / politique cookies & données.
5. **Vue entité** : *« supprimer compact, garder large, renommer en full »* → une seule présentation détaillée **`full`** utilisée **page ET modal** (seules les actions diffèrent).
6. **`entities.capabilities.show` / paramètre `capability`** : bug Ziggy décrit dans le fichier source — à traiter comme **priorité corrective** navigation capacités (routes + appels depuis tableaux/modals).
7. **`:knln` (ligne parasite)** dans le fichier source : ignoré comme bruit ; aucune exigence fonctionnelle dérivée.
8. **`Project:refresh` / `Projeck`** : typo dans la spec → viser **`project:refresh`** comme commande Laravel documentée dans le projet.
9. **État des sorts depuis vue minimal** : pas de notif ni requête — traiter comme **régression commune** potentielle aux autres entités (même mécanisme d'état).

Si une interprétation ci-dessus diffère du produit attendu : **priorité à la ligne du fichier Décisions** une fois cette ligne complétée (réponse signée dans [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md)), puis mise à jour de ce §2 pour refléter la décision.

---

## 3. Ordre d'exécution recommandé (phases)

Les phases suivent une logique **dépendances d'abord** : permissions et modèle d'affichage avant finesse UI ; pipeline données avant équilibrage combat ; stabilité navigation avant recherche globale.

### Phase A — Droits & visibilité (bloquant fonctionnel)

- Contenu majoritairement **lisible par les invités** ; restrictions par **rôle** et **auteurs** où pertinent.
- **Paramètres** page *Gérer l'affichage* : qui peut voir les entités selon **statut workflow** (brut, brouillon, jouable, archivé) et restriction **par type d'entité** selon les rôles.
- Définir / implémenter la liste des règles métier puis brancher queries + caches si besoin.

*Pourquoi en premier : impacte recherche globale, listes publiques, admin.*

---

### Phase B — Légal, cookies, RGPD, changelog

- Pages CGU / confidentialité / cookies + **changelog** : rendu **markdown** branché sur `storage/app/public/legal/*.md`.
- Flux **export** et **suppression** des données personnelles : tester de bout en bout.
- Doc interne **comment rédiger le changelog** : sections *Contenu* (prioritaire) vs *Technique* (court) — peut vivre dans `docs/` hors ce dossier TODO quand elle sera stabilisée.
- **Changelog rédigé** après stabilisation fonctionnelle (phases suivantes).

---

### Phase C — Architecture entités commune (patterns UI)

- **Une seule vue `full`** (ex-page « large ») pour affichage + **même gabarit d'édition** page / modal.
- **Création** : modal léger (champs obligatoires simples) → après création, ouverture **édition complète**.
- Supprimer densité tableau **compact / normal / dense** inutilisée (alléger surtout les tableaux).
- **Raccourcis tableau** : simple = sélection, double = modal affichage, Ctrl+clic = page, Alt+clic = édition modal, clic droit = menu actions — auditer et corriger partout.

---

### Phase D — Sections & TipTap (contenu riche)

- Pipeline **mentions** `@` + insertion : résultats **triés** (caractéristiques, sections, entités), limite + scroll sous la ligne `@`.
- **Chargement au popover** pour **sections** longues : card avec **aperçu + scroll vertical**, pas de scrollbar horizontale sauf besoin explicite (PDF, etc.).
- **Vue Texte entités / sections / caractéristiques** : harmoniser comportement preview (minimal entité dans popover quand pertinent).
- Unifier styles **tooltip vs popover** (ex. propriétés *nature physique / wakfu*).

---

### Phase E — Données : caractéristiques, conversion, scrapping

- Suivre le **cadre** du fichier source (Famille A/B, dés `ndX`, colonne Statut, doc 400/410…).
- **Travail série** : revue **ligne par ligne** ↔ JSON sous `characteristic-definitions/` ; CSV / JSON annexes dans `docs/110- To Do/` en support.
- Renforcer robustesse **scrapping** (+ limiter les requêtes) ; vérifier **panoplies** et effets sous-convertis (**IDs** `12352`, `18558`, `24680` mentionnés pour sorts ; logique **éléments** des sous-effets).
- Feedback édition caractéristiques monstres : **icône aide** → popover normes / graphique.

---

### Phase F — Corrections urgentes par entité (bugs / Ziggy)

| Priorité | Sujet |
| --- | --- |
| Haute | Capacités : route `capability` manquante, pin extended, ouverture page/modal |
| Haute | Sorts : état depuis vue minimal ; sous-effets / éléments |
| Haute | Items / ressources / consommables : actions état ; ouverture full / edit ; popover sorts sur objets |
| Moyenne | Monstres : langues badges ; box stats flexible ; formulaire édition aligné affichage ; **bouton sauvegarder sticky** avec hover reset/annuler |
| Moyenne | Bibliothèque **États / Traits** pages cassées |
| À plancher | Panoplies : scraping + affichages (bonus via render caractéristiques, équipements, etc.) |

Parallèle possible : sous-équipes **Spells** vs **Items** après Phase C/D communes.

---

### Phase G — Spécialisations & Classes (Breed)

- Navigation type **Bibliothèque > sous-pages** par entité (seed menus).
- **Spécialisation** : mise en page type **page**, sections ordonnées, tableau par niveau (sorts, capacités, ressources, équipements, panoplies) + ligne « sans niveau ».
- **Classe** : variante sorts (2–4 sorts par variante, niveaux, sorts hors variante), liens niveau / quantités objets ; retirer champs rendus obsolètes par les sections (**evolution, specifity, life** après migration contenu).

---

### Phase H — Admin & compte utilisateur

- **Admin** : scinder liens Contenu vs Administration ; **Vue d'ensemble** avec camemberts par entité/statut + nombre pages/sections.
- Nouvelle vue **Récapitulatif** (utilisateurs dans le temps + rôles).
- **Accueil** public : rédaction page projet + jeu.
- **Mon compte** : raccourcis entités (icônes + couleurs), règles, mises en avant **créations utilisateur**.
- Pages **paramètres notifications** ; **édition profil** alignée design system.
- Feedback : case à cocher **email récapitulatif** pour utilisateurs connectés (défaut `false`).

---

### Phase I — Recherche globale (nouveauté)

- Utiliser input header existant : **focus → agrandissement + overlay** (flou assombrissement type modal léger).
- **Filtres** style DaisyUI [Filter](https://daisyui.com/components/filter/) : types (entités avec couleur si actif sinon N&B), **pages**, **sections**, **filtre par état** (un, tous, mixte).
- Résultats groupés avec badges couleur ; **respect droits lecture** utilisateur ; vues Texte réutilisables.

---

### Phase J — Outils & qualité continu

- **`project:seed`** : nouveau — tous seeds « classiques », **sans** scrapping complet.
- **`project:review`** : complétude checklist (tests, phpstan, pint, eslint, audit, docs index…).
- **`project:init`**, **`project:update`**, **`project:clear`**, **`project:refresh`** : supprimer doublons entre commandes ; clarifier **`project:clear`** (non destructive, cron) ; clarifier **`project:refresh`** = « tout remettre à zéro » puis réinit (**à arbitrer avec le comportement actuel** : le fichier source note `migrate:fresh` + `DatabaseSeeder` au lieu du pipeline complet `project:init` — décision produit nécessaire).
- **`project:cron`** : passerelle options `--clear`, `--update`, etc.
- Réorganisation **répertoires** : dossiers où il y a trop de fichiers plats dans le codebase.

---

### Phase K — Polish transverse UI/UX & doc

- Passe **responsive** globale.
- **Accessibilité** : plugin / audit auto + corrections (structure, contrastes, métadonnées).
- **Bandeaux Alert** : contraste renforcé, style glass léger (peu arrondi, flou).
- **Toasts / notifications** : durée plus longue ; **pause du timer au focus/hover**.
- **Nettoyage documentation** : état présent uniquement ; retirer l'historique de refonte ; reclassement.

---

## 4. Parallélisation raisonnable

| Piste parallèle A | Piste parallèle B |
| --- | --- |
| Phase E (données) | Phase C–D (UI commune + sections) après Phase A posée |
| Phase F monstres/items | Phase F sorts/capacités si équipes différentes |
| Phase K (responsive/a11y) | petites tâches entre phases développement |

---

## 5. Définition de « prêt prod » pour la 1.3.2

Minimum suggéré avant ouverture publique :

- [ ] Phase A + B : droits stable, légal/RGPD vérifié manuellement.
- [ ] Phase C + D sans régressions critiques sur publications.
- [ ] Bugs **navigation / états** Phase F critiques (capabilities, sorts minimal, Ziggy) corrigés.
- [ ] Phase E : au minimum **parcours jeu** représentatif validé sur un échantillon d'entités (pas forcément 100 % des lignes tableau ; le restant suit la politique du fichier source).
- [ ] Une passe **tests auto** verte + **review** documentée (`project:review` ou équivalent).
- [ ] Changelog première version utilisateur présent.

Les enrichissements (recherche globale très poussée, doc réécrite intégrale, refactor dépôt profond) peuvent être **découpés** en 1.3.3 si la date impose.

---

## 6. Suivi du plan

Pour chaque phase : créer tickets (ou lignes checklist) reliées aux **titres §** du fichier [`To do 1.3.1 vers 1.3.2.md`](./To%20do%201.3.1%20vers%201.3.2.md).

**Révision après arbitrages** documentés dans [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md) (questions **Q22–Q27** couvrent entre autres `project:refresh`, panoplies, recherche globale, périmètre 1.3.2).

---

## Liens dossier `110- To Do`

| Document | Rôle |
| --- | --- |
| [`To do 1.3.1 vers 1.3.2.md`](./To%20do%201.3.1%20vers%201.3.2.md) | Spec détaillée + tableaux caractéristiques |
| **Ce fichier** | Ordre d'exécution des phases |
| [`DECISIONS-OUVERTES-release-1.3.2.md`](./DECISIONS-OUVERTES-release-1.3.2.md) | Questions ouvertes puis réponses tranchées |
