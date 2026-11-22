#!/usr/bin/env python3
"""Reconstruit eliotrope-level-1.json et eliotrope-progression.json depuis Eliotrope.md."""
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

SHEET = audit.SHEETS / "Eliotrope.md"
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


def hit_neutral(value: str, area: str = "point") -> dict:
    return {
        "slug": "frapper",
        "area": area,
        "params": {
            "value": value,
            "value_formula": value,
            "dice_formula": value,
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


SPELLS = [
    ("affront", "Affront", "14575", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([strip("pm", "1", turns="1")] if "pm" in (r.get("debuff") or "").lower() else [])],
     {"allows_reaction": True}),
    ("offense", "Offense", "14581", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([heal(dice(r.get("soin") or "1d4"))] if dice(r.get("soin") or "") else [])],
     {"allows_reaction": True}),
    ("affliction", "Affliction", "14591", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6"))],
     {"allows_reaction": True}),
    ("commotion", "Commotion", "14583", 1, 3, ["Placement", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d4")),
                *([move("pull", num(r.get("deplac") or "1", "1") or "1")]
                  if "attire" in (r.get("deplac") or "").lower() else [])],
     {"allows_reaction": True}),
    ("portail", "Portail", "14574", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"),
                other((r.get("autres") or "Téléport ou pose un portail (max 2).").strip())],
     {}),
    ("odyssee", "Odyssée", "14607", 3, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), move("pull", "1"), other("Toi : 1 case en arrière. Cible : attire 1.")],
     {}),
    ("cicatrisation", "Cicatrisation", "14587", 3, 1, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("soin") or "1d4"))],
     {}),
    ("etirement", "Étirement", "14595", 1, 0, ["Amélioration"], None, None,
     lambda r: [boost("po", "1", "1"), other("Tu perds 1d4 PV.")],
     {}),
    ("focus", "Focus", "14611", 2, 0, ["Amélioration"], None, None,
     lambda r: [other((r.get("buff") or "+1d4 après téléportation").strip(), turns="1")],
     {}),
    ("brimade", "Brimade", "14601", 1, 0, ["Entrave", "Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"] or "1d6"), zone(r, "cross-0-1")),
                strip("pa", num(r.get("debuff") or "1", "1") or "1", zone(r, "cross-0-1"), turns="1")],
     {}),
    ("rayon-de-wakfu", "Rayon de Wakfu", "14579", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6"), zone(r, "line-0-3")),
                heal(dice(r.get("soin") or "1d6"), zone(r, "line-0-3"))],
     {}),
    ("audace", "Audace", "14593", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4"))],
     {}),
    ("camouflet", "Camouflet", "14603", 1, 3, ["Placement", "Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "1d6"), zone(r, "cross-0-1")),
                move("pull", num(r.get("deplac") or "1", "1") or "1", zone(r, "cross-0-1"))],
     {}),
    ("neutral", "Neutral", "14577", 1, 0, ["Placement"], None, None,
     lambda r: [other("Ferme 1 portail.")],
     {}),
    ("stupeur", "Stupeur", "14613", 2, 0, ["Placement"], None, None,
     lambda r: [other("Piège → portail (plafond 2).")],
     {}),
    ("distribution", "Distribution", "14617", 1, 0, ["Amélioration"], None, "sagesse",
     lambda r: [other("Renvoie 1d4 au contact quand la cible subit un sort.", turns="1")],
     {}),
    ("entraide", "Entraide", "14619", 2, 0, ["Soin"], None, None,
     lambda r: [other("1d4 en croix 1 autour de toi à chaque passage allié.", "cross-0-1", turns="1")],
     {}),
    ("raillerie", "Raillerie", "14621", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")), other("Indéplaçable, 1 tour.", turns="1")],
     {}),
    ("virus", "Virus", "14615", 1, 1, ["Soin", "Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"] or "2d4"), zone(r, "rect-2x2")),
                heal(dice(r.get("soin") or "1d6"), zone(r, "rect-2x2"))],
     {}),
    ("poing-fulgurant", "Poing Fulgurant", "14609", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d8"))],
     {}),
    ("cataclysme", "Cataclysme", "14605", 1, 3, ["Placement", "Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "2d4"), zone(r, "rect-2x2")),
                move("push", num(r.get("deplac") or "1", "1") or "1", zone(r, "rect-2x2"))],
     {}),
    ("errance", "Errance", "14623", 1, 0, ["Placement"], None, "agi",
     lambda r: [move("teleport", "1"), other("Échange de place.")],
     {"allows_reaction": True}),
    ("interruption", "Interruption", "14625", 2, 0, ["Entrave"], None, "sagesse",
     lambda r: [strip("pa", "1", turns="1")],
     {}),
    ("cabale", "Cabale", "14627", 1, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "2d4"), turns="1")],
     {"allows_reaction": True}),
    ("conjuration", "Conjuration", "14629", 2, 0, ["Dégâts"], None, None,
     lambda r: [hit_neutral(dice(r["degat"] or "2d6")), other("Dégâts neutres.")],
     {}),
    ("coalition", "Coalition", "14631", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("+1 PM après téléportation (allié·e·s).", turns="1")],
     {}),
    ("sermon", "Sermon", "14633", 2, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("soin") or "2d4"), zone(r, "cross-0-1"))],
     {}),
    ("insolence", "Insolence", "14597", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d8")),
                strip("po", num(r.get("debuff") or "1", "1") or "1", turns="1")],
     {}),
    ("parasite", "Parasite", "14585", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d8")), heal("moitié")],
     {}),
    ("tribulation", "Tribulation", "14599", 1, 2, ["Dégâts"], "water", "chance",
     lambda r: [hit("water", dice(r["degat"] or "3d6"), zone(r, "line-0-3"))],
     {}),
    ("persiflage", "Persiflage", "14589", 1, 3, ["Placement", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")),
                move("teleport", "1"),
                other("Téléporte la cible vers toi ou un portail.")],
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
    return "wakfu" in lowered


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
            "official_id": f"jdr:eliotrope:{key}",
            "slot_index": slot,
            "choice_order": order,
            "character_level": rows[0]["niv"],
            "types": types,
            "state": "playable",
            "is_magic": is_magic_from(meta) or "wakfu" in (spell.get("description") or "").lower(),
            "resolution_mode": mode,
            "description": spell.get("description") or "",
            "effect": effect,
            **base,
        }
        # Faille / Portail : Wakfu
        if name in ("Portail", "Faille") or "wakfu" in meta.lower():
            entry["is_magic"] = True
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

    for filename, entries in (("eliotrope-level-1.json", level_1), ("eliotrope-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Eliotrope", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
