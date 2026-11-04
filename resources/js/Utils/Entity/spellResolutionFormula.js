/**
 * Formule publique d'un jet de sort (attaque, DD, sauvegarde, esquive PA/PM).
 *
 * Les termes sont des clés de caractéristiques créature : le survol ouvre
 * leur fiche, y compris pour un visiteur sans personnage.
 *
 * Un sort peut demander plusieurs jets (`lines`) : sur une touche, le même total
 * vs CA et vs Esquive PA/PM ; sur une sauvegarde, un jet d'attaque séparé vs Esquive
 * pour les retraits. La réussite automatique applique tout sans jet.
 *
 * @example
 * buildSpellResolutionFormula({ resolution_mode: "attack_roll", attack_characteristic_key: "agi" })
 */

import { readSpellField } from "@/Utils/Entity/spellMinimalUsageDisplay";
import { ELEMENT_PRIMARY_LABELS } from "@/Utils/Entity/Elements";
import { detectSpellDodgeChecks, SPELL_DODGE_TARGETS } from "@/Utils/Entity/spellDodgeChecks";

/**
 * Clé de caractéristique de sort → indice primaire d’élément (Elements.js).
 * @type {Readonly<Record<string, number>>}
 */
export const ABILITY_TO_ELEMENT_PRIMARY = Object.freeze({
    agi: 3,
    strong: 1,
    intel: 2,
    chance: 4,
    vitality: 6,
    sagesse: 5,
});

/** @type {Readonly<Record<string, { mod: string, save: string, dc: string }>>} */
const ABILITIES = Object.freeze({
    agi: {
        mod: "modifier_agility_creature",
        save: "save_agility_creature",
        dc: "save_dc_agility_creature",
    },
    strong: {
        mod: "modifier_strength_creature",
        save: "save_strength_creature",
        dc: "save_dc_strength_creature",
    },
    intel: {
        mod: "modifier_intelligence_creature",
        save: "save_intelligence_creature",
        dc: "save_dc_intelligence_creature",
    },
    chance: {
        mod: "modifier_chance_creature",
        save: "save_chance_creature",
        dc: "save_dc_chance_creature",
    },
    vitality: {
        mod: "modifier_vitality_creature",
        save: "save_vitality_creature",
        dc: "save_dc_vitality_creature",
    },
    sagesse: {
        mod: "modifier_wisdom_creature",
        save: "save_wisdom_creature",
        dc: "save_dc_wisdom_creature",
    },
});

const HIT_BONUS_KEY = "hit_bonus_creature";

/**
 * @typedef {{ type: "text", text: string }|{ type: "chip", key: string }} FormulaPart
 * @typedef {{ text: string, colored?: boolean }} HeadlinePart
 * @typedef {{
 *   kind: "attack"|"save"|"auto",
 *   elementLabel: string|null,
 *   elementPrimary: number|null,
 *   leftHeadline: HeadlinePart[],
 *   rightHeadline: HeadlinePart[],
 *   leftParts: FormulaPart[],
 *   rightParts: FormulaPart[],
 *   valueKeys: string[],
 *   headline: string,
 * }} ResolutionLine
 */

/**
 * @param {string|null|undefined} abilityKey
 * @returns {{ primary: number|null, label: string|null }}
 */
export function resolveSpellElementFromAbility(abilityKey) {
    const key = abilityKey != null ? String(abilityKey).trim() : "";
    if (!key || !(key in ABILITY_TO_ELEMENT_PRIMARY)) {
        return { primary: null, label: null };
    }
    const primary = ABILITY_TO_ELEMENT_PRIMARY[key];
    return { primary, label: ELEMENT_PRIMARY_LABELS[primary] ?? null };
}

/**
 * @param {Omit<ResolutionLine, "headline">} line
 * @returns {ResolutionLine}
 */
function withHeadline(line) {
    return { ...line, headline: joinHeadline(line.leftHeadline, line.rightHeadline) };
}

/**
 * @param {string} label
 * @param {string|null} elementLabel
 * @returns {HeadlinePart[]}
 */
function coloredHeadline(label, elementLabel) {
    return elementLabel
        ? [
              { text: label, colored: true },
              { text: ` ${elementLabel}`, colored: true },
          ]
        : [{ text: label, colored: true }];
}

/**
 * Jet d'attaque du lanceur vs CA et/ou Esquive PA/PM (même total).
 *
 * @param {string} abilityKey
 * @param {{ vsArmor: boolean, dodges: Array<{ key: string, label: string }> }} targets
 * @returns {ResolutionLine}
 */
function buildAttackLine(abilityKey, { vsArmor, dodges }) {
    const ability = ABILITIES[abilityKey];
    const { primary, label } = resolveSpellElementFromAbility(abilityKey);

    const rightLabels = [...(vsArmor ? ["CA"] : []), ...dodges.map((d) => d.label)];
    const rightHeadline = [{ text: rightLabels.join(" · "), colored: false }];

    /** @type {FormulaPart[]} */
    const rightParts = [];
    if (vsArmor) rightParts.push({ type: "text", text: "CA" });
    for (const dodge of dodges) {
        if (rightParts.length) rightParts.push({ type: "text", text: " · " });
        rightParts.push({ type: "chip", key: dodge.key });
    }

    const leftParts = ability
        ? [
              { type: "text", text: "1d20 + " },
              { type: "chip", key: ability.mod },
              { type: "text", text: " + " },
              { type: "chip", key: HIT_BONUS_KEY },
          ]
        : [{ type: "text", text: "1d20 + modificateur de l'élément + bonus de touche" }];

    return withHeadline({
        kind: "attack",
        elementLabel: label,
        elementPrimary: primary,
        leftHeadline: coloredHeadline("Jet d'attaque", label),
        rightHeadline,
        leftParts,
        rightParts,
        valueKeys: [
            ...(ability ? [ability.mod, HIT_BONUS_KEY] : []),
            ...dodges.map((d) => d.key),
        ],
    });
}

/**
 * DD du lanceur vs jet de sauvegarde de la cible.
 *
 * @param {string} abilityKey
 * @returns {ResolutionLine}
 */
function buildSaveLine(abilityKey) {
    const ability = ABILITIES[abilityKey];
    const { primary, label } = resolveSpellElementFromAbility(abilityKey);
    return withHeadline({
        kind: "save",
        elementLabel: label,
        elementPrimary: primary,
        leftHeadline: coloredHeadline("Degré de difficulté", label),
        rightHeadline: coloredHeadline("jet de sauvegarde", label),
        leftParts: ability
            ? [
                  { type: "text", text: "DD " },
                  { type: "chip", key: ability.dc },
              ]
            : [{ type: "text", text: "DD de l'élément du lanceur" }],
        rightParts: ability
            ? [
                  { type: "text", text: "1d20 + " },
                  { type: "chip", key: ability.save },
              ]
            : [{ type: "text", text: "1d20 + sauvegarde de l'élément" }],
        valueKeys: ability ? [ability.dc, ability.save] : [],
    });
}

/**
 * @param {object|null|undefined} entity
 * @returns {{
 *   show: boolean,
 *   mode: string,
 *   lines: ResolutionLine[],
 *   note: string,
 *   valueKeys: string[],
 *   headline: string,
 *   elementLabel: string|null,
 *   elementPrimary: number|null,
 *   leftHeadline: HeadlinePart[],
 *   rightHeadline: HeadlinePart[],
 *   leftParts: FormulaPart[],
 *   rightParts: FormulaPart[],
 *   parts: FormulaPart[],
 * }}
 */
export function buildSpellResolutionFormula(entity) {
    const mode = readSpellField(entity, "resolutionMode", "resolution_mode") || "attack_roll";

    /** @type {ResolutionLine[]} */
    const lines = [];
    if (mode === "auto_success") {
        lines.push(
            withHeadline({
                kind: "auto",
                elementLabel: null,
                elementPrimary: null,
                leftHeadline: [{ text: "Réussite automatique", colored: false }],
                rightHeadline: [],
                leftParts: [],
                rightParts: [],
                valueKeys: [],
            }),
        );
    } else {
        const dodgeInfo = detectSpellDodgeChecks(entity);
        const dodges = dodgeInfo.kinds.map((k) => SPELL_DODGE_TARGETS[k]);
        const removalOnly = dodges.length > 0 && !dodgeInfo.hasOtherEffects;
        const attackKey = String(
            readSpellField(entity, "attackCharacteristicKey", "attack_characteristic_key") || "",
        );

        if (mode === "saving_throw") {
            const saveKey = String(
                readSpellField(entity, "saveCharacteristicKey", "save_characteristic_key") || "",
            );
            if (!removalOnly) lines.push(buildSaveLine(saveKey));
            if (dodges.length) {
                lines.push(buildAttackLine(attackKey || saveKey, { vsArmor: false, dodges }));
            }
        } else {
            lines.push(buildAttackLine(attackKey, { vsArmor: !removalOnly, dodges }));
        }
    }

    const main = lines[0];
    return {
        show: true,
        mode,
        lines,
        note: "",
        valueKeys: [...new Set(lines.flatMap((l) => l.valueKeys))],
        headline: lines.map((l) => l.headline).join(" + "),
        elementLabel: main.elementLabel,
        elementPrimary: main.elementPrimary,
        leftHeadline: main.leftHeadline,
        rightHeadline: main.rightHeadline,
        leftParts: main.leftParts,
        rightParts: main.rightParts,
        parts: main.rightParts.length
            ? [...main.leftParts, { type: "text", text: " vs " }, ...main.rightParts]
            : [...main.leftParts],
    };
}

/**
 * @param {HeadlinePart[]} left
 * @param {HeadlinePart[]} right
 * @returns {string}
 */
function joinHeadline(left, right) {
    const l = left.map((p) => p.text).join("");
    const r = right.map((p) => p.text).join("");
    if (!r) return l;
    return `${l} vs ${r}`;
}
