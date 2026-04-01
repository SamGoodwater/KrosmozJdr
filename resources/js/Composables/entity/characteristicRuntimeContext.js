/**
 * Contexte Vue pour un payload runtime partagé (formules / `computed` enrichi).
 *
 * @description
 * Une page fiche peut `provideCharacteristicRuntime(computed(() => props.characteristicRuntime))`
 * pour que tous les `EntityPropertyDisplay` descendants reçoivent `:runtime` sans le répéter.
 * Une prop explicite `:runtime` sur le composant reste prioritaire.
 */

import { provide } from "vue";

export const CHARACTERISTIC_RUNTIME_INJECT_KEY = Symbol("characteristicRuntime");

/**
 * @param {import('vue').Ref|import('vue').ComputedRef|object} source — ref/computed ou objet (souvent `computed(() => payload)`)
 */
export function provideCharacteristicRuntime(source) {
    provide(CHARACTERISTIC_RUNTIME_INJECT_KEY, source);
}
