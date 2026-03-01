<?php

declare(strict_types=1);

/**
 * Mappings effectId DofusDB → [sub_effect_slug, characteristic_source, characteristic_key].
 * Généré par : php artisan dofusdb:fetch-effect-mappings --output=database/seeders/data/dofusdb_effect_mappings_suggested.php
 * Utilisé par DofusdbEffectMappingSeeder si le fichier existe.
 * Commentaires : description FR (API) + si source=characteristic, id carac. DofusDB (pour faire le lien vers characteristic_key).
 */

return [
    4 => ['déplacer', 'none', null], // 4 — Téléporte sur la case ciblée
    5 => ['déplacer', 'none', null], // 5 — Repousse de #1 case{{~ps}}
    6 => ['déplacer', 'none', null], // 6 — Attire de #1 case{{~ps}}
    9 => ['déplacer', 'none', null], // 9 — Esquive #1% des coups en reculant de #2 case(s)
    77 => ['booster', 'characteristic', null], // 77 — Vole #1{{~1~2 à }}#2 PM
    79 => ['frapper', 'element', null], // 79 — #3% soigné de x#2, sinon dégâts subis x#1
    80 => ['frapper', 'element', null],
    81 => ['soigner', 'element', null], // 81 — #1{{~1~2 à }}#2 soins
    82 => ['frapper', 'element', null], // 82 — #1{{~1~2 à }}#2 vol de vie Neutre (fixe)
    84 => ['booster', 'characteristic', null], // 84 — Vole #1{{~1~2 à }}#2 PA
    85 => ['frapper', 'element', null], // 85 — Dommages Eau : #1{{~1~2 à }}#2% <sprite name="PV"> PV du lanceur
    86 => ['frapper', 'element', null], // 86 — Dommages Terre : #1{{~1~2 à }}#2% <sprite name="PV"> PV du lanceur
    87 => ['frapper', 'element', null], // 87 — Dommages Air : #1{{~1~2 à }}#2% <sprite name="PV"> PV du lanceur
    88 => ['frapper', 'element', null], // 88 — Dommages Feu : #1{{~1~2 à }}#2% <sprite name="PV"> PV du lanceur
    89 => ['frapper', 'element', null], // 89 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="PV"> PV du lanceur
    91 => ['frapper', 'element', null], // 91 — #1{{~1~2 à }}#2 vol Eau
    92 => ['frapper', 'element', null], // 92 — #1{{~1~2 à }}#2 vol Terre
    93 => ['frapper', 'element', null], // 93 — #1{{~1~2 à }}#2 vol Air
    94 => ['frapper', 'element', null], // 94 — #1{{~1~2 à }}#2 vol Feu
    95 => ['frapper', 'element', null], // 95 — #1{{~1~2 à }}#2 vol Neutre
    96 => ['frapper', 'element', null], // 96 — #1{{~1~2 à }}#2 dommages Eau
    97 => ['frapper', 'element', null], // 97 — #1{{~1~2 à }}#2 dommages Terre
    98 => ['frapper', 'element', null], // 98 — #1{{~1~2 à }}#2 dommages Air
    99 => ['frapper', 'element', null], // 99 — #1{{~1~2 à }}#2 dommages Feu
    100 => ['frapper', 'element', null], // 100 — #1{{~1~2 à }}#2 dommages Neutre
    105 => ['frapper', 'element', null], // 105 — -#1{{~1~2 à }}#2 dommages reçus
    107 => ['frapper', 'element', null], // 107 — #1{{~1~2 à }}#2 Dommages Renvoyés
    108 => ['frapper', 'element', null], // 108 — #1{{~1~2 à }}#2 soins Feu
    109 => ['frapper', 'element', null], // 109 — #1{{~1~2 à }}#2 (dommages au lanceur)
    110 => ['soigner', 'element', null], // 110 — Rend #1{{~1~2 à }}#2 points de vie
    111 => ['booster', 'characteristic', 'pa'], // 111 — #1{{~1~2 à }}#2 PA ; carac DofusDB id=1
    112 => ['frapper', 'element', null], // 112 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}}
    113 => ['frapper', 'element', null], // 113 — Double les dommages ou rend  #1{{~1~2 à }}#2 PDV
    115 => ['booster', 'characteristic', 'critical'], // 115 — #1{{~1~2 à }}#2% Critique ; carac DofusDB id=18
    116 => ['booster', 'characteristic', 'po'], // 116 — -#1{{~1~2 à -}}#2 Portée ; carac DofusDB id=19
    117 => ['booster', 'characteristic', 'po'], // 117 — #1{{~1~2 à }}#2 Portée ; carac DofusDB id=19
    118 => ['booster', 'characteristic', 'strong'], // 118 — #1{{~1~2 à }}#2 Force ; carac DofusDB id=10
    119 => ['booster', 'characteristic', 'agi'], // 119 — #1{{~1~2 à }}#2 Agilité ; carac DofusDB id=14
    121 => ['frapper', 'element', null], // 121 — #1{{~1~2 à }}#2 Dommages
    122 => ['booster', 'characteristic', 'echec_critique'], // 122 — #1{{~1~2 à }}#2 Échecs Critiques ; carac DofusDB id=39
    123 => ['booster', 'characteristic', 'chance'], // 123 — #1{{~1~2 à }}#2 Chance ; carac DofusDB id=13
    124 => ['booster', 'characteristic', 'sagesse'], // 124 — #1{{~1~2 à }}#2 Sagesse ; carac DofusDB id=12
    125 => ['booster', 'characteristic', 'vitality'], // 125 — #1{{~1~2 à }}#2 Vitalité ; carac DofusDB id=11
    126 => ['booster', 'characteristic', 'intel'], // 126 — #1{{~1~2 à }}#2 Intelligence ; carac DofusDB id=15
    128 => ['booster', 'characteristic', 'pm'], // 128 — #1{{~1~2 à }}#2 PM ; carac DofusDB id=23
    132 => ['retirer', 'characteristic', null], // 132 — Enlève les envoûtements
    135 => ['booster', 'characteristic', 'po'], // 135 — Portée du lanceur réduite de : #1{{~1~2 à }}#2 ; carac DofusDB id=19
    136 => ['booster', 'characteristic', 'po'], // 136 — +#1{{~1~2 à }}#2 Portée (lanceur) ; carac DofusDB id=19
    137 => ['frapper', 'element', null], // 137 — Dommages physiques du lanceur augmentés de : #1{{~1~2 à }}#2
    138 => ['booster', 'characteristic', null], // 138 — #1{{~1~2 à }}#2 Puissance ; carac DofusDB id=25
    140 => ['booster', 'characteristic', null], // 140 — Tour annulé ; carac DofusDB id=100
    142 => ['frapper', 'element', null], // 142 — #1{{~1~2 à }}#2 Dommages Physiques
    143 => ['soigner', 'element', null], // 143 — #1{{~1~2 à }}#2 Soins
    144 => ['frapper', 'element', null], // 144 — #1{{~1~2 à }}#2 dommages Neutre (fixe)
    145 => ['frapper', 'element', null], // 145 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}}
    149 => ['booster', 'characteristic', null], // 149 — Change l'apparence ; carac DofusDB id=38
    150 => ['booster', 'characteristic', null], // 150 — Rend la cible invisible ; carac DofusDB id=24
    152 => ['booster', 'characteristic', 'chance'], // 152 — -#1{{~1~2 à -}}#2 Chance ; carac DofusDB id=13
    153 => ['booster', 'characteristic', 'vitality'], // 153 — -#1{{~1~2 à -}}#2 Vitalité ; carac DofusDB id=11
    154 => ['booster', 'characteristic', 'agi'], // 154 — -#1{{~1~2 à -}}#2 Agilité ; carac DofusDB id=14
    155 => ['booster', 'characteristic', 'intel'], // 155 — -#1{{~1~2 à -}}#2 Intelligence ; carac DofusDB id=15
    156 => ['booster', 'characteristic', 'sagesse'], // 156 — -#1{{~1~2 à -}}#2 Sagesse ; carac DofusDB id=12
    157 => ['booster', 'characteristic', 'strong'], // 157 — -#1{{~1~2 à -}}#2 Force ; carac DofusDB id=10
    160 => ['booster', 'characteristic', null], // 160 — #1{{~1~2 à }}#2 Esquive PA ; carac DofusDB id=27
    161 => ['booster', 'characteristic', null], // 161 — #1{{~1~2 à }}#2 Esquive PM ; carac DofusDB id=28
    162 => ['booster', 'characteristic', null], // 162 — -#1{{~1~2 à -}}#2 Esquive PA ; carac DofusDB id=27
    163 => ['booster', 'characteristic', null], // 163 — -#1{{~1~2 à -}}#2 Esquive PM ; carac DofusDB id=28
    164 => ['frapper', 'element', null], // 164 — Dommages réduits de #1%
    165 => ['frapper', 'element', null], // 165 — #2% Dommages #1
    168 => ['booster', 'characteristic', 'pa'], // 168 — -#1{{~1~2 à -}}#2 PA ; carac DofusDB id=1
    169 => ['booster', 'characteristic', 'pm'], // 169 — -#1{{~1~2 à -}}#2 PM ; carac DofusDB id=23
    171 => ['booster', 'characteristic', 'critical'], // 171 — -#1{{~1~2 à -}}#2% Critique ; carac DofusDB id=18
    176 => ['booster', 'characteristic', null], // 176 — #1{{~1~2 à }}#2 Prospection ; carac DofusDB id=48
    177 => ['booster', 'characteristic', null], // 177 — -#1{{~1~2 à -}}#2 Prospection ; carac DofusDB id=48
    178 => ['soigner', 'element', null], // 178 — #1{{~1~2 à }}#2 Soin{{~ps}}{{~zs}}
    179 => ['soigner', 'element', null], // 179 — -#1{{~1~2 à -}}#2 Soin{{~ps}}{{~zs}}
    180 => ['invoquer', 'none', null], // 180 — Invoque un double du lanceur
    181 => ['invoquer', 'none', null], // 181 — Invoque : #1
    182 => ['invoquer', 'none', null], // 182 — #1{{~1~2 à }}#2 Invocation{{~ps}}{{~zs}}
    183 => ['booster', 'characteristic', null], // 183 — #1{{~1~2 à }}#2 Réduction Magique ; carac DofusDB id=20
    184 => ['booster', 'characteristic', null], // 184 — #1{{~1~2 à }}#2 Réduction Physique ; carac DofusDB id=21
    185 => ['invoquer', 'none', null], // 185 — Invoque : #1 (statique)
    186 => ['booster', 'characteristic', null], // 186 — -#1{{~1~2 à -}}#2 Puissance ; carac DofusDB id=25
    210 => ['booster', 'characteristic', 'res_terre'], // 210 — #1{{~1~2 à }}#2% Résistance Terre ; carac DofusDB id=33
    211 => ['booster', 'characteristic', 'res_eau'], // 211 — #1{{~1~2 à }}#2% Résistance Eau ; carac DofusDB id=35
    212 => ['booster', 'characteristic', 'res_air'], // 212 — #1{{~1~2 à }}#2% Résistance Air ; carac DofusDB id=36
    213 => ['booster', 'characteristic', 'res_feu'], // 213 — #1{{~1~2 à }}#2% Résistance Feu ; carac DofusDB id=34
    214 => ['booster', 'characteristic', 'res_neutre'], // 214 — #1{{~1~2 à }}#2% Résistance Neutre ; carac DofusDB id=37
    215 => ['booster', 'characteristic', 'res_terre'], // 215 — -#1{{~1~2 à -}}#2% Résistance Terre ; carac DofusDB id=33
    216 => ['booster', 'characteristic', 'res_eau'], // 216 — -#1{{~1~2 à -}}#2% Résistance Eau ; carac DofusDB id=35
    217 => ['booster', 'characteristic', 'res_air'], // 217 — -#1{{~1~2 à -}}#2% Résistance Air ; carac DofusDB id=36
    218 => ['booster', 'characteristic', 'res_feu'], // 218 — -#1{{~1~2 à -}}#2% Résistance Feu ; carac DofusDB id=34
    219 => ['booster', 'characteristic', 'res_neutre'], // 219 — -#1{{~1~2 à -}}#2% Résistance Neutre ; carac DofusDB id=37
    220 => ['frapper', 'element', null], // 220 — #1{{~1~2 à }}#2 Dommages Renvoyés
    225 => ['frapper', 'element', null], // 225 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Pièges
    240 => ['booster', 'characteristic', null], // 240 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Terre ; carac DofusDB id=54
    241 => ['booster', 'characteristic', null], // 241 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Eau ; carac DofusDB id=56
    242 => ['booster', 'characteristic', null], // 242 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Air ; carac DofusDB id=57
    243 => ['booster', 'characteristic', null], // 243 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Feu ; carac DofusDB id=55
    244 => ['booster', 'characteristic', null], // 244 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Neutre ; carac DofusDB id=58
    245 => ['booster', 'characteristic', null], // 245 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Terre ; carac DofusDB id=54
    246 => ['booster', 'characteristic', null], // 246 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Eau ; carac DofusDB id=56
    247 => ['booster', 'characteristic', null], // 247 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Air ; carac DofusDB id=57
    248 => ['booster', 'characteristic', null], // 248 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Feu ; carac DofusDB id=55
    249 => ['booster', 'characteristic', null], // 249 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Neutre ; carac DofusDB id=58
    265 => ['frapper', 'element', null], // 265 — -#1{{~1~2 à }}#2 dommages reçus
    266 => ['booster', 'characteristic', 'chance'], // 266 — Vole #1{{~1~2 à -}}#2 Chance ; carac DofusDB id=13
    267 => ['booster', 'characteristic', 'vitality'], // 267 — Vole #1{{~1~2 à -}}#2 Vitalité ; carac DofusDB id=11
    268 => ['booster', 'characteristic', 'agi'], // 268 — Vole #1{{~1~2 à -}}#2 Agilité ; carac DofusDB id=14
    269 => ['booster', 'characteristic', 'intel'], // 269 — Vole #1{{~1~2 à -}}#2 Intelligence ; carac DofusDB id=15
    270 => ['booster', 'characteristic', 'sagesse'], // 270 — Vole #1{{~1~2 à -}}#2 Sagesse ; carac DofusDB id=12
    271 => ['booster', 'characteristic', 'strong'], // 271 — Vole #1{{~1~2 à -}}#2 Force ; carac DofusDB id=10
    275 => ['frapper', 'element', null], // 275 — Dommages Eau : #1{{~1~2 à }}#2% <sprite name="erosion"> PV manquants d…
    276 => ['frapper', 'element', null], // 276 — Dommages Terre : #1{{~1~2 à }}#2% <sprite name="erosion"> PV manquants…
    277 => ['frapper', 'element', null], // 277 — Dommages Air : #1{{~1~2 à }}#2% <sprite name="erosion">PV manquants du…
    278 => ['frapper', 'element', null], // 278 — Dommages Feu : #1{{~1~2 à }}#2% <sprite name="erosion"> PV manquants d…
    279 => ['frapper', 'element', null], // 279 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="erosion"> PV manquant…
    280 => ['booster', 'characteristic', null], // 280 — #1 : +#3 Portée minimale
    281 => ['booster', 'characteristic', null], // 281 — #1 : +#3 Portée maximale
    282 => ['booster', 'characteristic', null], // 282 — #1 : Portée modifiable
    283 => ['frapper', 'element', null], // 283 — #1 : +#3 Dommages
    284 => ['soigner', 'element', null], // 284 — #1 : +#3 Soins
    285 => ['booster', 'characteristic', null], // 285 — #1 : -#3 PA
    286 => ['booster', 'characteristic', null], // 286 — #1 : -#3 de relance
    287 => ['booster', 'characteristic', null], // 287 — #1 : +#3% Critique
    288 => ['booster', 'characteristic', null], // 288 — #1 : lancer en ligne désactivé
    289 => ['booster', 'characteristic', null], // 289 — #1 : ligne de vue désactivée
    290 => ['booster', 'characteristic', null], // 290 — #1 : +#3 lancer(s) par tour
    291 => ['booster', 'characteristic', null], // 291 — #1 : +#3 lancer(s) par cible
    292 => ['booster', 'characteristic', null], // 292 — #1 : relance fixée à #3
    293 => ['frapper', 'element', null], // 293 — #1 : +#3 dégâts de base
    294 => ['booster', 'characteristic', null], // 294 — #1 : -#3 Portée maximale
    295 => ['booster', 'characteristic', null], // 295 — #1 : -#3 Portée minimale
    296 => ['booster', 'characteristic', null], // 296 — #1 : +#3 PA
    297 => ['booster', 'characteristic', null], // 297 — #1 : case occupée nécessaire désactivée
    298 => ['booster', 'characteristic', null], // 298 — #1 : case libre nécessaire désactivée
    299 => ['booster', 'characteristic', null], // 299 — #1 : case libre nécessaire activée
    314 => ['booster', 'characteristic', null], // 314 — #1 : case occupée nécessaire activée
    320 => ['booster', 'characteristic', null], // 320 — Vole #1{{~1~2 à }}#2 Portée
    333 => ['booster', 'characteristic', null], // 333 — Change une couleur ; carac DofusDB id=38
    335 => ['booster', 'characteristic', null], // 335 — Change l'apparence
    405 => ['invoquer', 'none', null], // 405 — Tue la cible et remplace par l'invocation : #1
    406 => ['retirer', 'characteristic', null], // 406 — Enlève les effets du sort #2
    407 => ['soigner', 'element', null], // 407 — #1{{~1~2 à }}#2 Soins (fixes)
    410 => ['booster', 'characteristic', 'retrait_pa'], // 410 — #1{{~1~2 à }}#2 Retrait PA ; carac DofusDB id=82
    411 => ['booster', 'characteristic', 'retrait_pa'], // 411 — -#1{{~1~2 à -}}#2 Retrait PA ; carac DofusDB id=82
    412 => ['booster', 'characteristic', 'retrait_pm'], // 412 — #1{{~1~2 à }}#2 Retrait PM ; carac DofusDB id=83
    413 => ['booster', 'characteristic', 'retrait_pm'], // 413 — -#1{{~1~2 à -}}#2 Retrait PM ; carac DofusDB id=83
    414 => ['frapper', 'element', null], // 414 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Poussée
    415 => ['frapper', 'element', null], // 415 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Poussée
    416 => ['booster', 'characteristic', null], // 416 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Poussée ; carac DofusDB id=85
    417 => ['booster', 'characteristic', null], // 417 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Poussée ; carac DofusDB id=85
    418 => ['frapper', 'element', null], // 418 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Critiques
    419 => ['frapper', 'element', null], // 419 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Critiques
    420 => ['booster', 'characteristic', null], // 420 — #1{{~1~2 à }}#2 Résistance{{~ps}}{{~zs}} Critiques ; carac DofusDB id=87
    421 => ['booster', 'characteristic', null], // 421 — -#1{{~1~2 à -}}#2 Résistance{{~ps}}{{~zs}} Critiques ; carac DofusDB id=87
    422 => ['frapper', 'element', null], // 422 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Terre
    423 => ['frapper', 'element', null], // 423 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Terre
    424 => ['frapper', 'element', null], // 424 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Feu
    425 => ['frapper', 'element', null], // 425 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Feu
    426 => ['frapper', 'element', null], // 426 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Eau
    427 => ['frapper', 'element', null], // 427 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Eau
    428 => ['frapper', 'element', null], // 428 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Air
    429 => ['frapper', 'element', null], // 429 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Air
    430 => ['frapper', 'element', null], // 430 — #1{{~1~2 à }}#2 Dommage{{~ps}}{{~zs}} Neutre
    431 => ['frapper', 'element', null], // 431 — -#1{{~1~2 à -}}#2 Dommage{{~ps}}{{~zs}} Neutre
    440 => ['booster', 'characteristic', null], // 440 — Vole #1{{~1~2 à }}#2 PA
    441 => ['booster', 'characteristic', null], // 441 — Vole #1{{~1~2 à }}#2 PM
    621 => ['invoquer', 'none', null], // 621 — Invoque : #3 (grade #1)
    642 => ['retirer', 'characteristic', null], // 642 — Retire #3 points d'honneur ; carac DofusDB id=52
    646 => ['soigner', 'element', null], // 646 — <sprite name="soin"> Soins
    667 => ['booster', 'characteristic', null], // 667 — Combat annulé ; carac DofusDB id=100
    671 => ['frapper', 'element', null], // 671 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="PV"> PV du lanceur (f…
    672 => ['frapper', 'element', null], // 672 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="erosion"> du milieu d…
    750 => ['booster', 'characteristic', null], // 750 — +#1{{~1~2 à }}#2% chances de capture ; carac DofusDB id=72
    751 => ['booster', 'characteristic', null], // 751 — #1{{~1~2 à }}#2% <sprite name="XP"> monture ; carac DofusDB id=73
    752 => ['booster', 'characteristic', 'fuite'], // 752 — #1{{~1~2 à }}#2 Fuite ; carac DofusDB id=78
    753 => ['booster', 'characteristic', 'tacle'], // 753 — #1{{~1~2 à }}#2 Tacle ; carac DofusDB id=79
    754 => ['booster', 'characteristic', 'fuite'], // 754 — -#1{{~1~2 à -}}#2 Fuite ; carac DofusDB id=78
    755 => ['booster', 'characteristic', 'tacle'], // 755 — -#1{{~1~2 à -}}#2 Tacle ; carac DofusDB id=79
    765 => ['frapper', 'element', null], // 765 — Intercepte les dommages
    770 => ['booster', 'characteristic', null], // 770 — Confusion horaire : #1{{~1~2 à }}#2 degrés ; carac DofusDB id=74
    771 => ['booster', 'characteristic', null], // 771 — Confusion horaire : #1{{~1~2 à }}#2 Pi/2 ; carac DofusDB id=74
    772 => ['booster', 'characteristic', null], // 772 — Confusion horaire : #1{{~1~2 à }}#2 Pi/4 ; carac DofusDB id=74
    773 => ['booster', 'characteristic', null], // 773 — Confusion contre horaire : #1{{~1~2 à }}#2 degrés ; carac DofusDB id=74
    774 => ['booster', 'characteristic', null], // 774 — Confusion contre horaire : #1{{~1~2 à }}#2 Pi/2 ; carac DofusDB id=74
    775 => ['booster', 'characteristic', null], // 775 — Confusion contre horaire : #1{{~1~2 à }}#2 Pi/4 ; carac DofusDB id=74
    776 => ['booster', 'characteristic', null], // 776 — #1{{~1~2 à }}#2% Érosion ; carac DofusDB id=75
    780 => ['invoquer', 'none', null], // 780 — Invoque le dernier allié mort avec #1{{~1~2 à }}#2 % de ses PV
    781 => ['booster', 'characteristic', null], // 781 — Minimise les effets aléatoires de la cible
    782 => ['booster', 'characteristic', null], // 782 — Maximise les effets aléatoires sur la cible
    783 => ['déplacer', 'none', null], // 783 — Pousse jusqu'à la case visée
    786 => ['frapper', 'element', null], // 786 — Soin sur l'attaquant : #1{{~1~2 à }}#2% des dommages
    798 => ['booster', 'characteristic', null], // 798 — #1 : cible visible nécessaire activée
    799 => ['booster', 'characteristic', null], // 799 — #1 : cible visible nécessaire désactivée
    946 => ['retirer', 'characteristic', null], // 946 — Retirer temporairement un objet d'élevage
    950 => ['booster', 'characteristic', null], // 950 — État #3 ; carac DofusDB id=71
    951 => ['retirer', 'characteristic', null], // 951 — Enlève l'état #3 ; carac DofusDB id=71
    952 => ['booster', 'characteristic', null], // 952 — Désactive l'état #3 ; carac DofusDB id=71
    1008 => ['invoquer', 'none', null], // 1008 — Invoque : #1
    1011 => ['invoquer', 'none', null], // 1011 — Invoque : #1
    1012 => ['frapper', 'element', null], // 1012 — #1{{~1~2 à }}#2 dommages Neutre (% <sprite name="PM">PM restants)
    1013 => ['frapper', 'element', null], // 1013 — #1{{~1~2 à }}#2 dommages Air (% <sprite name="PM">PM restants)
    1014 => ['frapper', 'element', null], // 1014 — #1{{~1~2 à }}#2 dommages Eau (% <sprite name="PM">PM restants)
    1015 => ['frapper', 'element', null], // 1015 — #1{{~1~2 à }}#2 dommages Feu (% <sprite name="PM">PM restants)
    1016 => ['frapper', 'element', null], // 1016 — #1{{~1~2 à }}#2 dommages Terre (% <sprite name="PM">PM restants)
    1020 => ['booster', 'characteristic', null], // 1020 — Bouclier : #1{{~1~2 à }}#2% du niveau
    1021 => ['déplacer', 'none', null], // 1021 — Repousse de #1 case{{~ps}} (forcé)
    1022 => ['déplacer', 'none', null], // 1022 — Attire de #1 case{{~ps}} (forcé)
    1027 => ['frapper', 'element', null], // 1027 — #1{{~1~2 à }}#2% Dommages Combo
    1033 => ['booster', 'characteristic', 'vitality'], // 1033 — -#1{{~1~2 à -}}#2% Vitalité ; carac DofusDB id=11
    1034 => ['invoquer', 'none', null], // 1034 — Invoque le dernier allié mort avec #1{{~1~2 à }}#2 % de ses PV
    1037 => ['booster', 'characteristic', null], // 1037 — [TEST] PDV rendus : #1{{~1~2 à }}#2
    1038 => ['booster', 'characteristic', null], // 1038 — Aura : #1
    1039 => ['booster', 'characteristic', null], // 1039 — Bouclier : #1{{~1~2 à }}#2% des PV max
    1040 => ['booster', 'characteristic', null], // 1040 — #1{{~1~2 à }}#2 Bouclier
    1041 => ['déplacer', 'none', null], // 1041 — Recule de #1 case{{~ps}}
    1042 => ['déplacer', 'none', null], // 1042 — Avance de #1 case{{~ps}}
    1043 => ['déplacer', 'none', null], // 1043 — Attire jusqu'à la case visée
    1044 => ['booster', 'characteristic', null], // 1044 — Immunité : #1
    1047 => ['booster', 'characteristic', null], // 1047 — -#1{{~1~2 à -}}#2 PV
    1048 => ['booster', 'characteristic', null], // 1048 — -#1{{~1~2 à -}}#2% PV
    1054 => ['booster', 'characteristic', null], // 1054 — #1{{~1~2 à }}#2 Puissance Sorts ; carac DofusDB id=98
    1060 => ['booster', 'characteristic', null], // 1060 — Taille : #1{{~1~2 à }}#2 ; carac DofusDB id=99
    1061 => ['frapper', 'element', null], // 1061 — Partage les dommages
    1063 => ['frapper', 'element', null], // 1063 — #1{{~1~2 à }}#2 dommages Terre (fixe)
    1064 => ['frapper', 'element', null], // 1064 — #1{{~1~2 à }}#2 dommages Air (fixe)
    1065 => ['frapper', 'element', null], // 1065 — #1{{~1~2 à }}#2 dommages Eau (fixe)
    1066 => ['frapper', 'element', null], // 1066 — #1{{~1~2 à }}#2 dommages Feu (fixe)
    1067 => ['frapper', 'element', null], // 1067 — Dommages Air : #1{{~1~2 à }}#2% <sprite name="PV"> PV de la cible
    1068 => ['frapper', 'element', null], // 1068 — Dommages Eau : #1{{~1~2 à }}#2% <sprite name="PV"> PV de la cible
    1069 => ['frapper', 'element', null], // 1069 — Dommages Feu : #1{{~1~2 à }}#2% <sprite name="PV"> PV de la cible
    1070 => ['frapper', 'element', null], // 1070 — Dommages Terre : #1{{~1~2 à }}#2% <sprite name="PV"> PV de la cible
    1071 => ['frapper', 'element', null], // 1071 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="PV"> PV de la cible
    1072 => ['booster', 'characteristic', null], // 1072 — Provoque la cible
    1076 => ['booster', 'characteristic', null], // 1076 — #1{{~1~2 à }}#2% Résistance ; carac DofusDB id=101
    1077 => ['booster', 'characteristic', null], // 1077 — -#1{{~1~2 à -}}#2% Résistance ; carac DofusDB id=101
    1078 => ['booster', 'characteristic', 'vitality'], // 1078 — #1{{~1~2 à }}#2% Vitalité ; carac DofusDB id=11
    1079 => ['booster', 'characteristic', 'pa'], // 1079 — -#1{{~1~2 à -}}#2 PA ; carac DofusDB id=1
    1080 => ['booster', 'characteristic', 'pm'], // 1080 — -#1{{~1~2 à -}}#2 PM ; carac DofusDB id=23
    1092 => ['frapper', 'element', null], // 1092 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés d…
    1093 => ['frapper', 'element', null], // 1093 — Dommages Air : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés de l…
    1094 => ['frapper', 'element', null], // 1094 — Dommages Feu : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés de l…
    1095 => ['frapper', 'element', null], // 1095 — Dommages Eau : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés de l…
    1096 => ['frapper', 'element', null], // 1096 — Dommages Terre : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés de…
    1103 => ['frapper', 'element', null], // 1103 — Repousse de #1 case{{~ps}} (sans dommages)
    1109 => ['soigner', 'element', null], // 1109 — Soin : #1{{~1~2 à }}#2% des PV max
    1118 => ['frapper', 'element', null], // 1118 — Dommages Neutre : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés d…
    1119 => ['frapper', 'element', null], // 1119 — Dommages Air : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés du l…
    1120 => ['frapper', 'element', null], // 1120 — Dommages Feu : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés du l…
    1121 => ['frapper', 'element', null], // 1121 — Dommages Eau : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés du l…
    1122 => ['frapper', 'element', null], // 1122 — Dommages Terre : #1{{~1~2 à }}#2% <sprite name="erosion"> PV érodés du…
    1123 => ['frapper', 'element', null], // 1123 — Dommages : #1{{~1~2 à }}#2% des dommages initiaux subis
    1124 => ['frapper', 'element', null], // 1124 — Dommages Neutre : #1{{~1~2 à }}#2% des dommages initiaux subis
    1125 => ['frapper', 'element', null], // 1125 — Dommages Air : #1{{~1~2 à }}#2% des dommages initiaux subis
    1126 => ['frapper', 'element', null], // 1126 — Dommages Feu : #1{{~1~2 à }}#2% des dommages initiaux subis
    1127 => ['frapper', 'element', null], // 1127 — Dommages Eau : #1{{~1~2 à }}#2% des dommages initiaux subis
    1128 => ['frapper', 'element', null], // 1128 — Dommages Terre : #1{{~1~2 à }}#2% des dommages initiaux subis
    1131 => ['frapper', 'element', null], // 1131 — #2 dommages Air pour #1 <sprite name="PA"> PA utilisé
    1132 => ['frapper', 'element', null], // 1132 — #2 dommages Eau pour #1 <sprite name="PA"> PA utilisé
    1133 => ['frapper', 'element', null], // 1133 — #2 dommages Feu pour #1 <sprite name="PA"> PA utilisé
    1134 => ['frapper', 'element', null], // 1134 — #2 dommages Neutre pour #1 <sprite name="PA"> PA utilisé
    1135 => ['frapper', 'element', null], // 1135 — #2 dommages Terre pour #1 <sprite name="PA"> PA utilisé
    1136 => ['frapper', 'element', null], // 1136 — #2 dommages Air pour #1 <sprite name="PM"> PM utilisé
    1137 => ['frapper', 'element', null], // 1137 — #2 dommages Eau pour #1 <sprite name="PM"> PM utilisé
    1138 => ['frapper', 'element', null], // 1138 — #2 dommages Feu pour #1 <sprite name="PM"> PM utilisé
    1139 => ['frapper', 'element', null], // 1139 — #2 dommages Neutre pour #1 <sprite name="PM"> PM utilisé
    1140 => ['frapper', 'element', null], // 1140 — #2 dommages Terre pour #1 <sprite name="PM"> PM utilisé
    1144 => ['booster', 'characteristic', 'do_fixe_multiple'], // 1144 — #1{{~1~2 à }}#2 Puissance Armes ; carac DofusDB id=103
    1153 => ['invoquer', 'none', null], // 1153 — Invoque un percepteur
    1159 => ['soigner', 'element', null], // 1159 — Soins reçus x#1%
    1163 => ['frapper', 'element', null], // 1163 — Dommages subis x#1%
    1164 => ['frapper', 'element', null], // 1164 — Convertit les dommages subis en soins
    1166 => ['booster', 'characteristic', null], // 1166 — #1{{~1~2 à }}#2 Puissance Glyphes ; carac DofusDB id=106
    1167 => ['booster', 'characteristic', null], // 1167 — #1{{~1~2 à }}#2 Puissance Runes ; carac DofusDB id=110
    1171 => ['frapper', 'element', null], // 1171 — #1% Dommages finaux
    1172 => ['frapper', 'element', null], // 1172 — -#1% Dommages finaux
    1181 => ['frapper', 'element', null], // 1181 — Pose un portail (+#3% dommages, +#1% dommages par case d'éloignement e…
    1189 => ['invoquer', 'none', null], // 1189 — Invoque un double du lanceur
    1223 => ['frapper', 'element', null], // 1223 — Dommages : #1{{~1~2 à }}#2% des dommages finaux subis
    1224 => ['frapper', 'element', null], // 1224 — Dommages Neutre : #1{{~1~2 à }}#2% des dommages finaux subis
    1225 => ['frapper', 'element', null], // 1225 — Dommages Air : #1{{~1~2 à }}#2% des dommages finaux subis
    1226 => ['frapper', 'element', null], // 1226 — Dommages Feu : #1{{~1~2 à }}#2% des dommages finaux subis
    1227 => ['frapper', 'element', null], // 1227 — Dommages Eau : #1{{~1~2 à }}#2% des dommages finaux subis
    1228 => ['frapper', 'element', null], // 1228 — Dommages Terre : #1{{~1~2 à }}#2% des dommages finaux subis
    1406 => ['retirer', 'characteristic', null], // 1406 — Enlève les effets du rang #1 du sort #2
    2010 => ['booster', 'characteristic', null], // 2010 — Le propriétaire du combat prend le contrôle de l'entité
    2011 => ['booster', 'characteristic', null], // 2011 — Le propriétaire du combat perd le contrôle de l'entité
    2020 => ['frapper', 'element', null], // 2020 — Soin : #1{{~1~2 à }}#2% des dommages subis
    2027 => ['booster', 'characteristic', null], // 2027 — Prend le contrôle de l'entité
    2028 => ['booster', 'characteristic', null], // 2028 — Transfère #3% de la caractéristique #1
    2406 => ['retirer', 'characteristic', null], // 2406 — Enlève l'effet #2
    2414 => ['frapper', 'element', null], // 2414 — #1{{~1~2 à }}#2% Dommage{{~ps}}{{~zs}} Poussée
    2415 => ['frapper', 'element', null], // 2415 — -#1{{~1~2 à -}}#2% Dommage{{~ps}}{{~zs}} Poussée
    2796 => ['invoquer', 'none', null], // 2796 — Tue la cible et remplace par l'invocation : #1
    2800 => ['frapper', 'element', null], // 2800 — #1{{~1~2 à }}#2% Dommages mêlée
    2801 => ['frapper', 'element', null], // 2801 — -#1{{~1~2 à -}}#2% Dommages mêlée
    2802 => ['booster', 'characteristic', null], // 2802 — -#1{{~1~2 à -}}#2% Résistance mêlée ; carac DofusDB id=124
    2803 => ['booster', 'characteristic', null], // 2803 — #1{{~1~2 à }}#2% Résistance mêlée ; carac DofusDB id=124
    2804 => ['frapper', 'element', null], // 2804 — #1{{~1~2 à }}#2% Dommages distance
    2805 => ['frapper', 'element', null], // 2805 — -#1{{~1~2 à -}}#2% Dommages distance
    2806 => ['booster', 'characteristic', null], // 2806 — -#1{{~1~2 à -}}#2% Résistance distance ; carac DofusDB id=121
    2807 => ['booster', 'characteristic', null], // 2807 — #1{{~1~2 à }}#2% Résistance distance ; carac DofusDB id=121
    2808 => ['frapper', 'element', null], // 2808 — #1{{~1~2 à }}#2% Dommages d'armes
    2809 => ['frapper', 'element', null], // 2809 — -#1{{~1~2 à -}}#2% Dommages d'armes
    2810 => ['booster', 'characteristic', null], // 2810 — -#1{{~1~2 à -}}#2% Résistance aux armes ; carac DofusDB id=142
    2811 => ['booster', 'characteristic', null], // 2811 — #1{{~1~2 à }}#2% Résistance aux armes ; carac DofusDB id=142
    2812 => ['frapper', 'element', null], // 2812 — #1{{~1~2 à }}#2% Dommages aux sorts
    2813 => ['frapper', 'element', null], // 2813 — -#1{{~1~2 à -}}#2% Dommages aux sorts
    2814 => ['booster', 'characteristic', null], // 2814 — -#1{{~1~2 à -}}#2% Résistance aux sorts ; carac DofusDB id=141
    2815 => ['booster', 'characteristic', null], // 2815 — #1{{~1~2 à }}#2% Résistance aux sorts ; carac DofusDB id=141
    2822 => ['frapper', 'element', null], // 2822 — #1{{~1~2 à }}#2 dommages du meilleur élément
    2824 => ['frapper', 'element', null], // 2824 — Tous les sorts : +#3 dégâts de base
    2829 => ['frapper', 'element', null], // 2829 — Dommages du meilleur élément : #1{{~1~2 à }}#2% des dommages initiaux …
    2830 => ['frapper', 'element', null], // 2830 — Dommages du meilleur élément : #1{{~1~2 à }}#2% des dommages finaux su…
    2832 => ['frapper', 'element', null], // 2832 — #1{{~1~2 à }}#2 dommages du pire élément
    2834 => ['booster', 'characteristic', null], // 2834 — #1{{~1~2 à }}#2% Force ; carac DofusDB id=127
    2835 => ['booster', 'characteristic', null], // 2835 — -#1{{~1~2 à -}}#2% Force ; carac DofusDB id=127
    2836 => ['booster', 'characteristic', null], // 2836 — #1{{~1~2 à }}#2% Agilité ; carac DofusDB id=126
    2837 => ['booster', 'characteristic', null], // 2837 — -#1{{~1~2 à -}}#2% Agilité ; carac DofusDB id=126
    2838 => ['booster', 'characteristic', null], // 2838 — #1{{~1~2 à }}#2% Intelligence ; carac DofusDB id=129
    2839 => ['booster', 'characteristic', null], // 2839 — -#1{{~1~2 à -}}#2% Intelligence ; carac DofusDB id=129
    2840 => ['booster', 'characteristic', null], // 2840 — #1{{~1~2 à }}#2% Chance ; carac DofusDB id=128
    2841 => ['booster', 'characteristic', null], // 2841 — -#1{{~1~2 à -}}#2% Chance ; carac DofusDB id=128
    2842 => ['booster', 'characteristic', null], // 2842 — #1{{~1~2 à }}#2% Sagesse ; carac DofusDB id=131
    2843 => ['booster', 'characteristic', null], // 2843 — -#1{{~1~2 à -}}#2% Sagesse ; carac DofusDB id=131
    2844 => ['booster', 'characteristic', null], // 2844 — #1{{~1~2 à }}#2% Vitalité ; carac DofusDB id=130
    2845 => ['booster', 'characteristic', null], // 2845 — -#1{{~1~2 à -}}#2% Vitalité ; carac DofusDB id=130
    2846 => ['booster', 'characteristic', null], // 2846 — #1{{~1~2 à }}#2% PA ; carac DofusDB id=134
    2847 => ['booster', 'characteristic', null], // 2847 — -#1{{~1~2 à -}}#2% PA ; carac DofusDB id=134
    2848 => ['booster', 'characteristic', null], // 2848 — #1{{~1~2 à }}#2% PM ; carac DofusDB id=135
    2849 => ['booster', 'characteristic', null], // 2849 — -#1{{~1~2 à -}}#2% PM ; carac DofusDB id=135
    2850 => ['booster', 'characteristic', null], // 2850 — #1{{~1~2 à }}#2% Tacle ; carac DofusDB id=132
    2851 => ['booster', 'characteristic', null], // 2851 — -#1{{~1~2 à -}}#2% Tacle ; carac DofusDB id=132
    2852 => ['booster', 'characteristic', null], // 2852 — #1{{~1~2 à }}#2% Fuite ; carac DofusDB id=133
    2853 => ['booster', 'characteristic', null], // 2853 — -#1{{~1~2 à -}}#2% Fuite ; carac DofusDB id=133
    2854 => ['booster', 'characteristic', null], // 2854 — #1{{~1~2 à }}#2% Esquive PA ; carac DofusDB id=138
    2855 => ['booster', 'characteristic', null], // 2855 — -#1{{~1~2 à -}}#2% Esquive PA ; carac DofusDB id=138
    2856 => ['booster', 'characteristic', null], // 2856 — #1{{~1~2 à }}#2% Esquive PM ; carac DofusDB id=139
    2857 => ['booster', 'characteristic', null], // 2857 — -#1{{~1~2 à -}}#2% Esquive PM ; carac DofusDB id=139
    2858 => ['booster', 'characteristic', null], // 2858 — #1{{~1~2 à }}#2% Retrait PA ; carac DofusDB id=136
    2859 => ['booster', 'characteristic', null], // 2859 — -#1{{~1~2 à -}}#2% Retrait PA ; carac DofusDB id=136
    2860 => ['booster', 'characteristic', null], // 2860 — #1{{~1~2 à }}#2% Retrait PM ; carac DofusDB id=137
    2861 => ['booster', 'characteristic', null], // 2861 — -#1{{~1~2 à -}}#2% Retrait PM ; carac DofusDB id=137
    2868 => ['booster', 'characteristic', null], // 2868 — Taille : +#1{{~1~2 à }}#2% ; carac DofusDB id=140
    2871 => ['booster', 'characteristic', null], // 2871 — Taille : -#1{{~1~2 à }}#2% ; carac DofusDB id=140
    2872 => ['booster', 'characteristic', null], // 2872 — Seuil : #1 PV
    2891 => ['frapper', 'element', null], // 2891 — Dommages du pire élément : #1{{~1~2 à }}#2% des dommages finaux subis
    2896 => ['frapper', 'element', null], // 2896 — Dommages du pire élément : #1{{~1~2 à }}#2% des dommages initiaux subi…
    2905 => ['booster', 'characteristic', null], // 2905 — #1 : Portée maximale fixée à #3
    2906 => ['booster', 'characteristic', null], // 2906 — #1 : Portée minimale fixée à #3
    2908 => ['booster', 'characteristic', null], // 2908 — #1 : case sans portail nécessaire désactivée
    2909 => ['booster', 'characteristic', null], // 2909 — #1 : projection dans un portail désactivée
    2910 => ['booster', 'characteristic', null], // 2910 — #1 : projection dans un portail activée
    2911 => ['booster', 'characteristic', null], // 2911 — #1 : case sans portail nécessaire activée
    2914 => ['booster', 'characteristic', null], // 2914 — #1 : Portée non modifiable
    2932 => ['booster', 'characteristic', null], // 2932 — #1 : lancer en ligne activé
    2933 => ['booster', 'characteristic', null], // 2933 — #1 : lancer en diagonale désactivé
    2934 => ['booster', 'characteristic', null], // 2934 — #1 : lancer en diagonale activé
    2935 => ['soigner', 'element', null], // 2935 — #1 : +#3 soins de base
    2971 => ['soigner', 'element', null], // 2971 — #1% Soins finaux
    2972 => ['soigner', 'element', null], // 2972 — -#1% Soins finaux
    2973 => ['frapper', 'element', null], // 2973 — Soin : #1{{~1~2 à }}#2% des dommages occasionnés
    2974 => ['frapper', 'element', null], // 2974 — Dommages Air : #1{{~1~2 à }}#2% des dommages initiaux occasionnés
    2975 => ['frapper', 'element', null], // 2975 — Dommages Eau : #1{{~1~2 à }}#2% des dommages initiaux occasionnés
    2976 => ['frapper', 'element', null], // 2976 — Dommages Feu : #1{{~1~2 à }}#2% des dommages initiaux occasionnés
    2977 => ['frapper', 'element', null], // 2977 — Dommages Terre : #1{{~1~2 à }}#2% des dommages initiaux occasionnés
    2978 => ['frapper', 'element', null], // 2978 — Dommages Neutre : #1{{~1~2 à }}#2% des dommages initiaux occasionnés
    2979 => ['frapper', 'element', null], // 2979 — Dommages du meilleur élément : #1{{~1~2 à }}#2% des dommages initiaux …
    2980 => ['frapper', 'element', null], // 2980 — Dommages du pire élément : #1{{~1~2 à }}#2% des dommages initiaux occa…
    2981 => ['frapper', 'element', null], // 2981 — Dommages : #1{{~1~2 à }}#2% des dommages initiaux occasionnés
    2982 => ['frapper', 'element', null], // 2982 — Dommages Air : #1{{~1~2 à }}#2% des dommages finaux occasionnés
    2983 => ['frapper', 'element', null], // 2983 — Dommages Eau : #1{{~1~2 à }}#2% des dommages finaux occasionnés
    2984 => ['frapper', 'element', null], // 2984 — Dommages Feu : #1{{~1~2 à }}#2% des dommages finaux occasionnés
    2985 => ['frapper', 'element', null], // 2985 — Dommages Terre : #1{{~1~2 à }}#2% des dommages finaux occasionnés
    2986 => ['frapper', 'element', null], // 2986 — Dommages Neutre : #1{{~1~2 à }}#2% des dommages finaux occasionnés
    2987 => ['frapper', 'element', null], // 2987 — Dommages du meilleur élément : #1{{~1~2 à }}#2% des dommages finaux oc…
    2988 => ['frapper', 'element', null], // 2988 — Dommages du pire élément : #1{{~1~2 à }}#2% des dommages finaux occasi…
    2989 => ['frapper', 'element', null], // 2989 — Dommages : #1{{~1~2 à }}#2% des dommages finaux occasionnés
    2990 => ['invoquer', 'none', null], // 2990 — -#1{{~1~2 à }}#2 Invocation{{~ps}}{{~zs}}
    2998 => ['frapper', 'element', null], // 2998 — #1{{~1~2 à }}#2 soins Eau
    2999 => ['frapper', 'element', null], // 2999 — #1{{~1~2 à }}#2 soins Air
    3000 => ['frapper', 'element', null], // 3000 — #1{{~1~2 à }}#2 soins Terre
    3001 => ['frapper', 'element', null], // 3001 — #1{{~1~2 à }}#2 soins Neutre
    3002 => ['soigner', 'element', null], // 3002 — #1{{~1~2 à }}#2 soins du meilleur élément
    3281 => ['booster', 'characteristic', null], // 3281 — #1 : +#3 Portée maximale
    3282 => ['booster', 'characteristic', null], // 3282 — #1 : Portée modifiable
    3285 => ['booster', 'characteristic', null], // 3285 — #1 : -#3 PA
    3287 => ['booster', 'characteristic', null], // 3287 — #1 : +#3% Critique
    3289 => ['booster', 'characteristic', null], // 3289 — #1 : ligne de vue désactivée
    3290 => ['booster', 'characteristic', null], // 3290 — #1 : +#3 lancer(s) par tour
    3293 => ['frapper', 'element', null], // 3293 — #1 : +#3 dégâts de base
    3296 => ['booster', 'characteristic', null], // 3296 — #1 : +#3 PA
    3333 => ['booster', 'characteristic', null], // 3333 — #1 : +#3 Bonus critiques
    3407 => ['booster', 'characteristic', null], // 3407 — Durée du prochain tour : #1 seconde{{~ps}}
    3408 => ['booster', 'characteristic', null], // 3408 — +#1 seconde{{~ps}} au prochain tour
    3409 => ['booster', 'characteristic', null], // 3409 — -#1 seconde{{~ps}} au prochain tour
    3794 => ['booster', 'characteristic', null], // 3794 — Ajoute une palette de couleurs de personnage
    3804 => ['booster', 'characteristic', null], // 3804 — -#1{{~1~2 à }}#2% Érosion ; carac DofusDB id=75
    3805 => ['booster', 'characteristic', null], // 3805 — #1{{~1~2 à }}#2 Points de vie érodés ; carac DofusDB id=75
    3806 => ['booster', 'characteristic', null], // 3806 — #1{{~1~2 à }}#2% Points de vie érodés ; carac DofusDB id=75
    3807 => ['booster', 'characteristic', null], // 3807 — -#1{{~1~2 à }}#2 Points de vie érodés ; carac DofusDB id=75
    3808 => ['booster', 'characteristic', null], // 3808 — -#1{{~1~2 à }}#2% Points de vie érodés ; carac DofusDB id=75
    3935 => ['soigner', 'element', null], // 3935 — #1 : +#3 soins de base
    4001 => ['déplacer', 'none', null], // 4001 — Repousse de #1 case{{~ps}}
    4002 => ['déplacer', 'none', null], // 4002 — Repousse de #1 case{{~ps}} (forcé)
    4003 => ['frapper', 'element', null], // 4003 — Repousse de #1 case{{~ps}} (sans dommages)
    4007 => ['retirer', 'characteristic', null], // 4007 — Retire les portails sur la cellule
    4041 => ['frapper', 'element', null], // 4041 — #1% Dégâts
    4042 => ['frapper', 'element', null], // 4042 — #1% Dégâts
];