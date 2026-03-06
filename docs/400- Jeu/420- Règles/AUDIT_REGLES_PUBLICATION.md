# Audit des règles KrosmozJDR – Préparation à la publication

Ce document recense les points à vérifier ou corriger pour publier les règles : cohérence, ton, clarté, enchaînement logique, liens entre parties, mise en page et exhaustivité.

---

## 1. Cohérence (contradictions repérées)

### 1.1 Initiative : Agilité vs Intelligence

**Règle canonique** (section 2.2.2 Caractéristiques secondaires et 3.2.1.4 Mise en place du combat) : l’initiative se calcule avec **Intelligence** :

- **Formule** : 1d20 + modificateur d’Intelligence + bonus d’Initiative (équipement, capacités).

**Incohérences à corriger** :

- **1.2.2** (Ressources principales) : « Règles rapides » et tableau indiquent « 1d20 + Agilité » ; « À retenir » dit « utilise l’Agilité » → à remplacer par **Intelligence**.
- **3.2.1** (Mise en place du combat) : « Règles rapides » indiquent « 1d20 + Agilité + bonus de maîtrise » → à remplacer par **Intelligence** (+ bonus d’Initiative, pas de maîtrise).
- **4.2.2** (Créatures – Caractéristiques et statistiques) : « Calcul : Identique aux personnages (Agilité + modificateurs) » → remplacer par **Intelligence**.
- **FORMAT_REGLES.md** : exemples avec « 1d20 + Agilité » pour l’initiative → remplacer par **Intelligence**.

**Action** : Harmoniser toutes les mentions sur **Intelligence** (voir corrections effectuées dans les fichiers).

---

### 1.2 Durée d’un round / tour

**Référence D&D et 5.2.1** : « Durée du tour : 6 secondes (identique) ». **3.2.2** « À retenir » : « Un tour = 6 secondes ».

**Incohérences** : Plusieurs sections indiquent **10 secondes** pour un round :

- **1.2.3** (Structure d’un tour) : « Round : Unité de temps de 10 secondes », « Combat : 10 secondes par round ».
- **1.2.5** : « Combat : Tours de 10 secondes (rounds) ».
- **3.1.1** (Exploration) : « en 1 round (10 secondes) ».
- **3.1.2** (Gestion du temps) : « Rounds (combat = 10 secondes) », « round étant une unité de temps qui dure 10 secondes ».

**Recommandation** : Choisir une seule valeur. Pour rester aligné avec D&D et 5.2.1, **standardiser sur 6 secondes** partout (corrections proposées dans les fichiers).

---

### 1.3 Attaques d’opportunité

**3.2.3** (Système de réaction) : le corps du texte précise qu’il **n’y a pas** d’attaques d’opportunité (remplacées par le tacle et la fuite).

**Incohérence** : Les « Règles rapides » en tête de 3.2.3 disent : « **Attaques d’opportunité** : Limitées à 1 par tour, déclencheurs spécifiques », ce qui contredit le reste.

**Action** : Remplacer par une formulation du type : « **Attaques d’opportunité** : Aucune (remplacées par le tacle et la fuite). »

---

### 1.4 Tour vs round (terminologie)

- **Tour** : ce qu’un personnage fait à son rang dans l’initiative (il dépense PA/PM).
- **Round** : une « manche » complète où tout le monde a joué une fois.

**1.2.3** mélange un peu les deux (« Round : 10 secondes », « Tour : action d’un personnage pendant un round »). Une fois la durée fixée (6 s), vérifier que partout :
- **round** = durée (ex. 6 secondes) et ensemble des tours d’un cycle ;
- **tour** = action d’une créature pendant ce round.

---

## 2. Ton (tutoiement, style Ankama)

**Objectif** : Tutoiement et ton léger, façon Ankama.

**Constats** :

- La plupart des fichiers utilisaient le **vouvoiement** (pronom de politesse et impératifs associés).
- Le guide **FORMAT_REGLES.md** ne mentionne pas le tutoiement.

**Recommandations** :

1. Ajouter dans **FORMAT_REGLES.md** une section « Ton et personne » :
   - Utiliser **tu / ton / ta** pour s’adresser au lecteur (joueur ou MJ).
   - Ton léger, clins d’œil possibles, sans lourdeur ; rester clair et précis.
2. Lors des prochaines révisions, passer progressivement les textes en tutoiement (priorité : Introduction, Création de personnage, Jouer).

---

## 3. Clarté (phrases, exemples)

**Points positifs** :

- Bonne présence d’exemples (tours de combat, répartition de caractéristiques, tests).
- Règles rapides en tête de section.
- Tableaux récapitulatifs (DD, PA/PM, etc.).

**À améliorer** :

- **1.2.1** : La dernière phrase (« Sinon, utilisez un DD fixe… ») est orpheline après « Tests opposés » ; la rattacher au bon paragraphe ou à une sous-section.
- **Phrases longues** : relire les blocs denses (ex. 1.2.5 « Hors combat les joueurs… ») et les découper si nécessaire.
- **Exemples de niveau** : en 2.2.1, l’exemple « Eniripsa avec Sagesse +5 » peut laisser penser que c’est possible au niveau 1 ; préciser « à haut niveau » ou donner un niveau (ex. niveau 10).

---

## 4. Enchaînement logique

- **Table des matières** : structure claire (1 → 2 → 3 → 4 → 5).
- **Liens entre parties** : beaucoup de « Pour plus de détails » avec liens relatifs ; à vérifier que tous les liens sont valides (chemins, ancres).
- **2.1** (Introduction à la création) : les liens vers « 2.3 » et « 2.4 » pointent vers des dossiers (`2.3-choisir-sa-classe/`, `2.4-choisir-sa-specialisation/`) ; s’assurer qu’ils ciblent bien les bons fichiers `.md`.

---

## 5. Liens entre les différentes parties

- Index alphabétique (INDEX.md) et table des matières (TABLE_DES_MATIERES.md) : à garder à jour après toute modification de structure ou de titres.
- **TABLE_DES_MATIERES.md** : format redondant (ex. « - **1.1.1** 1.1.1. Concept général »). Préférer : « - **1.1.1** Concept général » pour alléger.

---

## 6. Mise en page et harmonisation

- **Structure des fichiers** : conforme à FORMAT_REGLES (Description, Règles rapides, Contenu, sous-sections, À retenir, Sources).
- **Encadrés** : usage cohérent de `> **💡 Conseil MJ**`, `> **⚠️ Règle importante**`, etc.
- **Numérotation** : 1.2.1.1, 1.2.1.2, etc. — à vérifier sur l’ensemble pour aucune rupture.
- **1.2.5** : un paragraphe très long (ligne ~154) mélange plusieurs idées ; à découper en phrases plus courtes et éventuellement en sous-sections.

---

## 7. Exhaustivité (réponses aux questions en partie)

**Thèmes bien couverts** : création de personnage, caractéristiques, classes, équipement, combat (tour, PA/PM, tacle/fuite), sorts, compétences, monde (créatures, métiers, économie), ressources MJ, équilibrage.

**Points à compléter ou à vérifier** :

- **Mort et retour à la vie** : 3.2.4 (Gérer la santé) aborde le jet contre la mort ; s’assurer que mort définitive, résurrection et conséquences sont clairement décrites.
- **Conditions de victoire / fuite** : comment terminer un combat (fuite, reddition, objectif) ; rappel possible en 3.2 ou 5.1.
- **Réactions et coût** : 1.2.3 indique que les réactions « coûtent des PA de la réserve de Wakfu » ; vérifier la cohérence avec 3.2.2 et 3.2.3 (réactions qui consomment des PA).
- **FAQ / Erreurs courantes** : FORMAT_REGLES prévoit des encadrés « Erreurs courantes » ; vérifier qu’ils sont présents sur les sujets sensibles (initiative, tacle, round/tour, PA/PM).

---

## 8. Checklist avant publication

- [x] Initiative : toutes les mentions en **Intelligence** (1.2.2, 3.2.1, 4.2.2, FORMAT_REGLES) — *corrigé*.
- [x] Durée du round : **6 secondes** partout (1.2.3, 1.2.5, 3.1.1, 3.1.2) — *corrigé*.
- [x] Attaques d’opportunité : règles rapides 3.2.3 corrigées (aucune attaque d’opportunité) — *corrigé*.
- [x] FORMAT_REGLES : section Ton (tutoiement, style Ankama) ajoutée — *corrigé*.
- [x] TABLE_DES_MATIERES : format des lignes simplifié (sans double numéro) — *corrigé*.
- [ ] Liens 2.1 : vérifier les chemins 2.3 et 2.4.
- [x] 1.2.1 : phrase orpheline sur le DD fixe rattachée — *corrigé*.
- [ ] Relecture ton : au moins chapitres 1 et 2 en tutoiement (à faire progressivement).
- [x] 1.2.3 : typo « coute » → « coûtent », formulation réactions — *corrigé*.

---

*Document généré pour la préparation à la publication des règles KrosmozJDR. À mettre à jour au fil des corrections.*
