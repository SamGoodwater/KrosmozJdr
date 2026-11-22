#!/usr/bin/env python3
"""Reconstruit enutrof-level-1.json et enutrof-progression.json depuis Enutrof.md."""
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

SHEET = audit.SHEETS / "Enutrof.md"
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


def poison_note(kind: str) -> dict:
    return other(f"Poison {kind}. 1d4 de l’élément, 2 déclenchements max.")


def push_cells(r: dict, default: str = "1") -> str:
    return num(r.get("deplac") or "", default) or default


SPELLS = [
    # L1 voies
    ("monnaie-sonnante", "Monnaie Sonnante", "13353", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d4")),
                *([poison_note("PA")] if "poison" in (r.get("poison") or "").lower() else []),
                *([strip("pa", num(r["debuff"] or "1", "1"), turns="1")] if num(r.get("debuff") or "") else [])],
     {"allows_reaction": True}),
    ("roulage-de-pelle", "Roulage de Pelle", "13331", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d4")),
                *([poison_note("soin")] if "poison" in (r.get("poison") or "").lower() else []),
                *([heal(dice(r["soin"] or "1d4"))] if dice(r.get("soin") or "") else []),
                other("Soin sur toi au contact : réveille le poison de soin.")],
     {"allows_reaction": True}),
    ("lancer-de-pieces", "Lancer de Pièces", "13338", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d4")),
                *([poison_note("PM")] if "poison" in (r.get("poison") or "").lower() else []),
                *([boost("pm", num(r["buff"] or "1", "1"), "1")] if num(r.get("buff") or "") else [])],
     {"allows_reaction": True}),
    ("lancer-de-pelle", "Lancer de Pelle", "13330", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "1d6")),
                *([poison_note("PO")] if "poison" in (r.get("poison") or "").lower() else []),
                *([strip("po", num(r["debuff"] or "1", "1"), turns="1")] if num(r.get("debuff") or "") else [])],
     {"allows_reaction": True}),
    # L1 généraux
    ("maladresse", "Maladresse", "13337", 2, 0, ["Entrave"], None, "sagesse",
     lambda r: [strip("pm", num(r["debuff"] or "1", "1"), turns="1"),
                other("Réveille un poison de PM. Ne pose pas de poison. PO +1 si Sag ≥ 3.")],
     {}),
    ("ruee-vers-l-or", "Ruée vers l’Or", "13342", 2, 1, ["Amélioration"], None, None,
     lambda r: [boost("pm", num(r["buff"] or "1", "1"), "1"),
                other((r.get("autres") or "+1 au prochain coup s’il finit au contact").strip())],
     {}),
    ("clef-de-bras", "Clef de Bras", "13359", 3, 0, ["Soin"], None, None,
     lambda r: [heal(dice(r["soin"] or "1d4")),
                strip("pm", num(r["debuff"] or "1", "1"), turns="1"),
                other("−PM à l’ennemi au contact : réveille poison PM. Soin +1 dé si Vit ≥ 3.")],
     {}),
    ("deambulation", "Déambulation", "13356", 3, 1, ["Placement"], None, None,
     lambda r: [move("teleport", "1"),
                other("Échange avec l’objet animé, sinon recul. +1 si Agi ≥ 3.")],
     {"allows_reaction": True}),
    # L2
    ("age-d-or", "Âge d’Or", "13329", 1, 0, ["Amélioration"], None, None,
     lambda r: [other((r.get("autres") or "Ne peut pas être taclé. Prochain coup +1.").strip(), turns="1")],
     {}),
    ("clef-du-tresor", "Clef du Trésor", "13332", 1, 1, ["Entrave"], None, "strong",
     lambda r: [strip("po", num(r["debuff"] or "1", "1"), turns="1"),
                other("Réveille un poison de PO. Ne pose pas de poison.")],
     {}),
    # L4
    ("abattement", "Abattement", "13365", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "1d6")),
                poison_note("PA"),
                strip("pa", num(r["debuff"] or "1", "1"), turns="1")],
     {}),
    ("feu-de-mine", "Feu de Mine", "13355", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "1d6")),
                poison_note("soin"),
                other((r.get("autres") or "Prochain coup allié/toi : soin (réveille).").strip())],
     {}),
    ("pelle-aurifere", "Pelle Aurifère", "13343", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "1d6")),
                poison_note("PM"),
                strip("pm", num(r["debuff"] or "1", "1"), turns="1"),
                boost("pm", num(r["buff"] or "1", "1"), "1")],
     {}),
    ("eboulement", "Éboulement", "13352", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6")),
                poison_note("PO"),
                strip("po", num(r["debuff"] or "1", "1"), turns="1")],
     {}),
    # L5
    ("sac-anime", "Sac Animé", "13328", 1, 0, ["Invocation"], None, None,
     lambda r: [summon("jdr:summon:enutrof-sac-anime"),
                other("Un seul objet animé. 3 tours. Frapper = soin moitié des dégâts.")],
     {}),
    ("tunnel-de-fortune", "Tunnel de Fortune", "14274", 1, 1, ["Amélioration"], None, None,
     lambda r: [other((r.get("autres") or "Invisible. Prochain coup +1d4 si 2 PM.").strip(), turns="1")],
     {}),
    # L6
    ("pelle-animee", "Pelle Animée", "13344", 1, 0, ["Invocation"], None, None,
     lambda r: [summon("jdr:summon:enutrof-pelle-animee"),
                other("Un seul objet animé. 3 tours.")],
     {}),
    ("boite-de-pandore", "Boîte de Pandore", "13334", 1, 1, ["Soin"], None, None,
     lambda r: [heal(dice(r["soin"] or "2d4"), zone(r)),
                other((r.get("soin") or "Toi 2d4. Allié·e·s 1d4.").strip(), zone(r))],
     {}),
    # L7
    ("banqueroute", "Banqueroute", "14278", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d4")),
                poison_note("PA"),
                other((r.get("debuff") or "−5 esquive PA").strip(), turns="1"),
                other("+1d4 vs invocation.")],
     {}),
    ("coup-de-grisou", "Coup de Grisou", "13368", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d4"), zone(r)),
                poison_note("soin"),
                other("Allié·e·s épargné·e·s.", zone(r))],
     {"spares_chosen_targets": True}),
    ("orpaillage", "Orpaillage", "13363", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d4")), poison_note("PM")],
     {}),
    ("remblai", "Remblai", "13335", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d6"), zone(r)),
                other("Allié·e·s et toi saufs. +1d4 vs invocation.", zone(r))],
     {"spares_chosen_targets": True}),
    # L8
    ("avarice", "Avarice", "13362", 1, 0, ["Entrave"], None, None,
     lambda r: [strip("pm", num(r["debuff"] or "1", "1"), zone(r), turns="1"),
                boost("pa", "1", "1"),
                other("Réveille poison PM. Toi hors retrait.", zone(r))],
     {}),
    ("decadence", "Décadence", "13369", 1, 1, ["Placement"], None, None,
     lambda r: [move("push", push_cells(r, "1")),
                other("Ou échange avec un·e allié·e. +1 case de repousse si Agi ≥ 3.")],
     {"allows_reaction": True}),
    # L10
    ("coffre-anime", "Coffre Animé", "13347", 1, 0, ["Invocation"], None, None,
     lambda r: [summon("jdr:summon:enutrof-coffre-anime"),
                other("Un seul objet animé. 3 tours.")],
     {}),
    ("retraite-anticipee", "Retraite Anticipée", "13349", 1, 1, ["Entrave"], None, None,
     lambda r: [strip("pm", num(r["debuff"] or "1", "1"), zone(r), turns="1"),
                other("Réveille poison PM. Recul si Force ≥ 3.", zone(r))],
     {}),
    # L11
    ("boite-a-outils", "Boîte à Outils", "13357", 1, 0, ["Amélioration"], None, None,
     lambda r: [boost("pa", "1", "1"), boost("pm", "1", "1"),
                other((r.get("buff") or "+1 PA +1 PM. Toi +1 PM si Intel ≥ 3.").strip())],
     {}),
    ("corruption", "Corruption", "13346", 1, 1, ["Protection"], None, None,
     lambda r: [other("Invulnérable et pacifiste, 1 tour. Peut encore se déplacer, soigner, subir un retrait.", turns="1")],
     {}),
    # L12
    ("beche-des-anciens", "Bêche des Anciens", "13367", 1, 0, ["Entrave", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"] or "2d6"), zone(r)),
                poison_note("PA"),
                move("push", push_cells(r, "1"), zone(r)),
                other("Allié·e·s épargné·e·s.", zone(r))],
     {"spares_chosen_targets": True}),
    ("gisement", "Gisement", "14273", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"] or "2d6"), zone(r)),
                poison_note("soin"),
                heal(dice(r["soin"] or "1d4"), zone(r)),
                other("Soins allié·e·s au contact : réveillent le poison.", zone(r))],
     {}),
    ("tamisage", "Tamisage", "13370", 1, 2, ["Amélioration", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"] or "2d6"), zone(r)),
                poison_note("PM"),
                boost("pm", num(r["buff"] or "1", "1"), "1", zone(r))],
     {}),
    ("tourbiere", "Tourbière", "13358", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"] or "2d8"), zone(r)),
                other("Indéplaçable, 1 tour. Bonus si PM déjà dépensés ce tour.", zone(r), turns="1")],
     {"spares_chosen_targets": True}),
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
            "official_id": f"jdr:enutrof:{key}",
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
            stat = save_key or "sagesse"
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

    for filename, entries in (("enutrof-level-1.json", level_1), ("enutrof-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Enutrof", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
