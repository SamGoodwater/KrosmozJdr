#!/usr/bin/env python3
"""Remplace 3 lignes agrégées characteristic_object par 24 lignes + renumérotation des clés."""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
path = ROOT / "data" / "characteristic_object.php"
text = path.read_text(encoding="utf-8")

SAVE_VIT = """    22 => [
        'characteristic_key' => 'save_vitality_object',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{"1":"0","7":"1","11":"2","17":"3","characteristic":"level"}',
        'formula_display' => 'Bonus sauvegarde Vitalité (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => null,
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => false,
        'forgemagie_max' => 0,
        'base_price_per_unit' => '800.00',
        'rune_price_per_unit' => null,
        'value_available' => null,
        'item_type_ids' => [],
    ],"""

SAVE_WIS = """    23 => [
        'characteristic_key' => 'save_wisdom_object',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{"1":"0","7":"1","11":"2","17":"3","characteristic":"level"}',
        'formula_display' => 'Bonus sauvegarde Sagesse (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => null,
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => false,
        'forgemagie_max' => 0,
        'base_price_per_unit' => '800.00',
        'rune_price_per_unit' => null,
        'value_available' => null,
        'item_type_ids' => [],
    ],"""

SAVE_CAPE = """    {k} => [
        'characteristic_key' => '{ckey}',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{{"1":"0","7":"1","11":"2","17":"3","characteristic":"level"}}',
        'formula_display' => 'Bonus sauvegarde (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => '[d]',
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => false,
        'forgemagie_max' => 0,
        'base_price_per_unit' => '700.00',
        'rune_price_per_unit' => null,
        'value_available' => null,
        'item_type_ids' => [],
    ],"""

cape_keys = [
    (24, "save_strength_object", "Force"),
    (25, "save_intelligence_object", "Intelligence"),
    (26, "save_chance_object", "Chance"),
    (27, "save_agility_object", "Agilité"),
]

cape_blocks = []
for k, ckey, label in cape_keys:
    block = SAVE_CAPE.format(k=k, ckey=ckey).replace(
        "Bonus sauvegarde (capes).",
        f"Bonus sauvegarde {label} (capes).",
    )
    cape_blocks.append(block)

passive_keys = [
    "acrobatics_passive_object",
    "animal_handling_passive_object",
    "arcana_passive_object",
    "athletics_passive_object",
    "deception_passive_object",
    "history_passive_object",
    "insight_passive_object",
    "intimidation_passive_object",
    "investigation_passive_object",
    "medicine_passive_object",
    "nature_passive_object",
    "perception_passive_object",
    "performance_passive_object",
    "persuasion_passive_object",
    "religion_passive_object",
    "sleight_of_hand_passive_object",
    "stealth_passive_object",
    "survival_passive_object",
]

PASSIVE_TMPL = """    {k} => [
        'characteristic_key' => '{ckey}',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{{"1":"0","5":"1","7":"2","11":"3","characteristic":"level"}}',
        'formula_display' => 'Bonus compétence passive (chapeaux). Bonus au jet +10 par compétence. Équip. max +3, forgemagie +2 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => '[d]',
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => true,
        'forgemagie_max' => 2,
        'base_price_per_unit' => '500.00',
        'rune_price_per_unit' => '1000.00',
        'value_available' => null,
        'item_type_ids' => [],
    ],"""

passive_blocks = []
start_k = 28
for i, ckey in enumerate(passive_keys):
    passive_blocks.append(PASSIVE_TMPL.format(k=start_k + i, ckey=ckey))

new_middle = "\n".join(
    [SAVE_VIT, SAVE_WIS] + cape_blocks + passive_blocks
)

old = """    22 => [
        'characteristic_key' => 'passive_skills_object',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{"1":"0","5":"1","7":"2","11":"3","characteristic":"level"}',
        'formula_display' => 'Bonus compétences passives (chapeaux). Bonus au jet +10 par compétence. Équip. max +3, forgemagie +2 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => '[d]',
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => true,
        'forgemagie_max' => 2,
        'base_price_per_unit' => '500.00',
        'rune_price_per_unit' => '1000.00',
        'value_available' => null,
        'item_type_ids' => [],
    ],
    23 => [
        'characteristic_key' => 'save_strength_intelligence_chance_agility_object',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{"1":"0","7":"1","11":"2","17":"3","characteristic":"level"}',
        'formula_display' => 'Bonus sauvegarde For/Int/Cha/Agi (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => '[d]',
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => false,
        'forgemagie_max' => 0,
        'base_price_per_unit' => '700.00',
        'rune_price_per_unit' => null,
        'value_available' => null,
        'item_type_ids' => [],
    ],
    24 => [
        'characteristic_key' => 'save_vitality_wisdom_object',
        'entity' => '*',
        'db_column' => null,
        'min' => '0',
        'max' => '3',
        'formula' => '{"1":"0","7":"1","11":"2","17":"3","characteristic":"level"}',
        'formula_display' => 'Bonus sauvegarde Vitalité/Sagesse (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'default_value' => '0',
        'conversion_formula' => null,
        'conversion_dofus_sample' => null,
        'conversion_krosmoz_sample' => null,
        'forgemagie_allowed' => false,
        'forgemagie_max' => 0,
        'base_price_per_unit' => '800.00',
        'rune_price_per_unit' => null,
        'value_available' => null,
        'item_type_ids' => [],
    ],"""

if old not in text:
    raise SystemExit("Bloc characteristic_object agrégé introuvable")

text = text.replace(old, new_middle.rstrip() + "\n", 1)

marker = "    25 => [\n        'characteristic_key' => 'skills_object',"
idx = text.find(marker)
if idx == -1:
    raise SystemExit("skills_object pivot introuvable")

head = text[:idx]
tail = text[idx:]
for n in range(65, 24, -1):
    needle = f"    {n} =>"
    c = tail.count(needle)
    if c != 1:
        raise SystemExit(f"pivot clé {n}: {c} occurrences")
    tail = tail.replace(needle, f"    {n + 21} =>", 1)

path.write_text(head + tail, encoding="utf-8")
print("OK", path)
