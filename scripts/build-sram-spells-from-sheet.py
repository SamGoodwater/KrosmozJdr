#!/usr/bin/env python3
"""Reconstruit sram-level-1.json et sram-progression.json depuis Sram.md."""
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

SHEET = audit.SHEETS / "Sram.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}
DOUBLE = "jdr:summon:sram-double"


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


def summon(monster_id: str = DOUBLE) -> dict:
    return {"slug": "invoquer", "area": "point", "params": {"monster_official_id": monster_id}}


def push_cells(r: dict, default: str = "1") -> str:
    return num(r.get("deplac") or "", default) or default


# key, name, dofus_id, slot, order, types, element, save_key, make, extras
SPELLS = [
    ("arsenic", "Arsenic", "12907", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([other(f"Poison d’Air {dice(r['poison'] or '1d4')}, 2 tours.", turns="2")] if dice(r.get("poison") or "") else []),
                *([strip("po", num(r["debuff"] or "1", "1"), turns="1")] if num(r.get("debuff") or "") else [])],
     {"allows_reaction": True}),
    ("sournoiserie", "Sournoiserie", "12904", 1, 1, ["Placement", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([move("push", push_cells(r, "1"))] if num(r.get("deplac") or "") else [])],
     {"allows_reaction": True}),
    ("cruaute", "Cruauté", "12919", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([boost("pm", "1", "1")] if "pm" in (r.get("buff") or "").lower() or "vol" in (r.get("buff") or "").lower() else [])],
     {"allows_reaction": True}),
    ("truanderie", "Truanderie", "12902", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")),
                other("Dès niv. 10 : prochain coup sur cette cible vole des dégâts.")],
     {"allows_reaction": True}),
    ("invisibilite", "Invisibilité", "12913", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("Invisible. Redeviens visible si une action de ta part inflige des dégâts (sauf si le coup achève).")],
     {"allows_reaction": True}),
    ("piege-d-immobilisation", "Piège d’Immobilisation", "12910", 3, 0, ["Entrave"], None, "sagesse",
     lambda r: [strip("pm", num(r["debuff"] or "1", "1"), zone(r), turns="1"),
                other("Piège. Toute créature, toi compris.", zone(r))],
     {"target_type": "trap"}),
    ("peur", "Peur", "12908", 3, 1, ["Placement"], None, "strong",
     lambda r: [move("push", push_cells(r, "1")),
                other("Repousse. Réaction possible.")],
     {"allows_reaction": True}),
    ("derobade", "Dérobade", "14312", 1, 0, ["Placement"], None, None,
     lambda r: [move("jump", "1"),
                other((r.get("autres") or "Saut / esquive.").strip())],
     {}),
    ("concentration-de-chakra", "Concentration de Chakra", "12944", 1, 1, ["Amélioration"], None, None,
     lambda r: [other((r.get("autres") or "Buff chakra.").strip(), turns="1")],
     {}),
    ("piege-repulsif", "Piège Répulsif", "12914", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6"), zone(r)),
                strip("pm", "1", zone(r), turns="1"),
                move("push", push_cells(r, "1"), zone(r)),
                other("Piège. Toute créature, toi compris.", zone(r))],
     {"target_type": "trap"}),
    ("piege-sournois", "Piège Sournois", "12906", 1, 1, ["Placement", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6"), zone(r)),
                move("pull", push_cells(r, "1"), zone(r)),
                other("Piège. Toute créature, toi compris.", zone(r))],
     {"target_type": "trap"}),
    ("pillage", "Pillage", "12934", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")),
                other((r.get("autres") or "Vol / n dés.").strip())],
     {}),
    ("piege-funeste", "Piège Funeste", "12948", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6"), zone(r)),
                move("push", "1", zone(r)),
                other("+1d4 par créature après la 1re (max +2d4). Toute créature, toi compris.", zone(r))],
     {"target_type": "trap"}),
    ("double", "Double", "12915", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(DOUBLE),
                other("Stats = lanceur, ½ PV. Aucun sort ni capacité : déplacement seul. Relancer = échange de place.")],
     {}),
    ("manigance", "Manigance", "14741", 1, 1, ["Amélioration"], None, None,
     lambda r: [other("+1d4 à chaque coup (toi + allié·e).", turns="1")],
     {}),
    ("meprise", "Méprise", "12945", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"),
                other("Échange avec cible, ou avec le Double.")],
     {"allows_reaction": True}),
    ("marque-mortuaire", "Marque Mortuaire", "14313", 1, 1, ["Amélioration"], None, None,
     lambda r: [other((r.get("autres") or "Marque mortuaire.").strip(), turns="1")],
     {}),
    ("piege-insidieux", "Piège Insidieux", "12918", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4"), zone(r)),
                strip("pa", num(r["debuff"] or "1", "1"), zone(r), turns="1"),
                move("push", "1", zone(r)),
                other("Piège. Direction de poussée choisie à la pose.", zone(r))],
     {"target_type": "trap"}),
    ("piege-de-derive", "Piège de Dérive", "12942", 1, 1, ["Placement", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4"), zone(r)),
                move("push", push_cells(r, "1"), zone(r)),
                other("Piège. Direction choisie à la pose.", zone(r))],
     {"target_type": "trap"}),
    ("guet-apens", "Guet-apens", "12939", 1, 2, ["Amélioration", "Placement"], "water", None,
     lambda r: [move("pull", push_cells(r, "2")),
                other("Attire vers toi / réseau de pièges.")],
     {}),
    ("piege-mortel", "Piège Mortel", "12921", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6"), zone(r)),
                other("Piège. Toute créature, toi compris.", zone(r))],
     {"target_type": "trap"}),
    ("brume", "Brume", "12930", 1, 0, ["Protection"], None, None,
     lambda r: [other("Brume opaque. Allié·e·s voient les silhouettes. 3 tours en combat.", zone(r), turns="3")],
     {}),
    ("effraction", "Effraction", "14742", 1, 1, ["Placement"], None, None,
     lambda r: [move("jump", "1"), other("Passe une porte / obstacle, ou placement.")],
     {}),
    ("arnaque", "Arnaque", "12911", 1, 0, ["Placement"], None, "vitality",
     lambda r: [other((r.get("autres") or "Arnaque.").strip(), turns="1")],
     {}),
    ("fosse-commune", "Fosse Commune", "14314", 1, 1, ["Entrave"], None, "chance",
     lambda r: [other("Piège. Indéplaçable si sauvegarde ratée. Toute créature, toi compris.", zone(r), turns="1")],
     {"target_type": "trap"}),
    ("poisse", "Poisse", "12922", 1, 0, ["Entrave"], None, "chance",
     lambda r: [heal("moitié d’un coup"),
                other("Vol de vie / poisse.", turns="1")],
     {}),
    ("piege-effroyable", "Piège Effroyable", "28737", 1, 1, ["Entrave"], None, "agi",
     lambda r: [other("Effrayé + soins /2 si sauvegarde ratée. Toute créature, toi compris.", zone(r),
                      turns=num(r.get("debuff") or r.get("autres") or "1", "1") or "1")],
     {"target_type": "trap", "image_filter": "violet"}),
    ("injection-toxique", "Injection Toxique", "12940", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6")),
                other("Poison d’Air transmissible.", turns="2")],
     {}),
    ("perquisition", "Perquisition", "12947", 1, 1, ["Placement", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6")),
                move("teleport", "1"),
                other("Place la cible ou échange avec le Double.")],
     {}),
    ("calamite", "Calamité", "12937", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6"), zone(r)),
                other("Intaclable pour toi et allié·e·s de la zone.", zone(r), turns="1")],
     {}),
    ("attaque-mortelle", "Attaque mortelle", "12932", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "3d6")),
                other("Bonus si cible < ½ PV ou sort d’un de tes pièges.")],
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
            "official_id": f"jdr:sram:{key}",
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
            stat = save_key or "agi"
            entry["attack_characteristic_key"] = stat
            entry["save_characteristic_key"] = stat
            entry["save_dc_formula"] = DC
            entry["save_success_note"] = meta
        if "réaction" in meta.lower() or spell.get("reaction"):
            entry["allows_reaction"] = True
        if "piège" in meta.lower() or "piege" in meta.lower():
            entry.setdefault("target_type", "trap")
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

    for filename, entries in (("sram-level-1.json", level_1), ("sram-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Sram", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
