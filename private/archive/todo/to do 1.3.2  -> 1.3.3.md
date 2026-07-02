# To do — fin 1.3.2 / 1.3.3 (prod visée en 1.3.4)

Liste de travail simple, sans jalons ni tags.  
Objectif : stabiliser le produit avant la première mise en production (**1.3.4**).

Références utiles : [checklist 1.3.2](./CHECKLIST-release-1.3.2.md) · [recette manuelle](../legacy-docs/10-BestPractices/MANUAL_RECIPE_RELEASE_1.3.2.md)

---

## Entités — édition (page vs modal)

**Besoin** : le formulaire affiché pour modifier une entité doit être **strictement le même**, que l’édition s’ouvre en **page dédiée** ou en **modal**.

**À faire**
- [ ] Vérifier que page `Edit.vue` et modal (`EntityModal` / édition rapide) utilisent bien le même composant racine (`EntityEditForm` ou équivalent), sans duplication de champs ou de logique.
- [ ] Si des divergences existent : fusionner vers **un seul** gabarit d’édition ; la modal et la page ne doivent gérer que le conteneur (layout, fermeture, URL).
- [ ] Tester au moins un type « simple » (ex. condition) et un type « lourd » (ex. sort, monstre, classe).

---

## Entités — suppressions (soft delete, restauration, suppression définitive)

**Contexte actuel (audit code)**  
- Les entités JDR ont une route **`delete`** → suppression logique (corbeille / `deleted_at`). Ex. `entities.spells.delete`.  
- Les **pages**, **sections** et **utilisateurs** ont en plus `restore` et `forceDelete`.  
- Pour les **entités JDR**, les policies prévoient `forceDelete`, et le service de notifications `notifyEntityForceDeleted` existe, **mais il n’y a pas (encore) de routes/contrôleurs `restore` / `forceDelete` côté entités**. Ce n’est donc pas toi qui regardes mal : la feature est **incomplète ou absente** pour les entités de jeu.  
- Plusieurs index entités ont encore des `TODO` « implémenter la suppression avec confirmation ».

**Besoin**
1. **Suppression logique** (`delete`) : retirer l’entité de l’usage normal, conserver la possibilité de restaurer.  
2. **Suppression définitive** (`forceDelete`) : effacer l’entité et ce qui doit disparaître avec elle.  
3. Avant toute suppression : **modal de confirmation** listant tout ce qui sera impacté.  
4. Lors d’une suppression (surtout définitive) : **nettoyer les relations** (pivots, liens vers d’autres entités) et **supprimer les médias** (images, documents Spatie) associés.

**À faire**
- [ ] Cartographier, par type d’entité, ce qui doit être détaché vs supprimé (relations, effets, médias, krefs CMS, etc.).
- [ ] Implémenter ou rétablir le parcours complet pour les entités JDR :
  - [ ] `delete` (soft) avec modal récapitulatif ;
  - [ ] `restore` ;
  - [ ] `forceDelete` (réservé aux rôles autorisés par policy).
- [ ] Centraliser la logique dans un **service de suppression** (éviter 15 implémentations divergentes dans les contrôleurs).
- [ ] Brancher la suppression des **fichiers médias** orphelins ou liés à l’entité supprimée définitivement.
- [ ] Remplacer les `TODO` des index entités par le flux unifié (confirmation + actions bulk si pertinent).
- [ ] Tests Feature : soft delete → restore → force delete + vérification des pivots et médias.

---

## Fichiers sans référence en base

**Besoin** : une commande de maintenance pour supprimer les **fichiers stockés** (images, documents…) qui ne sont **plus référencés** en base.

**À faire**
- [ ] Concevoir une commande du type `project:clear --orphan-files` (ou option dédiée dans `project:clear` / `project:cron`).
- [ ] Mode **`--dry-run`** obligatoire par défaut ou en option : lister ce qui serait supprimé avant action réelle.
- [ ] Périmètre : médias Spatie, fichiers uploadés sections, autres chemins documentés (`storage/app/public/…`).
- [ ] Ne jamais toucher aux fichiers légaux / changelog publics sans règle explicite.
- [ ] Documenter la commande dans la doc opérations.

**État actuel** : `project:clear` gère caches, logs, backups project, etc., **mais pas** le nettoyage des fichiers orphelins liés aux entités.

---

## Export PDF

**Besoin**
- PDF avec un rendu proche d’une **vue minimal étendue** (lisible, couleurs, bordures cohérentes avec le design system).
- Possibilité de **sélectionner plusieurs entités** dans un tableau et de générer **un seul PDF** multi-fiches.

**À faire**
- [ ] Auditer le rendu PDF actuel (`PdfService`, templates) : couleurs, bordures, typographie, icônes.
- [ ] Aligner le layout sur la vue **minimal étendue** (ou documenter l’écart volontaire).
- [ ] Vérifier que **tous les types d’entités** concernés exposent bien l’export (aujourd’hui le multi-ID existe au moins sur les sorts via `?ids=` — généraliser et uniformiser).
- [ ] Ajouter l’action **« PDF groupé »** depuis la sélection multiple des tableaux TanStack.
- [ ] Recette visuelle sur 2–3 types (sort, item, monstre) en mono et multi-sélection.

---

## Planification des tâches (cron)

**Besoin**
- Un **cron Laravel** correctement paramétré en production (`schedule:run`).
- Une page admin (super_admin) pour **activer/désactiver** des tâches prédéfinies et régler leur **expression cron** — **pas de commande libre** (cases à cocher / catalogue fixe pour la sécurité).
- **Notification** à la fin de chaque tâche planifiée (succès ou échec).

**À faire**
- [ ] Vérifier que la table `project_schedule_tasks` et la page `/admin/project-schedule` couvrent bien toutes les tâches attendues (update DofusDB, clear, backup, etc.).
- [ ] Vérifier l’enregistrement réel dans `routes/console.php` / scheduler après migration BDD.
- [ ] S’assurer qu’à chaque exécution une notif **`project_maintenance`** (ou type dédié) part vers les admins concernés.
- [ ] Recette : activer une tâche test, lancer `schedule:run`, contrôler logs + notification.

**État actuel** : UI « Planning (cron Laravel) » et modèle BDD existent ; valider le branchement bout en bout en environnement proche prod.

---

## Sauvegardes automatiques

**Besoin**
- Sauvegardes **régulières** via cron (base + fichiers selon ce que couvre `ProjectBackupService`).
- **Notification** associée (comme pour les autres tâches planifiées).

**À faire**
- [ ] Confirmer que `project:backup` (ou équivalent) fonctionne et produit des archives exploitables.
- [ ] Vérifier la page admin backup (`ProjectBackupWebController`) et son lien avec le planning cron.
- [ ] Paramétrer la fréquence (ex. quotidienne) via une ligne du planning — pas de commande arbitraire.
- [ ] Émettre une notification admin à la fin (succès / échec, taille, chemin ou nom du fichier).
- [ ] Définir une politique de **rétention** (nombre de backups, purge automatique — voir si déjà partiellement géré).

**État actuel** : `ProjectBackupService`, `ProjectBackupCommand`, job et interface web existent ; vérifier cron + notifs.

---

## Notifications — suppressions définitives

**Besoin** : les notifications de **suppression définitive** d’entité ne doivent aller qu’aux **admin** et **super_admin**, **même si** l’entité a été créée par un utilisateur normal. **Pas de notification** de suppression définitive pour le créateur utilisateur.

**À faire**
- [ ] Corriger `NotificationService::notifyEntityForceDeleted` : retirer l’envoi au `created_by` ; conserver admin / super_admin (hors auteur de l’action si pertinent).
- [ ] Revoir `config/notifications.php` : le type `entity_force_deleted` est aujourd’hui en catégorie `personal` — le passer en **`admin`** avec rôles `admin`, `super_admin` uniquement.
- [ ] Distinguer clairement :
  - suppression **logique** → notif créateur + admins (selon réglages actuels) ;
  - suppression **définitive** → admins seulement.
- [ ] Tests sur page/section (déjà branchées) et entités une fois `forceDelete` implémenté.

---

## Journal admin des activités (corbeille centralisée)

**Besoin** : une page admin listant les **activités récentes** (créations, modifications, suppressions…), avec pour les éléments supprimés la possibilité de **restaurer** ou **supprimer définitivement** depuis cette interface.

**À faire**
- [ ] Définir le périmètre v1 : entités JDR + pages + sections ? (utilisateurs déjà gérés à part ?)
- [ ] Décider du stockage : table d’audit dédiée vs agrégation des soft-deleted existants + events.
- [ ] Créer la page admin (tableau filtrable : type, auteur, date, action, statut corbeille).
- [ ] Actions ligne : **Restaurer** / **Supprimer définitivement** (avec la même modal récapitulative que § suppressions).
- [ ] Lier les notifications admin aux entrées du journal quand c’est possible.

**État actuel** : pas de page « activités / corbeille globale » repérée dans les routes admin. À **créer**.

---

## Zone sensible — confirmation mot de passe (régression)

**Symptôme** : en accédant à une zone admin sensible, erreur Inertia :

> All Inertia requests must receive a valid Inertia response, however a plain JSON response was received.  
> `{"message":"Password confirmation required."}`

**Cause** : le middleware `RequirePasswordWithInactivity` renvoyait du JSON (423) dès que le header `X-Inertia` était présent.

**Correctif (2026-06)** : redirection HTTP vers `password.confirm` pour les visites Inertia ; JSON 423 conservé uniquement pour les appels API JSON sans `X-Inertia`.

**À valider manuellement**
- [ ] Accéder à une route protégée (ex. récap admin) sans session confirmée → page de confirmation (pas d’erreur Inertia).
- [ ] Après confirmation → retour à la page demandée (`redirect()->intended`).
- [ ] Cadenas vert + déblocage 1 h toujours OK.

---

## CMS — liens TipTap vers une page

**Symptôme (capture)** : un lien vers une page n’affiche que le titre et « Cliquez pour ouvrir la page » ; pas de sommaire des sections ; le **clic ne redirige pas**.

**Besoin**
- Au survol / preview : afficher le **titre de la page** + la **liste des titres de sections** (sommaire / « menu » de la page).
- Au clic : **navigation fonctionnelle** vers la page (ancre section optionnelle plus tard).

**À faire**
- [ ] Corriger la redirection (href, handler click, kref page dans TipTap).
- [ ] Enrichir l’API de preview page (sections ordonnées : id, titre, niveau).
- [ ] Afficher ce sommaire dans le popover (même style que les autres previews `kref-rich-preview-panel`).
- [ ] Tests : lien inséré manuellement + via menu `@` / bouton insertion.

---

## CMS — lettrine (première lettre stylisée)

**Besoin** : retirer le style où la **première lettre** d’un paragraphe est agrandie, colorée et sur **deux lignes** (effet lettrine).

**À faire**
- [ ] Supprimer ou neutraliser la règle dans `resources/scss/src/_rich-text.scss` (`> p:first-of-type::first-letter`).
- [ ] Vérifier qu’aucun autre fichier ne réintroduit ce style (sections CMS, règles, accueil).

---

## Droits — ne pas exposer l’admin aux autres rôles

**Besoin** : pour les utilisateurs **sans** droits admin, **ne pas afficher** les entrées, liens, textes ou zones réservés à l’administration. Éviter les messages du type « fonctionnalité réservée aux admins » : **masquer complètement** ce qui ne les concerne pas.

**À faire**
- [ ] Passe sur menus (header, aside, dropdown compte), pages entité, tableaux, paramètres.
- [ ] S’appuyer sur `usePermissions` / props Inertia — pas de branche « grisée + message » si l’élément doit être invisible.
- [ ] Vérifier en navigation **invité**, **joueur**, **MJ** : aucune coquille admin visible.
- [ ] Corriger au fil de l’eau les écrans repérés (liste à compléter en recette).

---

## Footer du site

**Besoin**
- Remettre le footer au goût du jour (design system : glass, carré, sombre, cohérent avec le reste).
- Ajouter un lien **GitHub** du projet (URL du dépôt public).

**À faire**
- [ ] Refonte composant footer (Organismes / Layout).
- [ ] Liens : accueil, règles, légal, Discord, **GitHub**, etc. (liste à valider).
- [ ] Responsive mobile + contrastes.
- [ ] Vérifier sur toutes les layouts (`Main`, pages publiques, compte).

---

## Accessibilité et contrastes

**Besoin** : corriger les problèmes d’**accessibilité** et de **contraste**, notamment sur les **bandeaux** (Alert et similaires), même si une partie a déjà été traitée en Phase K.

**À faire**
- [ ] Passe ciblée contrastes (WCAG) sur Alert, badges, tooltips, footer, feedback.
- [ ] Relancer `pnpm run test:a11y` et corriger les régressions.
- [ ] Audit manuel clavier (focus visible, modales, skip link).
- [ ] *(Optionnel)* rapport automatisé ponctuel (axe ou équivalent) sur les pages principales.

---

## Feedback utilisateur — UI et conversations

**Constats**
- Le **bouton FAB** de retour ne respecte pas le design system (rond, bleu trop vif ; le site est plutôt **carré** et sobre).
- Le **modal** de feedback manque de finition visuelle.
- Besoin d’un **fil de conversation** avec l’utilisateur (réponses possibles côté équipe) — **réservé aux utilisateurs connectés**.

**À faire**

### Interface existante
- [ ] Refondre le FAB : forme, couleur, icône alignées DaisyUI / design system (Btn carré, tokens thème).
- [ ] Refondre le modal `FeedbackFab` (typographie, espacements, champs, états erreur).

### Conversations (nouvelle feature)
- [ ] Modèle de données : ticket / conversation / messages (auteur, statut, lu/non lu).
- [ ] Envoi initial depuis le modal feedback → ouvre ou alimente une conversation.
- [ ] Page ou section **« Mes retours »** accessible depuis le **dropdown compte** (liste + détail + réponses).
- [ ] Interface admin pour répondre (MJ / admin — rôle à préciser).
- [ ] Notifications (nouveau message) configurables comme les autres types.
- [ ] Les **invités** conservent l’envoi simple par email sans conversation.

**Question ouverte** : qui peut répondre côté staff (admin seul, MJ, les deux) ?

---

## Caractéristiques — conversion DofusDB → Krosmoz

*(Chantier transverse déjà identifié — à traiter en parallèle des points ci-dessus.)*

**Besoin** : les **données de conversion** (JSON `characteristic-definitions/`, formules, normes) doivent produire des valeurs **cohérentes en jeu**, pas seulement un audit technique à 100 %.

**À faire**
- [ ] Rapport : `php artisan characteristics:audit-definitions --report=storage/app/characteristics-audit.md`
- [ ] Revue métier par familles : créature → objet → sort (échantillons d’entités importées).
- [ ] Mettre à jour les JSON et re-tester le scrapping ciblé.

Réf. : [CALIBRATION_282_INDEX.md](../legacy-docs/50-Fonctionnalités/Characteristics-DB/CALIBRATION_282_INDEX.md)

---

## Recette globale avant prod 1.3.4

- [ ] `php artisan project:review --all`
- [ ] Recette manuelle (base 1.3.2, complétée avec les items ci-dessus)
- [ ] Changelog public `storage/app/public/changelog/1.3.4.md`
- [ ] Environnement prod : cron, queue, backup, mail, OAuth

---

## Questions à trancher (si besoin de précision)

1. **Journal admin** : v1 limitée aux entités + CMS, ou aussi utilisateurs / scrapping ?
2. **Feedback conversations** : quels rôles staff peuvent répondre ?
3. **PDF multi-sélection** : tous les types d’entités dès v1, ou priorité sorts / items / monstres ?
4. **Suppression définitive** : délai de rétention en corbeille avant purge auto (comme RGPD user), ou uniquement action manuelle admin ?
