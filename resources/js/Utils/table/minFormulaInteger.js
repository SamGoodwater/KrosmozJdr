/**
 * Entier minimal d’une saisie nombre / formule, pour les filtres tableau.
 *
 * @example
 * minFormulaInteger("10") // 10
 * minFormulaInteger("{[niveau]}", 1) // 1
 */
import { enumerateFormulaOutcomes, evaluateFormulaValue } from "@/Utils/characteristic/formulaGrammar.js";

/**
 * @param {unknown} raw
 * @param {number} [atLevel=1]
 * @returns {number|null}
 */
export function minFormulaInteger(raw, atLevel = 1) {
    const value = String(raw ?? "").trim();
    if (value === "") return null;
    if (/^-?\d+$/.test(value)) return Number(value);

    const outcomes = enumerateFormulaOutcomes(value, { level: atLevel });
    if (outcomes.length > 0) return Math.round(outcomes[0]);

    const evaluated = evaluateFormulaValue(value, { level: atLevel });
    return evaluated === null ? null : Math.round(evaluated);
}
