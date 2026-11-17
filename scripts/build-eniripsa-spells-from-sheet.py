#!/usr/bin/env python3
"""Reconstruit eniripsa-level-1.json et eniripsa-progression.json depuis la fiche Obsidian.

30 sorts : 16 élémentaires + Mot d'Amitié + Mot Féerique (obligatoires, une variante)
+ 12 généraux. Mot Féerique : PA stocké = 1 (supplément) ; le coût du sort porté
reste dans l'effet.

Usage :
  python3 scripts/build-eniripsa-spells-from-sheet.py
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

SHEET = audit.SHEETS / "Eniripsa.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {"air": ("agi", "Agilité"), "fire": ("intel", "Intelligence"), "water": ("chance", "Chance"), "earth": ("strong", "Force")}
LAPINO = "jdr:summon:eniripsa-lapino"
FEE = "jdr:summon:eniripsa-fee"


def dice(cell: str, default: str = "") -> str:
    match = re.search(r"\d+d\d+", cell or "")
    if match:
        return match.group(0)
    match = re.search(r"\d+", cell or "")
    return match.group(0) if match else default


def num(cell: str, default: str = "") -> str:
    match = re.search(r"\d+", cell or "")
    return match.group(0) if match else default


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


def heal(value: str, area: str = "point", turns: str | None = None) -> dict:
    sub = {
        "slug": "soigner",
        "area": area,
        "params": {"value": value, "value_formula": value, "dice_formula": value, "effect_direction": "action"},
    }
    if turns:
        sub["duration_formula"] = turns
        sub["params"]["duration_formula"] = turns
    return sub


def shield(value: str, area: str = "point", direction: str = "action", turns: str = "1") -> dict:
    return {
        "slug": "protéger",
        "area": area,
        "duration_formula": turns,
        "params": {
            "value": value,
            "value_formula": value,
            "dice_formula": value,
            "effect_direction": direction,
            "duration_formula": turns,
        },
    }


def boost(characteristic: str, value: str, turns: str, direction: str = "action", area: str = "point") -> dict:
    params = {
        "characteristic": characteristic,
        "value": value,
        "value_formula": value,
        "effect_direction": direction,
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


def push_cells(r: dict, default: str = "1") -> str:
    cell = r.get("deplac") or ""
    match = re.search(r"(?:repousse|recul|attire|bond|saut).*?(\d+)", cell, re.I)
    if match:
        return match.group(1)
    return num(cell, default)


def first_heal(r: dict, default: str = "1d4") -> str:
    return dice(r.get("autres") or "", default) or dice(r.get("buff") or "", default) or default


def first_shield(r: dict, default: str = "1d4") -> str:
    return dice(r.get("protect") or "", default) or default


def make_mot_de_jouvence(r: dict) -> list[dict]:
    ds = re.findall(r"\d+d\d+", r.get("autres") or "")
    return [
        heal(ds[0] if ds else "1d6"),
        heal(ds[1] if len(ds) > 1 else "1d4", turns="1"),
        other("+1d4 au second soin si Sag ≥ 3."),
    ]


# key, name, slot, order, types, element, save_key, make, extras
SPELLS = [
    # Air
    ("murmure", "Murmure", 1, 0, ["Amélioration", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"], "1d4")),
                other((r.get("buff") or r.get("autres") or "À partir du niv. 5 : +1 PM aux allié·e·s au contact.").strip())], {}),
    ("mot-fleuri", "Mot Fleuri", 1, 0, ["Amélioration", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"], "1d10")),
                other((r.get("buff") or "+1d4 au premier coup allié sur la cible.").strip(), turns="1")], {}),
    ("mot-secret", "Mot Secret", 1, 0, ["Amélioration", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"], "2d4"), zone(r)),
                other("Allié·e·s (toi sauf) : avantage au prochain jet d'attaque.", zone(r), turns="1")],
     {"spares_chosen_targets": True}),
    ("bosquet-enchante", "Bosquet Enchanté", 1, 0, ["Amélioration", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"], "3d4"), zone(r)),
                other((r.get("buff") or "Résistance et avantage attaque / 1 tour.").strip(), zone(r), turns="1")],
     {"spares_chosen_targets": True}),
    # Feu
    ("vacarme", "Vacarme", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"], "1d4")),
                heal(dice(r.get("autres") or "1d4", "1d4")),
                other("Niv. 1 : dégâts. À partir du niv. 5 : sur allié·e / toi = soin à la place.")], {}),
    ("mot-tapageur", "Mot Tapageur", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"], "1d8"), zone(r)),
                heal(first_heal(r, "1d6"), zone(r))],
     {"spares_chosen_targets": True}),
    ("mot-turbulent", "Mot Turbulent", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"], "2d4"), zone(r)),
                heal(first_heal(r, "1d4"), zone(r)),
                move("push", push_cells(r, "1"), zone(r)),
                other((r.get("autres") or "Soin allié·e·s ; ennemi·e·s reculent depuis le centre.").strip(), zone(r))],
     {"spares_chosen_targets": True}),
    ("choeur-strident", "Chœur Strident", 1, 1, ["Soin", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"], "2d10"), zone(r)),
                heal(first_heal(r, "2d8"), zone(r)),
                other("Encouragé·e : +1d6 de soin.", zone(r))],
     {"spares_chosen_targets": True}),
    # Eau
    ("mot-vampirique", "Mot Vampirique", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"], "1d4")),
                shield(first_shield(r, "1d4")) if (r.get("protect") or "").strip() else other("À partir du niv. 5 : bouclier à un·e allié·e si le coup porte.")], {}),
    ("sanglots", "Sanglots", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"], "1d8")),
                other((r.get("protect") or "Résistance aux allié·e·s au contact.").strip(), turns="1")], {}),
    ("mot-exsangue", "Mot Exsangue", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"], "2d4")),
                shield(dice(r.get("protect") or "1d6", "1d6")),
                other((r.get("protect") or "Bouclier si allié·e frappé·e / sur allié·e.").strip())], {}),
    ("cryotherapie", "Cryothérapie", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"], "2d8")),
                other((r.get("debuff") or "Désavantage attaque + réaction annulée.").strip(), turns="1")], {}),
    # Terre
    ("juron", "Juron", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"], "1d4")),
                move("push", push_cells(r, "1")) if (r.get("deplac") or "").strip() else other("À partir du niv. 5 : repousse.")], {}),
    ("onguent-ancestral", "Onguent Ancestral", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"], "1d6")),
                strip("pm", num(r.get("debuff") or "1", "1"))], {}),
    ("cri-de-guerre", "Cri de Guerre", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"], "2d6"), zone(r)),
                other("Indéplaçable / 1 tour.", zone(r), turns="1")], {}),
    ("mot-furieux", "Mot Furieux", 1, 3, ["Entrave", "Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"], "3d6"), zone(r)),
                move("push", push_cells(r, "2"), zone(r)),
                other((r.get("debuff") or "Désavantage à l'esquive PM.").strip(), zone(r), turns="1")],
     {"spares_chosen_targets": True}),
    # Obligatoire niv. 1 (slot 3, seule variante)
    ("mot-d-amitie", "Mot d'Amitié", 3, 0, ["Invocation"], None, None,
     lambda r: [summon(LAPINO),
                other((r.get("deplac") or r.get("autres") or "Invoque le Lapino (mignon). Relancé : change d'état.").strip())], {}),
    # Généraux niv. 1 (slot 2)
    ("mot-de-jouvence", "Mot de Jouvence", 2, 0, ["Soin"], None, None, make_mot_de_jouvence, {}),
    ("mot-deprimant", "Mot Déprimant", 2, 1, ["Entrave"], None, "strong",
     lambda r: [other((r.get("debuff") or "−1d4 dégâts par coup.").strip(), turns="1"),
                other("Moquerie.")], {}),
    # Niv. 2
    ("mot-vivifiant", "Mot Vivifiant", 1, 0, ["Amélioration"], None, "agi",
     lambda r: [boost("pm", num(r.get("buff") or "1", "1"), "1"),
                other("Avantage contre le tacle. PO +1 si Agi ≥ 3.", turns="1")], {}),
    ("mot-d-envol", "Mot d'Envol", 1, 1, ["Placement"], None, "agi",
     lambda r: [move("teleport", "1"),
                other("Échange de place avec un·e allié·e. +1 PM à l'allié·e si Agi ≥ 3.")], {}),
    # Niv. 5
    ("mot-galvanisant", "Mot Galvanisant", 1, 0, ["Amélioration"], None, "strong",
     lambda r: [boost("dommages_spell", dice(r.get("buff") or "1d4", "1d4"), "1"),
                shield(first_shield(r, "1d6")),
                other("Mot de boost. +1d6 si Force ≥ 3.")], {}),
    ("mot-accablant", "Mot Accablant", 1, 1, ["Entrave"], None, "chance",
     lambda r: [strip("pm", num(r.get("debuff") or "1", "1")),
                other("Désavantage contre le tacle. Moquerie. −2 PM si Chance ≥ 3.", turns="1")], {}),
    # Obligatoire niv. 6
    ("mot-feerique", "Mot Féerique", 1, 0, ["Invocation"], None, None,
     lambda r: [summon(FEE),
                other("PA = coût du sort porté + 1. Porte un sort ≤ 3 PA (sauf Mot d'Amitié) pendant 2 tours.")],
     {"pa": "1"}),
    # Niv. 8
    ("mot-stimulant", "Mot Stimulant", 1, 0, ["Amélioration"], None, "intel",
     lambda r: [boost("pa", num(r.get("buff") or "1", "1"), "1"),
                other("Mot de boost. +2 PA si Intel ≥ 3.")], {}),
    ("mot-decourageant", "Mot Décourageant", 1, 1, ["Entrave"], None, "sagesse",
     lambda r: [strip("pa", num(r.get("debuff") or "1", "1")),
                other("Moquerie. −2 PA si Sag ≥ 3.", turns="1")], {}),
    # Niv. 10
    ("fontaine-de-jouvence", "Fontaine de Jouvence", 1, 0, ["Soin"], None, None,
     lambda r: [heal(first_heal(r, "2d6"), zone(r)),
                other("Intacable jusqu'au prochain tour ; allié·e·s aussi si Vit ≥ 3.", zone(r), turns="1")],
     {"spares_chosen_targets": True}),
    ("mot-de-declin", "Mot de Déclin", 1, 1, ["Entrave"], None, "intel",
     lambda r: [other("Désenvoutement : fin des effets désenvoutables.")], {}),
    # Niv. 11
    ("mot-de-reconstitution", "Mot de Reconstitution", 1, 0, ["Soin"], None, None,
     lambda r: [heal("PV max"),
                other("Puis insoignable 4 tours (3 si Vit ≥ 3). 1 fois / repos long.")], {}),
    ("mot-de-solidarite", "Mot de Solidarité", 1, 1, ["Soin"], None, "chance",
     lambda r: [heal("montant choisi (1–20)", zone(r)),
                other("Tu perds autant de PV. Encouragé·e·s : soin doublé.", zone(r))],
     {"spares_chosen_targets": True}),
]


def sheet_text(path: Path) -> dict[str, dict]:
    texts: dict[str, dict] = {}
    current = None
    for line in path.read_text().splitlines():
        if (
            re.match(r"^#{3,6}\s+Niv\s+\d+\s*:", line)
            or re.match(r"^\*\*Variante\s+\d+", line)
            or (re.match(r"^\*\*[^*]+\*\*", line) and not re.match(r"^\*\*Variante\s+\d+", line))
        ):
            name = audit.spell_name_from(line)
            if name and not re.match(r"^Niv\s+\d+", name, re.I):
                current = name
                texts[current] = {}
            continue
        if current is None:
            continue
        for label, key in (("*Général*", "description"), ("*Effets*", "effect"), ("*Résolution*", "resolution")):
            if line.startswith(label):
                texts[current][key] = re.sub(r"^\*[^*]+\*\s*:\s*", "", line).strip()
    return texts


def build() -> None:
    sheet = {spell["name"]: spell for spell in audit.parse_sheet(SHEET)}
    texts = sheet_text(SHEET)
    missing = [name for _, name, *_ in SPELLS if name not in sheet]
    if missing:
        raise SystemExit("Sorts absents de la fiche : " + ", ".join(missing))

    level_1, progression = [], []
    for key, name, slot, order, types, element, save_key, make, extras in SPELLS:
        spell = sheet[name]
        rows = audit.resolve_rows(spell["rows"])
        text = texts.get(name, {})
        mode = audit.resolution_mode(text.get("resolution", ""))
        base = audit.usage_dict(rows[0])
        entry = {
            "key": key,
            "name": name,
            "dofusdb_id": None,
            "official_id": f"jdr:eniripsa:{key}",
            "slot_index": slot,
            "choice_order": order,
            "character_level": rows[0]["niv"],
            "types": types,
            "state": "playable",
            "is_magic": True,
            "resolution_mode": mode,
            "description": text.get("description", ""),
            "effect": text.get("effect", ""),
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
            entry["save_success_note"] = text.get("resolution", "")
        elif save_key and mode == "attack_roll":
            entry["attack_characteristic_key"] = save_key
        elif save_key and mode == "auto_success":
            entry["attack_characteristic_key"] = save_key
        entry.update(extras)
        subs = make(rows[0])
        # Filtre les éventuels None (branches conditionnelles)
        entry["sub_effects"] = [dict(sub, order=index) for index, sub in enumerate(s for s in subs if s)]
        tiers = []
        for index, row in enumerate(rows[1:]):
            made = [s for s in make(row) if s]
            tier = {
                "tier": audit.ROMAN[index],
                "required_creature_level": row["niv"],
                "effect": "",
                "sub_effects": [dict(sub, order=i) for i, sub in enumerate(made)],
            }
            for usage_key, value in audit.usage_dict(row).items():
                if base.get(usage_key) != value:
                    tier[usage_key] = value
            tiers.append(tier)
        entry["intensification"] = tiers
        (level_1 if entry["character_level"] == 1 else progression).append(entry)

    for filename, entries in (("eniripsa-level-1.json", level_1), ("eniripsa-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Eniripsa", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
