#!/usr/bin/env python3
"""Reconstruit pandawa-level-1.json et pandawa-progression.json depuis Pandawa.md."""
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

SHEET = audit.SHEETS / "Pandawa.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}
PANDAWASTA = "jdr:summon:pandawa-pandawasta"
BAMBOU = "jdr:summon:pandawa-bambou"


def dice(cell: str, default: str = "") -> str:
    match = re.search(r"\d+d\d+", cell or "")
    return match.group(0) if match else default


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


def heal(value: str, area: str = "point") -> dict:
    return {
        "slug": "soigner",
        "area": area,
        "params": {"value": value, "value_formula": value, "dice_formula": value, "effect_direction": "action"},
    }


def shield(value: str, area: str = "point", turns: str = "1") -> dict:
    return {
        "slug": "protéger",
        "area": area,
        "duration_formula": turns,
        "params": {
            "value": value,
            "value_formula": value,
            "dice_formula": value,
            "effect_direction": "action",
            "duration_formula": turns,
        },
    }


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


def boost(characteristic: str, value: str, turns: str = "1", area: str = "point") -> dict:
    params = {
        "characteristic": characteristic,
        "value": value,
        "value_formula": value,
        "effect_direction": "action",
        "duration_formula": turns,
    }
    if "d" in value:
        params["dice_formula"] = value
    return {"slug": "booster", "area": area, "duration_formula": turns, "params": params}


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


def summon(monster_id: str) -> dict:
    return {"slug": "invoquer", "area": "point", "params": {"monster_official_id": monster_id}}


SPELLS = [
    ("schnaps", "Schnaps", "12782", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([move("push", num(r["deplac"] or "1", "1") or "1")] if num(r.get("deplac") or "") else [])],
     {"allows_reaction": True}),
    ("souffle-enflamme", "Souffle Enflammé", "12805", 1, 1, ["Tank", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                other((r.get("debuff") or "Tank / menace.").strip() or "Menace.", turns="1")],
     {"allows_reaction": True}),
    ("ethylo", "Ethylo", "12791", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([strip("po", "1", turns="1")] if "po" in (r.get("debuff") or "").lower() else [])],
     {"allows_reaction": True}),
    ("ribote", "Ribote", "12786", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6"))],
     {"allows_reaction": True}),
    ("karcham", "Karcham", "12787", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Sans portage : soulève. Avec portage : lance. Consentant·e ou inconscient·e.")],
     {}),
    ("main-de-pandawa", "Main de Pandawa", "24039", 2, 1, ["Protection"], None, None,
     lambda r: [other("−1 dégât par coup (−2 si Vit ≥ 3).", turns="1")],
     {}),
    ("epouvante", "Épouvante", "12783", 3, 0, ["Placement"], None, None,
     lambda r: [move("push", num(r["deplac"] or "2", "2") or "2"), other("Recul. Réaction possible.")],
     {"allows_reaction": True}),
    ("consolation", "Consolation", "12806", 1, 0, ["Soin", "Placement"], None, None,
     lambda r: [heal(dice(r.get("soin") or "1d6")), move("pull", "1"), other("Attire un·e allié·e à ton contact.")],
     {"allows_reaction": True}),
    ("pandanlku", "Pandanlku", "12798", 2, 0, ["Amélioration"], None, None,
     lambda r: [boost("pm", "1", "1"), other("+2 PM si Agi ≥ 3.", turns="1")],
     {}),
    ("propulsion", "Propulsion", "12788", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), move("push", "1"), other("Jet : il faut porter.")],
     {}),
    ("pandikulation", "Pandikulation", "12793", 1, 1, ["Tank", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4"), zone(r)),
                shield(dice(r.get("protect") or "1d6")), move("pull", "1", zone(r)),
                other("Jet : il faut porter.")],
     {}),
    ("eau-de-vie", "Eau-de-vie", "12808", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")), strip("pm", "1", turns="1"), other("Jet : il faut porter.")],
     {}),
    ("brancard", "Brancard", "12811", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4")), move("jump", "2"), other("Jet : il faut porter. Tu avances.")],
     {}),
    ("lien-spiritueux", "Lien Spiritueux", "12799", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(PANDAWASTA), other("Compte dans la limite d’invocations. Monture possible.")],
     {}),
    ("fermentation", "Fermentation", "12819", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d6")), other("Second bouclier au prochain tour.", turns="1")],
     {}),
    ("chamrak", "Chamrak", "12810", 1, 0, ["Placement"], None, None,
     lambda r: [move("pull", "2"), other("Attirance puis portage (comme Karcham).")],
     {}),
    ("varappe", "Varappe", "12813", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("Intaclable. Karcham +1 / tour. +1 PM si Agi ≥ 3.", turns="1")],
     {}),
    ("souffle-alcoolise", "Souffle Alcoolisé", "12784", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4"), zone(r)), move("push", "2", zone(r))],
     {}),
    ("absinthe", "Absinthe", "12820", 1, 1, ["Tank", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4")), heal("moitié"), other("Soin de la moitié des dégâts.")],
     {}),
    ("distillation", "Distillation", "12815", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4"), zone(r)), other("Indéplaçable 1 tour.", zone(r), turns="1")],
     {}),
    ("gueule-de-bois", "Gueule de Bois", "12803", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")), other("+1d6 si tu portes.")],
     {}),
    ("bambouseraie", "Bambouseraie", "12785", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(BAMBOU), other("Bambous 1 PV, ne comptent pas dans la limite. 1 tour. Ligne 5 si Cha ≥ 3.")],
     {}),
    ("stabilisation", "Stabilisation", "12789", 2, 0, ["Protection"], None, None,
     lambda r: [other("Enraciné·e. +1 CA si Vit ≥ 3.", turns="1")],
     {}),
    ("lait-de-bambou", "Lait de Bambou", "24038", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Effets néfastes −1 tour (−2 si Sag ≥ 3).")],
     {}),
    ("prohibition", "Prohibition", "12817", 2, 0, ["Entrave", "Protection"], None, None,
     lambda r: [other("Moitié dégâts au contact. Insoignable.", turns="1")],
     {}),
    ("brassage", "Brassage", "12816", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("+1d6 au premier coup subi (+2d4 si Intel ≥ 3).", turns="1")],
     {}),
    ("souillure", "Souillure", "12792", 2, 0, ["Protection"], None, None,
     lambda r: [other("−1 dégât par coup. Bonus −1 tour (−2 si Cha ≥ 3).", turns="1")],
     {}),
    ("engourdissement", "Engourdissement", "12807", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6"), zone(r)),
                boost("pm", "1", "1"), other("+1 PM par ennemi·e touché·e, max 3.", zone(r))],
     {}),
    ("flasque-explosive", "Flasque Explosive", "12796", 1, 1, ["Tank", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6"), zone(r)), shield(dice(r.get("protect") or "2d6"), zone(r))],
     {}),
    ("vague-a-lame", "Vague à Lame", "12794", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6"), zone(r)), strip("pa", "1", zone(r), turns="1")],
     {}),
    ("pandatak", "Pandatak", "12797", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d8"), zone(r))],
     {}),
]


def usage_from(row: dict, meta: str = "") -> dict:
    usage = audit.usage_dict(row)
    usage.update(audit.los_flags(" ".join([row.get("los") or "", row.get("po") or "", meta])))
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
            "official_id": f"jdr:pandawa:{key}",
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
            stat = save_key or "strong"
            entry["attack_characteristic_key"] = stat
            entry["save_characteristic_key"] = stat
            entry["save_dc_formula"] = DC
            entry["save_success_note"] = meta
        if "réaction" in meta.lower() or spell.get("reaction"):
            entry["allows_reaction"] = True
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

    for filename, entries in (("pandawa-level-1.json", level_1), ("pandawa-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Pandawa", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
