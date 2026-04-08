/**
 * Icônes / couleurs des champs sort depuis le store Inertia `characteristics`
 * (même source que {@link CharacteristicMetaByDbColumnService} côté API).
 */

import { getByDbColumn } from "@/Composables/store/useCharacteristicsStore";
import { hexToRgba } from "@/Utils/color/Color";

/** @readonly */
const SPELL_GROUPS = Object.freeze(["spell", "capability"]);

/**
 * @param {string} dbColumn
 * @returns {object|null}
 */
function pickSpellCharacteristicDef(dbColumn) {
    const key = String(dbColumn || "").trim();
    if (!key) return null;
    for (const g of SPELL_GROUPS) {
        const d = getByDbColumn(g, key);
        if (d && typeof d === "object") return d;
    }
    return null;
}

/**
 * Icône « physique » à partir de celle du magique (chemins déjà normalisés côté API).
 *
 * @param {string|null|undefined} magicIcon
 * @returns {string}
 */
function deriveSpellPhysicIconPath(magicIcon) {
    const m = typeof magicIcon === "string" ? magicIcon.trim() : "";
    if (!m) {
        return "icons/caracteristics/spellPhysic.webp";
    }
    if (/spellphysic/i.test(m)) {
        return m;
    }
    if (/spellMagic\.webp/i.test(m)) {
        return m.replace(/spellMagic\.webp/gi, "spellPhysic.webp");
    }
    if (/spell_wakfu\.webp/i.test(m)) {
        return m.replace(/spell_wakfu\.webp/gi, "spellPhysic.webp");
    }
    const slash = m.lastIndexOf("/");
    if (slash !== -1) {
        return `${m.slice(0, slash + 1)}spellPhysic.webp`;
    }
    return "icons/caracteristics/spellPhysic.webp";
}

/**
 * Icône « pas de rituel » à partir de celle du rituel (chemins API).
 *
 * @param {string|null|undefined} ritualIcon
 * @returns {string}
 */
function deriveNotRitualIconPath(ritualIcon) {
    const r = typeof ritualIcon === "string" ? ritualIcon.trim() : "";
    if (!r) {
        return "icons/caracteristics/notRitual.webp";
    }
    if (/notritual/i.test(r)) {
        return r;
    }
    if (/\/ritual\.webp$/i.test(r)) {
        return r.replace(/\/ritual\.webp$/i, "/notRitual.webp");
    }
    const slash = r.lastIndexOf("/");
    if (slash !== -1) {
        return `${r.slice(0, slash + 1)}notRitual.webp`;
    }
    return "icons/caracteristics/notRitual.webp";
}

/**
 * Icône « pas de ligne de vue » à partir de celle avec ligne de vue.
 *
 * @param {string|null|undefined} sightIcon
 * @returns {string}
 */
function deriveNoSightLineIconPath(sightIcon) {
    const s = typeof sightIcon === "string" ? sightIcon.trim() : "";
    if (!s) {
        return "icons/caracteristics/noSightLine.webp";
    }
    if (/nosightline/i.test(s)) {
        return s;
    }
    if (/\/sightLine\.webp$/i.test(s)) {
        return s.replace(/\/sightLine\.webp$/i, "/noSightLine.webp");
    }
    const slash = s.lastIndexOf("/");
    if (slash !== -1) {
        return `${s.slice(0, slash + 1)}noSightLine.webp`;
    }
    return "icons/caracteristics/noSightLine.webp";
}

/**
 * Icône « portée non modifiable » à partir de celle « portée modifiable ».
 *
 * @param {string|null|undefined} rangeEditableIcon
 * @returns {string}
 */
function deriveNoPoEditableIconPath(rangeEditableIcon) {
    const s = typeof rangeEditableIcon === "string" ? rangeEditableIcon.trim() : "";
    if (!s) {
        return "icons/caracteristics/noPoEditable.webp";
    }
    if (/nopoeditable/i.test(s)) {
        return s;
    }
    if (/\/rangeEditable\.webp$/i.test(s)) {
        return s.replace(/\/rangeEditable\.webp$/i, "/noPoEditable.webp");
    }
    if (/\/range\.webp$/i.test(s)) {
        return s.replace(/\/range\.webp$/i, "/noPoEditable.webp");
    }
    const slash = s.lastIndexOf("/");
    if (slash !== -1) {
        return `${s.slice(0, slash + 1)}noPoEditable.webp`;
    }
    return "icons/caracteristics/noPoEditable.webp";
}

/**
 * @param {string|null|undefined} color
 * @returns {Record<string, string>}
 */
export function spellUsageTextColorStyle(color) {
    if (!color || typeof color !== "string") return {};
    return { color };
}

/**
 * Fond discret derrière une petite icône (teinte caractéristique).
 *
 * @param {string|null|undefined} color
 * @returns {Record<string, string>}
 */
export function spellUsageIconBackdropStyle(color) {
    if (!color || typeof color !== "string" || !color.startsWith("#")) return {};
    const bg = hexToRgba(color, 0.14);
    if (!bg) return {};
    return {
        backgroundColor: bg,
        borderRadius: "0.25rem",
    };
}

/**
 * Fond léger + bordure gauche pour panneaux tooltip (caractéristiques sort).
 *
 * @param {string|null|undefined} color
 * @returns {Record<string, string>}
 */
export function spellUsageTooltipPanelStyle(color) {
    if (!color || typeof color !== "string" || !color.startsWith("#")) {
        return {};
    }
    const bg = hexToRgba(color, 0.1);
    if (!bg) return {};
    return {
        borderLeftColor: color,
        backgroundColor: bg,
    };
}

/**
 * Résout icône + couleur pour une colonne sort (`db_column` dans characteristic_spell).
 * Pour les booléens, passez la valeur pour appliquer `icon_false` si présent en BDD.
 *
 * @param {string} dbColumn - ex. `po_editable`, `cast_per_turn`
 * @param {boolean} [booleanValue] - si défini, bascule icône true/false quand `icon_false` existe
 * @returns {{
 *   source: string,
 *   color: string,
 *   hasIcon: boolean,
 *   hasDistinctFalseIcon: boolean,
 *   characteristicName: string,
 *   characteristicHelper: string
 * }}
 */
export function resolveSpellUsageCharacteristicVisual(dbColumn, booleanValue) {
    const def = pickSpellCharacteristicDef(dbColumn);
    if (!def) {
        return {
            source: "",
            color: "",
            hasIcon: false,
            hasDistinctFalseIcon: false,
            characteristicName: "",
            characteristicHelper: "",
        };
    }
    const falseIconRaw = def.icon_false;
    const falseIconStr = String(falseIconRaw ?? "").trim();
    const hasStoredFalseIcon = falseIconStr !== "";
    let hasDistinctFalseIcon = hasStoredFalseIcon;
    let icon = def.icon;

    if (dbColumn === "is_magic" && typeof booleanValue === "boolean" && booleanValue === false) {
        const falseIsSpellPhysic = /spellphysic/i.test(falseIconStr);
        if (hasStoredFalseIcon && falseIsSpellPhysic) {
            icon = falseIconStr;
        } else {
            icon = deriveSpellPhysicIconPath(def.icon);
            hasDistinctFalseIcon = true;
        }
    } else if (
        dbColumn === "ritual_available" &&
        typeof booleanValue === "boolean" &&
        booleanValue === false
    ) {
        const falseIsNotRitual = /notritual/i.test(falseIconStr);
        if (hasStoredFalseIcon && falseIsNotRitual) {
            icon = falseIconStr;
        } else {
            icon = deriveNotRitualIconPath(def.icon);
            hasDistinctFalseIcon = true;
        }
    } else if (
        dbColumn === "sight_line" &&
        typeof booleanValue === "boolean" &&
        booleanValue === false
    ) {
        const falseIsNoSight = /nosightline/i.test(falseIconStr);
        if (hasStoredFalseIcon && falseIsNoSight) {
            icon = falseIconStr;
        } else {
            icon = deriveNoSightLineIconPath(def.icon);
            hasDistinctFalseIcon = true;
        }
    } else if (
        dbColumn === "po_editable" &&
        typeof booleanValue === "boolean" &&
        booleanValue === false
    ) {
        const falseIsNoPo = /nopoeditable/i.test(falseIconStr);
        if (hasStoredFalseIcon && falseIsNoPo) {
            icon = falseIconStr;
        } else {
            icon = deriveNoPoEditableIconPath(def.icon);
            hasDistinctFalseIcon = true;
        }
    } else if (typeof booleanValue === "boolean" && hasStoredFalseIcon) {
        icon = booleanValue ? def.icon : def.icon_false;
    }

    let color = def.color ?? "";
    const colorFalseRaw = def.color_false;
    const colorFalseHex =
        typeof colorFalseRaw === "string" && colorFalseRaw.trim().startsWith("#")
            ? colorFalseRaw.trim()
            : "";
    if (typeof booleanValue === "boolean" && booleanValue === false && colorFalseHex) {
        color = colorFalseHex;
    } else if (
        dbColumn === "is_magic" &&
        typeof booleanValue === "boolean" &&
        booleanValue === false &&
        !colorFalseHex
    ) {
        color = "#ff8a65";
    } else if (
        dbColumn === "ritual_available" &&
        typeof booleanValue === "boolean" &&
        booleanValue === false &&
        !colorFalseHex
    ) {
        color = "#78909c";
    }
    const source = typeof icon === "string" ? icon.trim() : "";
    const helperRaw =
        typeof def.helper === "string" && def.helper.trim() !== ""
            ? def.helper.trim()
            : typeof def.descriptions === "string"
              ? def.descriptions.trim()
              : "";
    return {
        source,
        color: typeof color === "string" ? color : "",
        hasIcon: source.length > 0 && !source.startsWith("fa-"),
        hasDistinctFalseIcon,
        characteristicName: typeof def.name === "string" ? def.name : "",
        characteristicHelper: helperRaw,
    };
}
