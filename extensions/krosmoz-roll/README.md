# KrosmozRoll

Extension de navigateur qui relie une fiche KrosmozJDR ouverte à une partie Roll20 ouverte dans le **même navigateur**. KrosmozJDR calcule la formule (caractéristiques, équipement, consommables). Roll20 lance les dés et montre le résultat à la table.

L’extension est un tuyau. Elle ne recalcule pas les jets et ne remplace pas la carte Roll20.

Référence de ce qui marche déjà ailleurs : [Beyond20](https://beyond20.here-for-more.info/) (fiche D&D Beyond vers Roll20 et Foundry). Roll20 ne fournit aucune API publique pour un site externe. Les scripts Mod (`sendChat`) tournent dans un bac à sable sans accès réseau, et exigent un créateur de partie Pro. Ils ne peuvent pas interroger KrosmozJDR.

## Navigateurs

Une seule base de code pour **Firefox et Chrome**, au format WebExtensions Manifest V3.

- API `browser.*` via [`webextension-polyfill`](https://github.com/mozilla/webextension-polyfill), pour ne pas écrire deux extensions.
- Firefox a besoin de `browser_specific_settings.gecko.id` dans le manifeste pour une installation durable.
- On n’utilise pas les API réservées à Chrome (panneau latéral, par exemple).
- Chargement en développement : Chrome « extension non empaquetée », Firefox « module complémentaire temporaire ».
- Publication éventuelle plus tard : Chrome Web Store et addons.mozilla.org, chacun avec son paquet.

Chaque joueur installe l’extension, ouvre sa fiche KrosmozJDR et la partie Roll20. Le message part avec le compte Roll20 déjà connecté dans cet onglet. Aucun mot de passe Roll20 n’est stocké.

## Technologies

| Élément | Choix |
| --- | --- |
| Extension | JavaScript, Manifest V3, sans framework |
| Compatibilité Firefox / Chrome | `webextension-polyfill` |
| Lien entre onglets | `runtime.sendMessage` : le service worker reçoit la requête, le script Roll20 écrit dans le chat |
| Journal | Popup de l’extension. Tant que la session de jeu n’existe pas, stockage local du navigateur. Ensuite, la même liste vient de l’API KrosmozJDR |
| Contrat | Requête de jet (ci-dessous). Le calcul reste dans KrosmozJDR |
| Build du site | Le dossier `extensions/krosmoz-roll/` est hors Vite, hors Laravel et hors déploiement du site |
| Appel API | Le script qui tourne sur `roll20.net` n’a pas le cookie KrosmozJDR. L’extension appelle l’API avec un jeton créé pendant que le joueur est connecté au site (`host_permissions`) |

Exemple de requête :

```json
{
  "id": "01J...",
  "session_id": "01J...",
  "author_id": "01J...",
  "executor_id": "01J...",
  "sheet": { "type": "character", "id": "01J...", "name": "Iop" },
  "label": "Initiative",
  "formula": "1d20+4",
  "breakdown": ["mod. Int +2", "équipement +1", "consommable +1"],
  "visibility": "public",
  "status": "pending"
}
```

`visibility` vaut `public` ou `gm`. Le script Roll20 le traduit en message de chat ou en jet caché (`/gmroll`, chuchotement au MJ). `executor_id` est le compte dont l’extension doit envoyer le message : le joueur pour sa fiche, le MJ pour un monstre.

## Journal dans le popup

Le popup (clic sur l’icône de l’extension) est un historique des requêtes, la plus récente en haut. Chaque ligne montre le nom, le libellé, la formule, l’heure et un marqueur d’état.

Le marqueur distingue l’acheminement et l’exécution. Roll20 peut accepter le texte sans que le dé soit vraiment résolu.

| État | Marqueur | Signification |
| --- | --- | --- |
| `pending` | En attente | Créée, pas encore écrite dans Roll20 |
| `sending` | Envoi | L’extension a trouvé l’onglet et écrit dans le champ |
| `delivered` | Acheminée | Le chat Roll20 a accepté le message |
| `executed` | Exécutée | Le message de jet est visible dans le journal Roll20 |
| `failed` | Échec | Onglet absent, champ introuvable, envoi refusé |
| `cancelled` | Annulée | Arrêtée avant le départ |

« Exécutée » se vérifie en relisant le journal de chat après l’envoi. L’animation 3D n’est pas un signal fiable.

Trois actions sur la ligne :

- **Annuler.** Visible tant que la requête est `pending`. Elle ne part pas. Dès `delivered`, le message est déjà dans le chat Roll20 : annuler ne le retire pas.
- **Relancer.** En `failed`, la même requête repasse en `pending` : les dés n’ont pas été lancés. En `executed`, relancer crée une **nouvelle** requête avec la même formule. L’ancienne ligne reste. Roll20 lance les dés une seconde fois.
- **Supprimer.** Retire la ligne du journal. Le message déjà affiché dans Roll20 reste.

Le joueur voit ses requêtes. Le MJ voit celles de tous les joueurs de la partie, avec le nom de l’auteur, et les requêtes `gm`. Il peut annuler une requête encore en attente, la relancer ou la supprimer du journal. Un joueur ne voit pas les jets cachés des autres.

Ce partage ne tient pas dans le stockage du navigateur. La liste du MJ est celle de la session KrosmozJDR. Le popup ne fait que l’afficher. Tant que cette session n’existe pas, le popup garde l’historique en local, avec la même forme d’objet, pour un seul navigateur.

## Ce que le pont peut faire

Tout ce qui est visible par les autres joueurs doit passer par le **serveur de chat** Roll20. Modifier seulement la page locale ne s’affiche que chez soi.

### Chat et dés

C’est le socle, le plus stable. Le script écrit dans le champ de chat comme le joueur, puis envoie.

- Texte, emote, chuchotement, jet public, jet visible seulement par le MJ.
- Formule laissée à Roll20 : `Iop — Initiative [[1d20+4]] (Int +2, équip. +1, conso +1)`. Le moteur de dés de Roll20 lance le dé. Les dés 3D du client actuel passent par ce moteur (`quantumRoll`). Un nombre déjà calculé dans KrosmozJDR s’affiche en texte, sans animation.
- Le détail du bonus (équipement, consommable) est du texte autour de la formule.

### Images dans le chat

Possible pour toute la table si l’image voyage dans le message de chat.

- Portrait, icône de sort, rappel visuel : URL publique d’une image KrosmozJDR.
- Cette URL doit être joignable par les autres navigateurs. Une adresse `localhost` ne s’affiche que sur la machine qui héberge le site.
- Roll20 filtre le HTML du chat. Une carte riche (cadre, boutons, plusieurs images) dépend de ce filtre et casse plus souvent qu’une ligne de texte avec un jet. Beyond20 le fait pour les cartes de sort ; on le traitera après les jets simples.

### Points de vie, barres, états

Possible, sur le modèle de Beyond20, en pilotant le client Roll20 depuis la page. Cette partie n’est pas une API supportée : elle suit les objets internes de la partie, et le client Roll20 (ancien moteur Legacy, moteur actuel) peut changer sans préavis.

Principe visé :

- KrosmozJDR reste la source des chiffres.
- Le MJ prépare une fois le jeton Roll20 : même nom que la fiche, barres reliées à des attributs du personnage.
- L’extension met à jour ces attributs. La barre du jeton suit si elle est liée.
- Répartition envisagée des 3 ou 4 barres génériques de Roll20 : PV, PA, PM. Roll20 n’a pas de notion de PA/PM ; ce ne sont que des nombres sur le jeton.
- Un joueur ne met à jour que les personnages qu’il contrôle. Le MJ met à jour les monstres.
- Deux jetons du même nom sont mis à jour ensemble. Les monstres homonymes doivent être distingués dans le nom.

Au-delà des barres, le même canal fragile permet plus tard :

- des marqueurs d’état sur le jeton (états de combat déjà décrits dans les règles) ;
- le suivi d’initiative de Roll20, rempli avec les jets lancés depuis les fiches.

Ces deux-là attendent que le chat et les barres soient en place.

### Ce qui reste dans Roll20, à la main

La carte, le déplacement des jetons, le brouillard, la lumière, les portes et la musique. Les piloter demanderait de conduire le canvas. Ce n’est pas l’objectif du pont.

On ne recopie pas non plus la fiche KrosmozJDR dans une feuille de personnage Roll20. L’équipement, les consommables et les normes n’existent qu’une fois, dans KrosmozJDR.

## Session de jeu, plus tard

Ces écrans n’existent pas encore dans KrosmozJDR. On ne les construit pas avec l’extension. Le système de jeu en temps réel devra toutefois porter les données que le popup consomme.

Écrans visés :

- Fiche du personnage du joueur dans la session.
- Vue de session : son personnage, et, en combat, les monstres visibles, leur équipement, le coffre commun du groupe.
- Pour le MJ : toutes les fiches joueurs et toutes les fiches monstres de la partie, plus le journal complet des requêtes.

Besoins à prévoir dans ce système, pour ne pas réécrire l’extension :

1. Une requête de jet rattachée à la session, à l’auteur, à l’exécuteur et à une fiche (personnage ou monstre), avec les états du tableau ci-dessus et les heures de passage.
2. Un canal temps réel vers le popup et vers l’extension qui exécute. Le projet n’a pas encore de diffusion (pas de Reverb, pas d’Echo). Le choix technique se fera avec la session.
3. Un jeton d’extension émis depuis la session KrosmozJDR déjà ouverte. L’onglet Roll20 est un autre site : il ne porte pas le cookie du site.
4. Les droits alignés sur la table. Le joueur lit sa fiche et ses requêtes. Le MJ lit toutes les fiches joueurs, toutes les fiches monstres, et toutes les requêtes de la session.
5. La formule déjà résolue dans la requête. Monstres visibles, équipement et coffre commun servent au calcul dans KrosmozJDR. L’extension ne les stocke pas et ne les relit pas.
6. L’exécuteur écrit `delivered`, `executed` ou `failed` après son passage dans Roll20. Le popup du MJ se met à jour avec cette écriture, sans ouvrir l’onglet du joueur.

Annuler, relancer et supprimer sont des écritures sur cette requête. Supprimer masque la ligne. Cela n’efface pas le message Roll20.

## Ordre de construction

1. **Chat et journal local.** Un bouton de test envoie `Test [[1d20+3]]` dans une partie ouverte, sur Firefox et sur Chrome. La ligne apparaît dans le popup avec son marqueur, Annuler, Relancer et Supprimer. L’historique reste dans le navigateur.
2. **Initiative.** Clic sur Initiative dans la fiche : formule `1d20 + mod. Intelligence + équipement + consommables + autres bonus`, détail lisible dans le chat.
3. **Autres jets de fiche.** Caractéristiques, compétences, dégâts, jets cachés MJ. Même objet de requête.
4. **Images** utiles à la table (portrait, sort), avec une URL publique.
5. **Barres de jeton** (PV, puis PA et PM) pour un personnage dont le nom correspond.
6. **Marqueurs d’état et suivi d’initiative**, si les barres tiennent dans le temps.

Si l’onglet Roll20 est introuvable ou si le champ de chat a changé, l’extension copie la formule dans le presse-papiers pour un collage manuel.

## Limites à accepter

- Roll20 peut renommer le champ de chat ou fermer l’accès aux objets internes. Le chat se répare ; les barres et le tracker peuvent casser jusqu’au correctif.
- Le pont est non officiel. Il n’y a pas d’engagement de Roll20 sur ce fonctionnement.
- L’extension et la fiche doivent sortir ensemble quand le format de `krosmoz:roll` change. C’est pour cela que le code vit dans ce dépôt, dossier `extensions/krosmoz-roll/`, et pas dans un second GitHub.
