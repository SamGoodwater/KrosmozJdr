# Ce qui a été fait

## Août 2026 — Bibliothèques CMS : plus de sorts brouillon

Les pages Classes / Spécialisations du menu Bibliothèques affichaient encore les sorts (et autres liaisons) en brouillon aux visiteurs. Elles suivent maintenant la même règle que la fiche entité : seul ce qui est visible pour le visiteur est chargé.

## Août 2026 — Filtres de tableau : largeur selon le contenu

Les filtres des catalogues ne s’alignent plus sur trois colonnes identiques. Rareté, état et autres listes courtes s’affichent en pastilles ; type, race et listes longues restent un menu avec recherche. Les interrupteurs et champs texte prennent seulement la place dont ils ont besoin.

## Août 2026 — Tableaux : plus de quick edit, sélection et raccourcis

L’édition groupée dans les catalogues (panneau latéral, toggle Quick edit) a été retirée : on modifie une fiche via la page Modifier. On peut à nouveau cocher des lignes dans les trois vues (minimal, ligne, colonnes) pour l’export CSV ou le PDF. Les cases restent visibles. Les raccourcis du tableau ne volent plus la saisie (recherche, filtres) ni Entrée/Espace sur un bouton ; Ctrl+N (nouvelle fenêtre du navigateur) n’est plus intercepté.

## Août 2026 — API DofusDB, entrée dev et hub commandes

L’atelier parle à `/api/dofusdb` (l’ancienne adresse `/api/scrapping` redirige). `composer run dev` lance la même chose que `project:dev` avec la file. Les fiches isolées de type de ressource ouvrent la page Types commune. Sur le récapitulatif, le super-admin voit la liste des commandes avec un lien vers chaque page déjà en place.

## Août 2026 — URLs contenu sous `/admin/content`

Caractéristiques, effets, langues et mappings DofusDB sont dans l’espace contenu, avec la même racine d’URL que l’atelier. Les anciens liens `/admin/characteristics` etc. redirigent. Les boutons « types » des catalogues ouvrent directement la page Types.

## Août 2026 — Types : en jeu et scrap

Sur la page Types, chaque registre (équipements, ressources, consommables, races, sorts) a les mêmes deux cases : visible en jeu (les tableaux catalogues le pré-cochent) et scrapable (atelier, CLI et maj d’une fiche DofusDB ne touchent que ces types, sauf demande explicite « tout récupérer »). On peut toujours déplacer un type d’équipement, ressource ou consommable vers une autre de ces trois familles.

## Août 2026 — Menus, jobs et types

On passe d’un espace à l’autre par le menu du header, plus par un raccourci dans les barres. Les retours utilisateurs gardent le menu d’administration. Un job (mise à jour stack, sync…) s’annule depuis le toast ou le panneau ; s’il reste coincé à 1 %, c’est souvent que le worker de file n’est pas lancé. Les cinq registres de types (équipements, ressources, consommables, races, sorts) partagent une même page, avec un menu d’actions par ligne.

## Août 2026 — Atelier DofusDB dans le contenu, maj unitaire sur la fiche

L’import DofusDB n’est plus un coin « scrapping » à part. Les **admins** gèrent tout ça dans **Gestion du contenu** : un atelier unique pour chercher, importer, mettre à jour en masse ou ne récupérer que les images, plus les types, les mappings et les référentiels (caractéristiques, effets, langues). Les **MJ** restent sur les fiches : créer, modifier, et lancer une mise à jour DofusDB **pour une fiche à la fois** (contenu ou images). Le **super-admin** s’occupe du serveur (cron, sauvegardes, caches) ; le sync de masse vit dans l’atelier, plus dans l’ops. Les types du catalogue (équipements, ressources, consommables) se cochent désormais dans les registres, plus via une liste figée.

## Août 2026 — Lien admin ↔ contenu et suivi live des jobs

Depuis l’espace administration, un lien mène à la gestion du contenu (et l’inverse pour les admins). Les commandes lancées depuis les pages thématiques (review, nettoyage, stack, sauvegarde, sync) affichent un pourcentage, un toast animé et la sortie console filtrée. Tant qu’un job du même type tourne, on ne peut pas en relancer un second.

## Août 2026 — Pages admin thématiques et planning cron

Les commandes utiles restent chacune sur leur page (sync, backup, review, stack, orphelins, **nettoyage caches**). Le planning cron affiche la commande Artisan réellement lancée et un lien vers la page correspondante.

## Août 2026 — Confirmations CLI `-y` / `--no`

`project:prepare`, `project:dev`, `project:deps` et les autres commandes qui demandent oui/non (IDE Helper, apt, refresh, permissions) acceptent `-y` pour valider et `--no` pour refuser. `-n` reste le mode non interactif de Symfony.

## Août 2026 — Commandes projet simplifiées

Une commande par rôle : `project:dev`, `project:deps`, `project:review`, `project:data sync`, `project:clear`, `project:backup`. Plus d’alias ni de wrappers. La liste tient dans `app/Console/COMMANDS.md`. Le cron admin appelle les mêmes noms.

## Août 2026 — Une seule `main`, branches courtes

Le dépôt n’a plus que `main`. Les fuites de brouillons (panoplies, pièces de set, sorts via monstre / classe / spécialisation), les tooltips catalogue et le travail local (états Auto, table de bonus, conditions canoniques) sont dans `main`. On crée une branche par sujet, on merge, on la supprime. Pas de branches longues `ui` ou `security`.

## Août 2026 — Sorts branchés sur les états de base

Les sorts n’affichent plus chaque état Dofus comme une fiche à part. Quand l’effet correspond à Pesanteur, Empoisonné, Étourdi, Ralenti ou Affaibli, le sort pointe vers cette fiche JDR. Les milliers d’états scrapés restent en Brut, pour le travail d’import.

## Août 2026 — Pages Pour les MJ et atelier Création

Les MJ ont un cinquième bloc de menu, **Pour les MJ**, avec l’atelier **Création**. Dedans : le tableau vivant des bonus d’équipement (par emplacement et par niveau) et les chartes créatures / objets / sorts. La page Contribution, toujours visible de tous, ne sert plus qu’à **Nous rejoindre** (Discord, GitHub, droits).

## Août 2026 — Catalogue des états : Brut masqué, effets visibles

Le catalogue d’états n’affiche plus par défaut les milliers d’états Dofus (ils restent en **Brut**, on peut les recocher dans le filtre). Les quelques états JDR (Pesanteur, Empoisonné, etc.) restent jouables. Sur une fiche, on voit les effets mécaniques (ne pas être déplacé, invulnérable…).

## Août 2026 — État Auto des fiches

Les fiches (sorts, objets, pages, etc.) ont un cinquième état : **Auto**. C’est une proposition à relire (IA ou script), pas encore jouable. Les joueurs ne la voient pas ; les MJ et éditeurs oui, comme un brouillon. Dans les listes, l’ordre est Brut → Brouillon → Auto → Jouable → Archivé.

## Août 2026 — Catalogue ressources : types par défaut

Le filtre Type du catalogue de ressources arrive précoché sur les types métier (bois, minerai, plante, cuir, runes, etc.). Quêtes, souvenirs, zones et essences de donjon restent disponibles dans le filtre, mais seulement si on les coche.

## Août 2026 — Infobulles sur type, rareté et caractéristiques

Survoler le type, la rareté ou le niveau d’une ressource, d’un équipement ou d’un consommable affiche une courte explication (rareté de Commun à Unique, catégorie métier, etc.). Les stats du tableau des caractéristiques ajoutent aussi leurs limites min/max quand elles sont des nombres fixes.

## Août 2026 — Catalogue consommables : types par défaut

Le filtre Type du catalogue de consommables arrive précoché sur potions, nourritures, boissons, parchemins, pierres d’âme, objets utilisables, etc. Certificats, coffres et fées d’artifice restent disponibles dans le filtre, mais seulement si on les coche.

## Août 2026 — Cartes minimales, tooltips et bibliothèques

Les tooltips et popovers d’entités n’affichent plus une seconde boîte autour du contenu (sorts inclus). Les bibliothèques s’ouvrent en vue minimale plutôt qu’en lignes. Sur une carte déployée, la description des consommables, ressources et équipements se lit en entier.

## Août 2026 — Sets brouillon masqués sur un équipement public

Sur une fiche ou un catalogue d’objet visible par tout le monde, on ne voit plus une panoplie encore en brouillon, ni les autres pièces cachées de ce set. Un MJ continue de les voir pour travailler.

## Août 2026 — Panoplie publique : pas les pièces brouillon

Sur une panoplie jouable, le catalogue et la fiche de lecture ne listent que les équipements que le visiteur a le droit de voir. Un objet encore en brouillon, même accroché au set, n’apparaît plus pour un joueur. La page Modifier continue d’afficher toutes les pièces liées, pour ne pas les retirer par erreur.

## Août 2026 — Sorts brouillon masqués sur un monstre jouable

Sur une fiche monstre (catalogue ou page), un joueur ne voit plus les sorts encore en brouillon. Seuls les sorts qu’il a le droit de consulter apparaissent, comme pour les équipements.

## Août 2026 — Sorts brouillon masqués sur les classes

Un sort (ou une capacité) encore en brouillon n’apparaît plus sur la fiche, le catalogue ou le PDF d’une classe jouable. Il reste visible pour l’auteur, les MJ et les admins, et sur la page Modifier.

## Août 2026 — Sorts brouillon masqués sur les spécialisations

Un sort (ou une capacité) encore en brouillon n’apparaît plus sur la fiche, le catalogue ou le PDF d’une spécialisation jouable. Il reste visible pour l’auteur, les MJ et les admins, et sur la page Modifier.

## Août 2026 — Équipements du set dans le tooltip panoplie

Le survol de l’icône de panoplie sur une fiche équipement liste les pièces en vue texte : un clic sur un nom ouvre la fiche de l’objet, comme sur la page de la panoplie.

## Août 2026 — Tooltip sur une carte minimale

Survoler le tooltip d’une fiche minimale (grille) ne replie plus la carte : elle reste ouverte le temps d’atteindre et de lire l’infobulle.

## Août 2026 — Tooltip : on peut le survoler

Les infobulles ne se ferment plus dès que la souris quitte le déclencheur : tant que le pointeur est sur le tooltip, il reste affiché, partout sur le site.

## Août 2026 — Panoplie visible sur l’équipement

Si un objet fait partie d’une panoplie, les fiches équipement montrent l’icône du set. En carte repliée, l’icône seule ouvre un aperçu (nom, autres pièces, bonus). En carte déployée et en liste, le nom apparaît à côté. La fiche complète liste les pièces et les bonus par palier (boutons chiffrés, comme les sorts).

## Août 2026 — Modifier une panoplie : ordre et style

Sur la page Modifier, on commence par les équipements et les bonus de set. Nom, description et droits (lecture / écriture) sont en bas. Les bonus utilisent les mêmes champs verre que le reste du site, plus de panneau bleu plein.

## Août 2026 — Éditer ouvre la page Modifier

Le crayon du menu d’options (carte, ligne, tableau) ouvre la même page Modifier que le bouton Éditer de la fiche. L’ancienne fenêtre « Édition rapide » d’une seule entité a été retirée : elle n’avait plus les mêmes champs que la page. On peut toujours modifier plusieurs lignes d’un coup via le panneau du tableau.

## Août 2026 — Modifier une panoplie : pièces et bonus

On peut chercher un équipement à ajouter (catalogue, pas une liste figée), le retirer de la panoplie, et saisir les bonus de set comme les effets d’un objet : une caractéristique et une valeur, par nombre de pièces.

## Août 2026 — Afficher ouvre la fiche en modal

Sur toutes les entités, Afficher (carte minimale, ligne, liste) ouvre le détail en fenêtre, pas la page. On passe à la page complète depuis cette fenêtre (Agrandir) ou avec Ctrl+clic.

## Août 2026 — Afficher une panoplie ouvre la fiche

Le bouton Afficher d’une panoplie dans la liste ouvre la modal de détail, au lieu de recharger le tableau. Agrandir (ou Ctrl+clic) mène à une vraie page de lecture.

## Août 2026 — Fiches overlay : une seule carte

Ouvrir une entité depuis un nom (équipement d’une panoplie, sort d’un monstre, etc.) n’affiche plus une grande boîte vide autour de la fiche. La carte s’ajuste à son contenu. Le menu « ⋮ » n’a plus de fond gris derrière les icônes.

## Août 2026 — Vues panoplie : pièces et vignette

Les fiches panoplie (ligne, carte, détail) listent les équipements en vue texte (icône + nom, aperçu au clic). La vignette reprend les images des pièces s’il y en a, sinon le nom en initiales. Les bonus de set ne s’affichent plus en `[object Object]`.

## Août 2026 — Saisie dans les filtres des tableaux

On peut à nouveau taper dans les champs des filtres (recherche d’un type, filtres texte, etc.) : la valeur n’est plus effacée à chaque rafraîchissement de la liste.

## Août 2026 — Catalogue objets : types de jeu par défaut

Le filtre Type du catalogue d’équipements arrive précoché sur les emplacements utiles en jeu (amulette, armes, cape, dofus, familiers, trophées, etc.). Apparats, costumes et autres cosmétiques restent disponibles dans le filtre, mais seulement si on les coche.

## Août 2026 — Bonus et rareté des équipements

Les fiches équipement (Minimal, Line, Full, tableau) lisent les **bonus**
(`items.bonus`, repli sur `effect`). Les libellés de rareté sont les mêmes
partout : Commun, Peu commun, Rare, Très rare, Légendaire, Unique (0 à 5).

## Août 2026 — CVE-2026-13149 brace-expansion

`brace-expansion` (via `minimatch` / `glob` / ESLint) est forcé en 1.1.18, 2.1.4 et 5.0.9 dans `pnpm.overrides`, au-dessus des correctifs 1.1.16 / 2.1.2 / 5.0.7. Ça évite le DoS O(2ⁿ) sur des groupes `{}` non expansifs.

## Août 2026 — CI MySQL : TEXT sans DEFAULT

MySQL 8 refuse un DEFAULT SQL sur TEXT/BLOB/JSON (`SQLSTATE 1101`). Après le JSON `notification_channels`, les résistances fixes créature (`res_fixe_*`) bloquaient encore `php artisan migrate` en CI. Le défaut `'0'` est désormais dans `Creature::$attributes`.

## Août 2026 — GitHub : une seule `main`, Dependabot cadré

Le dépôt n’a plus qu’une branche active (`main`). Les PR Cursor et l’ancienne PR Vitest 4 (base trop vieille) sont fermées. Les alertes Dependabot déjà corrigées restent en « fixed ». Un fichier `.github/dependabot.yml` lance des mises à jour weekly npm (groupe minor+patch), Composer (PR individuelles) et Actions (minor+patch) ; Vitest 4 et les majors d’actions restent un chantier dédié.

## Août 2026 — Équipements monstre et menu d’options

Les fiches monstre (Minimal, Line, Full) listent les équipements de la créature
s’il y en a, sur le même modèle que les sorts (nom + aperçu Minimal). Le menu
d’options à côté du titre n’affiche que les raccourcis qui tiennent ; le reste
passe dans le « ⋮ », pour toutes les vues d’entités.

## Août 2026 — Aperçu sort depuis un monstre

Ouvrir un sort depuis la fiche minimale d’un monstre montre enfin les effets (chips inclus dans le payload, sans dépendre d’un second fetch). Le menu d’options suit la ligne du titre (raccourcis + overflow).

## Août 2026 — Tableaux d’entités + aperçu des sorts

Les filtres, le tri et la recherche des catalogues marchent à nouveau : multi-sélection (`whereIn`), application automatique, alias de tri (type d’objet, niveau de créature), recherche serveur et repli sur le nom en client.

## Août 2026 — Tableau objets plus lisible

Le catalogue d’équipements (vue Colonnes) montre d’abord portrait, nom, niveau, type, rareté et bonus. Description, résumé et prix restent optionnels. L’état de publication n’apparaît que pour les éditeurs. Un survol des en-têtes explique chaque colonne. En vue Line, la recette n’encombre plus la liste.

## Août 2026 — CI MySQL, resolved-stats, tri catalogue monstres

Les migrations CI passent sur MySQL : plus de DEFAULT SQL sur `notification_channels` (JSON). Les stats runtime d’une créature suivent la visibilité du monstre/PNJ (un brouillon n’est plus lisible par id). Trier le catalogue monstres par nom ne provoque plus d’erreur SQL pour un non-admin.

## Août 2026 — Alertes Dependabot Node (extract-zip, undici)

Les alertes GitHub sur `extract-zip` et `undici` (CRLF, cookies, retry, keep-alive, blob) sont traitées : Puppeteer inutilisé est retiré ; `undici` transitif du SDK Cursor est forcé en 6.28.0. D’autres paquets jamais importés ont aussi été enlevés (`@playwright/mcp`, `mysql-mcp`, Pikaday, Precognition, Font Awesome npm, etc.). La config MCP Cursor locale n’est plus versionnée.

## Août 2026 — Cadrage de l’IA générative

Le besoin (sorts/monstres trop « Dofus », PNJ trop combinatoires pour un algo) est documenté dans `docs/IA/` : pas de modèle entraîné au départ, Laravel prépare le contexte, l’IA propose en file d’attente, les objets se réduisent par grille algorithmique, monstres et PNJ se génèrent à la demande (monstre + sorts dans le même paquet). Rien n’est encore branché au site.

## Août 2026 — Fiches créature : mods, CA, compétences

L’affichage des caractéristiques monstre (Minimal / Line / Full) met les
**modificateurs** en avant, calcule la CA / l’initiative via formules + runtime
(équipements inclus), empile score → mod → sauvegarde, libelle les résistances
relatives (sans les 0 %), et montre les compétences groupées par stat.

Le **Dommage fixe Multiples (DO mult.)** a maintenant une colonne composable
(`do_fixe_multiple`) et apparaît dans le groupe Dommages (y compris à 0, ou via
équipements / runtime).

## Août 2026 — PDF multi + index server-side (gros volumes)

Les exports PDF multi-entités respectent la même visibilité que les listes
(`visibleToUser`). Les index items / monstres / ressources / consommables /
conditions paginent côté serveur (plus de plafond silencieux à 5000).

## Août 2026 — Optimisation & polish monstres

Pass type « sorts » côté monstres : validation race (`monster_races`), PDF multi
filtré par visibilité, API tableau allégée (plus d’arbre d’effets de sorts par
ligne), catalogues d’édition bornés + recherche API relations, vue Full enrichie
(sorts, empty states), Line alignée (traits / PA boss).

## Août 2026 — Bibliothèques : plus de plafond à 50 fiches

Les tableaux embarqués dans les pages Bibliothèque (CMS) paginent côté serveur :
on voit l’ensemble du scrap (milliers de sorts / objets / etc.), plus seulement
les 50 premières lignes (qui donnaient l’impression de n’avoir que 2 pages).

## Août 2026 — Qualité effets « autre » + listes sécurisées

Les téléports déjà mappés en `déplacer` mais restés en `autre` se reclasse via
`scrapping:effects:reapply-mappings` (sans re-import). Le remboursement de PA
(Dofus 120) est mappé en booster PA. L’invisibilité (150) devient l’état
`Invisible` via `appliquer-etat`. L’API des définitions d’effet est paginée.
Les tables legacy `spell_effects` / `spell_effect_types` sont retirées. Les
listes Inertia et tableaux API filtrent la visibilité comme la policy `view`.

## Août 2026 — Polish UI sorts (usage unifié)

Minimal et Line partagent `SpellUsageBlock` (méta, résolution, chips). La fiche Full
aligne l’utilisation sur cette méta, ajoute un bandeau d’effets au-dessus du journal,
et clarifie les empty states. L’éditeur d’effets signale les modifications non
enregistrées (badge dirty).

## Août 2026 — Visibilité liste sorts + perf area

L’API tableau des sorts (et l’index / PDF multi) ne renvoie plus les brouillons
ou contenus hors droits : filtre aligné sur la policy `view`. La zone (`area`)
d’un sort réutilise les effets déjà chargés, sans requête SQL par ligne.
L’édition des champs suit la policy (auteur ou admin). Le tri `po`/`area` utilise
des colonnes ou une sous-requête valides. La sync des classes conserve les
emplacements pivot existants. L’éditeur de sort ne charge plus toutes les
définitions d’effet : recherche via API. Le legacy `spellEffects` n’est plus
exposé dans la resource sort.

## Août 2026 — Affichage des états de sorts

Les noms d’états issus de DofusDB qui arrivaient sous forme d’hyperlien
(`{{spell,…::Évadé}}`) s’affichent désormais avec le libellé lisible uniquement.
Nettoyage à l’import, à la résolution des effets, côté interface, et commande de
maintenance pour les données déjà en base.

## Août 2026 — Nettoyage des images orphelines

Les super-admins peuvent lancer depuis le menu maintenance (**Fichiers orphelins**) un scan
des fichiers MediaLibrary sans référence en base, suivre la progression, annuler, et recevoir
une notification du nombre de fichiers supprimés. La suppression définitive d’une entité
continue de purger ses images ; la mise en corbeille les conserve pour permettre la
restauration. Une tâche cron optionnelle (désactivée par défaut) peut enfiler le même job.

## Août 2026 — Favoris en base + modal / page

Les favoris sont enregistrés en base pour chaque compte. Un bouton cœur dans le header ouvre
un modal (sans quitter la page) avec les fiches groupées par type et une recherche pour en
ajouter ; « Ouvrir en page » mène à `/favoris`. Sans connexion, un message invite à se connecter.
L’icône des menus d’options est un cœur plein ou vide. La recherche globale et les pickers
affichent d’abord les favoris.

## Août 2026 — Refonte vues entités + caractéristiques

Parcours unifié : minimal → overlay → modal → page. Actions réordonnées (presets), DofusDB
accessible dès le minimal déployé. Caractéristiques : densités `icon` / `labeled` / `spacious`,
groupes canoniques, résumé 5 stats. Épinglage multi-fenêtres flottantes. Favoris locaux et
refresh DofusDB (droit d’écriture) branchés côté monstre / entités scrappables.

Extension multi-entités : shell `useEntityMinimalShell` + titre modal sur sorts, objets,
consommables, ressources, races, panoplies, capacités ; PNJ avec stats créature. Tous les
Minimal (condition, trait, spé, shop, campagne, scénario, type de ressource…) passent par
`EntityMinimalCard` (hors `language`).

## Août 2026 — Caractéristiques calculées (base / objets / contexte)

Les fiches de créatures peuvent désormais composer une caractéristique à partir de sa formule
de base, des bonus d’équipement et d’un bonus contextuel (nombre ou formule). Un total stocké
reste prioritaire pour ne pas casser le scrap. Le niveau peut être une fourchette ou un dé :
l’interface affiche la première valeur possible et recalcule toute la fiche via un sélecteur.
Un popover détaille la décomposition et le tableau par niveau. Les formulaires monstre / objet /
sort ont été allégés (sections repliables).

## Août 2026 — Gates et préparation scrap serveur

Le pipeline d’import est verrouillé pour un scrap catalogue complet :

- audits live : conversion effets à 100 %, 0 mapping caractéristique manquant ; ~33 % d’effets `autre`
  surtout glyphes / pièges / placeholders (hors périmètre volontaire) ;
- 80 écarts qualité objet corrigés (`item_type_dofus_ids` dérivés des aides) ;
- quality gate **active par défaut** sur `scrapping:run` (hors simulate) + gate effets après import sorts ;
- samples créature / objet alignés sur les formules + tests automatiques ;
- mappings téléports / échanges ajoutés ; checklist scrap serveur documentée ;
- workflow CI dédié aux gates scrapping.

## Août 2026 — Référence DofusDB pendant l’édition

Sur les fiches **Full** et les écrans d’**édition** (page ou modal), une action avec l’icône Dofus
ouvre un **panneau flottant** de référence lorsque l’entité a un `dofusdb_id`. Le panneau affiche
le lien profond vers DofusDB et ouvre le site dans une fenêtre séparée (pas d’iframe), sans bloquer
l’édition de la fiche Krosmoz.

## Août 2026 — Almanax interactif dans le header

Le badge Almanax du header affiche désormais le **jour** (identique au calendrier réel) avec le mois
Dofus, un **tooltip** au survol, et un **calendrier** au clic pour parcourir les mois du Monde des Douze.

## Août 2026 — Conversion Dofus plus fiable

Le socle d'import Dofus a été simplifié pour rendre les imports massifs plus sûrs :

- les valeurs numériques suivent désormais les formules et limites administrables des caractéristiques ;
- un élément incorrect n'interrompt plus tout un lot ;
- les effets et caractéristiques inconnus sont signalés pour revue au lieu d'être perdus ;
- une commande d'audit vérifie le paramétrage avant un import massif ;
- l'administration indique les mappings incomplets et les prévisualisations exposent les étapes de conversion.

Cette évolution ne cherche pas à deviner toutes les exceptions Dofus. Elle privilégie une donnée brute
importable et clairement signalée lorsqu'une règle métier doit encore être définie.

### Fiches de sort enrichies

Les fiches de sort conservent maintenant les contraintes globales de lancer en ligne ou en diagonale, le
mode direct/piège/glyphe, la limite de cumul et le temps de relance global. Ces informations sont importées
depuis le premier niveau DofusDB et restent séparées du moteur détaillé des effets.

### Première calibration des monstres

Le niveau des monstres suit maintenant une conversion lisible : dix niveaux Dofus correspondent à
un niveau Krosmoz, dans une plage de 1 à 30. Une analyse des statistiques de l'ensemble du catalogue
DofusDB sert de base à la calibration progressive des autres caractéristiques.

Les autres caractéristiques des monstres disposent désormais de leurs propres marges : scores jusqu'à 30,
PA/PM/PO élargis, bonus tactiques conservés et initiative non plafonnée. Les résistances en pourcentage
ont été remplacées par cinq paliers faciles à jouer, tandis que critiques et soins suivent des conversions
simples et limitées. Les Kamas ne sont plus importés sur les monstres.

La chaîne d'import conserve maintenant aussi la description, le tacle, la fuite, les critiques et les soins.
Les races DofusDB sont reliées par leur identifiant source (et créées en brouillon si nécessaire). Les règles
JSON, leur copie de secours PHP et la base sont synchronisées et contrôlées par les audits automatisés.

### Calibration des objets

Les bonus d'équipement Dofus conservent désormais leurs malus : les conversions sont symétriques autour
de zéro. Les plafonds distinguent l'équipement de la forgemagie, notamment ±6 pour les caractéristiques
principales, ±5 PA et ±2 PM hors forgemagie. Les résistances relatives ne sont transformées en paliers que
pour les panoplies. Les faux PV issus de l'identifiant technique DofusDB 0 sont ignorés et les dommages
multi-éléments utilisent leur véritable identifiant source.

La calibration objet a ensuite été consolidée : chaque bonus réservé à un emplacement pointe vers le bon
type DofusDB, les grilles et exemples respectent leurs plafonds et reproduisent exactement les formules.
Les PV et l'initiative restent non plafonnés, le critique accepte les malus jusqu'à -3 et les résistances
fixes des boucliers sont limitées à ±7 hors forgemagie. Ces règles sont synchronisées en base et couvertes
par les contrôles automatiques.

### Calibration des sorts

Les caractéristiques de sorts utilisent maintenant des unités Krosmoz cohérentes avant leur branchement
au système d'effets : niveau 1–20, limites jouables de portée et de cadence, critique signé de −3 à +3,
résistances relatives par paliers et résistances fixes plafonnées à 10. Les dégâts fixes, soins, bonus de
portée et initiative ont également été harmonisés avec les créatures et les objets.

Cinq métadonnées manquantes ont été définies : lancement en ligne, lancement en diagonale, type de cible,
cumul maximal et délai global. Un contrôle automatique vérifie désormais les bornes, les exemples et
l'invariant « zéro reste zéro » sur l'ensemble des définitions de sorts.

La conversion des effets a ensuite été sécurisée : les plages DofusDB ne sont plus confondues avec des
jets de dés, et les formules exécutées en jeu utilisent désormais les valeurs Krosmoz converties, y compris
en critique. Les bonus, retraits et vols sont distingués correctement ; les paliers de résistance restent
valides et le vol de vie utilise sa courbe réduite dédiée.

Les dégâts, soins et vols de vie sont maintenant proportionnés aux PV de référence du niveau. Le budget
total d'un tour est réparti selon le coût en PA, la portée, la zone et les différentes lignes d'effet du
sort, sur la base du premier équilibrage fourni dans « Creation sort ».

Les effets de bouclier DofusDB sont enfin reconnus comme `protéger` (et non plus comme boosts génériques),
puis budgétés comme les soins pour rester proportionnés aux PV.

La résolution des sorts (jet d'attaque, sauvegarde ou réussite auto) et le booléen Wakfu/physique sont
désormais déduits des effets convertis, car DofusDB ne fournit pas `isMagic`. Les DD de sauvegarde suivent
la formule des règles : 8 + modificateur + maîtrise.

Les états (`appliquer-etat` / `s-appliquer-etat`) propagent correctement leur durée vers
`duration_formula`, mettent à jour les pivots existants au réimport, et alimentent le référentiel
`Condition` avec leurs flags (immobilisation, invulnérabilité, etc.).

Les déplacements distinguent mieux push / pull / téléport (y compris « sans dommages »), et le triage
des mappings restants sépare les clés jouables des effets hors périmètre documentés.

Le snapshot des mappings scrapping a été reconstruit depuis les JSON d’entité (clés item/spell
réalignées). L’audit ignore les catalogues sans cible (`monster-race`). Les sorts encore présents
sur DofusDB ont été resynchronisés : résolution inférée, DD en `8 + …`, couverture d’effets à 100 %.

Les PV temporaires de sort sont désormais distincts du bouclier : sous-effet `donner-pv-temporaires`,
caractéristique `pv_temporaires_spell` (charac DofusDB 95), même enveloppe de budget que les soins.
Le max PV via sort reste porté par la vitalité.

Les compétences actives (18) peuvent être boostées ou retirées par un effet de sort
(`acrobatics_spell`, `stealth_spell`, etc.), sur le même modèle que l’équipement, plafonnées à ±5.

Les sorts locaux absents de l’API DofusDB (404, ex. anciennes flèches Cra) ont été passés en
`archived` avec `auto_update=false` : conservés pour historique, exclus des resync automatiques.
