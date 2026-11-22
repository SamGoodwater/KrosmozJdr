#!/usr/bin/env python3
"""Reconstruit ouginak-level-1.json et ouginak-progression.json depuis Ouginak.md."""
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

SHEET = audit.SHEETS / "Ouginak.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
REPAIRE = "jdr:summon:ouginak-repaire"
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


def rage(n: str = "1") -> dict:
    return other(f"+{n} POINT(S) DE RAGE (plafond).")


SPELLS = [
    ("carcasse", "Carcasse", "13770", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6"))], {"allows_reaction": True}),
    ("charogne", "Charogne", "13766", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4"))], {"allows_reaction": True}),
    ("cubitus", "Cubitus", "13758", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4"))], {"allows_reaction": True}),
    ("molosse", "Molosse", "13756", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d4"))], {"allows_reaction": True}),
    ("traque", "Traque", "13755", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Vers la Proie / piste.")], {"allows_reaction": True}),
    ("amarok", "Amarok", "13802", 2, 1, ["Amélioration"], None, None,
     lambda r: [other("Désigne la Proie.")], {}),
    ("butin", "Butin", "13788", 3, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("1 case vers la cible.")], {}),
    ("veille", "Veille", "13790", 3, 1, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d4"), turns="1")], {"allows_reaction": True}),
    ("rage-bestiale", "Rage Bestiale", "13792", 1, 0, ["Amélioration"], None, None,
     lambda r: [rage("2")], {}),
    ("marche-forcee", "Marche Forcée", "13794", 2, 0, ["Amélioration"], None, None,
     lambda r: [boost("pm", "1", "1")], {}),
    ("limier", "Limier", "13772", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6"))], {}),
    ("tetanisation", "Tétanisation", "13768", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")), strip("pa", "1", turns="1")], {}),
    ("os-a-moelle", "Os à Moelle", "13762", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")), heal(dice(r.get("soin") or "1d4"))], {}),
    ("dogue", "Dogue", "13764", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")), shield(dice(r.get("protect") or "1d4"))], {}),
    ("chasse", "Chasse", "13796", 1, 0, ["Amélioration"], None, None,
     lambda r: [rage("1"), other("+1 dégât allié·e·s ≤3.", "circle-0-3", turns="1")], {}),
    ("pourriture", "Pourriture", "13798", 2, 0, ["Entrave"], None, "sagesse",
     lambda r: [strip("pm", "1", turns="1")], {}),
    ("meute", "Meute", "13800", 1, 0, ["Amélioration"], None, None,
     lambda r: [rage("1"), other("+1d4 si consomme Rage (selon fiche).")], {}),
    ("soif-de-sang", "Soif de Sang", "13804", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("Consomme Rage : +1d4/point à la prochaine attaque.", turns="1")], {}),
    ("battue", "Battue", "13774", 1, 0, ["Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"] or "2d4"), zone(r, "cross-0-1"))], {}),
    ("rabattage", "Rabattage", "13778", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4")), move("pull", "2")], {}),
    ("tibia", "Tibia", "13760", 1, 2, ["Amélioration", "Dégâts"], "water", "chance",
     lambda r: [hit("water", dice(r["degat"] or "2d4")), boost("pa", "1", "1")], {}),
    ("machoire", "Mâchoire", "13784", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4"))], {}),
    ("repaire", "Repaire", "13806", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(REPAIRE), other("Compagnon 1d4. Compte dans les invocations.")], {}),
    ("contre-piste", "Contre-piste", "13808", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Échange / piste.")], {}),
    ("poursuite", "Poursuite", "13812", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "2"), other("Près de la Proie (≤8 cases).")], {}),
    ("roquet", "Roquet", "13810", 2, 0, ["Entrave"], None, "agi",
     lambda r: [other("Indéplaçable ou −PM (selon fiche).", turns="1")], {}),
    ("colliers", "Colliers", "13816", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Liens de meute / buff allié·e·s.", turns="1")], {}),
    ("hurlement-de-meute", "Hurlement de Meute", "13814", 2, 0, ["Amélioration"], None, None,
     lambda r: [rage("1"), other("Buff meute / Proie.", turns="1")], {}),
    ("hurlement", "Hurlement", "13776", 1, 0, ["Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"] or "2d6"), zone(r, "cross-0-1")),
                other("Bonus si Proie dans la zone.")], {}),
    ("radius", "Radius", "13780", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6"))], {}),
    ("calcaneus", "Calcaneus", "13782", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6"))], {}),
    ("bestial", "Bestial", "13786", 1, 3, ["Tank", "Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "2d6"), zone(r, "cross-0-1")),
                shield(dice(r.get("protect") or "1d4"), "cross-0-1")], {}),
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
            "official_id": f"jdr:ouginak:{key}",
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

    for filename, entries in (("ouginak-level-1.json", level_1), ("ouginak-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Ouginak", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
