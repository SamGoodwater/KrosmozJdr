#!/usr/bin/env python3
"""
Enrichit characteristic_definitions_index.csv et les tableaux du To-do
à partir des agrégats DofusDB (storage/app/characteristics_*_samples.json).

Usage:
  python3 scripts/enrich-characteristic-dofusdb.py
  python3 scripts/enrich-characteristic-dofusdb.py --markdown-only
  python3 scripts/enrich-characteristic-dofusdb.py --csv-only
"""

from __future__ import annotations

import csv
import json
import re
import sys
from datetime import datetime, timezone
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SEED_DIRS = {
    "spell": ROOT / "database/seeders/data/characteristic-definitions/spell",
    "object": ROOT / "database/seeders/data/characteristic-definitions/object",
    "creature": ROOT / "database/seeders/data/characteristic-definitions/creature",
}
CREATURE_SAMPLES = ROOT / "storage/app/characteristics_creature_samples.json"
OBJECT_SAMPLES = ROOT / "storage/app/characteristics_object_samples.json"
DOFUS_OBJECT_MAP = ROOT / "resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json"
DOFUS_CREATURE_MAP = ROOT / "resources/scrapping/config/sources/dofusdb/dofusdb_monster_grade_to_creature.json"
CSV_OUT = ROOT / "docs/110- To Do/characteristic_definitions_index.csv"
MARKDOWN = ROOT / "docs/110- To Do/To do 1.3.1 vers 1.3.2.in_writing.md"
ENRICHMENT_JSON = ROOT / "docs/110- To Do/characteristic_dofusdb_enrichment.json"

REF_LEVELS = [1, 40, 80, 120, 160, 200]

# Clés d’agrégat (samples) → clés seeders actuelles
SAMPLE_TO_SEED_CREATURE: dict[str, str] = {
    "pa_creature": "action_points_creature",
    "pm_creature": "movement_points_creature",
    "po_creature": "range_creature",
    "life_creature": "life_points_creature",
    "strong_creature": "strength_creature",
    "intel_creature": "intelligence_creature",
    "agi_creature": "agility_creature",
    "sagesse_creature": "wisdom_creature",
    "dodge_pa_creature": "dodge_action_points_creature",
    "dodge_pm_creature": "dodge_movement_points_creature",
    "do_fixe_terre_creature": "fixed_damage_earth_creature",
    "do_fixe_feu_creature": "fixed_damage_fire_creature",
    "do_fixe_eau_creature": "fixed_damage_water_creature",
    "do_fixe_air_creature": "fixed_damage_air_creature",
    "do_fixe_neutre_creature": "fixed_damage_neutral_creature",
    "res_fixe_terre_creature": "fixed_resistance_earth_creature",
    "res_fixe_feu_creature": "fixed_resistance_fire_creature",
    "res_fixe_eau_creature": "fixed_resistance_water_creature",
    "res_fixe_air_creature": "fixed_resistance_air_creature",
    "res_fixe_neutre_creature": "fixed_resistance_neutral_creature",
    "tacle_creature": "tackle_creature",
    "fuite_creature": "dodge_creature",
    "ini_creature": "initiative_creature",
    "ca_creature": "armor_class_creature",
    "invocation_creature": "summoning_creature",
    "critical_hit_creature": "critical_hit_creature",
    "heal_bonus_creature": "heal_bonus_creature",
    "touch_creature": "hit_bonus_creature",
    "de_vie_creature": "life_dice_creature",
    "modificateur_agi_creature": "modifier_agility_creature",
    "modificateur_intel_creature": "modifier_intelligence_creature",
    "modificateur_force_creature": "modifier_strength_creature",
    "modificateur_vitality_creature": "modifier_vitality_creature",
    "modificateur_sagesse_creature": "modifier_wisdom_creature",
    "modificateur_chance_creature": "modifier_chance_creature",
    "save_agi_creature": "save_agility_creature",
    "save_intel_creature": "save_intelligence_creature",
    "save_force_creature": "save_strength_creature",
}

SAMPLE_TO_SEED_OBJECT: dict[str, str] = {
    "pa_object": "action_points_object",
    "pm_object": "movement_points_object",
    "agi_object": "agility_object",
    "strong_object": "strength_object",
    "intel_object": "intelligence_object",
    "sagesse_object": "wisdom_object",
    "pv_max_object": "life_points_max_object",
    "esquive_pa_object": "dodge_action_points_object",
    "esquive_pm_object": "dodge_movement_points_object",
    "ini_object": "initiative_object",
    "invocation_object": "summoning_object",
    "res_fixe_terre_object": "fixed_resistance_earth_object",
    "res_fixe_feu_object": "fixed_resistance_fire_object",
    "res_fixe_eau_object": "fixed_resistance_water_object",
    "res_fixe_air_object": "fixed_resistance_air_object",
    "res_fixe_neutre_object": "fixed_resistance_neutral_object",
    "do_fixe_terre_object": "fixed_damage_earth_object",
    "do_fixe_feu_object": "fixed_damage_fire_object",
    "do_fixe_eau_object": "fixed_damage_water_object",
    "do_fixe_air_object": "fixed_damage_air_object",
    "do_fixe_neutre_object": "fixed_damage_neutral_object",
    "do_fixe_multiple_object": "fixed_damage_multiple_object",
}


def spread_pct(values: dict[str, int | float]) -> float | None:
    if not values:
        return None
    try:
        v1 = float(values.get("1") or values.get(1))
        v200 = float(values.get("200") or values.get(200))
    except (TypeError, ValueError):
        return None
    if v1 <= 0:
        return None
    return round((v200 - v1) / v1 * 100, 1)


def global_min_max(full: dict[str, int | float]) -> tuple[int | None, int | None]:
    if not full:
        return None, None
    nums = [int(v) for v in full.values() if v is not None]
    return (min(nums), max(nums)) if nums else (None, None)


def format_anchors(ref: dict[str, int | float], prefix: str = "") -> str:
    parts = []
    for lvl in REF_LEVELS:
        v = ref.get(str(lvl)) if ref.get(str(lvl)) is not None else ref.get(lvl)
        if v is not None:
            parts.append(f"{lvl}→{int(v) if float(v).is_integer() else v}")
    return f"{prefix}{', '.join(parts)}" if parts else ""


def invert_creature_fields() -> dict[str, str]:
    data = json.loads(DOFUS_CREATURE_MAP.read_text(encoding="utf-8"))
    return {v: k for k, v in data.get("mapping", {}).items()}


def invert_object_ids() -> dict[str, str]:
    data = json.loads(DOFUS_OBJECT_MAP.read_text(encoding="utf-8"))
    return {v: f"characteristic_id={k}" for k, v in data.get("mapping", {}).items()}


def load_sample_block(block: dict) -> dict:
    full = block.get("conversion_dofus_sample") or {}
    ref = block.get("conversion_dofus_sample_reference") or full
    gmin, gmax = global_min_max(full)
    kref = block.get("conversion_krosmoz_sample_reference") or block.get("conversion_krosmoz_sample") or {}
    return {
        "dofus_global_min": gmin,
        "dofus_global_max": gmax,
        "dofus_anchors": format_anchors(ref),
        "dofus_spread_pct_1_200": spread_pct(ref),
        "krosmoz_anchors": format_anchors(kref, prefix="K: "),
        "item_count": block.get("item_count") or block.get("grade_count"),
        "krosmoz_rule": block.get("conversion_krosmoz_source_rule"),
    }


def build_enrichment() -> dict[str, dict]:
    out: dict[str, dict] = {}
    creature_fields = invert_creature_fields()
    object_ids = invert_object_ids()

    if CREATURE_SAMPLES.is_file():
        data = json.loads(CREATURE_SAMPLES.read_text(encoding="utf-8"))
        extracted = data.get("meta", {}).get("extracted_at", "")
        for sample_key, block in data.get("by_characteristic_key", {}).items():
            seed_key = SAMPLE_TO_SEED_CREATURE.get(sample_key, sample_key)
            info = load_sample_block(block)
            info.update(
                {
                    "dofus_source": "creature_monster_grades",
                    "dofus_field": creature_fields.get(sample_key, sample_key),
                    "dofus_extracted_at": extracted,
                    "sample_key": sample_key,
                }
            )
            out[seed_key] = info

    if OBJECT_SAMPLES.is_file():
        data = json.loads(OBJECT_SAMPLES.read_text(encoding="utf-8"))
        extracted = data.get("meta", {}).get("extracted_at", "")
        # by_characteristic_key (clés courtes dans le JSON d’agrégat)
        official_map = json.loads(DOFUS_OBJECT_MAP.read_text(encoding="utf-8")).get("mapping", {})

        for sample_key, block in data.get("by_characteristic_key", {}).items():
            seed_key = SAMPLE_TO_SEED_OBJECT.get(sample_key, sample_key)
            if seed_key in out:
                continue
            info = load_sample_block(block)
            info.update(
                {
                    "dofus_source": "object_equipment_effects",
                    "dofus_field": object_ids.get(seed_key, object_ids.get(sample_key, sample_key)),
                    "dofus_extracted_at": extracted,
                    "sample_key": sample_key,
                }
            )
            out[seed_key] = info
        # Compléter via mapping officiel seed (dofusdb_characteristic_to_krosmoz)
        id_to_sample = data.get("dofusdb_characteristic_id_to_characteristic_key", {})
        for eid, sample_key in id_to_sample.items():
            official = official_map.get(str(eid))
            seed_key = official or SAMPLE_TO_SEED_OBJECT.get(sample_key, sample_key)
            if seed_key in out:
                continue
            block = data.get("by_effect_id", {}).get(str(eid))
            if not block:
                continue
            info = load_sample_block(block)
            info.update(
                {
                    "dofus_source": "object_equipment_effects",
                    "dofus_field": f"characteristic_id={eid}",
                    "dofus_extracted_at": extracted,
                    "sample_key": sample_key,
                }
            )
            out[seed_key] = info

    return out


def semantics_text(e: dict, group: str) -> str:
    src = "grades monstres" if e["dofus_source"] == "creature_monster_grades" else "effets équipement"
    count = e.get("item_count")
    n = f" (n≈{count})" if count else ""
    gmin, gmax = e.get("dofus_global_min"), e.get("dofus_global_max")
    spread = e.get("dofus_spread_pct_1_200")
    parts = [
        f"Agrégat DofusDB ({src}{n}, {e.get('dofus_field', '?')})",
        f"obs. {gmin}–{gmax}" if gmin is not None and gmax is not None else None,
    ]
    if spread is not None:
        parts.append(f"écart 1→200: {spread}%")
    if group == "creature":
        parts.append("→ `d` au grade")
    else:
        parts.append("→ `d` sur l’item")
    return ". ".join(p for p in parts if p) + "."


def anchors_text(e: dict) -> str:
    """Séparateur · (pas |) pour ne pas casser les colonnes Markdown."""
    parts = []
    if e.get("dofus_anchors"):
        parts.append(f"Dofus: {e['dofus_anchors']}")
    if e.get("krosmoz_anchors"):
        parts.append(e["krosmoz_anchors"].strip())
    if e.get("krosmoz_rule"):
        parts.append(f"({e['krosmoz_rule']})")
    return " · ".join(parts) if parts else ""


def load_seeds() -> list[dict]:
    rows = []
    for group, directory in SEED_DIRS.items():
        for path in sorted(directory.glob("*.json")):
            data = json.loads(path.read_text(encoding="utf-8"))
            char = data.get("characteristic", {})
            ent = data.get("entities", {}).get("*", data.get("entities", {}).get("monster", {}))
            if not ent and isinstance(data.get("entities"), dict):
                ent = next(iter(data["entities"].values()), {})
            formula = (ent.get("conversion_formula") or "")[:80]
            rows.append(
                {
                    "group": group,
                    "seed_file": f"{group}/{path.name}",
                    "characteristic_key": char.get("key", ""),
                    "name_fr": char.get("name", ""),
                    "type": char.get("type", ""),
                    "db_column": ent.get("db_column") or "",
                    "min": ent.get("min", ""),
                    "max": ent.get("max", ""),
                    "conversion_function": ent.get("conversion_function") or "",
                    "conversion_formula_trunc": formula.replace("\n", " "),
                    "has_norms_grid": 1 if ent.get("norms_grid") else 0,
                }
            )
    return rows


def write_csv(rows: list[dict], enrichment: dict[str, dict]) -> None:
    fieldnames = [
        "group",
        "seed_file",
        "characteristic_key",
        "name_fr",
        "type",
        "db_column",
        "min",
        "max",
        "conversion_function",
        "conversion_formula_trunc",
        "has_norms_grid",
        "dofus_source",
        "dofus_field",
        "dofus_global_min",
        "dofus_global_max",
        "dofus_anchors",
        "dofus_spread_pct_1_200",
        "krosmoz_anchors",
        "dofus_extracted_at",
    ]
    with CSV_OUT.open("w", encoding="utf-8", newline="") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        for row in rows:
            e = enrichment.get(row["characteristic_key"], {})
            out = {**row}
            for k in fieldnames:
                if k not in out:
                    out[k] = e.get(k, "") if k in e else ""
            if e:
                out["dofus_source"] = e.get("dofus_source", "")
                out["dofus_field"] = e.get("dofus_field", "")
                out["dofus_global_min"] = e.get("dofus_global_min", "")
                out["dofus_global_max"] = e.get("dofus_global_max", "")
                out["dofus_anchors"] = e.get("dofus_anchors", "")
                out["dofus_spread_pct_1_200"] = e.get("dofus_spread_pct_1_200", "")
                out["krosmoz_anchors"] = e.get("krosmoz_anchors", "")
                out["dofus_extracted_at"] = e.get("dofus_extracted_at", "")
            writer.writerow(out)


def famille_for_key(key: str, group: str) -> str:
    if group == "object":
        if key.endswith("_passive_object"):
            return "B"
        return "B"
    if "_mastery_" in key or key.endswith("_mastery_creature"):
        return "A/B"
    if "_passive_" in key or key.endswith("_passive_creature"):
        return "A/B"
    return "A"


def build_markdown_row(
    num: int,
    row: dict,
    group: str,
    enrichment: dict[str, dict],
) -> str:
    key = row["characteristic_key"]
    e = enrichment.get(key)
    db = row["db_column"] or "—"
    minmax = f"{row['min']} / {row['max']}" if row["min"] != "" or row["max"] != "" else "—"
    conv = row["conversion_formula_trunc"] or "—"
    fn = row["conversion_function"] or "—"
    norms = "grille 5×20" if str(row.get("has_norms_grid")) == "1" else "—"
    regulators = "norms_conditions dans seed"
    if group == "creature":
        sem_default = "Stat monstre DofusDB → `d` (+ niveau si formule)"
        cas = "d=0 ; formules liées"
        doc = "doc 400 ; creature"
        anc_default = "conversion_*_sample dans seed"
    else:
        sem_default = "Bonus item DofusDB → `d`"
        cas = "d=0 ; rareté / FM"
        doc = "doc 400 ; PROPRIETES_CONVERSION"
        anc_default = "conversion_*_sample dans seed"

    sem = semantics_text(e, group) if e else sem_default
    anc = anchors_text(e) if e else anc_default
    seed_path = f"../../database/seeders/data/characteristic-definitions/{row['seed_file']}"
    statut = "aligné dépôt"

    parts = [
        str(num),
        row["name_fr"],
        key,
        famille_for_key(key, group),
        row["type"] or "int",
        db,
        sem,
        minmax,
        conv,
        fn,
        anc,
        norms,
        regulators,
        cas,
        doc,
        seed_path,
        statut,
    ]
    return "| " + " | ".join(parts) + " |"


def rebuild_markdown_tables(rows: list[dict], enrichment: dict[str, dict]) -> None:
    """Reconstruit les tableaux créature / objet (17 colonnes garanties)."""
    text = MARKDOWN.read_text(encoding="utf-8")
    lines = text.splitlines()
    header = (
        "| N° | Libellé (FR) | Clé BDD | Famille | Type | Colonne SQL | Sémantique Dofus & d | "
        "Min/max | Conversion (extrait) | conversion_function | Ancres/échantillons | Normes | "
        "Régulateurs | Cas limites | Doc réf. | Fichier seed | Statut |"
    )
    sep = (
        "| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | "
        "--- | --- | --- | --- |"
    )

    creature_rows = [r for r in rows if r["group"] == "creature"]
    object_rows = [r for r in rows if r["group"] == "object"]

    def replace_section(start_marker: str, end_marker: str, body_lines: list[str]) -> None:
        nonlocal lines
        start = next(i for i, l in enumerate(lines) if l.startswith(start_marker))
        header_i = next(i for i in range(start, len(lines)) if lines[i] == header)
        sep_i = header_i + 1
        end = next(i for i in range(sep_i + 1, len(lines)) if lines[i].startswith(end_marker))
        lines = lines[: sep_i + 1] + body_lines + lines[end:]

    creature_body = [
        build_markdown_row(n, r, "creature", enrichment) for n, r in enumerate(creature_rows, 1)
    ]
    object_body = [build_markdown_row(n, r, "object", enrichment) for n, r in enumerate(object_rows, 1)]

    replace_section("## Groupes objets", "## Groupes monsters", object_body)
    replace_section("## Groupes monsters", "# ", creature_body)

    MARKDOWN.write_text("\n".join(lines), encoding="utf-8")
    enriched_creature = sum(1 for r in creature_rows if r["characteristic_key"] in enrichment)
    enriched_object = sum(1 for r in object_rows if r["characteristic_key"] in enrichment)
    print(
        f"Markdown: {len(creature_body)} lignes créature ({enriched_creature} avec DofusDB), "
        f"{len(object_body)} lignes objet ({enriched_object} avec DofusDB)."
    )


def update_markdown(enrichment: dict[str, dict], rows: list[dict]) -> None:
    rebuild_markdown_tables(rows, enrichment)


def patch_markdown_intro(text: str) -> str:
    """Ajoute la doc des colonnes DofusDB si absente."""
    marker = "### Données DofusDB empiriques (agrégats)"
    if marker in text:
        return text

    block = f"""
{marker}

Colonnes ajoutées au **CSV** (`characteristic_definitions_index.csv`) et texte injecté dans **Sémantique Dofus & d** / **Ancres/échantillons** pour les lignes créature et objet disposant d’un agrégat :

| Colonne CSV | Signification |
| --- | --- |
| `dofus_source` | `creature_monster_grades` (≈2500 monstres, tous les grades) ou `object_equipment_effects` (équipements) |
| `dofus_field` | Champ grade Dofus (`lifePoints`, …) ou `characteristic_id=N` (objets) |
| `dofus_global_min` / `dofus_global_max` | Min / max sur toutes les tranches de niveau de l’agrégat |
| `dofus_anchors` | Médianes aux niveaux 1, 40, 80, 120, 160, 200 |
| `dofus_spread_pct_1_200` | `(valeur@200 − valeur@1) / valeur@1 × 100` — indicatif pour la règle des dés |
| `krosmoz_anchors` | Cibles JDR quand présentes dans l’agrégat (règles 2.2.x) |
| `dofus_extracted_at` | Date du fichier `storage/app/characteristics_*_samples.json` |

**Régénération** : `python3 scripts/enrich-characteristic-dofusdb.py` (après mise à jour des JSON d’agrégat ou des seeders).

**Limite** : ordres de grandeur **indicatifs** ; la validation métier reste manuelle. Sorts : pas d’agrégat automatique dans cette passe.

"""
    insert_after = "## CSV vs JSON — rôles différents"
    if insert_after in text:
        return text.replace(insert_after, block + "\n" + insert_after, 1)
    return text + block


def main() -> int:
    csv_only = "--csv-only" in sys.argv
    md_only = "--markdown-only" in sys.argv

    enrichment = build_enrichment()
    ENRICHMENT_JSON.write_text(
        json.dumps(
            {
                "generated_at": datetime.now(timezone.utc).isoformat(),
                "creature_samples": str(CREATURE_SAMPLES),
                "object_samples": str(OBJECT_SAMPLES),
                "keys_enriched": sorted(enrichment.keys()),
                "by_characteristic_key": enrichment,
            },
            ensure_ascii=False,
            indent=2,
        ),
        encoding="utf-8",
    )
    print(f"Enrichment: {len(enrichment)} clés → {ENRICHMENT_JSON.relative_to(ROOT)}")

    rows = load_seeds()

    if not md_only:
        write_csv(rows, enrichment)
        print(f"CSV: {len(rows)} lignes → {CSV_OUT.relative_to(ROOT)}")

    if not csv_only:
        md = MARKDOWN.read_text(encoding="utf-8")
        md = patch_markdown_intro(md)
        MARKDOWN.write_text(md, encoding="utf-8")
        update_markdown(enrichment, rows)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
