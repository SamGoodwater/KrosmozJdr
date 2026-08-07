/**
 * Résolution color/variant pour Btn (compat legacy hors <script setup>).
 *
 * @description
 * Extrait hors du SFC car `defineProps` est hoisté et ne peut pas référencer
 * des variables locales du `<script setup>`.
 */
import { colorList, variantList } from "@/Pages/Atoms/atomMap";

/** Styles DaisyUI acceptés par Btn (variantList + link). */
export const BTN_VARIANT_TOKENS = new Set([...variantList, "link"]);

/** Couleurs DaisyUI non vides (jamais confondues avec un variant). */
export const BTN_COLOR_TOKENS = new Set(colorList.filter(Boolean));

export const DEFAULT_BTN_VARIANT = "glass";

/**
 * Indique si la valeur est une couleur DaisyUI (pas un style).
 *
 * @param {string} value
 * @returns {boolean}
 * @example
 * isBtnColorToken('primary') // true
 */
export function isBtnColorToken(value) {
    return BTN_COLOR_TOKENS.has(value);
}

/**
 * Indique si la valeur est un style de bouton reconnu.
 *
 * @param {string} value
 * @returns {boolean}
 * @example
 * isBtnVariantToken('ghost') // true
 */
export function isBtnVariantToken(value) {
    return BTN_VARIANT_TOKENS.has(value);
}

/**
 * Valide la prop `color` (couleur ou style legacy).
 *
 * @param {unknown} value
 * @returns {boolean}
 */
export function isValidBtnColorProp(value) {
    return (
        typeof value === "string" &&
        (value === "" || isBtnColorToken(value) || isBtnVariantToken(value))
    );
}

/**
 * Valide la prop `variant` (style ou couleur legacy).
 *
 * @param {unknown} value
 * @returns {boolean}
 */
export function isValidBtnVariantProp(value) {
    return typeof value === "string" && (isBtnVariantToken(value) || isBtnColorToken(value));
}

/**
 * Résout color/variant avec compat legacy (`variant="primary"` → color primary + glass).
 *
 * @param {string} color
 * @param {string} variant
 * @returns {{ color: string, variant: string }}
 * @example
 * resolveBtnAppearance('', 'primary') // { color: 'primary', variant: 'glass' }
 */
export function resolveBtnAppearance(color, variant) {
    const colorValue = typeof color === "string" ? color : "";
    const variantValue = typeof variant === "string" ? variant : DEFAULT_BTN_VARIANT;

    if (isBtnColorToken(variantValue) && !isBtnVariantToken(variantValue)) {
        return {
            color: colorValue || variantValue,
            variant: DEFAULT_BTN_VARIANT,
        };
    }

    if (isBtnVariantToken(colorValue) && !isBtnColorToken(colorValue) && colorValue !== "") {
        const variantIsExplicit =
            isBtnVariantToken(variantValue) && variantValue !== DEFAULT_BTN_VARIANT;
        return {
            color: "",
            variant: variantIsExplicit ? variantValue : colorValue,
        };
    }

    return {
        color: isBtnColorToken(colorValue) || colorValue === "" ? colorValue : "",
        variant: isBtnVariantToken(variantValue) ? variantValue : DEFAULT_BTN_VARIANT,
    };
}
