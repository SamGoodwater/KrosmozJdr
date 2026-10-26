/**
 * Formule publique d'un jet de sort (attaque, DD, sauvegarde).
 *
 * Les termes sont des clés de caractéristiques créature : le survol ouvre
 * leur fiche, y compris pour un visiteur sans personnage.
 *
 * @example
 * buildSpellResolutionFormula({ resolution_mode: "attack_roll", attack_characteristic_key: "agi" })
 */

import { readSpellField } from "@/Utils/Entity/spellMinimalUsageDisplay";
import { ELEMENT_PRIMARY_LABELS } from "@/Utils/Entity/Elements";

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
 * @param {object|null|undefined} entity
 * @returns {{
 *   show: boolean,
 *   mode: string,
 *   elementLabel: string|null,
 *   elementPrimary: number|null,
 *   leftHeadline: HeadlinePart[],
 *   rightHeadline: HeadlinePart[],
 *   leftParts: FormulaPart[],
 *   rightParts: FormulaPart[],
 *   note: string,
 *   valueKeys: string[],
 *   headline: string,
 *   parts: FormulaPart[],
 * }}
 */
export function buildSpellResolutionFormula(entity) {
    const mode = readSpellField(entity, "resolutionMode", "resolution_mode") || "attack_roll";
    const willing = Boolean(
        readSpellField(entity, "autoSuccessIfWillingTarget", "auto_success_if_willing_target"),
    );
    const note =
        willing && mode !== "auto_success" ? "Réussite automatique si la cible est consentante." : "";

    const empty = {
        show: true,
        mode,
        elementLabel: null,
        elementPrimary: null,
        leftHeadline: [],
        rightHeadline: [],
        leftParts: [],
        rightParts: [],
        note: "",
        valueKeys: [],
        headline: "",
        parts: [],
    };

    if (mode === "auto_success") {
        return {
            ...empty,
            leftHeadline: [{ text: "Réussite automatique", colored: false }],
            headline: "Réussite automatique",
            note: "",
        };
    }

    if (mode === "saving_throw") {
        const abilityKey = String(
            readSpellField(entity, "saveCharacteristicKey", "save_characteristic_key") || "",
        );
        const ability = ABILITIES[abilityKey];
        const { primary, label } = resolveSpellElementFromAbility(abilityKey);
        const leftHeadline = label
            ? [
                  { text: "Degré de difficulté", colored: true },
                  { text: ` ${label}`, colored: true },
              ]
            : [{ text: "Degré de difficulté", colored: true }];
        const rightHeadline = label
            ? [
                  { text: "jet de sauvegarde", colored: true },
                  { text: ` ${label}`, colored: true },
              ]
            : [{ text: "jet de sauvegarde", colored: true }];

        if (!ability) {
            const leftParts = [
                { type: "text", text: "DD de l'élément du lanceur" },
            ];
            const rightParts = [
                { type: "text", text: "1d20 + sauvegarde de l'élément" },
            ];
            return {
                show: true,
                mode,
                elementLabel: label,
                elementPrimary: primary,
                leftHeadline,
                rightHeadline,
                leftParts,
                rightParts,
                note,
                valueKeys: [],
                headline: joinHeadline(leftHeadline, rightHeadline),
                parts: [...leftParts, { type: "text", text: " vs " }, ...rightParts],
            };
        }

        const leftParts = [
            { type: "text", text: "DD " },
            { type: "chip", key: ability.dc },
        ];
        const rightParts = [
            { type: "text", text: "1d20 + " },
            { type: "chip", key: ability.save },
        ];
        return {
            show: true,
            mode,
            elementLabel: label,
            elementPrimary: primary,
            leftHeadline,
            rightHeadline,
            leftParts,
            rightParts,
            note,
            valueKeys: [ability.dc, ability.save],
            headline: joinHeadline(leftHeadline, rightHeadline),
            parts: [...leftParts, { type: "text", text: " vs " }, ...rightParts],
        };
    }

    const abilityKey = String(
        readSpellField(entity, "attackCharacteristicKey", "attack_characteristic_key") || "",
    );
    const ability = ABILITIES[abilityKey];
    const { primary, label } = resolveSpellElementFromAbility(abilityKey);
    const leftHeadline = label
        ? [
              { text: "Jet d'attaque", colored: true },
              { text: ` ${label}`, colored: true },
          ]
        : [{ text: "Jet d'attaque", colored: true }];
    const rightHeadline = [{ text: "CA", colored: false }];

    if (!ability) {
        const leftParts = [
            { type: "text", text: "1d20 + modificateur de l'élément + bonus de touche" },
        ];
        const rightParts = [{ type: "text", text: "CA" }];
        return {
            show: true,
            mode,
            elementLabel: label,
            elementPrimary: primary,
            leftHeadline,
            rightHeadline,
            leftParts,
            rightParts,
            note,
            valueKeys: [],
            headline: joinHeadline(leftHeadline, rightHeadline),
            parts: [...leftParts, { type: "text", text: " vs " }, ...rightParts],
        };
    }

    const leftParts = [
        { type: "text", text: "1d20 + " },
        { type: "chip", key: ability.mod },
        { type: "text", text: " + " },
        { type: "chip", key: HIT_BONUS_KEY },
    ];
    const rightParts = [{ type: "text", text: "CA" }];
    return {
        show: true,
        mode,
        elementLabel: label,
        elementPrimary: primary,
        leftHeadline,
        rightHeadline,
        leftParts,
        rightParts,
        note,
        valueKeys: [ability.mod, HIT_BONUS_KEY],
        headline: joinHeadline(leftHeadline, rightHeadline),
        parts: [...leftParts, { type: "text", text: " vs " }, ...rightParts],
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
