/**
 * Affichage compact (vues Minimal / Line) : portée, lancers, résolution.
 *
 * @description
 * Les icônes / couleurs « utilisation » viennent du store `characteristics` (voir
 * {@link resolveSpellUsageCharacteristicVisual}) — pas de chemins codés en dur.
 */

import { SAVE_ABILITY_OPTIONS } from "@/Entities/spell/spell-descriptors";
import { resolveSpellUsageCharacteristicVisual } from "@/Utils/Entity/spellUsageCharacteristicVisual";

/**
 * Suffixe « délai entre deux lancers » : dès qu’on a des lancers/tour, on indique l’échelle en tours
 * (y compris délai 0, 1 ou non renseigné → « / tour »).
 *
 * @param {number|null} t
 * @returns {{ showNumeric: boolean, tourWord: string, numeric: number|null }}
 */
function cooldownTourPresentation(t) {
    if (t !== null && t >= 2) {
        return { showNumeric: true, tourWord: "tours", numeric: t };
    }
    return { showNumeric: false, tourWord: "tour", numeric: null };
}

/**
 * @param {object|null|undefined} entity
 * @param {string} camelKey
 * @param {string} snakeKey
 * @returns {unknown}
 */
export function readSpellField(entity, camelKey, snakeKey) {
    if (entity == null) return undefined;
    const c = entity[camelKey];
    if (c !== undefined) return c;
    if (entity._data && entity._data[snakeKey] !== undefined) {
        return entity._data[snakeKey];
    }
    if (entity[snakeKey] !== undefined) return entity[snakeKey];
    return undefined;
}

/**
 * @param {unknown} raw
 * @returns {number|null}
 */
function parseNonNegativeInt(raw) {
    if (raw == null || raw === "") return null;
    const n = Number.parseInt(String(raw), 10);
    if (!Number.isFinite(n) || n < 0) return null;
    return n;
}

/**
 * @param {object|null|undefined} entity
 * @returns {{ show: boolean, text: string, tooltip: string }}
 */
export function buildCastUsageSummary(entity) {
    const nRaw = readSpellField(entity, "castPerTurn", "cast_per_turn");
    const cRaw = readSpellField(entity, "castPerTarget", "cast_per_target");
    const tRaw = readSpellField(entity, "numberBetweenTwoCast", "number_between_two_cast");
    const n = parseNonNegativeInt(nRaw);
    const c = parseNonNegativeInt(cRaw);
    const t = parseNonNegativeInt(tRaw);
    if (n === null) {
        return { show: false, text: "", tooltip: "" };
    }
    const lancerWord = n === 1 ? "lancer" : "lancers";
    let text = `${n} ${lancerWord}`;
    const showC = c !== null && c > 0 && c !== n;
    if (showC) {
        text += ` (max cible ${c})`;
    }
    const cool = cooldownTourPresentation(t);
    text += cool.showNumeric ? ` / ${cool.numeric} ${cool.tourWord}` : " / tour";
    const cLabel = c === null ? "—" : String(c);
    const tLabel = t === null ? "—" : String(t);
    const tooltip = [
        `Lancers par tour : ${n}`,
        `Lancers par cible : ${cLabel}`,
        `Délai entre deux lancers (tours) : ${tLabel}`,
    ].join("\n");
    return { show: true, text, tooltip };
}

/**
 * Données pour afficher les lancers avec icônes / couleurs BDD.
 *
 * @param {object|null|undefined} entity
 * @returns {{
 *   show: boolean,
 *   text: string,
 *   tooltip: string,
 *   n: number|null,
 *   c: number|null,
 *   t: number|null,
 *   showPerTarget: boolean,
 *   showCooldownSegment: boolean,
 *   cooldownShowNumeric: boolean,
 *   lancerWord: string,
 *   cooldownTourWord: string,
 *   metas: { perTurn: object, perTarget: object, cooldown: object }|null
 * }}
 */
export function buildSpellCastUsagePresentation(entity) {
    const nRaw = readSpellField(entity, "castPerTurn", "cast_per_turn");
    const cRaw = readSpellField(entity, "castPerTarget", "cast_per_target");
    const tRaw = readSpellField(entity, "numberBetweenTwoCast", "number_between_two_cast");
    const n = parseNonNegativeInt(nRaw);
    const c = parseNonNegativeInt(cRaw);
    const t = parseNonNegativeInt(tRaw);
    if (n === null) {
        return {
            show: false,
            text: "",
            tooltip: "",
            n: null,
            c: null,
            t: null,
            showPerTarget: false,
            showCooldownSegment: false,
            cooldownShowNumeric: false,
            lancerWord: "",
            cooldownTourWord: "",
            metas: null,
        };
    }
    const lancerWord = n === 1 ? "lancer" : "lancers";
    let text = `${n} ${lancerWord}`;
    const showPerTarget = c !== null && c > 0 && c !== n;
    if (showPerTarget) {
        text += ` (max cible ${c})`;
    }
    const cool = cooldownTourPresentation(t);
    const showCooldownSegment = true;
    const cooldownShowNumeric = cool.showNumeric;
    if (cooldownShowNumeric) {
        text += ` / ${cool.numeric} ${cool.tourWord}`;
    } else {
        text += " / tour";
    }
    const cLabel = c === null ? "—" : String(c);
    const tLabel = t === null ? "—" : String(t);
    const tooltip = [
        `Lancers par tour : ${n}`,
        `Lancers par cible : ${cLabel}`,
        `Délai entre deux lancers (tours) : ${tLabel}`,
    ].join("\n");
    return {
        show: true,
        text,
        tooltip,
        n,
        c,
        t,
        showPerTarget,
        showCooldownSegment,
        cooldownShowNumeric,
        lancerWord,
        cooldownTourWord: cool.tourWord,
        metas: {
            perTurn: resolveSpellUsageCharacteristicVisual("cast_per_turn"),
            perTarget: resolveSpellUsageCharacteristicVisual("cast_per_target"),
            cooldown: resolveSpellUsageCharacteristicVisual("number_between_two_cast"),
        },
    };
}

/**
 * Incantation (texte) + indicateur rituel pour les vues compactes.
 *
 * @param {object|null|undefined} entity
 * @returns {object}
 */
export function buildSpellCastingRitualPresentation(entity) {
    const ctRaw = readSpellField(entity, "castingTime", "casting_time");
    const ctStr =
        ctRaw != null && String(ctRaw).trim() !== "" ? String(ctRaw).trim() : "";

    const ritualRaw = readSpellField(entity, "ritualAvailable", "ritual_available");
    const isRitualLegacy = readSpellField(entity, "isRitual", "is_ritual");

    const ritualDefined = ritualRaw !== null && ritualRaw !== undefined;
    const ritualOn = ritualDefined ? Boolean(ritualRaw) : Boolean(isRitualLegacy);

    const showCasting = ctStr.length > 0;
    /** N’afficher le rituel en UI que lorsqu’il est disponible (pas d’icône « non rituel »). */
    const showRitual = ritualOn === true;

    if (!showCasting && !showRitual) {
        return {
            show: false,
            showCasting: false,
            castingText: "",
            castingVisual: resolveSpellUsageCharacteristicVisual("casting_time"),
            showRitual: false,
            ritualOn: false,
            ritualVisual: null,
            tooltip: "",
        };
    }

    const castingVisual = resolveSpellUsageCharacteristicVisual("casting_time");
    const ritualVisual = showRitual
        ? resolveSpellUsageCharacteristicVisual("ritual_available", true)
        : null;

    const lines = [];
    if (showCasting) {
        lines.push(`Temps d'incantation : ${ctStr}`);
    }
    if (showRitual) {
        lines.push("Rituel disponible");
    }

    return {
        show: true,
        showCasting,
        castingText: ctStr,
        castingVisual,
        showRitual,
        ritualOn,
        ritualVisual,
        tooltip: lines.join("\n"),
    };
}

/**
 * @param {string|null|undefined} key
 * @returns {string|null}
 */
function labelForCharacteristicKey(key) {
    if (!key) return null;
    const opts = SAVE_ABILITY_OPTIONS();
    const hit = opts.find((o) => o.value === key);
    return hit?.label ?? String(key);
}

/**
 * @param {object|null|undefined} entity
 * @returns {{ show: boolean, text: string }}
 */
export function buildResolutionSummary(entity) {
    const mode = readSpellField(entity, "resolutionMode", "resolution_mode") || "attack_roll";
    const willing = Boolean(
        readSpellField(entity, "autoSuccessIfWillingTarget", "auto_success_if_willing_target"),
    );
    let text = "";
    if (mode === "auto_success") {
        text = "Réussite automatique";
    } else if (mode === "saving_throw") {
        const k = readSpellField(entity, "saveCharacteristicKey", "save_characteristic_key");
        const carac = labelForCharacteristicKey(k);
        const dd = readSpellField(entity, "saveDcFormula", "save_dc_formula");
        text = carac ? `Jet de sauvegarde (${carac})` : "Jet de sauvegarde";
        if (dd) text += ` · DD ${dd}`;
    } else {
        const k = readSpellField(entity, "attackCharacteristicKey", "attack_characteristic_key");
        const carac = labelForCharacteristicKey(k);
        text = carac ? `Jet d'attaque vs CA (${carac})` : "Jet d'attaque vs CA";
    }
    if (willing && mode !== "auto_success") {
        text += " · réussite auto si consentement";
    }
    return { show: Boolean(text), text };
}
