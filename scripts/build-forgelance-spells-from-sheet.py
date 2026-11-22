#!/usr/bin/env python3
"""Reconstruit forgelance-level-1.json et forgelance-progression.json depuis Forgelance.md."""
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

SHEET = audit.SHEETS / "Forgelance.md"
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
    ("javeline-de-myr", "Javeline de Myr", "23750", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([strip("pm", "1", turns="1")] if "pm" in (r.get("debuff") or "").lower() else [])],
     {"allows_reaction": True}),
    ("estoc-brulant", "Estoc Brûlant", "23754", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6"))], {"allows_reaction": True}),
    ("lance-du-lac", "Lance du Lac", "23432", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([shield(dice(r.get("protect") or "1d4"))] if dice(r.get("protect") or "") else [])],
     {"allows_reaction": True}),
    ("talon-d-argile", "Talon d'Argile", "23736", 1, 3, ["Placement", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d4")),
                *([move("pull", num(r.get("deplac") or "1", "1") or "1")] if "attire" in (r.get("deplac") or "").lower() else [])],
     {"allows_reaction": True}),
    ("charge-heroique", "Charge Héroïque", "23824", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Téléport de charge. Déclenche Estoc.")],
     {"allows_reaction": True}),
    ("phalange", "Phalange", "23330", 2, 1, ["Protection"], None, None,
     lambda r: [other("État Phalange : abri partiel pour un·e allié·e adjacent (N7+).", turns="1")], {}),
    ("parade", "Parade", "23826", 3, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d4"), turns="1")], {"allows_reaction": True}),
    ("epilogue", "Épilogue", "23828", 3, 1, ["Amélioration"], None, None,
     lambda r: [other("Fin de phrase / buff de lance.", turns="1")], {}),
    ("garde-au-fer", "Garde au Fer", "23830", 1, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d4"), turns="1")], {}),
    ("percee", "Percée", "23832", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Percée. Déclenche Estoc.")], {}),
    ("volee-d-airain", "Volée d'Airain", "23741", 1, 0, ["Entrave", "Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"] or "1d6"), zone(r, "cross-0-1")),
                strip("pa", "1", zone(r, "cross-0-1"), turns="1")], {}),
    ("fente", "Fente", "23760", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4"))], {}),
    ("trident-de-la-mer", "Trident de la Mer", "23440", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")), shield(dice(r.get("protect") or "1d4"))], {}),
    ("effondrement", "Effondrement", None, 1, 3, ["Placement", "Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "1d6"), zone(r, "cross-0-1")),
                move("pull", "1", zone(r, "cross-0-1"))], {}),
    ("oriflamme", "Oriflamme", "23834", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("+1d4 allié·e·s / bannière.", turns="1")], {}),
    ("galanterie", "Galanterie", "23836", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d6"), turns="1")], {}),
    ("kyrja", "Kyrja", "23838", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Déclenche Estoc.")], {}),
    ("chevalerie", "Chevalerie", "23840", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("Buff chevaleresque.", turns="1")], {}),
    ("noa", "Noa", "23755", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")), strip("po", "1", turns="1")], {}),
    ("muspel", "Muspel", "23762", 1, 1, ["Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"] or "2d6")), other("Bonus selon nombre d’ennemi·e·s.")], {}),
    ("balestra", "Balestra", "23442", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4")), shield(dice(r.get("protect") or "1d6"))], {}),
    ("soulevement", "Soulèvement", "23766", 1, 3, ["Placement", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4")), move("push", "1")], {}),
    ("ligne-de-front", "Ligne de Front", "23842", 1, 0, ["Protection"], None, None,
     lambda r: [other("Ligne / abri pour allié·e·s.", turns="1")], {}),
    ("rempart-de-lances", "Rempart de Lances", "23844", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "2d4"), turns="1")], {}),
    ("pluie-d-airain", "Pluie d'Airain", "23743", 1, 0, ["Entrave"], None, "agi",
     lambda r: [strip("pa", "1", turns="1"), other("Pluie d’entraves.")], {}),
    ("jormun", "Jormun", "23446", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "2d4"), turns="1")], {}),
    ("charge-de-fer", "Charge de Fer", "23846", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Charge. Déclenche Estoc.")], {}),
    ("mur-de-lances", "Mur de Lances", "23848", 2, 0, ["Protection"], None, None,
     lambda r: [other("Mur / zone de protection.", turns="1")], {}),
    ("moulin-a-vent", "Moulin à Vent", "23757", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6"), zone(r, "cross-0-1"))], {}),
    ("fer-rouge", "Fer Rouge", "23764", 1, 1, ["Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"] or "2d6"))], {}),
    ("elding", "Elding", "23444", 1, 2, ["Protection", "Dégâts"], "water", "chance",
     lambda r: [hit("water", dice(r["degat"] or "2d6")), shield(dice(r.get("protect") or "2d4"))], {}),
    ("terre-du-milieu", "Terre du Milieu", "23768", 1, 3, ["Placement", "Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "2d6"), zone(r, "rect-2x2"))], {}),
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
            "official_id": f"jdr:forgelance:{key}",
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

    for filename, entries in (("forgelance-level-1.json", level_1), ("forgelance-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Forgelance", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
