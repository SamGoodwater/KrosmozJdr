#!/usr/bin/env python3
"""Injecte sauvegardes ×6 + passifs ×18 + description_object dans characteristics.php."""
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
path = ROOT / "data" / "characteristics.php"
fragment = (ROOT / "scripts" / "inject_object_saves_passives.txt").read_text(encoding="utf-8")
text = path.read_text(encoding="utf-8")

old = """    131 => [
        'key' => 'passive_skills_object',
        'name' => 'Compétences passives',
        'short_name' => 'Comp. pass.',
        'helper' => 'Bonus compétences passives (chapeaux). Max 3, forgemagie +2.',
        'descriptions' => 'Bonus compétences passives (chapeaux). Bonus au jet +10 par compétence. Équip. max +3, forgemagie +2 (2.2.2).',
        'icon' => 'skill.webp',
        'color' => '#858585',
        'unit' => null,
        'type' => 'int',
        'sort_order' => 24,
        'group' => 'object',
        'linked_to_key' => null,
    ],
    132 => [
        'key' => 'save_strength_intelligence_chance_agility_object',
        'name' => 'Bonus sauvegarde Force',
        'short_name' => 'Sav Force',
        'helper' => 'Bonus jet de sauvegarde (capes). Max 3.',
        'descriptions' => 'Bonus sauvegarde For/Int/Cha/Agi (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'icon' => 'saveStrenght.webp',
        'color' => '#95743c',
        'unit' => null,
        'type' => 'int',
        'sort_order' => 24,
        'group' => 'object',
        'linked_to_key' => null,
    ],
    133 => [
        'key' => 'save_vitality_wisdom_object',
        'name' => 'Bonus sauvegarde Vitalité',
        'short_name' => 'Sav Vitalité',
        'helper' => 'Bonus jet de sauvegarde Vit. ou Sag. (chapeaux). Max 3.',
        'descriptions' => 'Bonus sauvegarde Vitalité/Sagesse (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'icon' => 'saveVitality.webp',
        'color' => '#d78b3d',
        'unit' => null,
        'type' => 'int',
        'sort_order' => 24,
        'group' => 'object',
        'linked_to_key' => null,
    ],"""

if old not in text:
    raise SystemExit("Bloc agrégé introuvable (déjà injecté ?)")

text = text.replace(old, fragment.rstrip() + "\n", 1)

marker = "    156 => [\n        'key' => 'acrobatics_object',"
idx = text.find(marker)
if idx == -1:
    raise SystemExit("acrobatics_object introuvable après injection")

head = text[:idx]
tail = text[idx:]
# Haut → bas pour éviter les collisions (ex. 134→155 avant de traiter l’ancien 155).
for n in range(272, 155, -1):
    needle = f"    {n} =>"
    c = tail.count(needle)
    if c != 1:
        raise SystemExit(f"Clé {n} dans suffixe: {c} occurrences")
    tail = tail.replace(needle, f"    {n + 21} =>", 1)

text = head + tail

desc = """
    272 => [
        'key' => 'description_object',
        'name' => 'Description',
        'short_name' => 'Desc.',
        'helper' => 'Texte descriptif de l\\'objet (aperçu, MJ, lore).',
        'descriptions' => 'Description libre : mise en forme riche selon l\\'éditeur de la fiche objet.',
        'icon' => 'skill.webp',
        'color' => '#78909c',
        'unit' => null,
        'type' => 'string',
        'sort_order' => 20,
        'group' => 'object',
        'linked_to_key' => null,
    ],
"""

if "\n    272 =>" in text:
    raise SystemExit("272 déjà présent")
text = text.replace("\n];", desc + "\n];", 1)

path.write_text(text, encoding="utf-8")
print("OK", path)
