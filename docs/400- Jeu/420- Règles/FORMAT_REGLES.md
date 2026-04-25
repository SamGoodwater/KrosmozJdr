# Guide de Rédaction des Règles – KrosmozJDR

Ce document sert de référence pour toute personne qui contribue à la rédaction ou à la mise à jour des règles.

**Navigation** : [Table des matières](TABLE_DES_MATIERES.md) · [Index](INDEX.md) · [Clés caractéristiques](REFERENCE_CLES_CARACTERISTIQUES.md) Il décrit le format attendu, les bonnes pratiques et les éléments à inclure afin de garantir l'homogénéité du livre de règles.

---

## 0. Ton et personne

- **Tutoiement** : On s'adresse au lecteur (joueur ou MJ) avec **tu / ton / ta**. Ex. : « Tu peux répartir tes points… », « Si ton personnage est taclé… ».
- **Style** : Ton léger et accessible, à la façon d'Ankama : clins d'œil possibles, pas de ton scolaire ou guindé, tout en restant clair et précis pour le jeu.
- **Cohérence** : Utiliser le même ton dans tout le livre (éviter de mélanger tutoiement et vouvoiement).

---

## 1. Structure générale d'un fichier

Chaque fichier Markdown doit suivre cette structure :

```markdown
# Titre (avec numérotation)

**Description** : Résumé en 1 à 3 phrases de la section.

> **Règles rapides** (facultatif)
> - Point clé 1
> - Point clé 2

## Sous-section

### Sous-sous-section

[Contenu détaillé, listes, tableaux, exemples]

> **💡 Conseil MJ**
> [Astuce ou clarification]

### À retenir

- Point important 1
- Point important 2

---

## Sources

## Source : Nom de la source
**Provenance** : `chemin/vers/la/source.ext`
```

### Règles générales

- Toujours inclure la **Description** au début.
- Utiliser `##` pour les sous-sections, `###` pour les sous-sous-sections.
- Terminer par `---` puis la liste des sources.
- Ajouter des liens vers d'autres sections lorsque pertinent (voir section 4).

---

## 2. Bonnes pratiques de contenu

### 2.1. Exemples concrets

Ajouter des exemples dès qu'une règle pourrait être ambiguë.

```markdown
**Exemple – Tour de combat**
> *Contexte* : Iop niveau 5 attaque un Bouftou.
> 1. Initiative : 1d20 + Intelligence = 16 → joue en premier.
> 2. Déplacement : 2 cases (2 PM).
> 3. Attaque : 1d20 + mod. + maîtrise = 19 vs CA 10 → touche.
> 4. Dégâts : 1d8 + 3 = 8 dégâts → Bouftou à 22 PV.
```

### 2.2. Conseils, variantes, erreurs courantes

- **Conseils MJ** : encadrés pour l'équilibrage ou les adaptations.
- **Variantes optionnelles** : règles alternatives pour différents styles de jeu.
- **Erreurs courantes** : aide-mémoire pour éviter les pièges fréquents.

### 2.3. Tables de référence rapide

Utiliser des tableaux synthétiques pour les valeurs clés (PA, DD, dégâts, PV, etc.).

```markdown
| Action            | Coût  | Description                     |
|-------------------|------:|---------------------------------|
| Attaquer          | 3-4 PA| Attaque de base                 |
| Sort puissant     | 5 PA  | Sort en zone / effets importants|
| Se déplacer       | 1 PM  | Déplacement d'une case          |
```

---

## 3. Formatage Markdown

### 3.1. Titres et numérotation

- `# 2.2.3. Répartition des points`
- `## 2.2.3.1. Budget initial`
- `### Exemple de répartition`

### 3.2. Listes

- Préférer les listes à puces pour les options.
- Utiliser des listes numérotées pour les étapes.

### 3.3. Encadrés et notes

Utiliser des blocs de citation avec des icônes :

- `> **⚠️ Règle importante**`
- `> **💡 Conseil MJ**`
- `> **📝 Note**`
- `> **✅ Astuce**`

### 3.4. Tableaux

- Toujours aligner les colonnes avec `|:---:|`.
- Ajouter des colonnes “Min/Max” si nécessaire.

---

## 4. Liens croisés et navigation

- Ajouter des liens vers les sections pertinentes :

```markdown
Pour plus de détails, consulte :
- [Section 2.2.1 – Caractéristiques principales](../2.2-les-caracteristiques/2.2.1-caracteristiques-principales.md)
- [Section 2.6 – S'équiper](../2.6-s-equiper/2.6.1-equipements-de-base.md)
```

- Utiliser des ancres internes lorsque possible (`[voir section 3.2.2](#3-2-2---tour-de-jeu)`).
- **Navigation globale** : renvoie aussi vers [TABLE_DES_MATIERES.md](TABLE_DES_MATIERES.md), [INDEX.md](INDEX.md) et [REFERENCE_CLES_CARACTERISTIQUES.md](REFERENCE_CLES_CARACTERISTIQUES.md) quand tu documentes un concept transversal.

### 4.1. Fichiers Markdown ↔ pages CMS (import TOC)

La commande `php artisan pages:import-rules-toc` (alias `project:data:import-rules-toc`) lit [TABLE_DES_MATIERES.md](TABLE_DES_MATIERES.md) et les fichiers `N.N.N-*.md` pour créer / mettre à jour les **pages** et **sections** du site.

- Les **liens relatifs** entre `.md` (dossier `420- Règles/`) restent la référence pour la lecture dans le dépôt et pour l’import (conversion Markdown → HTML des sections).
- Les **liens profonds côté site** utilisent l’identifiant de section en base : `/pages/{slug}#section-{id}` (l’`id` est attribué à l’import ; il n’est en général **pas** figé dans les sources Markdown du dépôt). Pour le texte riche **édité dans le CMS** avec références riches, utilise le sélecteur de pages/sections (`@`) plutôt que de deviner l’`id`.

---

## 5. Exemples d'application

### 5.1. Ajout d'un exemple de tour de combat

```markdown
### Exemple de tour complet

**Situation** : Cra niveau 3 (PA 6, PM 3) affronte un Bouftou (CA 11, 24 PV).

1. **Initiative** : 1d20 + Intelligence = 12 → joue après le Bouftou.
2. **Début de tour** : Récupère 6 PA, 3 PM.
3. **Action 1** : Lance \"Flèche Magique\" (3 PA, portée 7). Jet d'attaque 15 vs CA 11 → touche. Dégâts = 1d6 + 2 = 6.
4. **Action 2** : Se déplace de 3 cases (3 PM) pour garder ses distances.
5. **Action bonus** : Utilise \"Esquive\" (2 PA) pour gagner +2 CA jusqu'à son prochain tour.
```

### 5.2. Ajout d'un exemple de test

```markdown
### Exemple – Test opposé

**Contexte** : Un voleur tente de se faufiler (Discrétion) devant un garde (Perception).

- **Voleur** : 1d20 + Agilité (+2) + Maîtrise (+2) = 16.
- **Garde** : 1d20 + Sagesse (+1) + Maîtrise (+1) = 13.

**Résultat** : Le voleur passe inaperçu.
```

---

## 6. Checklist avant validation

- [ ] Description présente
- [ ] Règles rapides (si pertinent)
- [ ] Exemples concrets ajoutés
- [ ] Tableau(s) de référence si nécessaire
- [ ] Encadrés (Conseils, Variantes, Erreurs courantes)
- [ ] Liens vers sections connexes
- [ ] Sources listées
- [ ] Formatage cohérent (titres, listes, tableaux)
- [ ] Clés caractéristiques (voir §7) aux endroits données / outils

---

## 7. Caractéristiques : nom lisible + clé système

Pour rester aligné avec la BDD et les mentions `@` :

1. **Table de correspondance** : [REFERENCE_CLES_CARACTERISTIQUES.md](REFERENCE_CLES_CARACTERISTIQUES.md).
2. **Rédaction** : à la première mention notable d’une stat dans une sous‑section, indique la clé entre parenthèses avec **backticks** :

```markdown
### Points d'Action (PA)

La ressource **Points d'action** (`action_points_creature`) définit…
```

3. Les **exemples narratifs** peuvent garder « PA », « PM » seuls si le contexte est évident ; réintroduis la clé lorsque tu parles de **données**, d’**équipement** ou d’**API / outils**.

---

En suivant ce guide, nous garantissons une qualité homogène sur l'ensemble du livre de règles et facilitons la contribution de nouveaux rédacteurs.

