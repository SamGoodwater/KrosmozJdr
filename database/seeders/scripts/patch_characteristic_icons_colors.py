#!/usr/bin/env python3
from pathlib import Path

p = Path(__file__).resolve().parents[1] / "data" / "characteristic_icons_colors.php"
t = p.read_text(encoding="utf-8")

t = t.replace(
    "        'name_object' => 'skill.webp',\n        'rarity_object' => 'critical.webp',",
    "        'name_object' => 'skill.webp',\n        'description_object' => 'skill.webp',\n        'rarity_object' => 'critical.webp',",
)
t = t.replace(
    "        'name_object' => 'Nom de l\\'objet (équipement, ressource, consommable).',\n\n        // —— Creature : vie, PA, PM, CA, initiative, portée, touche ——",
    "        'name_object' => 'Nom de l\\'objet (équipement, ressource, consommable).',\n        'description_object' => 'Description libre de l\\'objet (lore, MJ, mécanique, mise en forme riche).',\n\n        // —— Creature : vie, PA, PM, CA, initiative, portée, touche ——",
)

old_icons = """        'life_points_max_object' => 'life.webp',
        'save_vitality_wisdom_object' => 'wisdom.webp',
        'save_strength_intelligence_chance_agility_object' => 'skillIntelligence.webp',
        'skills_object' => 'skill.webp',
        'passive_skills_object' => 'skill.webp',"""

new_icons = """        'life_points_max_object' => 'life.webp',
        'save_vitality_object' => 'saveVitality.webp',
        'save_wisdom_object' => 'saveWisdom.webp',
        'save_strength_object' => 'saveStrenght.webp',
        'save_intelligence_object' => 'saveIntel.webp',
        'save_chance_object' => 'saveLuck.webp',
        'save_agility_object' => 'saveAgi.webp',
        'skills_object' => 'skill.webp',
        'acrobatics_passive_object' => 'skillAgility.webp',
        'animal_handling_passive_object' => 'skillWisdom.webp',
        'arcana_passive_object' => 'skillIntelligence.webp',
        'athletics_passive_object' => 'skillStrength.webp',
        'deception_passive_object' => 'skillChance.webp',
        'history_passive_object' => 'skillIntelligence.webp',
        'insight_passive_object' => 'skillWisdom.webp',
        'intimidation_passive_object' => 'skillStrength.webp',
        'investigation_passive_object' => 'skillIntelligence.webp',
        'medicine_passive_object' => 'skillWisdom.webp',
        'nature_passive_object' => 'skillIntelligence.webp',
        'perception_passive_object' => 'skillWisdom.webp',
        'performance_passive_object' => 'skillChance.webp',
        'persuasion_passive_object' => 'skillChance.webp',
        'religion_passive_object' => 'skillIntelligence.webp',
        'sleight_of_hand_passive_object' => 'skillAgility.webp',
        'stealth_passive_object' => 'skillAgility.webp',
        'survival_passive_object' => 'skillWisdom.webp',"""

if old_icons not in t:
    raise SystemExit("icons block not found")
t = t.replace(old_icons, new_icons)

old_colors = """        'life_points_max_object' => '#e93323',
        'save_vitality_wisdom_object' => '#5c6bc0',
        'save_strength_intelligence_chance_agility_object' => '#5c6bc0',
        'skills_object' => '#ffb74d',
        'passive_skills_object' => '#ffb74d',"""

new_colors = """        'life_points_max_object' => '#e93323',
        'save_vitality_object' => '#d88d3d',
        'save_wisdom_object' => '#7d64f5',
        'save_strength_object' => '#8b6c38',
        'save_intelligence_object' => '#c16024',
        'save_chance_object' => '#92cefa',
        'save_agility_object' => '#96c270',
        'skills_object' => '#ffb74d',
        'acrobatics_passive_object' => '#96c270',
        'animal_handling_passive_object' => '#6b57d1',
        'arcana_passive_object' => '#e4732d',
        'athletics_passive_object' => '#8c7448',
        'deception_passive_object' => '#92cefa',
        'history_passive_object' => '#e4732d',
        'insight_passive_object' => '#6b57d1',
        'intimidation_passive_object' => '#8c7448',
        'investigation_passive_object' => '#e4732d',
        'medicine_passive_object' => '#6b57d1',
        'nature_passive_object' => '#e4732d',
        'perception_passive_object' => '#6b57d1',
        'performance_passive_object' => '#92cefa',
        'persuasion_passive_object' => '#92cefa',
        'religion_passive_object' => '#e4732d',
        'sleight_of_hand_passive_object' => '#96c270',
        'stealth_passive_object' => '#96c270',
        'survival_passive_object' => '#6b57d1',"""

if old_colors not in t:
    raise SystemExit("colors block not found")
t = t.replace(old_colors, new_colors)

old_desc = """        'life_points_max_object' => 'Bonus PV max (chapeaux, capes, bottes, amulettes, anneaux). 50 kamas/pt. Forgemagie +20 (2.2.2).',
        'save_vitality_wisdom_object' => 'Bonus sauvegarde Vitalité/Sagesse (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'save_strength_intelligence_chance_agility_object' => 'Bonus sauvegarde For/Int/Cha/Agi (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'skills_object' => 'Bonus compétences (chapeaux, capes). Jet : 1d20 + mod + bonus. Équip. max +5, forgemagie +3 (2.2.2).',
        'passive_skills_object' => 'Bonus compétences passives (chapeaux). Bonus au jet +10 par compétence. Équip. max +3, forgemagie +2 (2.2.2).',"""

new_desc = """        'life_points_max_object' => 'Bonus PV max (chapeaux, capes, bottes, amulettes, anneaux). 50 kamas/pt. Forgemagie +20 (2.2.2).',
        'description_object' => 'Description libre de l\\'objet (lore, mécanique MJ, mise en forme riche).',
        'save_vitality_object' => 'Bonus sauvegarde Vitalité (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'save_wisdom_object' => 'Bonus sauvegarde Sagesse (chapeaux). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'save_strength_object' => 'Bonus sauvegarde Force (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'save_intelligence_object' => 'Bonus sauvegarde Intelligence (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'save_chance_object' => 'Bonus sauvegarde Chance (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'save_agility_object' => 'Bonus sauvegarde Agilité (capes). Jet : 1d20 + mod + maîtrise. Équip. max +3 (2.2.2).',
        'skills_object' => 'Bonus compétences (chapeaux, capes). Jet : 1d20 + mod + bonus. Équip. max +5, forgemagie +3 (2.2.2).',
        'acrobatics_passive_object' => 'Bonus Acrobaties passive (chapeaux). Total sans jet +10 (2.2.2.4). Équip. max +3, forgemagie +2 (2.2.2).',
        'animal_handling_passive_object' => 'Bonus Dressage passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'arcana_passive_object' => 'Bonus Arcanes passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'athletics_passive_object' => 'Bonus Athlétisme passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'deception_passive_object' => 'Bonus Supercherie passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'history_passive_object' => 'Bonus Histoire passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'insight_passive_object' => 'Bonus Perspicacité passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'intimidation_passive_object' => 'Bonus Intimidation passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'investigation_passive_object' => 'Bonus Investigation passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'medicine_passive_object' => 'Bonus Médecine passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'nature_passive_object' => 'Bonus Nature passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'perception_passive_object' => 'Bonus Perception passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'performance_passive_object' => 'Bonus Représentation passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'persuasion_passive_object' => 'Bonus Persuasion passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'religion_passive_object' => 'Bonus Religion passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'sleight_of_hand_passive_object' => 'Bonus Escamotage passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'stealth_passive_object' => 'Bonus Discrétion passif (chapeaux). Total sans jet +10 (2.2.2.4).',
        'survival_passive_object' => 'Bonus Survie passif (chapeaux). Total sans jet +10 (2.2.2.4).',"""

t = t.replace(
    "        'name_object' => '#616161',\n        // —— Creature : vie, PA, PM, CA, initiative, portée, touche ——",
    "        'name_object' => '#616161',\n        'description_object' => '#78909c',\n        // —— Creature : vie, PA, PM, CA, initiative, portée, touche ——",
)

if old_desc not in t:
    raise SystemExit("descriptions block not found")
t = t.replace(old_desc, new_desc)

p.write_text(t, encoding="utf-8")
print("OK", p)
