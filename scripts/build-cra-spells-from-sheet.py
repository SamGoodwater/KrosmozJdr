#!/usr/bin/env python3
"""Reconstruit cra-level-1.json et cra-progression.json depuis Cra.md."""
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

SHEET = audit.SHEETS / "Cra.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}
BALISE_T = "jdr:summon:cra-balise-tactique"
BALISE_R = "jdr:summon:cra-balise-rappel"


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


def summon(monster_id: str) -> dict:
    return {"slug": "invoquer", "area": "point", "params": {"monster_official_id": monster_id}}


# key, name, dofus_id, slot, order, types, element, save_key, make, extras
SPELLS = [
    ("fleche-harcelante", "Flèche harcelante", None, 1, 0, ["Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")), other("Ignore la ligne de vue.")],
     {"allows_reaction": True}),
    ("fleche-enflammee", "Flèche enflammée", "32447", 1, 1, ["Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([move("push", num(r["deplac"] or "1", "1") or "1")] if num(r.get("deplac") or "") else []),
                *([other("Zone dans l’axe du tir.")] if r.get("niv", 0) >= 5 else [])],
     {"allows_reaction": True}),
    ("fleche-d-immobilisation", "Flèche d’immobilisation", "32436", 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                strip("pm", num(r.get("debuff") or "1", "1") or "1", turns="1")],
     {"allows_reaction": True}),
    ("fleche-du-vagabond", "Flèche du vagabond", None, 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6"))],
     {"allows_reaction": True}),
    ("fleche-evasive", "Flèche évasive", None, 2, 0, ["Placement"], None, None,
     lambda r: [move("jump", num(r["deplac"] or "1", "1") or "1"), other("Saut après le tir / hors de mêlée.")],
     {}),
    ("tir-de-repli", "Tir de repli", "32470", 2, 1, ["Placement"], None, None,
     lambda r: [move("jump", "2"), other("Repli. Réaction possible.")],
     {"allows_reaction": True}),
    ("tir-eloigne", "Tir éloigné", "32558", 3, 0, ["Amélioration"], None, None,
     lambda r: [other("Allonge la portée normale (donc la portée longue).", turns="1")],
     {}),
    ("tir-percant", "Tir perçant", "13080", 3, 1, ["Amélioration"], None, None,
     lambda r: [other("Ignore une partie de la protection / traverse.", turns="1")],
     {}),
    ("vendetta", "Vendetta", None, 1, 0, ["Amélioration"], None, None,
     lambda r: [other((r.get("buff") or r.get("autres") or "Vendetta.").strip(), turns="1")],
     {}),
    ("oeil-pour-oeil", "Œil pour œil", "32454", 2, 0, ["Protection"], None, None,
     lambda r: [other("Garde préparée : riposte quand la cible est touchée.", turns="2")],
     {"allows_reaction": True}),
    ("fleche-de-recul", "Flèche de recul", "32426", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")), move("push", num(r["deplac"] or "2", "2") or "2")],
     {}),
    ("fleche-devorante", "Flèche dévorante", "32446", 1, 1, ["Dégâts", "Amélioration"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")), other((r.get("buff") or "Brûlure / soin selon fiche.").strip())],
     {}),
    ("oeil-de-taupe", "Œil de taupe", "32437", 1, 2, ["Entrave"], "water", "chance",
     lambda r: [hit("water", dice(r["degat"] or "1d4"), zone(r)),
                other("Aveuglé. Révèle l’invisible.", zone(r), turns="1")],
     {}),
    ("fleche-punitive", "Flèche punitive", "32456", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")),
                other(f"Charge (max 3). Bonus +{num(r.get('autres') or r.get('buff') or '2', '2')} par charge, nd.")],
     {}),
    ("tir-puissant", "Tir puissant", "32466", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Prochains tirs renforcés.", turns="1")],
     {}),
    ("represailles", "Représailles", None, 2, 0, ["Entrave"], None, "chance",
     lambda r: [other("−1 PM par attaque qui la blesse.", turns=num(r.get("debuff") or "1", "1") or "1")],
     {}),
    ("balise-tactique", "Balise tactique", "32476", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(BALISE_T), other("Compte dans la limite d’invocations. Relais d’un sort, 1 tour. Tes dégâts ne l’abîment pas.")],
     {}),
    ("balise-de-rappel", "Balise de rappel", None, 2, 0, ["Invocation"], None, None,
     lambda r: [summon(BALISE_R), other("Compte dans la limite d’invocations. Téléportation sur sa case au début de ton prochain tour, puis disparition.")],
     {}),
    ("fleche-boomerang", "Flèche boomerang", None, 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6")), other((r.get("autres") or "Rebonds entre ennemi·e·s.").strip())],
     {}),
    ("fleche-explosive", "Flèche explosive", "32445", 1, 1, ["Amélioration", "Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"] or "2d8"), zone(r)),
                other("+mod. Intelligence aux dégâts de Feu, 1 tour.", turns="1")],
     {}),
    ("fleche-ralentissante", "Flèche ralentissante", None, 1, 2, ["Entrave", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6")),
                strip("pa", num(r["debuff"] or "1", "1") or "1", turns="1")],
     {}),
    ("fleche-massacrante", "Flèche massacrante", None, 1, 3, ["Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"] or "1d4") if "d" in (r.get("degat") or "") else "1d4", zone(r)),
                other("n dés = créatures dans la zone (allié·e·s compris), plafond 5.", zone(r))],
     {}),
    ("fleche-ecrasante", "Flèche écrasante", None, 1, 0, ["Entrave"], None, "vitality",
     lambda r: [other("Indéplaçable 1 tour, allié·e·s compris.", zone(r), turns="1")],
     {"allows_reaction": True}),
    ("acuite-absolue", "Acuité absolue", "32469", 2, 0, ["Amélioration"], None, None,
     lambda r: [other("Précision / critiques renforcés.", turns="1")],
     {}),
    ("pas-chasse", "Pas chassé", "32464", 1, 0, ["Placement"], None, None,
     lambda r: [move("jump", "2"), other("Pas de côté / chasse.")],
     {}),
    ("fleche-empoisonnee", "Flèche empoisonnée", "13060", 2, 0, ["Entrave"], None, "chance",
     lambda r: [other("Poison. Sauvegarde.", turns="2")],
     {}),
    ("sentinelle", "Sentinelle", "32479", 1, 0, ["Amélioration"], None, None,
     lambda r: [other("Allonge la portée normale (donc la portée longue).", turns="1")],
     {}),
    ("fleches-amoureuses", "Flèches amoureuses", None, 2, 0, ["Amélioration"], None, None,
     lambda r: [other("Buff / soins selon fiche.", turns="1")],
     {"allows_reaction": True}),
    ("pluie-de-fleches", "Pluie de flèches", "32481", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d8"), zone(r)),
                other("Terrain difficile pour tout le monde.", zone(r), turns="3")],
     {}),
    ("fleche-tyrannique", "Flèche tyrannique", "32488", 1, 1, ["Amélioration", "Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"] or "2d8")), other((r.get("buff") or "Buff Feu.").strip(), turns="1")],
     {}),
    ("fleche-paralysante", "Flèche paralysante", None, 1, 2, ["Entrave"], "water", "chance",
     lambda r: [other("Paralysie / entrave forte.", turns="1")],
     {}),
    ("carreaux-destructeurs", "Carreaux destructeurs", None, 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "3d6"))],
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
            "official_id": f"jdr:cra:{key}",
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

    for filename, entries in (("cra-level-1.json", level_1), ("cra-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Crâ", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
