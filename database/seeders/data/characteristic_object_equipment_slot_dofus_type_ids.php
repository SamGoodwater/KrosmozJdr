<?php

declare(strict_types=1);

/**
 * Restriction des caractéristiques **groupe object** par type d'équipement (PDF « Equipements et forgemagie »,
 * synthèse : docs/410- Ressources/VERIFICATION_CARACTERISTIQUES_PDF.md).
 *
 * Clés = `characteristic_key`. Valeurs = liste de **dofusdb_type_id** (table `item_types`, champ homonyme).
 * Résolution en `item_types.id` par {@see \Database\Seeders\ObjectCharacteristicSeeder::resolveItemTypeIdsFromDofusTypeIds()}.
 *
 * **Absence** d'une clé dans ce tableau → aucune ligne pivot (la caractéristique reste utilisable sur **tous** les types).
 *
 * @var array<string, list<int>>
 */
$dofusWeapon = [2, 3, 4, 5, 6, 7, 8, 19, 20, 21, 22, 114, 271];

$dofusHatCape = [16, 17];
$dofusHatOnly = [16];

$dofusAmulet = [1];
$dofusRing = [9];
$dofusBelt = [10];
$dofusBoots = [11];
$dofusShield = [82];

$dofusLifePdf = [16, 17, 1, 11, 9];
$dofusInitiativePdf = [17, 11];

$skillStems = [
    'acrobatics',
    'animal_handling',
    'arcana',
    'athletics',
    'deception',
    'history',
    'insight',
    'intimidation',
    'investigation',
    'medicine',
    'nature',
    'perception',
    'performance',
    'persuasion',
    'religion',
    'sleight_of_hand',
    'stealth',
    'survival',
];

$map = [];

foreach ($skillStems as $stem) {
    $map[$stem.'_object'] = $dofusHatCape;
    $map[$stem.'_passive_object'] = $dofusHatOnly;
}

$weaponOnlyKeys = [
    'hit_bonus_object',
    'fixed_damage_neutral_object',
    'fixed_damage_earth_object',
    'fixed_damage_fire_object',
    'fixed_damage_air_object',
    'fixed_damage_water_object',
    'fixed_damage_multiple_object',
];
foreach ($weaponOnlyKeys as $k) {
    $map[$k] = $dofusWeapon;
}

$shieldKeys = [
    'armor_class_object',
    'fixed_resistance_neutral_object',
    'fixed_resistance_earth_object',
    'fixed_resistance_fire_object',
    'fixed_resistance_air_object',
    'fixed_resistance_water_object',
    'resistance_percent_tier_earth_object',
    'resistance_percent_tier_fire_object',
    'resistance_percent_tier_water_object',
    'resistance_percent_tier_air_object',
    'resistance_percent_tier_neutral_object',
];
foreach ($shieldKeys as $k) {
    $map[$k] = $dofusShield;
}

$map['life_points_max_object'] = $dofusLifePdf;
$map['vitality_object'] = $dofusHatOnly;
$map['wisdom_object'] = $dofusHatOnly;
$map['save_vitality_object'] = $dofusHatOnly;
$map['save_wisdom_object'] = $dofusHatOnly;

$map['initiative_object'] = $dofusInitiativePdf;
$map['strength_object'] = [17];
$map['intelligence_object'] = [17];
$map['chance_object'] = [17];
$map['agility_object'] = [17];
$map['save_strength_object'] = [17];
$map['save_intelligence_object'] = [17];
$map['save_chance_object'] = [17];
$map['save_agility_object'] = [17];

$map['action_points_object'] = $dofusAmulet;
$map['dodge_action_points_object'] = $dofusAmulet;
$map['critical_hit_object'] = $dofusAmulet;
$map['failure_hit_object'] = $dofusAmulet;

$map['movement_points_object'] = $dofusBoots;
$map['dodge_movement_points_object'] = $dofusBoots;

$map['summoning_object'] = $dofusRing;
$map['range_object'] = $dofusRing;
$map['heal_bonus_object'] = $dofusRing;

$map['tackle_object'] = $dofusBelt;
$map['dodge_object'] = $dofusBelt;
$map['wakfu_recharge_object'] = $dofusBelt;

return $map;
