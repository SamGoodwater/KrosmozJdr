# Ce qui a été fait

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
