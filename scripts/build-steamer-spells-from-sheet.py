#!/usr/bin/env python3
"""Reconstruit steamer-level-1.json et steamer-progression.json depuis Steamer.md."""
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

SHEET = audit.SHEETS / "Steamer.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
HARP = "jdr:summon:steamer-harponneuse"
GARD = "jdr:summon:steamer-gardienne"
TACT = "jdr:summon:steamer-tactirelle"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}


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
    ("torpille", "Torpille", "13865", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([move("push", num(r.get("deplac") or "1", "1") or "1")]
                  if "recul" in (r.get("deplac") or "").lower() else []),
                other((r.get("autres") or "Via Harponneuse : cran supérieur.").strip())],
     {"allows_reaction": True}),
    ("flibuste", "Flibuste", "13860", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([heal("moitié")] if "vol" in (r.get("autres") or "").lower() else [])],
     {"allows_reaction": True}),
    ("longue-vue", "Longue-vue", "13837", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([boost("po", "1", "1")] if "po" in (r.get("autres") or "").lower() else [])],
     {"allows_reaction": True}),
    ("amarrage", "Amarrage", "13850", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")),
                *([move("pull", num(r.get("deplac") or "1", "1") or "1")]
                  if "attire" in (r.get("deplac") or "").lower() else []),
                other((r.get("autres") or "Via Harponneuse : cran supérieur. Sur tourelle : +1 PA.").strip())],
     {"allows_reaction": True}),
    ("harponneuse", "Harponneuse", "13829", 2, 0, ["Invocation"], None, None,
     lambda r: [summon(HARP), other("Rang I. Une seule. Neutre.")],
     {}),
    ("gardienne", "Gardienne", "13830", 2, 1, ["Invocation"], None, None,
     lambda r: [summon(GARD), other("Rang I. Une seule. Soin ou bouclier.")],
     {}),
    ("courant", "Courant", "13844", 3, 0, ["Placement"], None, "agi",
     lambda r: [move("pull", num(r.get("deplac") or "2", "2") or "2")],
     {"allows_reaction": True}),
    ("blindage", "Blindage", "13846", 3, 1, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d4"), turns="1")],
     {}),
    ("ancre", "Ancre", "13848", 1, 0, ["Entrave"], None, "sagesse",
     lambda r: [strip("pm", "1", turns="1")],
     {}),
    ("surtension", "Surtension", "13874", 2, 0, ["Amélioration"], None, None,
     lambda r: [other((r.get("buff") or "+1d4 prochain sort").strip(), turns="1")],
     {}),
    ("foene", "Foène", "13870", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6"), zone(r, "cross-0-1")),
                move("push", num(r.get("deplac") or "1", "1") or "1", zone(r, "cross-0-1")),
                other("Fourche 1 : repousse vers les extrémités.")],
     {}),
    ("sabotage", "Sabotage", "13858", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")),
                heal(dice(r.get("soin") or r.get("autres") or "1d6")),
                other("Sur tourelle : soin seulement.")],
     {}),
    ("aspiration", "Aspiration", "13838", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")),
                other((r.get("buff") or "+1 dégât prochain sort").strip(), turns="1")],
     {}),
    ("ressac", "Ressac", "13852", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")),
                other((r.get("autres") or "Via Harponneuse : cran supérieur. Sur tourelle : +2 PA.").strip())],
     {}),
    ("remorquage", "Remorquage", "13876", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Téléporte une tourelle.")],
     {"allows_reaction": True}),
    ("secourisme", "Secourisme", "13878", 2, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("soin") or "1d6"))],
     {}),
    ("piston", "Piston", "13880", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1")],
     {}),
    ("evolution", "Évolution", "13882", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("2 PA : +1 rang (1× III max, autres ≤ II). 3 PA : rang III jusqu’à fin de son tour, puis rang I.")],
     {}),
    ("harmattan", "Harmattan", "13872", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")),
                move("pull", num(r.get("deplac") or "2", "2") or "2")],
     {}),
    ("vapor", "Vapor", "13862", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4"), zone(r, "line-0-3")),
                heal(dice(r.get("soin") or "1d6"), zone(r, "line-0-3"))],
     {}),
    ("maree", "Marée", "13840", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4"), zone(r, "cross-0-1")),
                boost("pa", "1", "1")],
     {}),
    ("ancrage", "Ancrage", "13854", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")),
                other((r.get("buff") or r.get("autres") or "+1d4 si tourelle ≤2 cases").strip())],
     {}),
    ("tactirelle", "Tactirelle", "13884", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(TACT), other("Rang I. Une seule. Attire ou pousse.")],
     {}),
    ("scaphandre", "Scaphandre", "13888", 1, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "2d4"), turns="1")],
     {}),
    ("recif", "Récif", "13892", 2, 0, ["Entrave"], None, "sagesse",
     lambda r: [other("Indéplaçable, 1 tour.", turns="1")],
     {}),
    ("garde-a-vous", "Garde à Vous", "13894", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("+1d4 prochaine attaque allié·e·s ≤3 cases.", "circle-0-3", turns="1")],
     {}),
    ("bathyscaphe", "Bathyscaphe", "13896", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Sans ligne de vue.")],
     {}),
    ("embuscade", "Embuscade", "13890", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [move("teleport", "1"), hit("air", dice(r["degat"] or "2d6")),
                other("Téléporte une tourelle, puis frappe à son contact.")],
     {}),
    ("soupape", "Soupape", "13864", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6")), heal(dice(r.get("soin") or "2d6"))],
     {}),
    ("periscope", "Périscope", "13842", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6")),
                boost("po", "1", "1", "cross-0-1"),
                other("+1 PO alliés au contact.", turns="1")],
     {}),
    ("cabestan", "Cabestan", "13856", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "3d6"), zone(r, "rect-2x2"))],
     {}),
]


def usage_from(row: dict, meta: str = "") -> dict:
    usage = audit.usage_dict(row)
    usage.update(audit.los_flags(" ".join([row.get("los") or "", row.get("po") or "", row.get("vue") or "", meta])))
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
            "official_id": f"jdr:steamer:{key}",
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

    for filename, entries in (("steamer-level-1.json", level_1), ("steamer-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Steamer", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
