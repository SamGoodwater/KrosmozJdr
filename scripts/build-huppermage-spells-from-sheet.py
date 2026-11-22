#!/usr/bin/env python3
"""Reconstruit huppermage-level-1.json et huppermage-progression.json depuis Huppermage.md."""
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

SHEET = audit.SHEETS / "Huppermage.md"
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


ME = other("Applique Multiélément.")


SPELLS = [
    ("bourrasque", "Bourrasque", "13671", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), ME], {"allows_reaction": True}),
    ("lance-flamme", "Lance-flamme", "13666", 1, 1, ["Amélioration", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([other((r.get("buff") or "+1 dégât Feu").strip(), turns="1")] if (r.get("buff") or "").strip() else []),
                ME], {"allows_reaction": True}),
    ("stalagmite", "Stalagmite", "13667", 1, 2, ["Placement", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([move("pull", num(r.get("deplac") or "1", "1") or "1")] if "attire" in (r.get("deplac") or "").lower() else []),
                ME], {"allows_reaction": True}),
    ("rocher", "Rocher", "13668", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d4")),
                *([strip("pm", "1", turns="1")] if "pm" in (r.get("debuff") or "").lower() else []),
                ME], {"allows_reaction": True}),
    ("ether", "Éther", "13669", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Sans ligne de vue.")], {"allows_reaction": True}),
    ("runification", "Runification", "13670", 2, 1, ["Amélioration"], None, None,
     lambda r: [other("Multiélément → rune (élément au choix), ou +1 PO à un élément.", turns="1")], {}),
    ("traversee", "Traversée", "13698", 3, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "2")], {"allows_reaction": True}),
    ("bouclier-elementaire", "Bouclier Élémentaire", "13700", 3, 1, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d4"), turns="1")], {}),
    ("cycle-elementaire", "Cycle Élémentaire", "13702", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Prochain Contraste +1d4.", turns="1")], {}),
    ("contribution", "Contribution", "13704", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "1d4"), turns="1")], {}),
    ("lame-astrale", "Lame Astrale", "13680", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), ME], {}),
    ("trait-ardent", "Trait Ardent", "13676", 1, 1, ["Amélioration", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")), ME], {}),
    ("cataracte", "Cataracte", "13688", 1, 2, ["Placement", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")), ME], {}),
    ("onde-sismique", "Onde Sismique", None, 1, 3, ["Entrave", "Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "1d6"), zone(r, "cross-0-1")), ME], {}),
    ("empreinte", "Empreinte", "13706", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Multiélément → rune, ou pose rune (élément au choix).")], {}),
    ("drain-elementaire", "Drain Élémentaire", "13708", 2, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r.get("autres") or r.get("soin") or "1d6")),
                other("Consomme 1 rune pour le soin fort.")], {}),
    ("parcours", "Parcours", "13710", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Ou effet spatial d’une rune à portée.")], {}),
    ("astral", "Astral", "13712", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("+1d4 dégâts sorts de classe.", turns="1")], {}),
    ("rafale", "Rafale", "13682", 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")), heal("moitié"),
                other("Pose rune d’Air."), ME], {}),
    ("volcan", "Volcan", "13678", 1, 1, ["Amélioration", "Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"] or "2d4"), zone(r, "cross-0-1")),
                other("+1 dégât allié·e·s zone.", zone(r, "cross-0-1"), turns="1"),
                other("Pose rune de Feu."), ME], {}),
    ("glacier", "Glacier", "13690", 1, 2, ["Placement", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4")), move("teleport", "3"),
                other("Pose rune d’Eau."), ME], {}),
    ("asteroide", "Astéroïde", "13694", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4")), other("Indéplaçable, 1 tour.", turns="1"),
                other("Pose rune de Terre."), ME], {}),
    ("manifestation", "Manifestation", "13714", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Pose rune (élément au choix).")], {}),
    ("surcharge-runique", "Surcharge Runique", "13716", 2, 0, ["Soin"], None, None,
     lambda r: [heal("1d4×runes"), other("Consomme toutes les runes.")], {}),
    ("prisme-runique", "Prisme Runique", "13718", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Consomme 1 rune : +1d4 allié·e·s ≤3 cases.", "circle-0-3", turns="1")], {}),
    ("gardien-elementaire", "Gardien Élémentaire", "13720", 2, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r.get("protect") or "2d4"), turns="1")], {}),
    ("convection", "Convection", "13722", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Contrastes +1d4 ce tour.", turns="1")], {}),
    ("courant-quadramental", "Courant Quadramental", "13724", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "4"), other("Téléporte un·e allié·e consentant·e ≤4 cases de toi.")], {}),
    ("ouragan", "Ouragan", "13684", 1, 0, ["Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"] or "2d6"), zone(r, "rect-2x2")),
                other("Pose rune d’Air."), ME], {}),
    ("deflagration", "Déflagration", "13686", 1, 1, ["Amélioration", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6")),
                other("+1d6 si rune consommée. Pose rune de Feu sinon."), ME], {}),
    ("avalanche", "Avalanche", "13692", 1, 2, ["Placement", "Dégâts"], "water", "chance",
     lambda r: [hit("water", dice(r["degat"] or "2d6")), ME], {}),
    ("orage", "Orage", "13696", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")), ME], {}),
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
            "official_id": f"jdr:huppermage:{key}",
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

    for filename, entries in (("huppermage-level-1.json", level_1), ("huppermage-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Huppermage", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
