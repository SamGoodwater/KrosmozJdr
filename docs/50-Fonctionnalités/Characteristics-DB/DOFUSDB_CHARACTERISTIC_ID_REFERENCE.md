# Référence : id caractéristique DofusDB ↔ nom et KrosmozJDR

Ce document liste **toutes** les caractéristiques exposées par l’API DofusDB [`GET /characteristics`](https://api.dofusdb.fr/characteristics?$skip=0&visible=true&$limit=50), avec leur **keyword**, le **nom (fr)** et la correspondance **characteristic_key** KrosmozJDR lorsqu’elle existe.

- **dofusdb_characteristic_id** : champ `id` de l’API (et `item.effects[].characteristic`).
- **characteristic_key** : clé utilisée dans KrosmozJDR (groupe object) ; « — » = non utilisée dans KrosmozJDR.

Source : API DofusDB (dernière récupération documentée). Pour mettre à jour, relancer une requête sur `/characteristics` avec `$limit` suffisant.

---

## Tableau complet : id → keyword, nom (fr), characteristic_key

| id | keyword | nom (fr) | characteristic_key (KrosmozJDR) |
|----|---------|----------|----------------------------------|
| -1 | unknown | Inconnue | — |
| 0 | hitPoints | Points de vie | pv_max_object |
| 1 | actionPoints | PA | pa_object |
| 3 | statsPoints | Points de caractéristiques | — |
| 4 | spellsPoints | Points de sorts | — |
| 5 | level | Niveau | level_object |
| 10 | strength | Force | strong_object |
| 11 | vitality | Vitalité | vitality_object |
| 12 | wisdom | Sagesse | sagesse_object |
| 13 | chance | Chance | chance_object |
| 14 | agility | Agilité | agi_object |
| 15 | intelligence | Intelligence | intel_object |
| 16 | allDamageBonus | Dommages | — |
| 17 | damageFactor | *(visible: false)* | — |
| 18 | criticalHit | Critique | — |
| 19 | range | Portée | — |
| 20 | magicalReduction | Réduction des dégats magiques | — |
| 21 | physicalReduction | Réduction des dégats physiques | — |
| 22 | experienceBoost | Boost : expérience | — |
| 23 | movementPoints | PM | pm_object |
| 24 | invisibility | Invisibilité | — |
| 25 | damagePercent | Puissance | — |
| 26 | maxSummonedCreaturesBoost | Invocation | invocation_object |
| 27 | DodgeApLostProbability | Esquive PA | esquive_pa_object |
| 28 | DodgeMpLostProbability | Esquive PM | esquive_pm_object |
| 29 | energyPoints | Points d'énergie | — |
| 30 | alignementValue | Valeur d'alignement | — |
| 31 | weaponDamagePercent | Maitrise d'arme | — |
| 32 | physicalDamageBonus | Bonus aux dommages physiques | — |
| 33 | earthElementResistPercent | Terre (%) | res_50_object |
| 34 | fireElementResistPercent | Feu (%) | res_50_object |
| 35 | waterElementResistPercent | Eau (%) | res_50_object |
| 36 | airElementResistPercent | Air (%) | res_50_object |
| 37 | neutralElementResistPercent | Neutre (%) | res_50_object |
| 39 | criticalMiss | Echec critique | — |
| 40 | weight | Pods | weight_object |
| 41 | restrictionOnPlayer | Restriction sur l'acteur | — |
| 42 | restrictionOnOthers | Restriction sur les autres | — |
| 43 | alignementSide | Alignement | — |
| 44 | initiative | Initiative | ini_object |
| 45 | shopPercentReduction | Pourcentage de remise en magasin | — |
| 46 | alignementRank | Rang d'alignement | — |
| 47 | maxEnergyPoints | Maximum de points d'énergie | — |
| 48 | magicFind | Prospection | — |
| 49 | healBonus | Soins | — |
| 50 | reflectDamage | Renvoi | — |
| 51 | energyLoose | Perte d'énergie | — |
| 52 | honourPoints | Points d'honneur | — |
| 53 | disgracePoints | Points de déshonneur | — |
| 54 | earthElementReduction | Terre (fixe) | res_fixe_terre_object |
| 55 | fireElementReduction | Feu (fixe) | res_fixe_feu_object |
| 56 | waterElementReduction | Eau (fixe) | res_fixe_eau_object |
| 57 | airElementReduction | Air (fixe) | res_fixe_air_object |
| 58 | neutralElementReduction | Neutre (fixe) | — |
| 69 | trapDamageBonusPercent | Puissance Pièges | — |
| 70 | trapDamageBonus | Dommages Pièges | — |
| 71 | fakeSkillForStates | État *(visible: false)* | — |
| 72 | soulCaptureBonus | Bonus de capture d'âme | — |
| 73 | rideXPBonus | Bonus d'expérience de monture | — |
| 74 | confusion | Confusion | — |
| 75 | permanentDamagePercent | Érosion | — |
| 76 | unlucky | Poisse | — |
| 77 | maximizeRoll | Maximise les effets aléatoires | — |
| 78 | tackleEvade | Fuite | — |
| 79 | tackleBlock | Tacle | — |
| 80 | allianceAutoAgressRange | Rayon d'auto aggression JcJ | — |
| 81 | allianceAutoAgressResist | Esquive auto aggression JcJ | — |
| 82 | apReduction | Retrait PA | — |
| 83 | mpReduction | Retrait PM | — |
| 84 | pushDamageBonus | Poussée | — |
| 85 | pushDamageReduction | Poussée (fixe) | — |
| 86 | criticalDamageBonus | Critiques | — |
| 87 | criticalDamageReduction | Critiques (fixe) | — |
| 88 | earthDamageBonus | Terre (dommages) | — |
| 89 | fireDamageBonus | Feu (dommages) | — |
| 90 | waterDamageBonus | Eau (dommages) | — |
| 91 | airDamageBonus | Air (dommages) | — |
| 92 | neutralDamageBonus | Neutre (dommages) | — |
| 93 | maxBomb | Nombre max d'invocations de bombes | — |
| 94 | bombComboBonus | Bonus de combo de bombe | — |
| 95 | maxLifePoints | Bonus vitalité (n'enlève pas de PV quand dissipé) | — |
| 96 | shield | Bouclier | — |
| 97 | hitPointLoss | Malus de vie temporaire | — |
| 98 | damagePercentSpell | Bonus de dommage aux sorts (%) | — |
| 99 | extraScale | Taille (fixe) | — |
| 100 | passTurn | PasseSonTour | — |
| 101 | resistPercent | Pourcentage de résistance aux dommages | — |
| 102 | curPermanentDamage | Érosion *(visible: false)* | — |
| 103 | weaponPower | Bonus de puissance pour les armes | — |
| 104 | incomingPercentDamageMultiplicator | Multiplication des dommages reçus | — |
| 105 | incomingPercentHealMultiplicator | Multiplication des dommages reçus en soins | — |
| 106 | glyphPower | Bonus de puissance pour les glyphes | — |
| 107 | dealtDamageMultiplier | Multiplicateur de dommages | — |
| 108 | stopXP | Blocage de gains d'expérience | — |
| 109 | hunter | *(visible: false)* | — |
| 110 | runePower | Bonus de puissance pour les runes | — |
| 120 | dealtDamageMultiplierDistance | Distance (%) | — |
| 121 | receivedDamageMultiplierDistance | Distance (%) (résistance) | — |
| 122 | dealtDamageMultiplierWeapon | Armes (%) | — |
| 123 | dealtDamageMultiplierSpells | Sorts (%) | — |
| 124 | receivedDamageMultiplierMelee | Mêlée (%) (résistance) | — |
| 125 | dealtDamageMultiplierMelee | Mêlée (%) | — |
| 126 | agilityInitialPercent | Agilité % | — |
| 127 | strengthInitialPercent | Force % | — |
| 128 | chanceInitialPercent | Chance % | — |
| 129 | intelligenceInitialPercent | Intelligence % | — |
| 130 | vitalityInitialPercent | Vitalité % | — |
| 131 | wisdomInitialPercent | Sagesse % | — |
| 132 | tackleBlockInitialPercent | Tacle % | — |
| 133 | tackleEvadeInitialPercent | Fuite % | — |
| 134 | actionPointsInitialPercent | PA % | — |
| 135 | movementPointsInitialPercent | PM % | — |
| 136 | apAttackInitialPercent | Retrait PA % | — |
| 137 | mpAttackInitialPercent | Retrait PM % | — |
| 138 | dodgeApLostProbabilityInitialPercent | Esquive PA % | — |
| 139 | dodgeMpLostProbabilityInitialPercent | Esquive PM % | — |
| 140 | extraScalePercent | Scale | — |
| 141 | receivedDamageMultiplierSpells | Sorts (%) (résistance) | — |
| 142 | receivedDamageMultiplierWeapon | Armes (%) (résistance) | — |
| 143 | dealtHealMultiplier | Multiplicateur de soins | — |
| 150 | allDamageMultiplier | Multiplicateur sur tous les dégâts | — |
| 158 | pushDamagePercent | Poussée % | — |
| 199 | StopDrop | Blocage de drop | — |

---

## Légende

- **id** : identifiant dans l’API DofusDB (`/characteristics`) et dans `item.effects[].characteristic`.
- **keyword** : identifiant texte côté DofusDB.
- **nom (fr)** : libellé français dans l’API.
- **characteristic_key** : clé du seeder KrosmozJDR (groupe object) si le mapping existe dans `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json` ; **—** sinon (non utilisée dans KrosmozJDR).

Quelques lignes marquées *(visible: false)* dans l’API sont tout de même listées pour exhaustivité.

---

## Fichiers liés

- **Mapping utilisé en extraction** : `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json`
- **Structure du JSON d’extraction** : [STRUCTURE_JSON_OBJECT_SAMPLES.md](./STRUCTURE_JSON_OBJECT_SAMPLES.md)
- **API DofusDB** : https://api.dofusdb.fr/characteristics
