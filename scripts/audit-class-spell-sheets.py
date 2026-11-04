#!/usr/bin/env python3
"""Compare les fiches Obsidian des classes aux catalogues JSON de sorts.

Une case vide reprend la ligne précédente. Sur la première ligne, les défauts
de la fiche type s'appliquent (PA 3, /tour 2, relance 0, ligne de vue avec).
Si /cible n'est jamais écrit, il vaut le /tour de la ligne. S'il est écrit une
fois, les cases vides suivantes gardent ce nombre.

L'importeur ne reprend pas la ligne précédente : une clé absente d'un palier
retombe sur le sort de base. Le mode --apply réécrit donc la clé sur chaque
palier dont la valeur diffère de la base.

On ne réécrit un sort que si son niveau d'apprentissage est celui de la fiche.
Un homonyme appris à un autre niveau (ancien kit Iop) n'est pas écrasé.

Usage :
  python3 scripts/audit-class-spell-sheets.py
  python3 scripts/audit-class-spell-sheets.py --apply
"""
from __future__ import annotations

import argparse
import json
import re
import unicodedata
from copy import deepcopy
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SHEETS = Path(
    "/mnt/c/Users/sambo/kDrive/Documents/SamyCoffreObsidian/SamyCoffre/20-Projets/KrosmozJDR/Idées/Liste contenu/Classes"
)
JSON_DIR = ROOT / "database/seeders/data/entities/spells"
FILES = {
    "Cra.md": ["cra-level-1.json", "cra-progression.json"],
    "Iop.md": ["iop-level-1.json", "iop-progression.json"],
    "Ecaflip.md": ["ecaflip-level-1.json", "ecaflip-progression.json"],
}
ROMAN = ["I", "II", "III", "IV", "V", "VI"]
TEXT_COLS = ("zone", "degat", "deplac", "protect", "debuff", "buff", "autres")


def norm_name(value: str) -> str:
    text = re.sub(r"<[^>]+>", "", value or "")
    text = unicodedata.normalize("NFKD", text)
    text = "".join(char for char in text if not unicodedata.combining(char))
    text = text.lower().replace("'", " ")
    text = re.sub(r"#\S+", "", text)
    text = re.sub(r"[^a-z0-9]+", " ", text)
    text = re.sub(r"\s+", " ", text).strip()
    text = re.sub(r"\bvagabonde\b", "vagabond", text)
    text = re.sub(r"\bemflammee\b", "enflammee", text)
    text = re.sub(r"\bempoisonne\b", "empoisonnee", text)
    text = re.sub(r"\bfleches\b", "fleche", text)
    return text


def strip_html(value: str) -> str:
    return re.sub(r"<[^>]+>", "", value or "").replace("**", "").strip()


def split_row(line: str) -> list[str] | None:
    if not line.strip().startswith("|"):
        return None
    return [cell.strip() for cell in line.strip().strip("|").split("|")]


def header_key(cell: str) -> str | None:
    text = strip_html(cell).lower()
    text = unicodedata.normalize("NFKD", text)
    text = "".join(char for char in text if not unicodedata.combining(char))
    if text.startswith("niv"):
        return "niv"
    if text == "pa":
        return "pa"
    if text == "po":
        return "po"
    if text.startswith("zone"):
        return "zone"
    if "lign" in text or "vue" in text:
        return "los"
    if "cible" in text:
        return "cible"
    if text.startswith("/tour") or text == "tour":
        return "tour"
    if "relance" in text:
        return "relance"
    if "degat" in text:
        return "degat"
    if "deplac" in text:
        return "deplac"
    if "protect" in text:
        return "protect"
    if "debuff" in text:
        return "debuff"
    if text == "buff":
        return "buff"
    if "autre" in text:
        return "autres"
    return None


def is_sep(cells: list[str] | None) -> bool:
    return bool(cells) and all(re.fullmatch(r":?-{3,}:?", cell.replace(" ", "")) or cell == "" for cell in cells)


def spell_name_from(text: str) -> str | None:
    text = strip_html(text)
    text = re.sub(r"^#+\s*", "", text)
    text = re.sub(r"^Niv\s+\d+\s*:\s*", "", text, flags=re.I)
    text = re.sub(r"^Variante\s+\d+\s*:\s*", "", text, flags=re.I)
    text = re.sub(r"\s+#.*$", "", text).strip(" :*")
    if not text or len(text) > 80:
        return None
    return text


def parse_sheet(path: Path) -> list[dict]:
    lines = path.read_text().splitlines()
    spells: list[dict] = []
    current = None
    index = 0
    while index < len(lines):
        raw = lines[index]
        heading = None
        if re.match(r"^#{3,6}\s+Niv\s+\d+", raw) or re.match(r"^\*\*Variante\s+\d+", raw):
            heading = spell_name_from(raw)
        if heading:
            current = {"name": heading, "resolution": "", "rows": []}
            spells.append(current)
            index += 1
            continue
        if current is None:
            index += 1
            continue
        if raw.strip().startswith("*Résolution*") or raw.strip().startswith("*Resolution*"):
            current["resolution"] += " " + re.sub(r"^\*R[ée]solution\*\s*:?\s*", "", raw.strip())
            index += 1
            continue
        cells = split_row(raw)
        if cells and header_key(cells[0]) == "niv" and any(header_key(cell) == "pa" for cell in cells):
            keys = [header_key(cell) for cell in cells]
            index += 1
            if index < len(lines) and is_sep(split_row(lines[index]) or []):
                index += 1
            while index < len(lines):
                row_cells = split_row(lines[index])
                if not row_cells or not re.match(r"^\d+", strip_html(row_cells[0])):
                    if row_cells and re.search(r"Variante\s+\d+", strip_html(row_cells[0])):
                        name = spell_name_from(strip_html(row_cells[0]))
                        current = {"name": name or strip_html(row_cells[0]), "resolution": "", "rows": []}
                        spells.append(current)
                        index += 1
                    break
                data = {}
                for idx, key in enumerate(keys):
                    if key and idx < len(row_cells):
                        data[key] = strip_html(row_cells[idx])
                current["rows"].append(data)
                index += 1
            continue
        index += 1
    return [spell for spell in spells if spell["rows"]]


def to_int(value, default=None):
    if value is None:
        return default
    match = re.search(r"\d+", str(value))
    return int(match.group(0)) if match else default


def resolve_rows(rows: list[dict]) -> list[dict]:
    defaults = {"pa": "3", "po": "càc", "zone": "", "los": "", "cible": "", "tour": "2", "relance": "0"}
    previous = None
    explicit_cible = False
    resolved_rows = []
    for row in rows:
        resolved = {"_raw": {}}
        for key, default in defaults.items():
            cell = (row.get(key) or "").strip()
            resolved["_raw"][key] = cell
            if cell == "" and previous is not None:
                resolved[key] = previous[key]
            elif cell == "":
                resolved[key] = default
            else:
                resolved[key] = cell
        if resolved["_raw"]["cible"] == "":
            if not explicit_cible:
                resolved["cible"] = resolved["tour"]
        else:
            explicit_cible = True
            resolved["cible"] = resolved["_raw"]["cible"]
        for key in TEXT_COLS:
            cell = (row.get(key) or "").strip()
            resolved["_raw"][key] = cell
            if cell == "" and previous is not None:
                resolved[key] = previous.get(key, "")
            else:
                resolved[key] = cell
        resolved["niv"] = to_int(row.get("niv"))
        previous = resolved
        resolved_rows.append(resolved)
    return resolved_rows


def parse_po(text: str):
    cleaned = (text or "").lower().replace("m", "").replace(" ", "").replace("–", "-")
    if cleaned in ("", "cac", "càc"):
        return None
    if re.fullmatch(r"\d+", cleaned):
        number = int(cleaned)
        return (number, number)
    match = re.fullmatch(r"(\d+)-(\d+)", cleaned)
    if match:
        return (int(match.group(1)), int(match.group(2)))
    return ("?", "?")


def canon_zone(text: str) -> str | None:
    cleaned = (text or "").lower()
    cleaned = cleaned.replace("allié·e·s safe", "").replace("as", "")
    cleaned = re.sub(r"\s+", " ", cleaned).strip()
    if cleaned in ("", "point", "1x1", "1 x 1"):
        return "point" if cleaned else None
    match = re.search(r"croix(?:\s+de)?\s*(\d+)", cleaned)
    if match:
        return f"cross-0-{match.group(1)}"
    match = re.search(r"(\d+)\s*x\s*(\d+)", cleaned)
    if match:
        width, height = int(match.group(1)), int(match.group(2))
        if width == 1 and height == 1:
            return "point"
        if min(width, height) == 1:
            return f"line-1x{max(width, height)}"
        return f"rect-{width}x{height}"
    return None


def resolution_mode(text: str) -> str | None:
    lowered = (text or "").lower()
    if not lowered.strip():
        return None
    has_attack = "jet d'attaque" in lowered or "jet d’attaque" in lowered
    has_save = "sauvegarde" in lowered or re.search(r"\bdd\b", lowered) is not None
    has_auto = "automatique" in lowered
    if has_save and not has_attack:
        return "saving_throw"
    if has_attack:
        return "attack_roll"
    if has_auto:
        return "auto_success"
    return None


def los_flags(text: str) -> dict:
    lowered = (text or "").lower()
    return {
        "sight_line": "sans" not in lowered,
        "cast_in_line": "en ligne" in lowered,
        "cast_in_diagonal": "diagonal" in lowered,
    }


def usage_dict(row: dict) -> dict:
    usage = {
        "pa": str(to_int(row["pa"])),
        "cast_per_turn": str(to_int(row["tour"])),
        "cast_per_target": str(to_int(row["cible"])),
        "number_between_two_cast": str(to_int(row["relance"], 0)),
    }
    usage.update(los_flags(row["los"]))
    po = parse_po(row["po"])
    if po and po != ("?", "?"):
        usage["po_min"] = str(po[0])
        usage["po_max"] = str(po[1])
        usage["po_editable"] = po[1] > 0
    return usage


def replace_first_dice(text: str, notation: str) -> str:
    return re.sub(r"\d+d\d+", notation, str(text), count=1)


def bump_dice(subs: list[dict], cell: str, slugs: tuple[str, ...]) -> None:
    match = re.fullmatch(r"(\d+d\d+)", (cell or "").strip().lower())
    if not match:
        return
    notation = match.group(1)
    for sub in subs:
        if sub.get("slug") not in slugs:
            continue
        params = sub.setdefault("params", {})
        for key in ("value", "value_formula", "dice_formula"):
            if params.get(key):
                params[key] = replace_first_dice(params[key], notation)
        return


def zone_to_apply(row: dict) -> str | None:
    raw = (row.get("_raw") or {}).get("zone", "")
    if raw.strip():
        return canon_zone(raw)
    inherited = (row.get("zone") or "").strip()
    return canon_zone(inherited) if inherited else None


def apply_zone(subs: list[dict], zone: str | None) -> None:
    if not zone or not subs:
        return
    primary = subs[0].get("area")
    for sub in subs:
        if sub is subs[0] or sub.get("area") == primary:
            sub["area"] = zone


def duration_n(row: dict) -> str | None:
    parts = []
    for key in ("debuff", "buff", "protect", "autres", "degat"):
        cell = row.get("_raw", {}).get(key) or ""
        if re.search(r"relance", cell, re.I) and not re.search(r"pendant|/", cell):
            continue
        parts.append(cell)
    found = re.findall(r"(?:pendant|/)\s*(\d+)\s*tours?", " ".join(parts), re.I)
    if not found:
        found = re.findall(r"(\d+)\s*tours?", " ".join(parts), re.I)
    unique = list(dict.fromkeys(found))
    return unique[0] if len(unique) == 1 else None


def apply_duration(subs: list[dict], turns: str | None) -> None:
    if not turns:
        return
    for sub in subs:
        if sub.get("duration_formula") or (sub.get("params") or {}).get("duration_formula"):
            sub["duration_formula"] = turns
            sub.setdefault("params", {})["duration_formula"] = turns


def learn_level(entry: dict) -> int:
    return int(entry.get("character_level") or 1)


def apply_withdrawal(subs: list[dict], row: dict) -> None:
    text = row.get("debuff") or ""
    pairs = re.findall(r"[−\-]\s*(\d+)\s*(PA|PM|PO)", text, re.I)
    if not pairs:
        return
    wanted = {stat.lower(): str(int(amount)) for amount, stat in pairs}
    for sub in subs:
        if sub.get("slug") != "retirer":
            continue
        stat = str((sub.get("params") or {}).get("characteristic") or "").lower()
        if stat in wanted:
            sub.setdefault("params", {})["value"] = wanted[stat]


def apply_move(subs: list[dict], row: dict) -> None:
    text = row.get("deplac") or ""
    kinds = {
        "push": re.findall(r"repousse de\s*(\d+)", text, re.I),
        "jump": re.findall(r"saut de\s*(\d+)", text, re.I),
        "pull": re.findall(r"attire de\s*(\d+)", text, re.I),
    }
    grouped: dict[str, list[dict]] = {}
    for sub in subs:
        if sub.get("slug") != "déplacer":
            continue
        kind = str((sub.get("params") or {}).get("movement_kind") or "")
        grouped.setdefault(kind, []).append(sub)
    for kind, amounts in kinds.items():
        if len(amounts) == 1 and len(grouped.get(kind, [])) == 1:
            grouped[kind][0].setdefault("params", {})["cells_formula"] = str(int(amounts[0]))


def paint_effects(subs: list[dict], row: dict) -> None:
    bump_dice(subs, row["_raw"].get("degat", ""), ("frapper", "soigner"))
    bump_dice(subs, row["_raw"].get("protect", ""), ("protéger", "soigner", "booster"))
    apply_zone(subs, zone_to_apply(row))
    apply_duration(subs, duration_n(row))
    apply_withdrawal(subs, row)
    apply_move(subs, row)


def apply_spell(entry: dict, spell: dict) -> str:
    rows = resolve_rows(spell["rows"])
    if not rows or rows[0]["niv"] != learn_level(entry):
        return "ignored"
    base = usage_dict(rows[0])
    for key, value in base.items():
        entry[key] = value
    paint_effects(entry.get("sub_effects") or [], rows[0])
    opening = duration_n(rows[0])
    if opening:
        entry["duration"] = "1 tour" if opening == "1" else f"{opening} tours"
    mode = resolution_mode(spell["resolution"])
    if mode:
        entry["resolution_mode"] = mode
    growth = rows[1:]
    old = list(entry.get("intensification") or [])
    paired: dict[int, dict] = {}
    if len(old) <= len(growth):
        start = len(growth) - len(old)
        for offset, tier in enumerate(old):
            paired[start + offset] = tier
    else:
        for offset, tier in enumerate(old[: len(growth)]):
            paired[offset] = tier
    previous = entry.get("sub_effects") or []
    built = []
    for index, row in enumerate(growth):
        source = paired.get(index)
        subs = deepcopy(source["sub_effects"] if source else previous)
        paint_effects(subs, row)
        tier = {
            "tier": ROMAN[index],
            "required_creature_level": int(row["niv"]),
            "effect": (source or {}).get("effect") or "",
            "sub_effects": subs,
        }
        for key, value in usage_dict(row).items():
            if base.get(key) != value:
                tier[key] = value
        built.append(tier)
        previous = subs
    entry["intensification"] = built
    return "updated"


def json_degrees(entry: dict) -> list[tuple[int, dict]]:
    degrees = [(learn_level(entry), {})]
    for tier in entry.get("intensification") or []:
        degrees.append((int(tier["required_creature_level"]), tier))
    return degrees


def effective(entry: dict, tier: dict) -> dict:
    subs = entry.get("sub_effects") or [{}]
    out = {
        "pa": str(entry.get("pa")),
        "po_min": str(entry.get("po_min")),
        "po_max": str(entry.get("po_max")),
        "po_editable": bool(entry.get("po_editable")),
        "sight_line": bool(entry.get("sight_line", True)),
        "cast_in_line": bool(entry.get("cast_in_line", False)),
        "cast_in_diagonal": bool(entry.get("cast_in_diagonal", False)),
        "cast_per_turn": str(entry.get("cast_per_turn")),
        "cast_per_target": str(entry.get("cast_per_target") if entry.get("cast_per_target") is not None else "0"),
        "number_between_two_cast": str(entry.get("number_between_two_cast") or "0"),
        "duration": entry.get("duration"),
        "resolution_mode": entry.get("resolution_mode"),
        "area": subs[0].get("area") if subs else None,
        "duration_formulas": sorted(
            {str(sub.get("duration_formula")) for sub in (entry.get("sub_effects") or []) if sub.get("duration_formula")}
        ),
    }
    if tier:
        for key in (
            "pa", "po_min", "po_max", "po_editable", "sight_line", "cast_in_line", "cast_in_diagonal",
            "cast_per_turn", "cast_per_target", "number_between_two_cast", "duration",
        ):
            if key in tier:
                out[key] = tier[key]
        tier_subs = tier.get("sub_effects") or []
        if tier_subs:
            out["area"] = tier_subs[0].get("area")
            out["duration_formulas"] = sorted(
                {str(sub.get("duration_formula")) for sub in tier_subs if sub.get("duration_formula")}
            )
    return out


def audit_spell(spell: dict, entry: dict | None) -> list[str]:
    if entry is None:
        return [f"absent du catalogue : {spell['name']}"]
    rows = resolve_rows(spell["rows"])
    lines = []
    if rows[0]["niv"] != learn_level(entry):
        lines.append(
            f"{spell['name']}: appris niv. {rows[0]['niv']} sur la fiche, niv. {learn_level(entry)} dans le catalogue — non recopié"
        )
        return lines
    degrees = dict(json_degrees(entry))
    sheet_levels = [row["niv"] for row in rows]
    if sheet_levels != list(degrees):
        lines.append(f"{spell['name']}: paliers fiche {sheet_levels} ≠ json {list(degrees)}")
    mode = resolution_mode(spell["resolution"])
    for row in rows:
        level = row["niv"]
        if level not in degrees:
            continue
        got = effective(entry, {} if level == learn_level(entry) else degrees[level])
        problems = []
        if str(to_int(row["pa"])) != str(got["pa"]):
            problems.append(f"PA {row['pa']} ≠ {got['pa']}")
        po = parse_po(row["po"])
        if po and po != ("?", "?"):
            if str(po[0]) != str(got["po_min"]) or str(po[1]) != str(got["po_max"]):
                problems.append(f"PO {po[0]}-{po[1]} ≠ {got['po_min']}-{got['po_max']}")
            if bool(got["po_editable"]) != (po[1] > 0):
                problems.append(f"PO modifiable {po[1] > 0} ≠ {got['po_editable']}")
        elif po == ("?", "?"):
            problems.append(f"PO illisible « {row['po']} »")
        los = los_flags(row["los"])
        if los["sight_line"] != bool(got["sight_line"]):
            problems.append(f"ligne de vue {los['sight_line']} ≠ {got['sight_line']}")
        if los["cast_in_line"] != bool(got["cast_in_line"]):
            problems.append(f"en ligne {los['cast_in_line']} ≠ {got['cast_in_line']}")
        if str(to_int(row["tour"])) != str(got["cast_per_turn"]):
            problems.append(f"/tour {row['tour']} ≠ {got['cast_per_turn']}")
        if str(to_int(row["cible"])) != str(got["cast_per_target"]):
            problems.append(f"/cible {row['cible']} ≠ {got['cast_per_target']}")
        if str(to_int(row["relance"], 0)) != str(int(float(got["number_between_two_cast"] or 0))):
            problems.append(f"relance {row['relance']} ≠ {got['number_between_two_cast']}")
        zone = canon_zone(row["zone"]) if (row.get("_raw") or {}).get("zone", "").strip() or row["zone"] else None
        raw_zone = (row.get("_raw") or {}).get("zone", "")
        inherited = canon_zone(row["zone"]) if not raw_zone.strip() and row["zone"] else None
        zone = canon_zone(raw_zone) if raw_zone.strip() else inherited
        if zone and zone != (got["area"] or "point"):
            problems.append(f"zone {zone} ≠ {got['area']}")
        if mode and level == sheet_levels[0] and mode != got["resolution_mode"]:
            problems.append(f"résolution {mode} ≠ {got['resolution_mode']}")
        turns = duration_n(row)
        if turns and turns not in set(got["duration_formulas"]) and turns not in str(got["duration"] or ""):
            problems.append(f"durée {turns} ≠ {got['duration_formulas'] or got['duration'] or '—'}")
        if problems:
            lines.append(f"{spell['name']} @{level}: " + " ; ".join(problems))
    return lines


def load_files(names: list[str]) -> list[tuple[str, dict]]:
    loaded = []
    for name in names:
        path = JSON_DIR / name
        loaded.append((name, json.loads(path.read_text())))
    return loaded


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--apply", action="store_true", help="Réécrit les catalogues dont le niveau d'apprentissage correspond")
    args = parser.parse_args()
    for sheet_name, json_names in FILES.items():
        spells = parse_sheet(SHEETS / sheet_name)
        payloads = load_files(json_names)
        by_name: dict[str, dict] = {}
        owners: dict[int, str] = {}
        for name, payload in payloads:
            for entry in payload["entries"]:
                by_name.setdefault(norm_name(entry["name"]), entry)
                owners[id(entry)] = name
        dirty: set[str] = set()
        print(f"\n######## {sheet_name} — {len(spells)} sorts")
        used = set()
        for spell in spells:
            key = norm_name(spell["name"])
            entry = by_name.get(key)
            if entry is not None and args.apply and sheet_name != "Cra.md":
                status = apply_spell(entry, spell)
                if status == "updated":
                    dirty.add(owners[id(entry)])
                    print(f"  réécrit : {spell['name']}")
            used.add(key)
            for line in audit_spell(spell, entry):
                print("  " + line)
        extras = [entry["name"] for key, entry in by_name.items() if key not in used]
        if extras:
            print("  JSON sans fiche : " + ", ".join(extras))
        if args.apply:
            for name, payload in payloads:
                if name not in dirty:
                    continue
                path = JSON_DIR / name
                path.write_text(json.dumps(payload, ensure_ascii=False, indent=4) + "\n")


if __name__ == "__main__":
    main()
