#!/usr/bin/env python3
"""Reconstruit osamodas-level-1.json et osamodas-progression.json depuis Osamodas.md.

Usage :
  python3 scripts/build-osamodas-spells-from-sheet.py
  python3 scripts/audit-class-spell-sheets.py
"""
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

SHEET = audit.SHEETS / "Osamodas.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}
TOFU = "jdr:summon:tofu"


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
        "params": {
            "value": value,
            "value_formula": value,
            "dice_formula": value,
            "effect_direction": "action",
        },
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


def boost(characteristic: str, value: str, turns: str, area: str = "point") -> dict:
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


def summon(monster_id: str = TOFU) -> dict:
    return {"slug": "invoquer", "area": "point", "params": {"monster_official_id": monster_id}}


def push_cells(r: dict, default: str = "1") -> str:
    return num(r.get("deplac") or "", default) or default


SPELLS = [
    # L1 voies
    (
        "aeropique",
        "Aéropique",
        "31124",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "1d4")),
            *([move("push", push_cells(r, "1"))] if "recul" in (r.get("deplac") or "").lower() else []),
            other("Dès niv. 5 : recul, ou recul des ennemi·e·s au contact si tu vises une bête (pas de dégâts)."),
        ],
        {"allows_reaction": True},
    ),
    (
        "pics-du-prespic",
        "Pics du Prespic",
        "31111",
        1,
        1,
        ["Soin", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "1d4")),
            heal(dice(r["degat"] or "1d4")),
            other("Dès niv. 5 : soin même dé sur une bête, moitié sur un·e allié·e. Jamais sur toi."),
        ],
        {"allows_reaction": True},
    ),
    (
        "bave-du-crapaud",
        "Bave du Crapaud",
        "31139",
        1,
        2,
        ["Dégâts"],
        "water",
        None,
        lambda r: [hit("water", dice(r["degat"] or "1d6"))],
        {"allows_reaction": True},
    ),
    (
        "crocs-du-mulou",
        "Crocs du Mulou",
        "31113",
        1,
        3,
        ["Entrave", "Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "1d4")),
            *([strip("pm", num(r["debuff"] or "1", "1"), turns="1")] if num(r.get("debuff") or "") else []),
        ],
        {"allows_reaction": True},
    ),
    # L1 généraux
    (
        "invocation",
        "Invocation",
        "31115",
        2,
        0,
        ["Invocation"],
        None,
        None,
        lambda r: [
            summon(TOFU),
            other(
                "Choix élément + rang. Rang 1 : 3 PA. Rang 2 : 4 PA (niv. 7+). Rang 3 : 5 PA (niv. 13+). "
                "Limite de la horde. Bête au niveau du lanceur, mods du lanceur."
            ),
        ],
        {},
    ),
    (
        "fouet",
        "Fouet",
        "31114",
        3,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("jump", "2"),
            other("Bête : 2 cases sans tacle (3 si Agi ≥ 3). Allié·e : 1 case."),
        ],
        {"allows_reaction": True},
    ),
    (
        "relais-spirituel",
        "Relais Spirituel",
        "31130",
        3,
        1,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "1"),
            other("Échange avec une de tes bêtes. +1 CA à la bête si Sagesse ≥ 3."),
        ],
        {"allows_reaction": True},
    ),
    # L2
    (
        "piqure-motivante",
        "Piqûre Motivante",
        "31120",
        1,
        0,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            boost("pa", "1", "1"),
            boost("pm", "1", "1"),
            other("Bête : +1 PA +1 PM. Allié·e : +1 PM. +1 attaque à la bête si Intel ≥ 3."),
        ],
        {},
    ),
    (
        "communion-animale",
        "Communion Animale",
        "31142",
        1,
        1,
        ["Protection"],
        None,
        None,
        lambda r: [
            other("Dégâts partagés 50/50 avec une de tes bêtes. −1 par moitié si Vitalité ≥ 3.", turns="1"),
        ],
        {},
    ),
    # L4
    (
        "deplumage",
        "Déplumage",
        "31137",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "1d6"), zone(r)),
            move("push", push_cells(r, "1"), zone(r)),
            other("Allié·e·s épargné·e·s.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "griffes-du-chtigre",
        "Griffes du Chtigre",
        "31134",
        1,
        1,
        ["Soin", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "1d6")),
            heal("moitié des dégâts"),
            other("Soin moitié des dégâts à une de tes bêtes à 6 cases ou moins."),
        ],
        {},
    ),
    (
        "morsure-du-serpent",
        "Morsure du Serpent",
        "31112",
        1,
        2,
        ["Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"] or "2d4")),
            other((r.get("buff") or "+1d4 au premier coup d'une bête").strip(), turns="1"),
        ],
        {},
    ),
    (
        "charge-bestiale",
        "Charge Bestiale",
        "31123",
        1,
        3,
        ["Entrave", "Dégâts"],
        "earth",
        None,
        lambda r: [
            move("jump", "2"),
            hit("earth", dice(r["degat"] or "1d6")),
            other("Indéplaçable, 1 tour. Avance 2 cases en ligne avant le coup, sans tacle.", turns="1"),
        ],
        {},
    ),
    # L5
    (
        "laisse-spirituelle",
        "Laisse Spirituelle",
        "31152",
        1,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            other(
                "Ce tour, tes bêtes à 3 cases te suivent du même nombre de PM, sans tacle. "
                "Allié·e : moitié des cases si Chance ≥ 3."
            )
        ],
        {},
    ),
    (
        "cravache",
        "Cravache",
        "31136",
        1,
        1,
        ["Placement"],
        None,
        "strong",
        lambda r: [
            move("pull", "1", zone(r)),
            other("Allié·e·s et bêtes peuvent refuser. −1 PM aux ennemi·e·s si Force ≥ 3.", zone(r)),
        ],
        {},
    ),
    # L6
    (
        "toison-d-or",
        "Toison d'Or",
        "31138",
        1,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            shield(dice(r["protect"] or "2d6")),
            other("Bête : bouclier plein. Allié·e : moitié. +1 CA à la bête si Vitalité ≥ 3. Pas sur toi."),
        ],
        {},
    ),
    (
        "cri-du-corbac",
        "Cri du Corbac",
        "31110",
        1,
        1,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            boost("pm", "2", "1"),
            other("Bête : +2 PM. Allié·e : +1 PM. +1 PM pour toi si Agilité ≥ 3."),
        ],
        {},
    ),
    # L7
    (
        "serres-du-vautour",
        "Serres du Vautour",
        "31133",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "2d4")),
            move("jump", "1"),
            other("Une de tes bêtes à 6 cases se place au contact de la cible, sans tacle."),
        ],
        {},
    ),
    (
        "coeur-sauvage",
        "Cœur Sauvage",
        "31140",
        1,
        1,
        ["Soin", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "2d4"), "cross-0-1"),
            heal(dice(r["degat"] or "2d4"), "cross-0-1"),
            other("Vise une bête. Ennemi·e·s au contact : dégâts. Allié·e·s/bêtes au contact + elle : soin. Jamais toi."),
        ],
        {},
    ),
    (
        "dents-du-piranya",
        "Dents du Piranya",
        "31135",
        1,
        2,
        ["Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r.get("degat") or "2d4"), zone(r) if r.get("zone") else "cross-0-1"),
            strip("po", "1", zone(r) if r.get("zone") else "cross-0-1", turns="1"),
            other("Allié·e·s épargné·e·s.", zone(r) if r.get("zone") else "cross-0-1"),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "cri-de-l-ours",
        "Cri de l'Ours",
        "31132",
        1,
        3,
        ["Entrave", "Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "2d4"), zone(r)),
            other((r.get("debuff") or "Désavantage à la Fuite face à tes bêtes").strip(), zone(r), turns="1"),
        ],
        {"spares_chosen_targets": True},
    ),
    # L8
    (
        "martinet",
        "Martinet",
        "31147",
        1,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("jump", "2"),
            other("Tes bêtes à 6 cases avancent de 2 vers la cible, sans tacle. +1 attaque si Force ≥ 3."),
        ],
        {},
    ),
    (
        "tourbillon",
        "Tourbillon",
        "31146",
        1,
        1,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "1", zone(r)),
            other(
                "Allié·e·s et bêtes : téléport symétrique par rapport au centre si libre. Croix 2 si Chance ≥ 3.",
                zone(r),
            ),
        ],
        {},
    ),
    # L10
    (
        "cortege-sauvage",
        "Cortège Sauvage",
        "31119",
        1,
        0,
        ["Invocation"],
        None,
        None,
        lambda r: [
            other("Invocation coûte 1 PA de moins (2 au moins) ce tour et le suivant. Relance 3 si Intel ≥ 3.", turns="2"),
        ],
        {},
    ),
    (
        "pacte-bestial",
        "Pacte Bestial",
        "31141",
        1,
        1,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            other(
                "Une bête (pas le compagnon) disparaît. PV temp. = ses PV (max 5/rang, 8/rang si Sag ≥ 3). "
                "+1d6 par rang au prochain sort de dégâts ou soin."
            )
        ],
        {},
    ),
    # L11
    (
        "favoritisme",
        "Favoritisme",
        None,
        1,
        0,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            other("Bête : avantage aux jets d’attaque 2 tours. Allié·e : prochain jet. +1 CA à la bête si Chance ≥ 3.", turns="2"),
        ],
        {},
    ),
    (
        "tandem-animal",
        "Tandem Animal",
        None,
        1,
        1,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            other(
                (r.get("buff") or "+1d6 sur une cible commune").strip() + ". +1 PM à la bête si Intel ≥ 3.",
                turns="1",
            )
        ],
        {},
    ),
    # L12
    (
        "tornade-de-plumes",
        "Tornade de Plumes",
        "31143",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            move("teleport", "1"),
            hit("air", dice(r["degat"] or "2d6"), "cross-0-1"),
            move("push", "1", "cross-0-1"),
            other("Allié·e·s épargné·e·s.", "cross-0-1"),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "chant-du-phenix",
        "Chant du Phénix",
        "31145",
        1,
        1,
        ["Soin", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "2d6"), zone(r)),
            move("push", "1", zone(r)),
            heal(dice(r["degat"] or "2d6"), zone(r)),
            other("Allié·e·s épargné·e·s sur les dégâts. Jamais toi pour le soin.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
    (
        "saute-granouille",
        "Saute-granouille",
        "31121",
        1,
        2,
        ["Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"] or "2d8")),
            other("+1d6 si une de tes bêtes est au contact de la cible."),
        ],
        {},
    ),
    (
        "frappe-du-craqueleur",
        "Frappe du Craqueleur",
        "31144",
        1,
        3,
        ["Entrave", "Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "2d6"), zone(r)),
            move("pull", "1", zone(r)),
            strip("pa", "1", zone(r), turns="1"),
            other("Allié·e·s épargné·e·s.", zone(r)),
        ],
        {"spares_chosen_targets": True},
    ),
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

    # DofusDB id Cri de l'Ours — vérifier dans meta si présent
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

        # Récupère dofusdb_id depuis la meta si le script a None
        resolved_dofus = dofus_id
        if resolved_dofus is None:
            match = re.search(r"DofusDB\s*:\s*([0-9]+)", meta)
            if match:
                resolved_dofus = match.group(1)

        entry = {
            "key": key,
            "name": name,
            "dofusdb_id": resolved_dofus,
            "official_id": f"jdr:osamodas:{key}",
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
            stat = save_key or (STAT[element][0] if element else "sagesse")
            entry["attack_characteristic_key"] = stat
            entry["save_characteristic_key"] = stat
            entry["save_dc_formula"] = DC
            entry["save_success_note"] = meta
        if "réaction" in meta.lower() or spell.get("reaction"):
            entry["allows_reaction"] = True
        if "épargn" in meta.lower():
            entry["spares_chosen_targets"] = True
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

    for filename, entries in (("osamodas-level-1.json", level_1), ("osamodas-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Osamodas", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
