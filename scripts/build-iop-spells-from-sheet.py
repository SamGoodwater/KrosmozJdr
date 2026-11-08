#!/usr/bin/env python3
"""Reconstruit iop-level-1.json et iop-progression.json depuis la fiche Obsidian Iop.

La fiche donne les paramètres de lancer et les paliers (mêmes règles que
audit-class-spell-sheets.py). SPELLS donne, pour chaque sort, l'emplacement,
les types, le dofusdb_id déjà en base et les sous-effets construits depuis une
ligne de la fiche. Modèle à copier pour reconstruire une autre classe.

Usage :
  python3 scripts/build-iop-spells-from-sheet.py
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

SHEET = audit.SHEETS / "Iop.md"
OUT = audit.JSON_DIR
DC = "8 + modificateur de caractéristique + bonus de maîtrise"
STAT = {"air": ("agi", "Agilité"), "fire": ("intel", "Intelligence"), "water": ("chance", "Chance"), "earth": ("strong", "Force")}


def dice(cell: str, default: str = "") -> str:
    match = re.search(r"\d+d\d+", cell or "")
    if match:
        return match.group(0)
    match = re.search(r"\d+", cell or "")
    return match.group(0) if match else default


def all_dice(cell: str) -> list[str]:
    return re.findall(r"\d+d\d+", cell or "")


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


def shield(value: str, area: str = "point", direction: str = "action", turns: str = "1") -> dict:
    return {
        "slug": "protéger",
        "area": area,
        "duration_formula": turns,
        "params": {"value": value, "value_formula": value, "dice_formula": value, "effect_direction": direction, "duration_formula": turns},
    }


def boost(characteristic: str, value: str, turns: str, direction: str = "action", area: str = "point") -> dict:
    params = {"characteristic": characteristic, "value": value, "value_formula": value, "effect_direction": direction, "duration_formula": turns}
    if "d" in value:
        params["dice_formula"] = value
    return {"slug": "booster", "area": area, "duration_formula": turns, "params": params}


def other(text: str, area: str = "point", turns: str | None = None) -> dict:
    sub = {"slug": "autre", "area": area, "params": {"value": text}}
    if turns:
        sub["duration_formula"] = turns
        sub["params"]["duration_formula"] = turns
    return sub


def move(kind: str, cells: str, area: str = "point", turns: str | None = None) -> dict:
    params = {"cells_formula": cells, "movement_kind": kind, "effect_direction": "action"}
    if kind == "teleport":
        params["teleport"] = True
    sub = {"slug": "déplacer", "area": area, "params": params}
    if turns:
        sub["duration_formula"] = turns
    return sub


def turns_of(cell: str, default: str = "1") -> str:
    match = re.search(r"(\d+)\s*tours?", cell or "")
    return match.group(1) if match else default


# key, name, dofusdb_id, slot, order, types, element, stat (attaque/sauvegarde), subs(r), extras
SPELLS = [
    # Niveau 1 — voies
    ("intimidation", "Intimidation", "13108", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"])), move("push", num(r["deplac"]))], {}),
    ("epee-du-destin", "Épée du Destin", "13111", 1, 1, ["Amélioration", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"])), boost("dommages_spell", dice(r["buff"]), "1", "self")], {"max_stack": 1}),
    ("ferveur", "Ferveur", "14676", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"]), zone(r)), shield(dice(r["protect"]), zone(r))], {}),
    ("pression", "Pression", "13106", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"]))], {}),
    # Niveau 1 — généraux
    ("bond", "Bond", "15660", 2, 0, ["Placement"], None, None,
     lambda r: [move("teleport", audit.parse_po(r["po"])[1].__str__())], {}),
    ("determination", "Détermination", "13132", 2, 1, ["Protection"], None, None,
     lambda r: [shield(dice(r["protect"])), other("Indéplaçable.", turns="1")], {}),
    ("vindicte", "Vindicte", "14768", 3, 0, ["Entrave"], None, "sagesse",
     lambda r: [other("Insoignable.", turns=turns_of(r["debuff"]))], {}),
    ("puissance", "Puissance", "13118", 3, 1, ["Amélioration"], None, None,
     lambda r: [boost("dommages_spell", dice(r["buff"]), "2")], {"max_stack": 1}),
    # Niveau 2
    ("agitation", "Agitation", "32280", 1, 0, ["Placement"], None, None,
     lambda r: [boost("pm", num(r["buff"]), "1", "self"), other("Intaclable.", turns="1")], {}),
    ("coup-pour-coup", "Coup pour coup", "13152", 1, 1, ["Placement", "Protection"], None, None,
     lambda r: [move("push", num(r["deplac"]), turns="2"),
                other("En réaction, repousse celui qui attaque au contact la créature protégée.", turns="2")], {}),
    # Niveau 4
    ("souffle", "Souffle", "14062", 1, 0, ["Placement", "Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"]), zone(r)), move("push", num(r["deplac"]), zone(r))], {}),
    ("tumulte", "Tumulte", "13154", 1, 1, ["Amélioration", "Dégâts"], "fire", "intel",
     lambda r: [hit("fire", "1d" + re.search(r"n\s*d(\d+)", r["degat"]).group(1), zone(r)),
                other("Un dé de dégâts par créature dans la zone. Ne touche pas le lanceur.", zone(r))], {}),
    ("endurance", "Endurance", "13133", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"]), "cross-0-1"), shield(dice(r["protect"]), direction="self")], {}),
    ("concentration", "Concentration", "30767", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"])), other("+1d6 dégâts contre une invocation.")], {}),
    # Niveau 5
    ("precipitation", "Précipitation", "13114", 1, 0, ["Amélioration"], None, None,
     lambda r: [boost("pa", num(r["buff"]), "1"), other("Impossible d'attaquer avec une arme pendant ce tour.", turns="1")], {}),
    ("violence", "Violence", "13151", 1, 1, ["Placement"], None, "strong",
     lambda r: [move("push", num(r["deplac"]), zone(r))], {"spares_chosen_targets": True}),
    # Niveau 6
    ("duel", "Duel", "13165", 1, 0, ["Entrave", "Protection"], None, "sagesse",
     lambda r: [other("Désavantage si la cible attaque une autre créature que le lanceur.", turns=turns_of(r["autres"])),
                other("Désavantage aux attaques lancées à plus d'une case contre le lanceur.", turns=turns_of(r["autres"]))], {}),
    ("vitalite", "Vitalité", "32702", 1, 1, ["Protection"], None, None,
     lambda r: [shield(all_dice(r["protect"])[1]),
                other("Si tu es la cible : " + all_dice(r["protect"])[0] + " à la place.")], {}),
    # Niveau 7
    ("friction", "Friction", "13128", 1, 0, ["Placement", "Dégâts"], "air", None,
     lambda r: [hit("air", dice(r["degat"])), move("pull", num(r["deplac"]))], {}),
    ("tempete-de-puissance", "Tempête de Puissance", "13121", 1, 1, ["Amélioration", "Dégâts"], "fire", None,
     lambda r: [hit("fire", dice(r["degat"])),
                other(f"La première cible subit +{dice(r['autres'])} à chaque coup suivant de ce sort ce tour.")], {}),
    ("epee-du-jugement", "Épée du Jugement", "28590", 1, 2, ["Protection", "Dégâts"], "water", None,
     lambda r: [hit("water", dice(r["degat"])), shield(dice(r["protect"]), direction="self"),
                other(f"Au début de ton prochain tour, la cible subit {dice(r['autres'])} dégâts d'Eau.", turns="1")], {}),
    ("epee-de-iop", "Épée de Iop", "13125", 1, 3, ["Dégâts"], "earth", "strong",
     lambda r: [hit("earth", dice(r["degat"]), zone(r))], {"spares_chosen_targets": True}),
    # Niveau 8
    ("deferlement", "Déferlement", "15922", 1, 0, ["Placement"], None, None,
     lambda r: [other("Tu te rapproches jusqu'au contact de la cible. Pas de dégâts.")], {}),
    ("emprise", "Emprise", "13141", 1, 1, ["Entrave"], None, "sagesse",
     lambda r: [other("Invulnérable et Indéplaçable jusqu'au début de ton prochain tour.", turns="1")], {}),
    # Niveau 10
    ("accumulation", "Accumulation", "30772", 1, 0, ["Amélioration"], None, None,
     lambda r: [boost("dommages_spell", dice(r["buff"]), "2", "self"), other("Seulement sur tes sorts de Terre.", turns="2")], {"max_stack": 1}),
    ("massacre", "Massacre", "13127", 1, 1, ["Entrave"], None, "sagesse",
     lambda r: [other(f"La cible subit +{dice(r['debuff'])} dégâts à chaque coup reçu.", turns="1")], {}),
    # Niveau 11
    ("vertu", "Vertu", "29723", 1, 0, ["Protection"], None, None,
     lambda r: [shield(dice(r["protect"]), zone(r)),
                other("Le lanceur perd 1 aux dégâts par allié·e à son contact, jusqu'à 4.", turns="1")], {}),
    ("conquete", "Conquête", "13148", 1, 1, ["Invocation", "Protection"], None, None,
     lambda r: [{"slug": "invoquer", "area": "point", "params": {"monster_official_id": "jdr:summon:iop-stratege"}},
                other(f"Les allié·e·s au contact du Stratège subissent {dice(r['protect'])} dégâts de moins par coup.")], {}),
    # Niveau 12
    ("epee-celeste", "Épée Céleste", "13122", 1, 0, ["Placement", "Dégâts"], "air", "agi",
     lambda r: [hit("air", dice(r["degat"]), zone(r)), other("Ne touche pas le lanceur.", zone(r))], {}),
    ("epee-destructrice", "Épée Destructrice", "13119", 1, 1, ["Amélioration", "Dégâts"], "fire", "intel",
     lambda r: [hit("fire", dice(r["degat"]), zone(r)), boost("dommages_spell", dice(r["buff"]), "1", area=zone(r))],
     {"spares_chosen_targets": True}),
    ("fendoir", "Fendoir", "13149", 1, 2, ["Protection", "Dégâts"], "water", "chance",
     lambda r: [hit("water", dice(r["degat"]), zone(r)), shield(dice(r["protect"]), direction="self"),
                other("Bouclier par ennemi touché, jusqu'à 4. Ne touche pas le lanceur.")], {}),
    ("colere-de-iop", "Colère de Iop", "15661", 1, 3, ["Dégâts"], "earth", None,
     lambda r: [hit("earth", dice(r["degat"])), other("+2d12 par Colère de Iop déjà lancée dans ce combat.")], {}),
]

SHEET_FIX: dict[str, dict] = {}


def sheet_text(path: Path) -> dict[str, dict]:
    texts: dict[str, dict] = {}
    current = None
    for line in path.read_text().splitlines():
        if re.match(r"^#{3,6}\s+Niv\s+\d+", line) or re.match(r"^\*\*Variante\s+\d+", line):
            current = audit.spell_name_from(line)
            if current:
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
    level_1, progression = [], []
    for key, name, dofus_id, slot, order, types, element, save_key, make, extras in SPELLS:
        spell = sheet[name]
        rows = audit.resolve_rows(spell["rows"])
        for row in rows:
            row.update(SHEET_FIX.get(name, {}))
        text = texts[name]
        mode = audit.resolution_mode(text.get("resolution", ""))
        base = audit.usage_dict(rows[0])
        entry = {
            "key": key,
            "name": name,
            "dofusdb_id": dofus_id,
            "official_id": f"jdr:iop:{key}",
            "slot_index": slot,
            "choice_order": order,
            "character_level": rows[0]["niv"],
            "types": types,
            "state": "playable",
            "is_magic": False,
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
            for usage_key, value in audit.usage_dict(row).items():
                if base.get(usage_key) != value:
                    tier[usage_key] = value
            tiers.append(tier)
        entry["intensification"] = tiers
        (level_1 if entry["character_level"] == 1 else progression).append(entry)

    for filename, entries in (("iop-level-1.json", level_1), ("iop-progression.json", progression)):
        payload = {"_schema_version": "1", "breed": "Iop", "state": "playable", "entries": entries}
        (OUT / filename).write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")
        print(filename, len(entries))


if __name__ == "__main__":
    build()
