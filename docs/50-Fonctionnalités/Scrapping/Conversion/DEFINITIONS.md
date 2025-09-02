# Définitions des Conversions Dofus vers KrosmozJDR

## 📋 Objectif

Ce document détaille les règles de conversion des caractéristiques de jeu de Dofus vers KrosmozJDR. Il fournit des formules de conversion pour chaque caractéristique selon le type d'entité (créature, PNJ, joueur) et le niveau.

**Note** : Les formules présentées sont des approximations basées sur les mécaniques de Dofus et les objectifs d'équilibrage de KrosmozJDR. Elles devront être ajustées lors de l'implémentation et des tests.

## 🎯 Principes généraux

### Types d'entités
- **Joueur** : Personnage contrôlé par un joueur
- **PNJ** : Personnage non-joueur (marchands, donneurs de quêtes, etc.)
- **Créature** : Monstres, animaux, créatures sauvages

### Variables utilisées
- `level` : Niveau de l'entité (1-20 pour KrosmozJDR, 1-200 pour Dofus)
- `base_value` : Valeur de base de la caractéristique
- `bonus_equipment` : Bonus d'équipement (pour les joueurs)
- `class_modifier` : Modificateur de classe (pour les joueurs)

## 🔄 Caractéristiques de base

### 1. Niveau

#### Valeurs Dofus
- **Joueur** : 1-200
- **PNJ** : 1-200
- **Créature** : 1-200

#### Valeurs KrosmozJDR cibles
- **Joueur** : 1-20
- **PNJ** : 1-20
- **Créature** : 1-20

#### Formules de conversion
```php
// Tous les types
Niveau_KrosmozJDR = Niveau_Dofus / 10
```

### 2. Points de Vie (PV)

#### Valeurs Dofus
- **Joueur niveau 1** : 50-100 PV
- **Joueur niveau 50** : 500-1000 PV
- **Joueur niveau 100** : 1000-2000 PV
- **Joueur niveau 200** : 2000-4000 PV
- **PNJ niveau 1** : 30-80 PV
- **PNJ niveau 50** : 300-800 PV
- **Créature niveau 1** : 20-60 PV
- **Créature niveau 50** : 200-600 PV

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 5-10 PV
- **Joueur niveau 5** : 15-25 PV
- **Joueur niveau 10** : 25-40 PV
- **Joueur niveau 20** : 40-60 PV
- **PNJ niveau 1** : 3-8 PV
- **PNJ niveau 5** : 10-20 PV
- **Créature niveau 1** : 2-6 PV
- **Créature niveau 5** : 8-15 PV

#### Formules de conversion
```php
// Joueur
PV_KrosmozJDR = max(5, (PV_Dofus / 100) + (level * 1))

// PNJ
PV_KrosmozJDR = max(3, (PV_Dofus / 120) + (level * 0.8))

// Créature
PV_KrosmozJDR = max(2, (PV_Dofus / 80) + (level * 0.6))
```

### 3. Points d'Action (PA)

#### Valeurs Dofus
- **Joueur niveau 1** : 6 PA
- **Joueur niveau 50** : 6-8 PA
- **Joueur niveau 100** : 6-10 PA
- **Joueur niveau 200** : 6-12 PA
- **PNJ niveau 1** : 6 PA
- **PNJ niveau 50** : 6-8 PA
- **Créature niveau 1** : 4-6 PA
- **Créature niveau 50** : 4-8 PA

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 3 PA
- **Joueur niveau 5** : 3-4 PA
- **Joueur niveau 10** : 3-5 PA
- **Joueur niveau 20** : 3-6 PA
- **PNJ niveau 1** : 3 PA
- **PNJ niveau 5** : 3-4 PA
- **Créature niveau 1** : 2-3 PA
- **Créature niveau 5** : 2-4 PA

#### Formules de conversion
```php
// Joueur
PA_KrosmozJDR = max(3, PA_Dofus / 2)

// PNJ
PA_KrosmozJDR = max(3, PA_Dofus / 2)

// Créature
PA_KrosmozJDR = max(2, PA_Dofus / 2)
```

### 4. Points de Mouvement (PM)

#### Valeurs Dofus
- **Joueur niveau 1** : 3 PM
- **Joueur niveau 50** : 3-4 PM
- **Joueur niveau 100** : 3-5 PM
- **Joueur niveau 200** : 3-6 PM
- **PNJ niveau 1** : 3 PM
- **PNJ niveau 50** : 3-4 PM
- **Créature niveau 1** : 2-4 PM
- **Créature niveau 50** : 2-5 PM

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 3 PM
- **Joueur niveau 5** : 3-4 PM
- **Joueur niveau 10** : 3-5 PM
- **Joueur niveau 20** : 3-6 PM
- **PNJ niveau 1** : 3 PM
- **PNJ niveau 5** : 3-4 PM
- **Créature niveau 1** : 2-4 PM
- **Créature niveau 5** : 2-5 PM

#### Formules de conversion
```php
// Tous les types
PM_KrosmozJDR = PM_Dofus
```

### 5. Force / Intelligence / Chance / Agilité

#### Valeurs Dofus
- **Joueur niveau 1** : 10-20
- **Joueur niveau 50** : 100-200
- **Joueur niveau 100** : 200-300
- **Joueur niveau 200** : 300-400
- **PNJ niveau 1** : 8-15
- **PNJ niveau 50** : 80-150
- **Créature niveau 1** : 5-12
- **Créature niveau 50** : 50-120

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 1-2
- **Joueur niveau 5** : 8-15
- **Joueur niveau 10** : 15-25
- **Joueur niveau 20** : 25-35
- **PNJ niveau 1** : 1-2
- **PNJ niveau 5** : 6-12
- **Créature niveau 1** : 1-2
- **Créature niveau 5** : 5-10

#### Formules de conversion
```php
// Joueur
Caractéristique_KrosmozJDR = max(1, (Caractéristique_Dofus / 10) + (level * 0.5))

// PNJ
Caractéristique_KrosmozJDR = max(1, (Caractéristique_Dofus / 12) + (level * 0.4))

// Créature
Caractéristique_KrosmozJDR = max(1, (Caractéristique_Dofus / 8) + (level * 0.3))
```

### 6. Vitalité / Sagesse

#### Valeurs Dofus
- **Joueur niveau 1** : 10-20
- **Joueur niveau 50** : 100-200
- **Joueur niveau 100** : 200-300
- **Joueur niveau 200** : 300-400
- **PNJ niveau 1** : 8-15
- **PNJ niveau 50** : 80-150
- **Créature niveau 1** : 5-12
- **Créature niveau 50** : 50-120

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 1-2
- **Joueur niveau 5** : 8-15
- **Joueur niveau 10** : 15-25
- **Joueur niveau 20** : 25-35
- **PNJ niveau 1** : 1-2
- **PNJ niveau 5** : 6-12
- **Créature niveau 1** : 1-2
- **Créature niveau 5** : 5-10

#### Formules de conversion
```php
// Joueur
Vitalité_KrosmozJDR = max(1, (Vitalité_Dofus / 10) + (level * 0.5))

// PNJ
Vitalité_KrosmozJDR = max(1, (Vitalité_Dofus / 12) + (level * 0.4))

// Créature
Vitalité_KrosmozJDR = max(1, (Vitalité_Dofus / 8) + (level * 0.3))
```

### 7. Invocations

#### Valeurs Dofus
- **Joueur niveau 1** : 1 invocation
- **Joueur niveau 50** : 1-3 invocations
- **Joueur niveau 100** : 1-4 invocations
- **Joueur niveau 200** : 1-6 invocations
- **PNJ niveau 1** : 1 invocation
- **PNJ niveau 50** : 1-2 invocations
- **Créature niveau 1** : 0-1 invocation
- **Créature niveau 50** : 0-2 invocations

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 1 invocation
- **Joueur niveau 5** : 1-3 invocations
- **Joueur niveau 10** : 1-4 invocations
- **Joueur niveau 20** : 1-6 invocations
- **PNJ niveau 1** : 1 invocation
- **PNJ niveau 5** : 1-2 invocations
- **Créature niveau 1** : 0-1 invocation
- **Créature niveau 5** : 0-2 invocations

#### Formules de conversion
```php
// Joueur
Invocations_KrosmozJDR = min(6, max(1, Invocations_Dofus))

// PNJ
Invocations_KrosmozJDR = min(6, max(1, Invocations_Dofus))

// Créature
Invocations_KrosmozJDR = min(6, max(0, Invocations_Dofus))
```

## 🔄 Caractéristiques de combat

### 8. Initiative

#### Valeurs Dofus
- **Joueur niveau 1** : 100-200
- **Joueur niveau 50** : 500-1000
- **Joueur niveau 100** : 1000-2000
- **Joueur niveau 200** : 2000-4000
- **PNJ niveau 1** : 80-150
- **PNJ niveau 50** : 400-800
- **Créature niveau 1** : 50-100
- **Créature niveau 50** : 250-500

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 2-4
- **Joueur niveau 5** : 8-15
- **Joueur niveau 10** : 15-25
- **Joueur niveau 20** : 25-40
- **PNJ niveau 1** : 2-3
- **PNJ niveau 5** : 6-12
- **Créature niveau 1** : 1-2
- **Créature niveau 5** : 4-8

#### Formules de conversion
```php
// Joueur
Initiative_KrosmozJDR = max(2, (Initiative_Dofus / 50) + (level * 0.8))

// PNJ
Initiative_KrosmozJDR = max(2, (Initiative_Dofus / 60) + (level * 0.6))

// Créature
Initiative_KrosmozJDR = max(1, (Initiative_Dofus / 40) + (level * 0.4))
```

### 9. Touché

#### Valeurs Dofus
- Inexistante

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-1
- **Joueur niveau 5** : 2-5
- **Joueur niveau 10** : 5-10
- **Joueur niveau 20** : 10-20
- **PNJ niveau 1** : 0-1
- **PNJ niveau 5** : 1-4
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 1-3

#### Formules de conversion
```php
// Tous les types - Calculé à partir d'autres caractéristiques
Touché_KrosmozJDR = max(0, (level * 0.5) + (Agilité_KrosmozJDR * 0.3))
```

### 10. Classe d'Armure (CA)

#### Valeurs Dofus
- Inexistante

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-1
- **Joueur niveau 5** : 2-5
- **Joueur niveau 10** : 5-10
- **Joueur niveau 20** : 10-20
- **PNJ niveau 1** : 0-1
- **PNJ niveau 5** : 1-4
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 1-3

#### Formules de conversion
```php
// Tous les types - Calculé à partir d'autres caractéristiques
CA_KrosmozJDR = max(0, (level * 0.5) + (Vitalité_KrosmozJDR * 0.3))
```

### 11. Esquive PA

#### Valeurs Dofus
- **Joueur niveau 1** : 0-20
- **Joueur niveau 50** : 50-100
- **Joueur niveau 100** : 100-200
- **Joueur niveau 200** : 200-400
- **PNJ niveau 1** : 0-15
- **PNJ niveau 50** : 40-80
- **Créature niveau 1** : 0-10
- **Créature niveau 50** : 25-50

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-2
- **Joueur niveau 5** : 4-8
- **Joueur niveau 10** : 8-15
- **Joueur niveau 20** : 15-25
- **PNJ niveau 1** : 0-2
- **PNJ niveau 5** : 3-6
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 2-4

#### Formules de conversion
```php
// Joueur
EsquivePA_KrosmozJDR = max(0, (EsquivePA_Dofus / 10) + (level * 0.6))

// PNJ
EsquivePA_KrosmozJDR = max(0, (EsquivePA_Dofus / 12) + (level * 0.5))

// Créature
EsquivePA_KrosmozJDR = max(0, (EsquivePA_Dofus / 8) + (level * 0.4))
```

### 12. Esquive PM

#### Valeurs Dofus
- **Joueur niveau 1** : 0-20
- **Joueur niveau 50** : 50-100
- **Joueur niveau 100** : 100-200
- **Joueur niveau 200** : 200-400
- **PNJ niveau 1** : 0-15
- **PNJ niveau 50** : 40-80
- **Créature niveau 1** : 0-10
- **Créature niveau 50** : 25-50

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-2
- **Joueur niveau 5** : 4-8
- **Joueur niveau 10** : 8-15
- **Joueur niveau 20** : 15-25
- **PNJ niveau 1** : 0-2
- **PNJ niveau 5** : 3-6
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 2-4

#### Formules de conversion
```php
// Joueur
EsquivePM_KrosmozJDR = max(0, (EsquivePM_Dofus / 10) + (level * 0.6))

// PNJ
EsquivePM_KrosmozJDR = max(0, (EsquivePM_Dofus / 12) + (level * 0.5))

// Créature
EsquivePM_KrosmozJDR = max(0, (EsquivePM_Dofus / 8) + (level * 0.4))
```

### 13. Tacle

#### Valeurs Dofus
- **Joueur niveau 1** : 0-20
- **Joueur niveau 50** : 50-100
- **Joueur niveau 100** : 100-200
- **Joueur niveau 200** : 200-400
- **PNJ niveau 1** : 0-15
- **PNJ niveau 50** : 40-80
- **Créature niveau 1** : 0-10
- **Créature niveau 50** : 25-50

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-2
- **Joueur niveau 5** : 4-8
- **Joueur niveau 10** : 8-15
- **Joueur niveau 20** : 15-25
- **PNJ niveau 1** : 0-2
- **PNJ niveau 5** : 3-6
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 2-4

#### Formules de conversion
```php
// Joueur
Tacle_KrosmozJDR = max(0, (Tacle_Dofus / 10) + (level * 0.6))

// PNJ
Tacle_KrosmozJDR = max(0, (Tacle_Dofus / 12) + (level * 0.5))

// Créature
Tacle_KrosmozJDR = max(0, (Tacle_Dofus / 8) + (level * 0.4))
```

### 14. Fuite

#### Valeurs Dofus
- **Joueur niveau 1** : 0-20
- **Joueur niveau 50** : 50-100
- **Joueur niveau 100** : 100-200
- **Joueur niveau 200** : 200-400
- **PNJ niveau 1** : 0-15
- **PNJ niveau 50** : 40-80
- **Créature niveau 1** : 0-10
- **Créature niveau 50** : 25-50

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-2
- **Joueur niveau 5** : 4-8
- **Joueur niveau 10** : 8-15
- **Joueur niveau 20** : 15-25
- **PNJ niveau 1** : 0-2
- **PNJ niveau 5** : 3-6
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 2-4

#### Formules de conversion
```php
// Joueur
Fuite_KrosmozJDR = max(0, (Fuite_Dofus / 10) + (level * 0.6))

// PNJ
Fuite_KrosmozJDR = max(0, (Fuite_Dofus / 12) + (level * 0.5))

// Créature
Fuite_KrosmozJDR = max(0, (Fuite_Dofus / 8) + (level * 0.4))
```

## 🔄 Résistances élémentaires

### 15. Résistances (Neutre, Terre, Feu, Air, Eau)

#### Valeurs Dofus
- **Joueur niveau 1** : 0-20%
- **Joueur niveau 50** : 10-40%
- **Joueur niveau 100** : 20-60%
- **Joueur niveau 200** : 30-80%
- **PNJ niveau 1** : 0-15%
- **PNJ niveau 50** : 5-30%
- **Créature niveau 1** : 0-10%
- **Créature niveau 50** : 5-25%

#### Valeurs KrosmozJDR cibles
Pour plus de simplification la résistance est soit : -100% , -50% (vulnérabilité), 0%, 50%, 100% (résistance)

#### Formules de conversion
```php
// Tous les types - Simplification en paliers
function convertResistance($resistance) {
    if ($resistance <= -50) return -100;
    if ($resistance <= -25) return -50;
    if ($resistance <= 25) return 0;
    if ($resistance <= 75) return 50;
    return 100;
}
```

## 🔄 Dégâts élémentaires fixes

### 16. Dégâts fixes (Neutre, Terre, Feu, Air, Eau)

#### Valeurs Dofus
- **Joueur niveau 1** : 0-10
- **Joueur niveau 50** : 20-50
- **Joueur niveau 100** : 50-100
- **Joueur niveau 200** : 100-200
- **PNJ niveau 1** : 0-8
- **PNJ niveau 50** : 15-40
- **Créature niveau 1** : 0-5
- **Créature niveau 50** : 10-30

#### Valeurs KrosmozJDR cibles
- **Joueur niveau 1** : 0-1
- **Joueur niveau 5** : 2-5
- **Joueur niveau 10** : 5-10
- **Joueur niveau 20** : 10-20
- **PNJ niveau 1** : 0-1
- **PNJ niveau 5** : 1-4
- **Créature niveau 1** : 0-1
- **Créature niveau 5** : 1-3

#### Formules de conversion
```php
// Joueur
DégâtsFixes_KrosmozJDR = max(0, (DégâtsFixes_Dofus / 10) + (level * 0.5))

// PNJ
DégâtsFixes_KrosmozJDR = max(0, (DégâtsFixes_Dofus / 12) + (level * 0.4))

// Créature
DégâtsFixes_KrosmozJDR = max(0, (DégâtsFixes_Dofus / 8) + (level * 0.3))
```

## 🚫 Caractéristiques exclues

### Mécaniques Dofus non utilisées dans KrosmozJDR
- **Prospection** : Trouver des objets cachés
- **Chance critique** : Coups critiques
- **Alignement** : Système de moralité
- **Guildes** : Système de guildes
- **Kamas** : Monnaie du jeu (conservée pour les prix uniquement)

### Données techniques
- **ID internes** : Identifiants techniques Dofus
- **Métadonnées** : Données de version, timestamps
- **Données d'interface** : Positions UI, couleurs
- **Données de debug** : Informations de développement

## 🔧 Implémentation

### Fonction de conversion générique
```php
function convertStatistic($value, $type, $level, $baseMultiplier = 1) {
    $typeMultipliers = [
        'joueur' => 1.0,
        'pnj' => 0.8,
        'créature' => 1.2
    ];
    
    $multiplier = $typeMultipliers[$type] ?? 1.0;
    $levelBonus = $level * 0.5; // Ajusté pour le nouveau système de niveau
    
    return max(1, ($value / (10 * $baseMultiplier)) + $levelBonus);
}
```

### Exemple d'utilisation
```php
// Conversion des points de vie d'un joueur niveau 5 (niveau 50 Dofus)
$pvDofus = 1000;
$niveauKrosmozJDR = 50 / 10; // 5
$pvKrosmozJDR = convertStatistic($pvDofus, 'joueur', $niveauKrosmozJDR, 1);
// Résultat : environ 15 PV

// Conversion de la force d'un PNJ niveau 3 (niveau 30 Dofus)
$forceDofus = 150;
$niveauKrosmozJDR = 30 / 10; // 3
$forceKrosmozJDR = convertStatistic($forceDofus, 'pnj', $niveauKrosmozJDR, 1);
// Résultat : environ 12 Force
```

---

**Note importante** : Ces formules sont des approximations destinées à faciliter la conversion initiale. Elles devront être ajustées et validées lors de l'implémentation et des tests d'équilibrage.

*Document de référence pour la conversion Dofus → KrosmozJDR - Version 3.0*
