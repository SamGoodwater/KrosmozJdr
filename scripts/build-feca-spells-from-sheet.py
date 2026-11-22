#!/usr/bin/env python3
"""Reconstruit feca-level-1.json et feca-progression.json depuis la fiche Obsidian Feca.

Usage :
  python3 scripts/build-feca-spells-from-sheet.py
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

SHEET = audit.SHEETS / "Feca.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}


def dice(cell: str, default: str = "") -> str:
    match = re.search(r"\d+d\d+", cell or "")
    if match:
        return match.group(0)
    match = re.search(r"\d+", cell or "")
    return match.group(0) if match else default


def all_dice(cell: str) -> list[str]:
    return re.findall(r"\d+d\d+", cell or "")


def num(cell: str, default: str = "") -> str:
    match = re.search(r"-?\d+", cell or "")
    return match.group(0).lstrip("-") if match else default


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


def hit_neutral(value: str, area: str = "point") -> dict:
    return {
        "slug": "frapper",
        "area": area,
        "params": {
            "characteristic": "neutral",
            "value": value,
            "value_formula": value,
            "dice_formula": value,
            "effect_direction": "action",
        },
    }


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


def move(kind: str, cells: str, area: str = "point", turns: str | None = None) -> dict:
    params = {"cells_formula": cells, "movement_kind": kind, "effect_direction": "action"}
    if kind == "teleport":
        params["teleport"] = True
    sub = {"slug": "déplacer", "area": area, "params": params}
    if turns:
        sub["duration_formula"] = turns
    return sub


def push_cells(r: dict, default: str = "1") -> str:
    return num(r.get("deplac") or "", default) or default


def strip_amount(cell: str, default: str = "") -> str:
    return num(cell or "", default)


# key, name, dofusdb_id, slot, order, types, element, save_key, make, extras
SPELLS = [
    # Niveau 1 — voies
    (
        "silbo",
        "Silbo",
        "13011",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"]), zone(r)),
            move("push", push_cells(r), zone(r)),
            other("Toi épargné·e.", zone(r)),
        ],
        {"allows_reaction": True, "spares_chosen_targets": True},
    ),
    (
        "houlette",
        "Houlette",
        "13007",
        1,
        1,
        ["Entrave", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"])),
            *([strip("pm", strip_amount(r["debuff"]), turns="1")] if strip_amount(r.get("debuff") or "") else []),
        ],
        {"allows_reaction": True},
    ),
    (
        "bulle",
        "Bulle",
        "12994",
        1,
        2,
        ["Protection", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"])),
            *([shield(dice(r["protect"]))] if dice(r.get("protect") or "") else []),
            other("À partir du niv. 5 : bouclier à toi ou un·e allié·e à 1 case de la cible si le coup porte."),
        ],
        {"allows_reaction": True},
    ),
    (
        "retour-du-baton",
        "Retour du Bâton",
        "12983",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"])),
            *([strip("pa", strip_amount(r["debuff"]), turns="1")] if strip_amount(r.get("debuff") or "") else []),
        ],
        {"allows_reaction": True},
    ),
    # Niveau 1 — généraux
    (
        "rempart",
        "Rempart",
        "12981",
        2,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            shield(all_dice(r["protect"])[0] if all_dice(r["protect"]) else "2d4", zone(r)),
            other("Bouclier +1 dé si Vitalité ≥ 3.", zone(r)),
        ],
        {},
    ),
    (
        "excursion",
        "Excursion",
        "13024",
        2,
        1,
        ["Placement"],
        None,
        None,
        lambda r: [
            other("Glyphe, durée 2 tours. Échange de place avec un·e allié·e dessus.", zone(r), turns="2"),
            move("teleport", "1", zone(r)),
            other("Portée +1 si Agilité ≥ 3."),
        ],
        {"allows_reaction": True, "target_type": "glyph", "duration": "2 tours"},
    ),
    (
        "bouclier-feca",
        "Bouclier Féca",
        "12982",
        3,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            shield(all_dice(r["protect"])[0] if all_dice(r["protect"]) else "2d4"),
            other("Bouclier +1 dé si Intelligence ≥ 3."),
        ],
        {},
    ),
    (
        "renfort",
        "Renfort",
        "13020",
        3,
        1,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("jump", "1"),
            other("Saut sur une case libre au contact de l’allié·e. Portée +1 si Agilité ≥ 3."),
        ],
        {"allows_reaction": True},
    ),
    # Niveau 2
    (
        "fortification",
        "Fortification",
        "13009",
        1,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            shield(all_dice(r["protect"])[0] if all_dice(r["protect"]) else "2d6"),
            other("Bouclier +1 dé si Vitalité ≥ 3."),
        ],
        {},
    ),
    (
        "bergerie",
        "Bergerie",
        "12991",
        1,
        1,
        ["Entrave"],
        None,
        "chance",
        lambda r: [
            strip("pm", strip_amount(r["debuff"] or "1", "1"), zone(r), turns="2"),
            other("Ne peut pas être poussé·e tant que dessus.", zone(r), turns="2"),
        ],
        {"target_type": "glyph", "duration": "2 tours"},
    ),
    # Niveau 4
    (
        "frisson",
        "Frisson",
        "12989",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"]), zone(r)),
            move("pull", push_cells(r), zone(r)),
        ],
        {},
    ),
    (
        "lethargie",
        "Léthargie",
        "12993",
        1,
        1,
        ["Entrave", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"])),
            other((r.get("debuff") or "−5 esquive PM, 1 tour").strip(), turns="1"),
        ],
        {},
    ),
    (
        "nimbus",
        "Nimbus",
        "12984",
        1,
        2,
        ["Protection", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"]), zone(r)),
            shield(dice(r["protect"] or "1d4"), zone(r)),
            other("Allié·e·s épargné·e·s sur les dégâts.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "somnolence",
        "Somnolence",
        "12977",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"]), zone(r)),
            other((r.get("debuff") or "Prochain coup reçu +1d4").strip(), turns="1"),
            other("Allié·e·s épargné·e·s.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
    # Niveau 5
    (
        "bastion",
        "Bastion",
        "12995",
        1,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            other(
                "Toi et allié·e·s ignorez les dégâts des attaques de portée 2 ou plus. "
                "Le premier coup au contact met fin à l’effet pour chacun.",
                zone(r),
                turns="1",
            )
        ],
        {},
    ),
    (
        "langueur",
        "Langueur",
        "12978",
        1,
        1,
        ["Entrave", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"])),
            other((r.get("debuff") or "Critique un cran plus tard, 1 tour").strip(), turns="1"),
        ],
        {"image_filter": "violet"},
    ),
    # Niveau 6
    (
        "ataraxie",
        "Ataraxie",
        "13027",
        1,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            other((r.get("protect") or "Réduit le prochain dégât").strip(), turns="1"),
            strip("pm", "1", turns="1"),
            other("Réduction +1 dé si Intelligence ≥ 3."),
        ],
        {},
    ),
    (
        "transhumance",
        "Transhumance",
        "12986",
        1,
        1,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("jump", "1"),
            other("Si la case d’arrivée est dans un de tes glyphes, ce glyphe se déclenche. Portée +1 si Chance ≥ 3."),
        ],
        {"allows_reaction": True},
    ),
    # Niveau 7 — glyphes
    (
        "prairie",
        "Prairie",
        "12992",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"]), zone(r)),
            other((r.get("debuff") or "−5 esquive PM tant que dessus").strip(), zone(r), turns="2"),
        ],
        {"target_type": "glyph", "duration": "2 tours"},
    ),
    (
        "terre-brulee",
        "Terre Brûlée",
        "12985",
        1,
        1,
        ["Entrave", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"]), zone(r)),
            other("Indéplaçable tant que dessus.", zone(r), turns="2"),
        ],
        {"target_type": "glyph", "duration": "2 tours"},
    ),
    (
        "vallee",
        "Vallée",
        "12990",
        1,
        2,
        ["Protection", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"]), zone(r)),
            other((r.get("protect") or "−1d4 aux dégâts reçus tant que dessus").strip(), zone(r), turns="2"),
        ],
        {"target_type": "glyph", "duration": "2 tours"},
    ),
    (
        "terre-battue",
        "Terre Battue",
        "12987",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"]), zone(r)),
            strip("pa", strip_amount(r["debuff"] or "1", "1"), zone(r), turns="2"),
        ],
        {"target_type": "glyph", "duration": "2 tours"},
    ),
    # Niveau 8
    (
        "mise-en-garde",
        "Mise en Garde",
        "12997",
        1,
        0,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            other((r.get("buff") or "+1 à un mod. de voie").strip(), turns="1"),
        ],
        {},
    ),
    (
        "pavois",
        "Pavois",
        "13016",
        1,
        1,
        ["Protection"],
        None,
        None,
        lambda r: [
            other((r.get("autres") or "2 cases obstacles").strip(), turns="1"),
        ],
        {},
    ),
    # Niveau 10
    (
        "barriere",
        "Barrière",
        "13019",
        1,
        0,
        ["Entrave", "Dégâts"],
        None,
        None,
        lambda r: [
            strip("pm", strip_amount(r["debuff"] or "1", "1"), turns="1"),
            hit_neutral(dice(r["degat"] or "1d6"), "cross-0-1"),
            other("Glyphe neutre croix 1 au début du prochain tour de la cible, puis disparaît.", turns="1"),
        ],
        {},
    ),
    (
        "egide",
        "Égide",
        "13018",
        1,
        1,
        ["Protection", "Invocation"],
        None,
        None,
        lambda r: [
            other("Invoque un bouclier immobile (ton niveau, 2 tours, ne se déplace pas, n’attaque pas).", turns="2"),
            shield(dice(r["protect"] or "2d4")),
            other("Bouclier aux allié·e·s au contact à l’apparition."),
        ],
        {"duration": "2 tours"},
    ),
    # Niveau 11
    (
        "defiance",
        "Défiance",
        "12988",
        1,
        0,
        ["Amélioration", "Dégâts"],
        None,
        None,
        lambda r: [
            hit_neutral(dice(r["degat"] or "1d4"), zone(r)),
            boost("ca", num(r.get("buff") or "1", "1"), "1"),
            other("Glyphe croix 1 sous la cible, durée 1 tour. +2 CA si Sagesse ≥ 3.", zone(r), turns="1"),
        ],
        {"target_type": "glyph", "duration": "1 tour"},
    ),
    (
        "barricade",
        "Barricade",
        "12979",
        1,
        1,
        ["Protection"],
        None,
        None,
        lambda r: [
            other(
                "Toi et allié·e·s ignorez les dégâts des attaques au contact. "
                "Le premier coup de portée 2 ou plus met fin à l’effet pour chacun.",
                zone(r),
                turns="1",
            )
        ],
        {},
    ),
    # Niveau 12
    (
        "manoeuvre",
        "Manœuvre",
        "13012",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"])),
            move("teleport", "1"),
            other("Échange de place si le coup porte. Ne traverse pas un mur."),
        ],
        {},
    ),
    (
        "atonie",
        "Atonie",
        "13014",
        1,
        1,
        ["Entrave", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"]), zone(r)),
            other((r.get("debuff") or "−1 cran de résistances, 1 tour").strip(), zone(r), turns="1"),
            other("Allié·e·s épargné·e·s.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "stratus",
        "Stratus",
        "13015",
        1,
        2,
        ["Protection", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"]), zone(r)),
            other("Toi et allié·e·s : Résistant, 1 tour.", zone(r), turns="1"),
            other("Allié·e·s épargné·e·s sur les dégâts.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "tetanie",
        "Tétanie",
        "13017",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"])),
            other("Indéplaçable, 1 tour.", turns="1"),
        ],
        {},
    ),
]


def usage_from(row: dict, meta: str = "") -> dict:
    usage = audit.usage_dict(row)
    flags = audit.los_flags(" ".join([row.get("los") or "", row.get("po") or "", meta]))
    usage.update(flags)
    return usage


def is_magic_from(meta: str) -> bool:
    lowered = (meta or "").lower()
    if "wakfu" in lowered:
        return True
    if "physique" in lowered:
        return False
    return False


def build() -> None:
    sheet = {spell["name"]: spell for spell in audit.parse_sheet(SHEET)}
    missing = [name for _, name, *_ in SPELLS if name not in sheet]
    if missing:
        raise SystemExit("Sorts absents de la fiche : " + ", ".join(missing))

    level_1, progression = [], []
    for key, name, dofus_id, slot, order, types, element, save_key, make, extras in SPELLS:
        spell = sheet[name]
        rows = audit.resolve_rows(spell["rows"])
        meta = spell.get("meta") or spell.get("resolution") or ""
        mode = audit.resolution_mode(meta) or "auto_success"
        base = usage_from(rows[0], meta)
        effect = spell.get("effect") or ""
        if spell.get("reaction"):
            effect = (effect + " Réaction : " + spell["reaction"]).strip()

        entry = {
            "key": key,
            "name": name,
            "dofusdb_id": dofus_id,
            "official_id": f"jdr:feca:{key}",
            "slot_index": slot,
            "choice_order": order,
            "character_level": rows[0]["niv"],
            "types": types,
            "state": "playable",
            "is_magic": is_magic_from(meta),
            "resolution_mode": mode,
            "description": spell.get("description") or "",
            "effect": effect,
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
            entry["save_success_note"] = meta
        if mode == "attack_roll" and element:
            entry["attack_characteristic_key"] = STAT[element][0]
        if "réaction" in meta.lower() or spell.get("reaction"):
            entry["allows_reaction"] = True
        if "glyphe" in meta.lower():
            entry.setdefault("target_type", "glyph")
        if "épargn" in meta.lower() or "allié·e·s épargn" in (spell.get("effect") or "").lower():
            entry["spares_chosen_targets"] = True
        if "violet" in meta.lower():
            entry["image_filter"] = "violet"
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
            for usage_key, value in usage_from(row, meta).items():
                if base.get(usage_key) != value:
                    tier[usage_key] = value
            tiers.append(tier)
        entry["intensification"] = tiers
        (level_1 if entry["character_level"] == 1 else progression).append(entry)

    for filename, entries in (("feca-level-1.json", level_1), ("feca-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Féca", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
