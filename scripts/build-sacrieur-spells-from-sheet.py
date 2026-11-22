#!/usr/bin/env python3
"""Reconstruit sacrieur-level-1.json et sacrieur-progression.json depuis Sacrieur.md."""
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

SHEET = audit.SHEETS / "Sacrieur.md"
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
    ("fulgurance", "Fulgurance", "12724", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([move("teleport", "1"), other("Bond au contact.")] if "bond" in (r.get("deplac") or "").lower() else [])],
     {"allows_reaction": True}),
    ("douleur-cuisante", "Douleur Cuisante", "12730", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([strip("pa", num(r["debuff"] or "1", "1") or "1", turns="1")] if "pa" in (r.get("debuff") or "").lower() else [])],
     {"allows_reaction": True}),
    ("stase", "Stase", "12728", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")), other((r.get("autres") or "Selon Souffrance.").strip())],
     {"allows_reaction": True}),
    ("supplice", "Supplice", "12725", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")), other((r.get("autres") or "Tank / encaissement.").strip())],
     {"allows_reaction": True}),
    ("attirance", "Attirance", "12735", 2, 0, ["Placement"], None, None,
     lambda r: [move("pull", num(r["deplac"] or "2", "2") or "2"), other("Attire vers toi.")],
     {}),
    ("transposition", "Transposition", "12736", 2, 1, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Échange de place. Réaction possible.")],
     {"allows_reaction": True}),
    ("sacrifice", "Sacrifice", "12739", 3, 0, ["Protection"], None, None,
     lambda r: [other("Sacrifice : tu prends les dégâts à la place. Une protection à la fois.", turns="1")],
     {"allows_reaction": True}),
    ("couronne-d-epines", "Couronne d'Épines", "12761", 3, 1, ["Protection", "Dégâts"], None, None,
     lambda r: [other("Couronne : riposte / épines.", turns="1")],
     {}),
    ("liens-du-sang", "Liens du Sang", "12754", 1, 0, ["Protection"], None, None,
     lambda r: [other("Lien de sang / partage.", turns="1")],
     {}),
    ("penitence", "Pénitence", "12764", 2, 0, ["Entrave"], None, None,
     lambda r: [other((r.get("debuff") or "Pénitence.").strip(), turns="1")],
     {}),
    ("assaut", "Assaut", "12733", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), move("teleport", "1")],
     {}),
    ("hecatombe", "Hécatombe", "12750", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6"), zone(r)), other((r.get("debuff") or "").strip() or "Zone.")],
     {}),
    ("condensation", "Condensation", "12745", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")), other((r.get("autres") or "Condensation.").strip())],
     {}),
    ("ravage", "Ravage", "12746", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4")), other((r.get("autres") or "Ravage.").strip())],
     {}),
    ("mutilation", "Mutilation", None, 1, 0, ["Entrave"], None, None,
     lambda r: [other("Perte de PV / mutilation.", turns="1")],
     {}),
    ("pacte-de-sang", "Pacte de Sang", "12762", 2, 0, ["Protection"], None, None,
     lambda r: [other("Pacte : lien / sacrifice.", turns="1")],
     {}),
    ("pilori", "Pilori", "14011", 1, 0, ["Entrave"], None, None,
     lambda r: [other("Pilori : immobilise / marque.", turns="1")],
     {}),
    ("perfusion", "Perfusion", "12758", 2, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("soin") or r.get("degat") or "1d6")), other("Perfusion / vol de vie.")],
     {}),
    ("furie", "Furie", "12723", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")), other((r.get("autres") or "Furie.").strip())],
     {}),
    ("absorption", "Absorption", "12734", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4")), other((r.get("autres") or "Absorption.").strip())],
     {}),
    ("nervosite", "Nervosité", "12727", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4")), other((r.get("autres") or "Nervosité.").strip())],
     {}),
    ("afflux", "Afflux", "12729", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")), other((r.get("autres") or "Afflux.").strip())],
     {}),
    ("fluctuation", "Fluctuation", "12763", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Fluctuation / placement.")],
     {}),
    ("libation", "Libation", "12759", 2, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("soin") or "2d4")), other("Libation.")],
     {}),
    ("punition", "Punition", "12741", 1, 0, ["Dégâts"], None, None,
     lambda r: [other("Dégâts neutres selon Souffrance. Signature.")],
     {}),
    ("coagulation", "Coagulation", "12737", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "2d4")), other("Coagulation / bouclier.", turns="1")],
     {}),
    ("berserk", "Berserk", "12743", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Berserk : bonus selon Souffrance.", turns="1")],
     {}),
    ("rituel-de-jashin", "Rituel de Jashin", "14000", 2, 0, ["Protection"], None, None,
     lambda r: [other("Rituel de Jashin.", turns="1")],
     {}),
    ("carnage", "Carnage", "12752", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6")), other((r.get("autres") or "Carnage.").strip())],
     {}),
    ("immolation", "Immolation", "12747", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6")), other((r.get("autres") or "Immolation.").strip())],
     {}),
    ("dechainement", "Déchaînement", "12755", 1, 2, ["Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6")), other((r.get("autres") or "Déchaînement.").strip())],
     {}),
    ("bain-de-sang", "Bain de Sang", "12732", 1, 3, ["Tank", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "3d6")), other((r.get("autres") or "Bain de Sang.").strip())],
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
            "official_id": f"jdr:sacrieur:{key}",
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
            stat = save_key or "vitality"
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

    for filename, entries in (("sacrieur-level-1.json", level_1), ("sacrieur-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Sacrieur", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
