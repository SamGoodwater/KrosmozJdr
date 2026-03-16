# Inventaire des modifications — Caractéristiques principales

Document de référence listant toutes les modifications à appliquer suite à la refonte des caractéristiques principales (fiches Caractéristiques, fiche personnage, Equipements et forgemagie).

---

## 1. Scores et limites

| Élément | Avant | Après |
|--------|-------|-------|
| **Score max base** | 31 | **24** |
| **Progression** | +1 aux niv. 2, 4, 6, 9, 12, 15, 18, 20 (8 points) | +1 aux niv. **3, 6, 9, 12, 15, 18** (6 points) |
| **Budget total** | 10 initiaux + 8 progression = 18 | **10 initiaux + 6 progression = 16** |
| **Équipement max par carac** | +4 (règles 2.6.1) / +8 (PDF) | **+6** |
| **Forgemagie max par carac** | +2 | **+2** |
| **Modificateur max** | floor(Niveau/2)+1 (jusqu'à 11 à niv. 20) | **min(floor(Niveau/2)+1, 7)** — progression limitée puis plafond 7 |

---

## 2. Formules modificateur

**Formule inchangée** : `floor((Score − 10) / 2)`

**Plafond** : Le modificateur ne peut pas dépasser `min(floor(Niveau/2)+1, 7)` — limite de progression pour éviter de monter trop vite, avec plafond absolu à 7.

| Niveaux | Mod max |
|---------|---------|
| 1-2 | +2 |
| 3-4 | +3 |
| 5-6 | +4 |
| 7-8 | +5 |
| 9-10 | +6 |
| 11-20 | +7 |

**Formule complète** : `min(max(floor(([carac]_creature-10)/2), -2), min(floor([level_creature]/2)+1, 7))`

---

## 3. Caractéristiques secondaires impactées

Les max affichés utilisaient 11 (mod max) ; ils passent à 7 :

| Caractéristique | Formule | Max avant (mod 11) | Max après (mod 7) |
|-----------------|---------|-------------------|-------------------|
| **CA** | 10 + mod. Vitalité + bouclier | 21 + 5 = 26 | **17 + 5 = 22** |
| **Esquive PA/PM** | 8 + mod. Sagesse + équip. | 19 + 5 = 24 | **15 + 5 = 20** |
| **Tacle** | 1d20 + mod. Chance + équip. | 11 + 10 | **7 + 10 = 17** |
| **Fuite** | 1d20 + mod. Agilité + équip. | 11 + 10 | **7 + 10 = 17** |
| **Bonus de touche** | 1d20 + mod. + équip. | 11 + 5 | **7 + 5 = 12** |
| **Jets de sauvegarde** | 1d20 + mod. + maîtrise + équip. | 11 + 6 + 3 | **7 + 6 + 3 = 16** |

---

## 4. Fichiers à modifier

### Seeders
- `characteristic_creature.php` : max 24 pour les 6 carac principales, max 7 pour modificateurs, formules mod avec `min(..., 7)`, max des secondaires
- `characteristic_object.php` : vérifier max équipement (+6, forgemagie +2)

### Règles
- `2.2.1-caracteristiques-principales.md` : score max 24, progression niv. 3/6/9/12/15/18, mod max 7, équip. +6, forgemagie +2
- `2.2.2-caracteristiques-secondaires.md` : max CA, Esquive, Tacle, Fuite, sauvegardes
- `2.6.1-equipements-de-base.md` : chapeaux/capes +6 max

### Fiche personnage
- Mise à jour manuelle si nécessaire (valeurs max, progression)

---

## 5. Bonus de critique et bonus de soin (nouveaux)

### Bonus de critique (créatures)
- **Base** : 0 (critique sur nat 20)
- **Min** : 0 (nat 20 uniquement)
- **Formule** : critique si d20 >= 20 + bonus_critique
- **Équipement** : Amulettes (valeurs 0 à 3 ; affichage : seuil = 20 - bonus)

### Bonus de soin (créatures)
- **Base** : 0
- **Max** : 7 (équipement + forgemagie)
- **Usage** : ajouté à chaque soin

---

*Document généré dans le cadre de la refonte caractéristiques + critique/soin créatures.*
