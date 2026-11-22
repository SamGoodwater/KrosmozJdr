#!/usr/bin/env python3
"""Reconstruit roublard-level-1.json et roublard-progression.json depuis Roublard.md."""
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

SHEET = audit.SHEETS / "Roublard.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
BOMBE = "jdr:summon:roublard-bombe"
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


def bomb_element(element: str, explode: bool = False) -> dict:
    label = {"air": "Air", "fire": "Feu", "water": "Eau", "earth": "Terre"}[element]
    text = f"Bombe : devient {label}" + (" et explose." if explode else ".")
    return other(text)


# key, name, dofus_id, slot, order, types, element, save_key, make, extras
SPELLS = [
    ("espingole", "Espingole", "13440", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([move("push", "1")] if "recul" in (r.get("deplac") or "").lower() else []),
                bomb_element("air")],
     {"allows_reaction": True}),
    ("plombage", "Plombage", "13488", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")), bomb_element("fire")],
     {"allows_reaction": True}),
    ("recel", "Recel", "13477", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([strip("pa", "1", turns="1")] if "pa" in (r.get("debuff") or "").lower() else []),
                bomb_element("water")],
     {"allows_reaction": True}),
    ("obliteration", "Oblitération", "13469", 1, 3, ["Amélioration", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d4")),
                *([other((r.get("buff") or "Prochaine explosion +1d4.").strip(), turns="1")]
                  if (r.get("buff") or "").strip() else []),
                bomb_element("earth")],
     {"allows_reaction": True}),
    ("bombe", "Bombe", "13432", 2, 0, ["Invocation"], None, None,
     lambda r: [summon(BOMBE),
                other("Choix : poser (neutre, charge 1), charger (+1, max 1/tour), ou exploser.")],
     {}),
    ("botte", "Botte", "13434", 3, 0, ["Placement"], None, "strong",
     lambda r: [move("push", "2", zone(r, "circle-0-1")),
                shield(dice(r.get("protect") or "1d4"), zone(r, "circle-0-1")),
                move("push", "1", zone(r, "circle-0-1")),
                other("Bombes : poussée 2 (3 si For ≥ 3) + bouclier. Créatures : poussée 1.")],
     {}),
    ("entourloupe", "Entourloupe", "13439", 3, 1, ["Placement"], None, None,
     lambda r: [move("teleport", "1"), other("Échange avec bombe ou allié·e. Bombe : +1 charge. Intaclable si Agi ≥ 3.")],
     {}),
    ("cordeau", "Cordeau", None, 1, 0, ["Entrave"], None, "strong",
     lambda r: [other("Mur entre deux bombes (même élément). Dégâts ½ charge min en d4 + effet élément.",
                      "line-0-3"),
                other((r.get("autres") or "Bombes à 3 cases ou moins.").strip())],
     {}),
    ("aimantation", "Aimantation", "13437", 2, 0, ["Placement"], None, "strong",
     lambda r: [move("pull", "2", zone(r, "circle-0-2")),
                move("pull", "1", zone(r, "circle-0-2")),
                other("Bombes attirées de 2 +1 charge. Créatures de 1. Cercle 3 si Intel ≥ 3.")],
     {}),
    ("tornabombe", "Tornabombe", "13435", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")),
                move("pull", num(r.get("deplac") or "1", "1") or "1"),
                bomb_element("air", explode=True)],
     {}),
    ("explobombe", "Explobombe", "13444", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4")), bomb_element("fire", explode=True)],
     {}),
    ("bombe-a-eau", "Bombe à Eau", "13436", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")),
                strip("pm", "1", turns="1"),
                bomb_element("water", explode=True)],
     {}),
    ("sismobombe", "Sismobombe", "13491", 1, 3, ["Amélioration", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")),
                other((r.get("buff") or "+1d4 à un·e allié·e").strip(), turns="1"),
                bomb_element("earth", explode=True)],
     {}),
    ("bombe-collante", "Bombe Collante", "13479", 1, 0, ["Entrave"], None, "strong",
     lambda r: [other("Une bombe suit l’ennemi·e. −1 PM si For ≥ 3.", turns="1")],
     {}),
    ("casemate", "Casemate", "14412", 2, 0, ["Protection"], None, None,
     lambda r: [other("Bombe invulnérable 1 tour (2 si Vit ≥ 3).", turns="1"),
                other("Bombe : +1 niveau de charge.")],
     {}),
    ("roublardise", "Roublardise", "13431", 1, 0, ["Placement"], None, None,
     lambda r: [move("teleport", "1"),
                shield(dice(r.get("protect") or "1d6")),
                other("Leurre à ta place. Fin du tour. +2 PO si Agi ≥ 3.")],
     {}),
    ("imposture", "Imposture", "13489", 2, 0, ["Protection"], None, None,
     lambda r: [move("teleport", "1"),
                other("Avantage aux sauvegardes. Bombe : +1 charge. +2 CA allié·e si Cha ≥ 3.", turns="1")],
     {"allows_reaction": True}),
    ("cadence", "Cadence", "13475", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4"), zone(r, "cross-0-1")),
                move("push", "1", zone(r, "cross-0-1")),
                bomb_element("air")],
     {}),
    ("pulsar", "Pulsar", "13442", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6"), zone(r, "circle-0-1")), bomb_element("fire")],
     {}),
    ("shrapnel", "Shrapnel", "14414", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4"), zone(r, "cross-0-1")),
                strip("pa", "1", zone(r, "cross-0-1"), turns="1"),
                bomb_element("water")],
     {}),
    ("mousquet", "Mousquet", "13473", 1, 3, ["Amélioration", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d4"), zone(r, "cross-0-1")),
                bomb_element("earth"),
                other("Bombes : +1 niveau de charge.")],
     {}),
    ("kaboom", "Kaboom", "13450", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Allié·e·s : déclenchent une bombe. Bombes : +1 charge.",
                      zone(r, "circle-0-2"), turns="1")],
     {}),
    ("stratageme", "Stratagème", "13476", 2, 0, ["Protection"], None, None,
     lambda r: [move("teleport", "3"),
                shield(dice(r.get("protect") or "1d6")),
                other("Téléporte une bombe (deux si Sag ≥ 3).")],
     {"allows_reaction": True}),
    ("dernier-souffle", "Dernier Souffle", "13446", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Toutes les bombes : +1 charge hors limite. Tu perds 2d6 PV (1d6 si Vit ≥ 3).")],
     {}),
    ("megabombe", "Mégabombe", "13490", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("+1 charge par bombe absorbée dans un rayon de 2 (3 si For ≥ 3), hors limite.")],
     {}),
    ("poudre", "Poudre", "13441", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Bombe indéplaçable. Explose à 0 PV. Chaîne dans sa zone. +1 charge à l’explosion si Cha ≥ 3.",
                      turns="1")],
     {}),
    ("remission", "Rémission", "13445", 2, 0, ["Protection"], None, None,
     lambda r: [move("push", "2"),
                other("Allié·e : recul des attaquants au contact. Bombe : moitié dégâts à distance. Bouclier 1d6 si Sag ≥ 3.",
                      turns="1")],
     {}),
    ("dagues-boomerang", "Dagues Boomerang", "13438", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6"), zone(r, "line-0-5")),
                other("Recul ou attirance de 1, au choix.", zone(r, "line-0-5")),
                bomb_element("air")],
     {}),
    ("grenaille", "Grenaille", "14411", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d8"), zone(r, "circle-0-1")),
                bomb_element("fire", explode=True)],
     {}),
    ("resquille", "Resquille", "13443", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6"), zone(r, "circle-0-1")),
                other("Indéplaçable, 1 tour.", zone(r, "circle-0-1"), turns="1"),
                bomb_element("water"),
                other("Bombes : symétrie par rapport au centre.")],
     {}),
    ("bombarde", "Bombarde", "14415", 1, 3, ["Amélioration", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6"), zone(r, "circle-0-1")),
                move("push", "1", zone(r, "circle-0-1")),
                other((r.get("buff") or "+1d6 aux allié·e·s").strip(), zone(r, "circle-0-1"), turns="1"),
                bomb_element("earth")],
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
            "official_id": f"jdr:roublard:{key}",
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

    for filename, entries in (("roublard-level-1.json", level_1), ("roublard-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Roublard", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
