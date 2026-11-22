#!/usr/bin/env python3
"""Reconstruit xelor-level-1.json et xelor-progression.json depuis Xelor.md."""
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

SHEET = audit.SHEETS / "Xelor.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {
    "air": ("agi", "Agilité"),
    "fire": ("intel", "Intelligence"),
    "water": ("chance", "Chance"),
    "earth": ("strong", "Force"),
}
COMPLICE = "jdr:summon:xelor-complice"
CADRAN = "jdr:summon:xelor-cadran"


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


def has_telefrag(r: dict) -> bool:
    blob = " ".join(
        [
            r.get("deplac") or "",
            r.get("buff") or "",
            r.get("debuff") or "",
            r.get("autres") or "",
        ]
    ).lower()
    return "téléfrag" in blob or "telefrag" in blob or "déphase" in blob


# key, name, dofus_id, slot, order, types, element, save_key, make, extras
SPELLS = [
    (
        "souvenir",
        "Souvenir",
        "13281",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "1d4")),
            *(
                [move("teleport", num(r["deplac"] or "1", "1") or "1"), other("Cible en Téléfrag.")]
                if "téléporte" in (r.get("deplac") or "").lower()
                else []
            ),
        ],
        {"allows_reaction": True},
    ),
    (
        "perturbation",
        "Perturbation",
        "13288",
        1,
        1,
        ["Amélioration", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "1d4")),
            *(
                [
                    move("teleport", "1"),
                    boost("pa", "1", "1"),
                    other("Si Téléfrag : consomme, +1 PA allié, cible de l’autre côté."),
                ]
                if "autre côté" in (r.get("deplac") or "").lower() or has_telefrag(r)
                else []
            ),
        ],
        {"allows_reaction": True},
    ),
    (
        "gelure",
        "Gelure",
        "13245",
        1,
        2,
        ["Entrave", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"] or "1d4")),
            *(
                [move("teleport", "0"), other("Retour à la case de départ. Téléfrag.")]
                if "retour" in (r.get("deplac") or "").lower()
                else []
            ),
            *([strip("pa", num(r["debuff"] or "1", "1"), turns="1")] if "pa" in (r.get("debuff") or "").lower() else []),
        ],
        {"allows_reaction": True},
    ),
    (
        "frappe-de-xelor",
        "Frappe de Xélor",
        "13252",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "1d6")),
            *(
                [move("teleport", "1"), other("Cible de l’autre côté de toi. Téléfrag.")]
                if "autre côté" in (r.get("deplac") or "").lower()
                else []
            ),
        ],
        {"allows_reaction": True},
    ),
    (
        "teleportation",
        "Téléportation",
        "13249",
        2,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "1"),
            other((r.get("autres") or "Tu te téléportes. Téléfrag. Relance à 0 si tu consommes un Téléfrag.").strip()),
        ],
        {"allows_reaction": True},
    ),
    (
        "flou-temporel",
        "Flou Temporel",
        "13246",
        2,
        1,
        ["Entrave"],
        None,
        None,
        lambda r: [
            strip("pa", "1", zone(r), turns="1"),
            boost("pa", "1", "1", zone(r)),
            other("Sauf toi. −1 PA puis +1 PA. 2x2 si Sag ≥ 3.", zone(r)),
        ],
        {"allows_reaction": True},
    ),
    (
        "conservation",
        "Conservation",
        "13291",
        3,
        0,
        ["Protection"],
        None,
        None,
        lambda r: [
            other((r.get("protect") or "−1 dégât par coup (−2 si Vit ≥ 3).").strip(), turns="1"),
            *([boost("pa", "1", "1")] if "pa" in (r.get("buff") or "").lower() else []),
        ],
        {},
    ),
    (
        "momification",
        "Momification",
        "13260",
        3,
        1,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            boost("pm", "1", "1"),
            other("Intaclable. Téléfrag. +2 PM si Intel ≥ 3.", turns="1"),
        ],
        {},
    ),
    (
        "fletrissement",
        "Flétrissement",
        "13254",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "1d6")),
            other("Si Téléfrag : consomme, +1d6, téléporte de 1, repose Téléfrag."),
            *([move("teleport", "1")] if "téléporte" in (r.get("deplac") or "").lower() else []),
        ],
        {},
    ),
    (
        "refraction",
        "Réfraction",
        "13286",
        1,
        1,
        ["Amélioration", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "1d6")),
            other("Si Téléfrag : consomme, mêmes dégâts aux ennemi·e·s au contact, +1 PA allié."),
            *([boost("pa", "1", "1")] if "pa" in (r.get("buff") or "").lower() else []),
        ],
        {},
    ),
    (
        "ralentissement",
        "Ralentissement",
        "13242",
        1,
        2,
        ["Entrave", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"] or "1d6")),
            strip("pa", num(r["debuff"] or "1", "1") or "1", turns="1"),
            *([boost("pa", "1", "1")] if "pa" in (r.get("buff") or "").lower() else []),
            other("Si Téléfrag : consomme, tu gagnes le PA retiré."),
        ],
        {},
    ),
    (
        "horloge",
        "Horloge",
        "13256",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "2d4")),
            other((r.get("autres") or "Si Téléfrag : consomme, +1d6.").strip()),
        ],
        {},
    ),
    (
        "rembobinage",
        "Rembobinage",
        "13243",
        1,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "0"),
            other((r.get("autres") or "Retour case de départ. Téléfrag. Allié·e : fin de son tour.").strip()),
        ],
        {},
    ),
    (
        "remanence",
        "Rémanence",
        "13255",
        2,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "0"),
            other("Retour à la fin de son tour. Téléfrag. 2 tours si Sag ≥ 3.", turns="1"),
        ],
        {},
    ),
    (
        "complice",
        "Complice",
        "13287",
        1,
        0,
        ["Invocation"],
        None,
        None,
        lambda r: [
            summon(COMPLICE),
            other("Un seul. Contrôlé, juste après toi. Pas d’attaque. Relancer remplace."),
        ],
        {},
    ),
    (
        "fuite-du-temps",
        "Fuite du Temps",
        "13251",
        2,
        0,
        ["Amélioration", "Placement"],
        None,
        None,
        lambda r: [
            other("Avantage contre le tacle jusqu’à ton prochain tour.", turns="1"),
            move("teleport", "0"),
            other("Retour au choix au début de ton prochain tour. Téléfrag. +1 PM si Agi ≥ 3."),
        ],
        {},
    ),
    (
        "astrolabe",
        "Astrolabe",
        "13282",
        1,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "1"),
            other("Téléport libre, échange, ou retour. Téléfrag. PO +1 si Intel ≥ 3."),
        ],
        {},
    ),
    (
        "espace-temps",
        "Espace-temps",
        "13297",
        2,
        0,
        ["Protection", "Placement"],
        None,
        None,
        lambda r: [
            other(
                "1er coup : les deux reviennent case départ + Téléfrag. Attaquant déjà en Téléfrag : −1 PA. −1d4 si Vit ≥ 3.",
                turns="1",
            )
        ],
        {},
    ),
    (
        "distorsion",
        "Distorsion",
        "13289",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "2d4"), zone(r)),
            move("teleport", "0", zone(r)),
            other("Ennemi·e·s : dégâts. Toute créature zone : retour + Téléfrag. Toi sauf.", zone(r)),
        ],
        {},
    ),
    (
        "sablier-de-xelor",
        "Sablier de Xélor",
        "13261",
        1,
        1,
        ["Amélioration", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "2d4")),
            hit("fire", dice(re.sub(r"^.*?éclat\s*", "", r.get("degat") or "", flags=re.I) or "1d6") or "1d6", zone(r)),
            boost("pa", "1", "1", zone(r)),
            other("Éclat au prochain tour (immédiat si Téléfrag). +1 PA allié·e·s zone.", zone(r)),
        ],
        {},
    ),
    (
        "petrification",
        "Pétrification",
        "13290",
        1,
        2,
        ["Entrave", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"] or "2d4")),
            other("Indéplaçable 1 tour. Si Téléfrag : consomme, −1 PA.", turns="1"),
            *([strip("pa", "1", turns="1")] if "−1 pa" in (r.get("debuff") or "").lower() or "-1 pa" in (r.get("debuff") or "").lower() else []),
        ],
        {},
    ),
    (
        "engrenage",
        "Engrenage",
        "13299",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "2d6"), zone(r)),
            move("push", num(r["deplac"] or "1", "1") or "1", zone(r)),
            other("Recul depuis le centre. Téléfrag.", zone(r)),
        ],
        {},
    ),
    (
        "cadran-de-xelor",
        "Cadran de Xélor",
        "13258",
        1,
        0,
        ["Invocation"],
        None,
        None,
        lambda r: [
            summon(CADRAN),
            other("Statique, 3 tours. Heure juste sur les en Téléfrag."),
        ],
        {},
    ),
    (
        "premonition",
        "Prémonition",
        "13293",
        2,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "1"),
            other("Marque une case : téléport ou échange au début de ton prochain tour. Téléfrag. PO +1 si Chance ≥ 3."),
        ],
        {},
    ),
    (
        "raulebaque",
        "Raulebaque",
        "13262",
        1,
        0,
        ["Placement"],
        None,
        None,
        lambda r: [
            move("teleport", "0", "circle-0-6"),
            other("Retour case de départ pour tout le monde à 6 cases, toi compris. Téléfrag.", "circle-0-6"),
        ],
        {},
    ),
    (
        "instabilite",
        "Instabilité",
        "13301",
        2,
        0,
        ["Entrave"],
        None,
        None,
        lambda r: [
            strip("pa", "1", zone(r), turns="1"),
            other("Allié·e·s zone : intaclables. Ennemi Téléfrag : consomme + retour. 2x2 si Force ≥ 3.", zone(r), turns="1"),
        ],
        {},
    ),
    (
        "vingt-cinquieme-heure",
        "Vingt-cinquième Heure",
        "14650",
        1,
        0,
        ["Amélioration"],
        None,
        None,
        lambda r: [
            boost("pa", "2", "0"),
            strip("pa", "1", turns="1"),
            other("Hors limite 1 PA/tour. Pas de perte si Chance ≥ 3 et un Téléfrag consommé."),
        ],
        {},
    ),
    (
        "glas",
        "Glas",
        "13300",
        2,
        0,
        ["Dégâts"],
        None,
        None,
        lambda r: [
            other(
                f"Dégâts {dice(r['degat'] or '2d6')} dans ton meilleur élément (Force/Intel/Chance/Agi). "
                "+1d6 par Téléfrag consommé depuis dernier Glas (max +3d6). Ne se lance que si ≥1 Téléfrag consommé.",
                zone(r),
            )
        ],
        {"attack_characteristic_key": "intel"},
    ),
    (
        "pendule",
        "Pendule",
        "13294",
        1,
        0,
        ["Placement", "Dégâts"],
        "air",
        None,
        lambda r: [
            hit("air", dice(r["degat"] or "2d6")),
            move("teleport", "1"),
            other("Tu passes de l’autre côté, dégâts aussi aux ennemi·e·s à ton contact, retour au choix. Si Téléfrag : +1d6."),
        ],
        {},
    ),
    (
        "rayon-obscur",
        "Rayon Obscur",
        "13253",
        1,
        1,
        ["Amélioration", "Dégâts"],
        "fire",
        None,
        lambda r: [
            hit("fire", dice(r["degat"] or "2d6"), zone(r)),
            boost("pa", "1", "1"),
            other("+1d6 par ennemi Téléfrag (max +2d6, consomme). +1 PA allié par jeton.", zone(r)),
        ],
        {},
    ),
    (
        "clepsydre",
        "Clepsydre",
        "13298",
        1,
        2,
        ["Entrave", "Dégâts"],
        "water",
        None,
        lambda r: [
            hit("water", dice(r["degat"] or "2d6")),
            strip("pa", num(r["debuff"] or "1", "1") or "1", turns="1"),
            *([move("teleport", "0")] if "retour" in (r.get("deplac") or "").lower() else []),
            other("Si Téléfrag : consomme + retour case départ + repose Téléfrag."),
        ],
        {},
    ),
    (
        "regulateur",
        "Régulateur",
        "13248",
        1,
        3,
        ["Dégâts"],
        "earth",
        None,
        lambda r: [
            hit("earth", dice(r["degat"] or "2d8")),
            other("Si Téléfrag : consomme, un lancer de plus ce tour."),
        ],
        {},
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
            "official_id": f"jdr:xelor:{key}",
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

    for filename, entries in (("xelor-level-1.json", level_1), ("xelor-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Xélor", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
