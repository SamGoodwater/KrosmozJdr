#!/usr/bin/env python3
"""Reconstruit ecaflip-level-1.json et ecaflip-progression.json depuis la fiche Obsidian.

Même modèle que build-iop-spells-from-sheet.py. Les lectures Pile/Face restent
dans le texte d'effet ; les sous-effets portent le socle jouable (dégâts, soin,
déplacement, retrait, invocation…).

Pas de dofusdb_id : des sorts scrapés portent déjà ces IDs Dofus. L'importeur
retrouve les lignes JDR via official_id (jdr:ecaflip:…).

Usage :
  python3 scripts/build-ecaflip-spells-from-sheet.py
  python3 scripts/audit-class-spell-sheets.py
"""
from __future__ import annotations

import importlib.util
import json
import re
import sys
from pathlib import Path

sys.dont_write_bytecode = True

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location("audit", ROOT / "scripts/audit-class-spell-sheets.py")
audit = importlib.util.module_from_spec(spec)
spec.loader.exec_module(audit)

SHEET = audit.SHEETS / "Ecaflip.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {"air": ("agi", "Agilité"), "fire": ("intel", "Intelligence"), "water": ("chance", "Chance"), "earth": ("strong", "Force")}
CHATON = "jdr:summon:ecaflip-chaton"


def dice(cell: str, default: str = "") -> str:
    match = re.search(r"\d+d\d+", cell or "")
    if match:
        return match.group(0)
    match = re.search(r"\d+", cell or "")
    return match.group(0) if match else default


def all_dice(cell: str) -> list[str]:
    return re.findall(r"\d+d\d+", cell or "")


def num(cell: str, default: str = "") -> str:
    match = re.search(r"\d+", cell or "")
    return match.group(0) if match else default


def zone(r: dict, fallback: str = "point") -> str:
    return audit.canon_zone(r.get("zone") or "") or fallback


def hit(element: str, value: str, area: str = "point") -> dict:
    key, label = STAT[element]
    return {
        "slug": "frapper",
        "area": area,
        "params": {
            "characteristic": element,
            "value": f"{value}+{label}",
            "value_formula": f"{value}+[{key}]",
            "dice_formula": f"{value}+[{key}]",
            "effect_direction": "action",
        },
    }


def heal(value: str, area: str = "point", turns: str | None = None) -> dict:
    sub = {
        "slug": "soigner",
        "area": area,
        "params": {"value": value, "value_formula": value, "dice_formula": value, "effect_direction": "action"},
    }
    if turns:
        sub["duration_formula"] = turns
        sub["params"]["duration_formula"] = turns
    return sub


def shield(value: str, area: str = "point", direction: str = "action", turns: str = "1") -> dict:
    return {
        "slug": "protéger",
        "area": area,
        "duration_formula": turns,
        "params": {
            "value": value,
            "value_formula": value,
            "dice_formula": value,
            "effect_direction": direction,
            "duration_formula": turns,
        },
    }


def boost(characteristic: str, value: str, turns: str, direction: str = "action", area: str = "point") -> dict:
    params = {
        "characteristic": characteristic,
        "value": value,
        "value_formula": value,
        "effect_direction": direction,
        "duration_formula": turns,
    }
    if "d" in value:
        params["dice_formula"] = value
    return {"slug": "booster", "area": area, "duration_formula": turns, "params": params}


def strip(characteristic: str, value: str, area: str = "point", turns: str = "1") -> dict:
    return {
        "slug": "retirer",
        "area": area,
        "duration_formula": turns,
        "params": {
            "characteristic": characteristic,
            "value": value,
            "value_formula": value,
            "effect_direction": "action",
            "duration_formula": turns,
        },
    }


def other(text: str, area: str = "point", turns: str | None = None) -> dict:
    sub = {"slug": "autre", "area": area, "params": {"value": text}}
    if turns:
        sub["duration_formula"] = turns
        sub["params"]["duration_formula"] = turns
    return sub


def move(kind: str, cells: str, area: str = "point") -> dict:
    params = {"cells_formula": cells, "movement_kind": kind, "effect_direction": "action"}
    if kind == "teleport":
        params["teleport"] = True
    return {"slug": "déplacer", "area": area, "params": params}


def summon(monster_id: str = CHATON) -> dict:
    return {"slug": "invoquer", "area": "point", "params": {"monster_official_id": monster_id}}


def push_cells(r: dict, default: str = "1") -> str:
    cell = r.get("deplac") or ""
    match = re.search(r"(?:repousse|attire|bond|saut).*?(\d+)", cell, re.I)
    if match:
        return match.group(1)
    return num(cell, default)


# key, name, slot, order, types, element, save_key, make, extras
SPELLS = [
    # Air
    ("reflexes", "Réflexes", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"])), move("jump", push_cells(r, "1"))], {}),
    ("griffe-de-ceangal", "Griffe de Ceangal", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"])), move("push", push_cells(r, "1")),
                other("Pile : avance au contact. Face : repousse. Consommation (niv. 7+) : les deux.")], {}),
    ("coussinets", "Coussinets", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"])), move("jump", push_cells(r, "2")),
                other("Pile : bond arrière. Face : repousse. Consommation : les deux.")], {}),
    ("toupet", "Toupet", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"]), zone(r)), move("pull", push_cells(r, "3"), zone(r)),
                other("Pile : attire. Face : repousse. Consommation : avance, dégâts, repousse.")], {}),
    # Feu
    ("topkaj", "Topkaj", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"])), heal(dice(r["autres"] or "1d4")),
                other("Sans état : dégâts seuls. Pile : tu te soignes. Face : allié·e au contact. Consommation (niv. 7+) : les deux.")], {}),
    ("pelotage", "Pelotage", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"])), heal("1/2 dommages infligés"),
                other("Sans état : dégâts seuls. Pile / Face : soin selon la pièce. Consommation (niv. 7+) : les deux.")], {}),
    ("langue-rapeuse", "Langue Râpeuse", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"]), zone(r)), heal(dice(r["autres"] or "1d4"), zone(r)),
                other("Ennemi·e·s frappé·e·s, allié·e·s soigné·e·s. Toi sauf.")],
     {"spares_chosen_targets": True}),
    ("peril", "Péril", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"])), heal("moitié des dégâts"),
                other("Sans état : dégâts seuls. Pile : soin moitié. Face : +dégâts. Consommation : deux soins.")], {}),
    # Eau
    ("yams", "Yams", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"])), other((r.get("autres") or "Critique augmenté.").strip())], {}),
    ("baraka", "Baraka", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"])), other("Impossible de faire des critiques.", turns="1")], {}),
    ("griffe-joueuse", "Griffe Joueuse", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"]))], {}),
    ("infortune", "Infortune", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"])),
                boost("dommages_spell", num(r["debuff"] or "5", "5"), "1", direction="self"),
                other("Pile : +dommages lanceur. Face : −dommages cible. Consommation : les deux.")], {}),
    # Terre
    ("pile-ou-face", "Pile ou Face", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"])), strip("pa", num(r["debuff"] or "1", "1")),
                other("Sans état : dégâts seuls. Pile : −PA. Face : −PM. Consommation (niv. 7+) : les deux.")], {}),
    ("belote", "Belote", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"])), move("teleport", "1"),
                other("Sans état : dégâts seuls. Pile : tu te téléportes. Face : tu téléportes la cible. Consommation (niv. 7+) : les deux.")], {}),
    ("lapement", "Lapement", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"])), strip("pa", num(r["debuff"] or "1", "1")),
                other("Sans état : dégâts seuls. Pile : −PA. Face : −PM. Consommation : les deux.")], {}),
    ("destin-d-ecaflip", "Destin d'Ecaflip", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r.get("degat") or "2d10")), heal(dice(r.get("degat") or "2d10")),
                move("push", push_cells(r, "4")),
                other("Sans état : dégâts seuls. Pile : vol de vie. Face : attire ou repousse. Consommation : les deux.")], {}),
    # Généraux niv. 1
    ("bond-du-felin", "Bond du Félin", 2, 0, ["Placement"], None, None,
     lambda r: [move("jump", push_cells(r, "2")), boost("pm", num(r["buff"] or "1", "1"), "1"),
                other("Soit saut, soit +PM à un·e allié·e. +1 case / +1 PM si Agi ≥ 3.")], {}),
    ("perception", "Perception", 2, 1, ["Soin"], None, None,
     lambda r: [heal(dice(r["autres"] or "1d4"), zone(r)),
                other("Révèle les invisibles. +1d4 s'il y a un·e ennemi·e, +1d4 si Intel ≥ 3.", zone(r))], {}),
    ("entrechat", "Entrechat", 3, 0, ["Placement"], None, "agi",
     lambda r: [move("teleport", "1"), other("Saut ou échange de place. Portée +1 si Agi ≥ 3.")], {}),
    ("bonne-etoile", "Bonne Étoile", 3, 1, ["Protection"], None, None,
     lambda r: [heal(dice(r["autres"] or "1d4")),
                shield(dice(r["protect"] or "1d4")),
                other("Bouclier au prochain critique porté par la cible. +1 dé si Vit ≥ 3.")], {}),
    # Niv. 2
    ("odorat", "Odorat", 1, 0, ["Entrave"], None, "strong",
     lambda r: [strip("pa", num(r["debuff"] or "1", "1"), zone(r)),
                boost("pm", num(r["buff"] or "1", "1"), "1", direction="self"),
                other("Toi sauf du retrait. Au tour suivant : perds les PM, gagne autant de PA.", zone(r))],
     {"spares_chosen_targets": True}),
    ("neuf-vies", "Neuf Vies", 1, 1, ["Soin"], None, None,
     lambda r: [heal(dice(r["autres"] or "1d4")),
                heal(dice(r["autres"] or "1d4"), turns="1"),
                other("Second soin au début du prochain tour. +1 dé si Intel ≥ 3.")], {}),
    # Niv. 5
    ("tarot", "Tarot d'Ecaflip", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("1d4 sur la zone (toi compris) : 1 +PM, 2 +PA, 3 −PM, 4 bouclier. Relance du dé si Sag ≥ 3.", zone(r))], {}),
    ("griffe-invocatrice", "Griffe Invocatrice", 1, 1, ["Invocation"], None, None,
     lambda r: [summon(), other("Sans état : mêlé. Pile : enragé. Face : affectueux. Consommation (niv. 7+) : les deux au dé augmenté.")], {}),
    # Niv. 6
    ("roue-de-la-fortune", "Roue de la Fortune", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Critiques un cran plus tôt (deux si Sag ≥ 3). Prochain dé Tarot/Roulette lancé deux fois.", turns="1")], {}),
    ("seconde-chance", "Seconde Chance", 1, 1, ["Protection"], None, None,
     lambda r: [shield(dice(r["protect"] or "2d4")),
                other("+1d4 subi au tour d'après. Bouclier +1 dé si Vit ≥ 3.", turns="1")], {}),
    # Niv. 8
    ("bonne-pioche", "Bonne Pioche", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Le PA revient. +1 PM si Chance ≥ 3.", turns="1")], {}),
    ("chateau-de-cartes", "Château de Cartes", 1, 1, ["Protection"], None, None,
     lambda r: [shield(dice(r["protect"] or "2d4"))], {}),
    # Niv. 10
    ("redistribution", "Redistribution", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Le PA revient. Relance la pièce en plus du lancer du tour.")], {}),
    ("predation", "Prédation", 1, 1, ["Entrave"], None, "strong",
     lambda r: [strip("pm", num(r["debuff"] or "1", "1"), zone(r)),
                other("Révèle les invisibles. Allié·e·s saufs.", zone(r))],
     {"spares_chosen_targets": True}),
    # Niv. 11
    ("roulette", "Roulette", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("1d4 zone : voir Effets. Le PA revient. Relance si Chance ≥ 3. Face 3 peut frapper (neutre).", zone(r))], {}),
    ("tout-ou-rien", "Tout ou Rien", 1, 1, ["Soin", "Dégâts"], None, None,
     lambda r: [
         heal(dice(r.get("autres") or "2d4"), zone(r)),
         other(
             f"Allié·e·s soigné·e·s. Ennemi·e·s : {dice(r.get('degat') or '2d6')} "
             "de l'élément du meilleur mod. parmi Force, Intelligence, Chance, Agilité.",
             zone(r),
         ),
     ], {}),
]


def sheet_text(path: Path) -> dict[str, dict]:
    texts: dict[str, dict] = {}
    current = None
    for line in path.read_text().splitlines():
        if re.match(r"^#{3,6}\s+Niv\s+\d+", line) or re.match(r"^\*\*Variante\s+\d+", line):
            current = audit.spell_name_from(line)
            if current:
                texts[current] = {}
            continue
        if current is None:
            continue
        for label, key in (("*Général*", "description"), ("*Effets*", "effect"), ("*Résolution*", "resolution")):
            if line.startswith(label):
                texts[current][key] = re.sub(r"^\*[^*]+\*\s*:\s*", "", line).strip()
    return texts


def build() -> None:
    sheet = {spell["name"]: spell for spell in audit.parse_sheet(SHEET)}
    texts = sheet_text(SHEET)
    missing = [name for _, name, *_ in SPELLS if name not in sheet]
    if missing:
        raise SystemExit("Sorts absents de la fiche : " + ", ".join(missing))

    level_1, progression = [], []
    for key, name, slot, order, types, element, save_key, make, extras in SPELLS:
        spell = sheet[name]
        rows = audit.resolve_rows(spell["rows"])
        text = texts.get(name, {})
        mode = audit.resolution_mode(text.get("resolution", ""))
        base = audit.usage_dict(rows[0])
        entry = {
            "key": key,
            "name": name,
            "dofusdb_id": None,
            "official_id": f"jdr:ecaflip:{key}",
            "slot_index": slot,
            "choice_order": order,
            "character_level": rows[0]["niv"],
            "types": types,
            "state": "playable",
            "is_magic": False,
            "resolution_mode": mode,
            "description": text.get("description", ""),
            "effect": text.get("effect", ""),
            **base,
        }
        if element:
            entry["element"] = element
            entry["attack_characteristic_key"] = STAT[element][0]
        if mode == "saving_throw":
            stat = save_key or (STAT[element][0] if element else "sagesse")
            entry["attack_characteristic_key"] = stat
            entry["save_characteristic_key"] = stat
            entry["save_dc_formula"] = DC
            entry["save_success_note"] = text.get("resolution", "")
        elif save_key and mode == "attack_roll":
            entry["attack_characteristic_key"] = save_key
        entry.update(extras)
        entry["sub_effects"] = [dict(sub, order=index) for index, sub in enumerate(make(rows[0]))]
        tiers = []
        for index, row in enumerate(rows[1:]):
            tier = {
                "tier": audit.ROMAN[index],
                "required_creature_level": row["niv"],
                "effect": "",
                "sub_effects": [dict(sub, order=i) for i, sub in enumerate(make(row))],
            }
            for usage_key, value in audit.usage_dict(row).items():
                if base.get(usage_key) != value:
                    tier[usage_key] = value
            tiers.append(tier)
        entry["intensification"] = tiers
        (level_1 if entry["character_level"] == 1 else progression).append(entry)

    for filename, entries in (("ecaflip-level-1.json", level_1), ("ecaflip-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Ecaflip", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
