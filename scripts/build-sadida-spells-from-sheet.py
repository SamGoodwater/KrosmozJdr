#!/usr/bin/env python3
"""Reconstruit sadida-level-1.json et sadida-progression.json depuis Sadida.md."""
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

SHEET = audit.SHEETS / "Sadida.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}
ARBRE = "jdr:summon:sadida-arbre"
BLOQUEUSE = "jdr:summon:sadida-la-bloqueuse"
GONFLABLE = "jdr:summon:sadida-la-gonflable"
SACRIFIEE = "jdr:summon:sadida-la-sacrifiee"


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
        "params": {"value": value, "value_formula": value, "dice_formula": value, "effect_direction": "action", "duration_formula": turns},
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


def summon(monster_id: str) -> dict:
    return {"slug": "invoquer", "area": "point", "params": {"monster_official_id": monster_id}}


# key, name, dofus_id, slot, order, types, element, save_key, make, extras
SPELLS = [
    ("cigue", "Cigüe", "13577", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), other("Autour d’un arbre possible.")],
     {"allows_reaction": True}),
    ("buisson-ardent", "Buisson Ardent", "13574", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([strip("pm", "1", turns="1")] if r.get("niv", 0) >= 5 or "pm" in (r.get("debuff") or "").lower() else []),
                other("Autour d’un arbre possible.")],
     {"allows_reaction": True}),
    ("larme-de-sadida", "Larme de Sadida", "13528", 1, 2, ["Soin", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([heal(dice(r["degat"] or "1d4"))] if r.get("niv", 0) >= 5 else []),
                other("Allié·e : soin dès niv. 5. Autour d’un arbre possible.")],
     {"allows_reaction": True}),
    ("ronce", "Ronce", "13516", 1, 3, ["Protection", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d4")),
                *([shield(dice(r.get("protect") or "1d4"))] if r.get("niv", 0) >= 5 or r.get("protect") else []),
                other("Autour d’un arbre possible.")],
     {"allows_reaction": True}),
    ("arbre", "Arbre", "13519", 2, 0, ["Invocation"], None, None,
     lambda r: [summon(ARBRE), other("Compte dans la limite d’invocations. Sol vivant. Immobile.")],
     {}),
    ("vent-empoisonne", "Vent Empoisonné", "13529", 3, 0, ["Entrave"], None, "vitality",
     lambda r: [other("Pose le poison (marque, pas de dégâts).", turns="3")],
     {"allows_reaction": True}),
    ("ronce-apaisante", "Ronce Apaisante", "13525", 3, 1, ["Soin", "Entrave"], None, "sagesse",
     lambda r: [heal(dice(r.get("soin") or r.get("degat") or "1d4")),
                strip("pm", "1", turns="1"),
                other("Autour d’un arbre possible.")],
     {}),
    ("la-bloqueuse", "La Bloqueuse", "13561", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(BLOQUEUSE), other("Remplace un arbre (même place d’invocation). Une seule.")],
     {}),
    ("rempotage", "Rempotage", "13565", 2, 0, ["Placement", "Soin"], None, None,
     lambda r: [move("teleport", "3"), heal(dice(r.get("soin") or "1d4")), other("Replante un arbre/poupée sur sol vivant.")],
     {"allows_reaction": True}),
    ("mandragore", "Mandragore", "13559", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), other((r.get("autres") or "Autour d’un arbre / poison.").strip())],
     {}),
    ("chardons-ardents", "Chardons Ardents", "13517", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")), other((r.get("debuff") or "Entrave.").strip(), turns="1")],
     {}),
    ("montee-de-seve", "Montée de Sève", "13563", 1, 2, ["Soin", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")), heal(dice(r.get("soin") or "1d4")), other("Soin aux allié·e·s au contact.")],
     {}),
    ("tremblement", "Tremblement", "13514", 1, 3, ["Protection", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6"), zone(r)),
                move("pull", num(r["deplac"] or "1", "1") or "1", zone(r)),
                other("Autour de toi, d’un arbre ou d’une poupée. Dévoile.", zone(r))],
     {}),
    ("la-gonflable", "La Gonflable", "13573", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(GONFLABLE), other("Remplace un arbre. Une seule.")],
     {}),
    ("miasmes", "Miasmes", "13562", 2, 0, ["Entrave"], None, "vitality",
     lambda r: [other("Poison aux ennemi·e·s au contact de tes arbres/poupées.", turns="3")],
     {}),
    ("harmonie", "Harmonie", "13579", 1, 0, ["Protection"], None, None,
     lambda r: [other("Enraciné·e, moitié des dégâts sur un arbre. Bouclier si Vit ≥ 3.", turns="1")],
     {}),
    ("puissance-sylvestre", "Puissance Sylvestre", "13530", 2, 0, ["Placement"], None, None,
     lambda r: [move("push", num(r["deplac"] or "2", "2") or "2"), other("Repousse depuis un arbre.")],
     {}),
    ("contagion", "Contagion", "13575", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")), other("Propagation si empoisonné·e.")],
     {}),
    ("herbes-folles", "Herbes Folles", "13518", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4")), other((r.get("debuff") or "Entrave.").strip(), turns="1")],
     {}),
    ("fleau", "Fléau", "13571", 1, 2, ["Soin", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4")), heal(dice(r.get("soin") or "1d6"))],
     {}),
    ("foret-hantee", "Forêt Hantée", "13524", 1, 3, ["Protection", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4")),
                other("Désavantage hors jardin 1 tour.", turns="1")],
     {}),
    ("la-sacrifiee", "La Sacrifiée", "13567", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(SACRIFIEE), other("Remplace un arbre. Une seule. Éclate / poison.")],
     {}),
    ("seve-paralysante", "Sève Paralysante", "13533", 2, 0, ["Entrave"], None, "vitality",
     lambda r: [other("Poison + entrave.", turns="3")],
     {}),
    ("influence-vegetale", "Influence Végétale", "13566", 1, 0, ["Protection", "Entrave"], None, "chance",
     lambda r: [shield(dice(r.get("protect") or "1d6")), strip("pm", "1", turns="1"),
                other("Autour de tes arbres.")],
     {}),
    ("don-naturel", "Don Naturel", "13532", 2, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("soin") or "2d6")), other((r.get("autres") or "Soin fort.").strip())],
     {}),
    ("arbre-de-vie", "Arbre de Vie", "13534", 1, 0, ["Invocation", "Soin"], None, None,
     lambda r: [summon(ARBRE), heal(dice(r.get("soin") or "2d4")), other("Arbre soignant. Compte dans la limite d’invocations.")],
     {}),
    ("altruisme-vegetal", "Altruisme Végétal", "13572", 2, 0, ["Soin", "Protection"], None, None,
     lambda r: [heal(dice(r.get("soin") or "2d6")), other((r.get("autres") or "Transfert / soin.").strip())],
     {}),
    ("inoculation", "Inoculation", "13569", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6")), other("Propagation / poison.")],
     {}),
    ("feu-de-brousse", "Feu de Brousse", "13568", 1, 1, ["Entrave", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6")), other("Autour de tous tes arbres. −esquive PM.", turns="1")],
     {}),
    ("mangrove", "Mangrove", "14396", 1, 2, ["Soin", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6")), heal(dice(r.get("soin") or "2d6")),
                other("Autour de tous tes arbres.")],
     {}),
    ("force-de-la-nature", "Force de la Nature", "13570", 1, 3, ["Protection", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")), shield(dice(r.get("protect") or "2d6")),
                other("+1d4 par arbre (max +2d4). Propagation si poison.")],
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
            "official_id": f"jdr:sadida:{key}",
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

    for filename, entries in (("sadida-level-1.json", level_1), ("sadida-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Sadida", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
