# Template

````col
```col-md
flexGrow=1
===
### Nom de l'étape
>**État** :  #afaire  #encours #àverifier #fini  
>**Priorité** : ⏬ 🔽 🔼 ⏫ 
>**Tags** : UI, UX, Backend, Frontend, db, etc
```
```col-md
flexGrow=2
===
##### Description
Exemple de description
```
````

# Fonctionnalités

## Pages
````col
```col-md
flexGrow=1
===
### Architectures d'une page
>**État** : #encours  
>**Priorité** : ⏫ 
>**Tags** : UI, Backend, Frontend
```
```col-md
flexGrow=2
===
##### Description
- Titre dans le header du site.
- Bouton d'accès au modal de paramètrage de la page : à coté du titre (accessible seulement si on a les droits d'écriture sur la page)
- Il manque un bouton pour ajouter une section dans une page. Le bouton doit se placer à la fin de la page sur la droite. On va utiliser un bouton carré avec juste une icone dedans en mode glass.
  ##### Bogues :
- Modal de modification de la page ne fonctionne pas si on l'appelle depuis la page.
- Le titre de la page ne s'affiche pas, j'ai page par défaut.
```
````

## Sections
````col
```col-md
flexGrow=1
===
### Structure d'une section
>**État** : #encours  
>**Priorité** : ⏫
>**Tags** : UI, Backend, Frontend
```
```col-md
flexGrow=2
===
##### Description
- Une section prend 100% de la largeur de la page. Elle possède un titre (optionnelle).
- Au hover des icones apparaissent en haut à droite (au niveau du titre) :
	- copier le lien de la section (permet d'avoir le lien de la page avec en plus #slug de la section pour être rediriger (scroll) vers cette section en particulier)
- Si on a les droits d'écriture sur la section, on ajoute : 
	- une icone de paramètrage qui permet d'ouvrir le modal d'édition de la Section
	- une icone d'édition de la section. Par exemple faire apparaitre le WYSIWYG.
	  En fonction du type de modification qu'à le template, on va soit ouvrir un modal avec les différents paramètres : par exemple pour le template Image ou Video on va avoir un modal permettant d'upload le fichier ou de la supprimer et de choisir les quelques paramètres liés au média. Ou encore un tableau listant des entités, le modal permet de choisir quelle entité avec quelle filtre, etc. Si le template permet une modification directement à la place de la section, comme gallery ou text, on va alors remplacer la section par sa version modification.
```
````

````col
```col-md
flexGrow=1
===
### Ajout d'une section
>**État** : #encours  
>**Priorité** : ⏫
>**Tags** : UI,UX, Backend, Frontend
```
```col-md
flexGrow=2
===
##### Description
- L'ajout d'une section sur une page se fait via une modal depuis la page en question. La modal présente les différentes paramètres que l'on a et notamment les templates. Chaque template a un nom et un descriptif.
- Lors de l'ajout d'une section, on l'ouvre en mode édition. c'est à dire que si l'édition de la section se fait via une modal, on ouvre automatiquement cette modal après l'ajout et si l'édition se fait directement sur la page on ajoute la section en mode édition.
```
````


````col
```col-md
flexGrow=1
===
### Template de section
>**État** : #encours  
>**Priorité** : ⏫
>**Tags** : Frontend
```
```col-md
flexGrow=2
===
##### Description
- Un template de section est un fichier qui comprend un titre et une description.
- Il est composé de deux grandes parties : 
	- la version modifiable de la section ou une modal pour parampètre la section
	- la version de la section en lecture
- C'est ce fichier qui gèrent les deux et donc le design (css) des deux parties et le js des deux parties.
- Un composable peut superviser les échanges de donner avec le backend. Car la base de donnée attend un format de données (data pour le contenu et settings pour les paramtètres au format JSON)
```
````


````col
```col-md
flexGrow=1
===
### Choisir l'ordre des pages et des sections
>**État** : #encours  
>**Priorité** : ⏫
>**Tags** : UI,UX, Frontend
```
```col-md
flexGrow=2
===
##### Description
- L'ordre des pages dans le menu et des sections dans une page se fait via le paramètre order dans la db.
- Au niveau de l'UX, j'aimerai que l'on puisse faire un drag et drop pour déplacer une page par rapport à une autre :
	- soit dans le tableau des pages pour les pages. Cela implique de trier les pages dans l'ordre en considérant qu'il y a des pages parentes qui ont en ordre entre elles et que les pages parentes peuvent avoir des pages enfantes qui ont un ordre entre elles.
	- soit dans une page pour les sections. Dans ce cas, si on a les droits en écriture, on pourra accéder au modal d'édition de la page. Dans celui ci, on aura un onglet avec l'ordre des sections : on y retrouvera tout nos titres de sections avec le nom du template (si il n'y a pas de titre on Sans titre). Sur cette interface on peut trier les sections dans la page.
```
````
## Système de notification

````col
```col-md
flexGrow=1
===
### Notification Z-index
>**État** : #encours  
>**Priorité** : 🔼
>**Tags** : UI
```
```col-md
flexGrow=2
===
##### Description
Les notifications doivent aller par dessus les overlay des modals
```
````

## Formats Entités

| #afaire | 🔽  | Formats des entités          | UI / UX | Il existe 4 formats pour les entités : full, compact, minimal et texte.<br>Le format est choisi en fonction du contexte (par exemple via le dropdown dans le entity table, on peut afficher en full ou en compact). Il y aura aussi possibilité via les templates de section d'afficher certains formats plutôt que d'autres.                                                                                                                                                                                                                                                                                                                                                                                |
| ------- | --- | ---------------------------- | ------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| #afaire | 🔽  | Format des entités : full    | UI / UX | Le format full est le format pour les entités qui peut prendre autant de place qu'il y en a. Il est adapté à une page ou à une modal large.<br>On va privilégier ce format pour l'édition des entités (soit par modal, soit dans une page ou dans le mode edition rapide).<br>Il n'y a pas besoin de condensé les données. Il faut juste retrouver nos icones en haut à droite pour modifier si on a les droits, copier le lien, télécharger le pdf, rafraichir les données si on a les droits, etc<br>L'image sera a gauche et le nom avec les infos les plus pertinente à droite. Il faut que ce format soit responsive car en mobile on va privilégier l'ouverture dans des pages et non dans des modals. |
| #afaire | 🔽  | Format des entités : compact | UI / UX | Dans le format compact on retrouve dans les données d'une entité mais on va privilégier les icones et tronquer les textes trop long avec un clique pour les déplier.<br>L'idée de se format et qu'il puisse tenir dans un modal medium voir small. On veut avoir tout les infos facilement accessible même si il faut peut être apprendre certaines icones dans un premier temps. On retrouve notre menu en haut à gauche. L'image est toujours à gauche.                                                                                                                                                                                                                                                    |
| #afaire | 🔽  | Format des entités : minimal | UI / UX | Ce format est un format card (autour de 150 à 200px de largeur pour 80px à 100px d'hauteur). L'idée est que ce format soit contraint pour pouvoir créer des listes. On va afficher seulement les infos les plus pertinentes en premier lieux avec des tooltips pour les détails. L'image est petit est à gauche. Le menu est sous forme de dropdown en haut à droite.<br>Au hover, la card se déplie et laisse appaitre toutes les autres infos. Cela permet d'avoir quelques de très condensé mais avec les infos accessibles.                                                                                                                                                                              |
| #afaire | 🔽  | Format des entités : texte   | UI / UX | Le format texte est composé de l'image de l'entité sous forme d'icone (même taille que le texte) puis du nom de l'entité. Le texte est mis en valeur pour montrer qu'on peut interagir.<br>Lors du hover, on affichage le format minimal. Lors du click le format minimal reste affiché à l'écran tant que l'on click pas à l'extérieur ou avec le bouton ESC. De ce fait on peut déplier la card du format minimal pour accéder à l'ensemble des informations. La card ne remplace pas le texte, elle fonctionne comme un tooltips ou un popover.                                                                                                                                                           |
## Tableau des entités

````col
```col-md
flexGrow=1
===
### Options des tableaux d'entité
>**État** : #encours  
>**Priorité** : 🔼
>**Tags** : UI, UX
```
```col-md
flexGrow=2
===
##### Description
Les tableaux d'entités sont centrales dans le projet. Il est important de leurs accorder un soin tout particulier. Si nécessaire il faut passer par un plugin.
- Recherche dans des colonnes spécifique
- Trier les lignes en fonction d'une colonne (alphabet, numéric, automatiquement)
- Masquer / Afficher des colonnes
- Exporter un tableau au format pdf, csv
- Rafraichir les données depuis la base de donnée
- Filtrer en fonction de différents critère 
  
  Il faut un système qui soit flexible et facile à manipuler (composable, config) car chaque tableau aura ces propres configurations pour les filtres, ces propres colonnes a affiché par défaut, trie de ligne, etc
```
````

````col
```col-md
flexGrow=1
===
### Menu général du tableau
>**État** : #encours  
>**Priorité** : 🔼
>**Tags** : UI, UX
```
```col-md
flexGrow=2
===
##### Description
Il est possible de sélectionner une ou plusieurs ligne dans un tableau.
Dans ce cas le menu qui se trouve juste au dessus du tableau s'active, on trouve les options suivantes : 
- Ouverture (full page) (_blank si plusieurs sélectionner)
- Ouverture rapide (compact modal) (désactiver si plusieurs sélectionner)
- Copier le lien (désactiver si plusieurs sélectionner)
- Télécharger le pdf (ouverture de la modal de génération des pdf avec l'entité ou les entités sélectionnés)
- Si droit de modification : 
	- modification (modal full) (_blank si plusieurs sélectionner)
	- modification rapide (modal full) (désactiver si plusieurs sélectionner)
	- Toggle Edition rapide
	- Suppression
	- Rafraichir depuis DofusDB (admin)
```
````

````col
```col-md
flexGrow=1
===
### Menu individuel à chaque entité (ligne)
>**État** : #encours  
>**Priorité** : 🔼
>**Tags** : UI, UX
```
```col-md
flexGrow=2
===
##### Description
Au début de chaque ligne, un menu sous forme de dropdown propre des options pour interagir avec l'entité de la ligne (et seulement l'entité de la ligne) 
- Ouverture (full page)
- Ouverture rapide (compact modal)
- Copier le lien
- Télécharger le pdf (ouverture de la modal de génération des pdf avec l'entité)
- Si droit de modification : 
	- modification (modal full)
	- modification rapide (modal full)
	- Suppression
	- Rafraichir depuis DofusDB (admin)
	  
- Au click simple : on sélectionne la ligne. En mode edition rapide, on affiche l'entité sur la partie de droite.
- Au double-click : on ouvre en mode rapide (modal compact) si on est pas en mode edition rapide. Si on est en edition rapide, on ouvre la modification en mode modal
- Au click droite, on affiche le dropdown
```
````

````col
```col-md
flexGrow=1
===
### Mode Edition rapide
>**État** : #encours  
>**Priorité** : 🔼
>**Tags** : UI, UX, fonctionnalité
```
```col-md
flexGrow=2
===
##### Description
Les tableaux d'entités sont capables de se réduire en largeur pour ne prendre que 1/2 ou 2/3 (fonction de la taille de la fenêtre). Le reste est remplacé par le template de l'entité en mode full modifiable. J'entend template par les champs non rempli, une entité vide de donnée. 
- Lorsqu'on sélectionne une ligne du tableau alors le template se rempli avec les données, on peut donc les modifier. On enregistre seulement les champs modifiés.
- Lorqu'on sélectionne plusieurs ligne à la fois du tableau, le template est rempli avec seulement les champs qui ont des valeurs communes aux entités sélectionnées. Les autres champs restent vide (on modifie juste le placeholder pour indiquer que les valeurs sont différentes). Si on modifie un champs, alors on le modifie pour l'ensemble des entités sélectionnés. On enregistre seulement les champs qui ont été modifiés.
```
````


## PDF

````col
```col-md
flexGrow=1
===
### Génération de PDF
>**État** : #encours  
>**Priorité** : 🔼
>**Tags** : UI
```
```col-md
flexGrow=2
===
##### Description
Lorsqu'on génère un pdf, il peut y avoir des paramètres. C'est pourquoi avant de la générer on va ouvrir une modal. Dans cette modal on retrouvera les entités qui ont été sélectionné pour faire partie du pdf (format text pour avoir le hover). On pourra enelever ceux qu'on ne veut pas et ajouter via le moteur de recherche (quand il sera fonctionnel) d'autres entités.
On aura des options comme insérer les dépendances (true par default). C'est à dire mettre les sorts et les ressources pour un monstre, etc
Si il y a d'autres options utiles alors met les.
On aura alors 3 boutons, annuler, imprimer et télécharger.
```
````

# Correction UI / UX et design
## Modals

````col
```col-md
flexGrow=1
===
### Desgin et UX des modals
>**État** : #encours  
>**Priorité** : 🔽 
>**Tags** : UI, UX, Frontend
```
```col-md
flexGrow=2
===
##### Description
Les modals doivent pouvoir avoir plusieurs variants de style : glass, dash, outline, soft et ghost.
Ces variants modifient l'arrière plan et les bordures des modals.
Les modals peuvent aussi avoir une taille de xs à XL ainsi que les couleurs de base (via la classe color- ou la variable css --color (équivalent)).
Le variant glass utilise box-glass-md, alors que outline utilise border-glass-md.
L'overlay doit être discret (plus que maintenant), sans flou, et doit assombrir lorsqu'on est en mode dark et éclaircir lorsqu'on est en mode light.
Des animations simples sont attendus pour l'entrée et la sortie des modals.

En terme d'UX, les modals doivent avoir un paramètre permettant de les redimentionner avec la souris (false par default) et de les déplacer sur l'écran en cliquant et déplaçant le header (true par default).
```
````

## Inputs

````col
```col-md
flexGrow=1
===
### Desgin des inputs
>**État** : #encours  
>**Priorité** : 🔼 
>**Tags** : UI, Frontend
```
```col-md
flexGrow=2
===
##### Description
Un important travail d'harminisation des inputs (et d'autres atoms) a été fait.
Chaque input peut avoir des variants : ghost, soft, dash, outline et glass
Ils ont tous les colors de bases à l'aide de la classe color-XX ou de la variable css --color ainsi qu'un paramètre de taille de xs à xl avec la taille md par défault.
En partant de ceci voilà ce qui pose problème encore : 
- input de type texte ou assimilé : dans le variant glass le fond doit être éclairci avec un fond semi-transparent et flou (via box-glass-md fonctionne aussi pour les bordures). Dans dash le bg doit être opaque, dans outline et ghost il doit être transparent. Pour soft, le bg doit être semi-transparent. Les bordures ne doivent pas être présente dans ghost, elles sont gérées par box-glass-md avec glass, par border-glass-md avec outline. Pour soft et dash, on utilise les bordures natifs html en utilisant la variable --color.
```
````

````col
```col-md
flexGrow=1
===
### Mettre les champs de modification de mdp sous forme de colonne
>**État** : #afaire
>**Priorité** : 🔼 
>**Tags** : UI, Frontend
```
```col-md
flexGrow=2
===
##### Description
La modification des mots de passe sur la page de gestion des comptes est sous forme de ligne sans marge. Il faut que ça soit sous forme de colonne avec un gap-2.
```
````
## Autres composants

````col
```col-md
flexGrow=1
===
### Augmenter le padding des boutons dans les dropdown
>**État** : #afaire  
>**Priorité** : ⏬
>**Tags** : UI
```
```col-md
flexGrow=2
===
##### Description
Les éléments du dropdown sont correctement placé sauf lors du hover où il y a un effet de scale, alors ils sont trop proche du bord de gauche. Il faut ajouter du padding à gauche.
```
````
## 404
````col
```col-md
flexGrow=1
===
### Gérer les erreurs 404
>**État** : #afaire  
>**Priorité** : ⏬
>**Tags** : UI
```
```col-md
flexGrow=2
===
##### Description
Créer une page 404 pour gérer les erreurs avec un bouton pour retourner en arrière.
```
````

# Debug et vérification 
- [ ] Check les permissions et si il n'y a pas des liens vers des pages avec un compte sans droit.
- [ ] 
