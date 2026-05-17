# Décisions ouvertes — release 1.3.2

Ce fichier recense les **questions à trancher** avant développement, issues de [`To do 1.3.1 vers 1.3.2.md`](./To%20do%201.3.1%20vers%201.3.2.md) et du [`PLAN-EXECUTION-release-1.3.2.md`](./PLAN-EXECUTION-release-1.3.2.md).  
**À compléter** : remplacer chaque `*(à répondre)*` ci-dessous par la décision définitive (une phrase ou liste courte suffit).

| ID | Statut |
| --- | --- |
| Q1–Q… | *(à répondre)* |

---

## A — Administration, rôles, ré-authentification

### Q1 — Qui voit quoi dans le menu compte ?
- Entrées **« Gestion du contenu »** et **« Espace administration »** : quels **rôles** exactement (slug Laravel / noms métier) pour chaque entrée ? Un même utilisateur peut-il avoir les deux ?
- **Réponse décision :** *(à répondre)*

### Q2 — Ré-authentification (« redemande du mot de passe »)
- Mécanisme souhaité : middleware type `password.confirm` Laravel, modal custom, durée du « confirmez votre mot de passe » (minutes) ?
- S’applique **à chaque sous-page** concernée ou **une fois par session** pour la zone tout entière (contenu vs admin) ?
- **Réponse décision :** *(à répondre)*

### Q3 — « Vue d’ensemble » vs « Récapitulatif » (admin)
- **Vue d’ensemble** (game master / contenu) : confirmé = camemberts **par type d’entité** avec statuts brut / brouillon / jouable / archivé **+** compteurs pages & sections uniquement ?
- **Récapitulatif** (admin) : confirmé = courbe **évolution nombre d’utilisateurs dans le temps** + camembert **répartition par rôle** uniquement ?
- Granularité temporelle du graph utilisateurs : **jour / semaine / mois** depuis quand ?
- RGPD / affichage : uniquement **totaux agrégés** pour les admins (sans liste nominative dans ce graph), ok ?
- **Réponse décision :** *(à répondre)*

---

## B — Droits, invités et page « Gérer l’affichage »

### Q4 — Visibilité par défaut pour les **invités** (sans compte)
- Pour les **entités** : doit-on considérer que seules les entités en état « **jouable** » (ou « actif », selon nomenclature) sont visibles pour les guests, **sauf** règle explicite dans « Gérer l’affichage » ?
- Ou au contraire : **tous les états** visibles tant qu’aucune restriction n’a été configurée ?
- **Réponse décision :** *(à répondre)*

### Q5 — Modèle de configuration dans « Gérer l’affichage »
- Souhaites-tu une **matrice** (type d’entité × état workflow × groupe de rôles / rôle lisible), ou des **réglages plus simples** (ex. trois profils pré-définis : public / MJ / rédacteur) ?
- Les réglages s’appliquent-ils **en plus** des Policies Laravel existantes ou **remplacent-ils partiellement** la logique actuelle ?
- **Réponse décision :** *(à répondre)*

### Q6 — Pages / sections privées (« auteur seulement »)
- Une page ou section réservée à un rôle + **auteur** : les **MJ** peuvent-ils toujours tout voir, ou doit-on avoir un niveau au-dessus (ex. super_admin uniquement pour outrepasser « auteur seulement ») ?
- **Réponse décision :** *(à répondre)*

---

## C — Entités : vues, tableaux, clavier

### Q7 — Abandon du mode « compact »
- Confirmation : après fusion, **`full`** = ancienne vue **large** renommée, utilisée dans **modal + page**.
- **`minimal`** et **`line`** restent bien pour les **listes / tableaux / cartes**, avec **popover** où spécifié ?
- Confirme-t-on qu’**il n’y a plus aucun fallback** ou route nommée « compact » dans l’interface ?
- **Réponse décision :** *(à répondre)*

### Q8 — Raccourcis souris/clavier dans les tableaux
- **`Ctrl + clic`** (= ouverture dans la **page** pleine) : sur macOS utilise-t-on **`Ctrl`** (comme décrit), **`⌘`** (Cmd), ou **les deux** ?
- **`Alt + clic`** (édition en modal) : sur macOS, **`Alt`** = **`Option`** — ok ; conflits éventuels avec agrandissement léger clic — on garde ainsi ?
- Si l’utilisateur n’a pas le droit d’éditer : clic Alt ignore-t-il l’action, affiche-t-il un toast « interdit », ou désactive-t-on le raccourci visuellement ?
- **Réponse décision :** *(à répondre)*

### Q9 — Création d’entité (modal léger puis édition complète)
- Champs minimaux après création sont-ils bien **toujours** : nom + type/race si applicable + description + niveau, ou bien **liste par type d’entité** (monster vs item vs spell …) différente ?
- **Réponse décision :** *(à répondre)*

---

## D — Spécialisations & classes (Breed)

### Q10 — Variantes de sorts (classes)
- **De 2 à 4 sorts par variante** : est-ce une **contrainte stricte** en base (`min`/`max`) ou uniquement UX ?
- Un sort peut-il figurer dans **plus d’une** variante de la même classe ?
- **Réponse décision :** *(à répondre)*

### Q11 — Suppression des champs classe `evolution`, `specifity`, `life`
- Migration du contenu **obligatoire avant suppression** ou **migration progressive** (champs ignorés puis retirés en 1.3.3) ?
- Qui garantit que le HTML legacy a bien été repris dans **sections / liens d’import d’entité** ?
- **Réponse décision :** *(à répondre)*

### Q12 — Contenu Bibliothèque (menu seeder)
- **Une sous-page par spécialisation / par classe** : URL pattern souhaité (slug unique, arborescence `bibliotheque/classes/{slug}`, etc.) conforme aux conventions déjà utilisées dans le projet ?
- **Réponse décision :** *(à répondre)*

---

## E — TipTap / sections / filtres recherche inserts

### Q13 — Prévu « charge la section » dans le popover
- Charger **toujours** le HTML/markdown rendu côté **API**, ou permettre du **lazy** après ouverture du popover uniquement ?
- Longueur d’« aperçu » : nombre de **caractères** ou de **titres jusqu’à** un premier titre `h2` ?
- **Réponse décision :** *(à répondre)*

### Q14 — Limite résultats @-mention
- Nombre **max de résultats** par groupe (caractéristiques / sections / entités), ex. **5 + 5 + 5**, ou budget global ?
- **Réponse décision :** *(à répondre)*

---

## F — Sorts / sous-effets / éléments

### Q15 — Effets ou IDs non mappés (ex. sous-effets `12352`, `18558`, `24680`)
- Priorité : **mapper** ces effets comme les autres avec conversion, **`fallback** texte DofusDB brut ou libellé générique (« effet technique — réf … »), ou **les deux** (mapping quand possible, sinon fallback) ?
- Les IDs listés sont-ils une **liste exhaustive connue pour 1.3.2** ou des **exemples** ?
- **Réponse décision :** *(à répondre)*

### Q16 — Agrégation des « éléments » sur les sorts
- Référence métier où documenter **la liste des valeurs légitimes** (feu air, neutre…) pour valider après conversion depuis sous-effets ?
- **Réponse décision :** *(à répondre)*

---

## G — Recherche globale (nouveauté)

### Q17 — Moteur et périmètre technique
- V1 préférée : **requêtes SQL / Eloquent avec `LIKE`**, **indexes full-text MySQL**, **Laravel Scout** (Meilisearch, Algolia…) ou autre ?
- Contrainte : **temps réel** vs **pagination** forte (performance sur gros corpus) ?
- **Réponse décision :** *(à répondre)*

### Q18 — Filtre « état » dans la recherche
- États inclus : **exactement les mêmes** que workflow entités brut / brouillon / jouable / archivé + pages/sections équivalent ou **liste distincte** pour le CMS ?
- **Réponse décision :** *(à répondre)*

### Q19 — Recherche sur contenu très long des sections (TipTap HTML)
- Faut-il indexer / afficher uniquement les **titres et extraits**, ou recherche dans **full body** même si résultats lourd ?
- **Réponse décision :** *(à répondre)*

---

## H — Légal, markdown, changelog

### Q20 — Convention des fichiers `storage/app/public/legal/*.md`
- Liste **exacte des slugs/pages** qui pointent vers quels fichiers (ex. `cgu.md`, `politique-donnees.md`, `changelog.md`) ?
- Gestion multi-langues : **un fichier par locale** ou un seul FR pour la 1.3.2 ?
- **Réponse décision :** *(à répondre)*

### Q21 — Directive « changelog » dans la doc
- Emplacement cible définitif (`docs/00-Project/CHANGELOG_GUIDE.md` ou équivalent), **approuvé** avant rédaction ?
- **Réponse décision :** *(à répondre)*

---

## I — Commandes Artisan (`project:*`)

### Q22 — Comportement unique de `project:refresh`
- Option **A** : `migrate:fresh` + **`DatabaseSeeder`** uniquement (rapide, sans API DofusDB).  
- Option **B** : **pipeline complet équivalent à `project:init`** après fresh.  
- Option **C** : **les deux**, exposés sous options (`--minimal` vs `--full`) avec une valeur **par défaut** documentée.  
→ Quelle est la **décision** ?
- **Réponse décision :** *(à répondre)*

### Q23 — `project:seed` nouveau
- Inclusion : **exactement quelles classes** Seeders/commandes hors scrapping (`capabilities:import-legacy`, règles TOC, specialization HTML, etc.) ? Aligné sur liste Phase 2 de `project:init` **sans** Phase 5 scrapping uniquement ?
- **Réponse décision :** *(à répondre)*

### `project:clear` (sans numérotation Q supplémentaire si couvert ci-dessous)
- Cf. PLAN § Phase J ; confirme ce qui doit être **exclusivement vidé sans danger** pour un cron régulier (caches Laravel, fichier review, dossiers précis…) — sera complété après **Réponse utilisateur**.

### Q24 — `project:cron`
- Une seule entrée `php artisan project:cron` avec **quelles sous-actions par défaut** (ex. aucune, `--update` uniquement…) pour éviter un cron trop agressif en prod ?
- **Réponse décision :** *(à répondre)*

---

## J — Divers projet / périmètre 1.3.2 vs report

### Q25 — Accueil : « cf rule »
- De quelle **règle Cursor / doc projet** précise s’agit-il pour le ton et le périmètre de la page d’accueil (lien vers fichier) ?
- **Réponse décision :** *(à répondre)*

### Q26 — Panoplies avant la 1.3.2
- **Blocage release** ou **livraison fonctionnel minimale en 1.3.3** avec message « incomplet » côté admin ?
- Si minimal en 1.3.2 : quelles lignes fonctionnelles **obligatoires** ?
- **Réponse décision :** *(à répondre)*

### Q27 — Recherche globale vs autres chantiers pour la première prod
- Classer selon votre date : recherche globale fait-elle partie **obligatoire** de 1.3.2 ou **version suivante** si la fenêtre date est courte ?
- **Réponse décision :** *(à répondre)*

---

## Historique des mises à jour de ce fichier

| Date | Auteur | Changement |
| --- | --- | --- |
| 2026-05-17 | — | Création : première liste de questions depuis plan + spec. |

---

*Après remplissage des réponses, mettre à jour le statut tableau en tête (`Validé`), puis synchroniser §2 ou § référence dans [`PLAN-EXECUTION-release-1.3.2.md`](./PLAN-EXECUTION-release-1.3.2.md).* 
