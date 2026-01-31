<script setup>
/**
 * SelectField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Pour corriger définitivement les soucis de rendu (menu natif OS parfois illisible),
 * `SelectField` utilise par défaut un dropdown custom (glass) via `SelectSearchField`.
 *
 * Exception: `multiple` continue d'utiliser un `<select>` natif via `SelectFieldNative`.
 */
import { useAttrs } from "vue";
import { computed } from "vue";
import { getInputPropsDefinition } from "@/Utils/atomic-design/inputHelper";
import SelectSearchField from "@/Pages/Molecules/data-input/SelectSearchField.vue";
import SelectFieldNative from "@/Pages/Molecules/data-input/SelectFieldNative.vue";

const props = defineProps(getInputPropsDefinition("select", "field"));
const emit = defineEmits(["update:modelValue"]);
const $attrs = useAttrs();

const forwardUpdate = (v) => emit("update:modelValue", v);

const passThrough = computed(() => {
    // Vue n'autorise pas les attributs dupliqués (ex: deux `v-bind` sans argument).
    // On fusionne ici pour ne faire qu'un seul `v-bind`.
    const { class: attrClass, style: attrStyle, ...restAttrs } = $attrs || {};
    const { class: propClass, style: propStyle, ...restProps } = props || {};

    return {
        ...restAttrs,
        ...restProps,
        class: [attrClass, propClass].filter(Boolean),
        style: [attrStyle, propStyle].filter(Boolean),
    };
});
</script>

<template>
    <SelectFieldNative
        v-if="props.multiple"
        v-bind="passThrough"
        @update:modelValue="forwardUpdate"
    >
        <slot />
    </SelectFieldNative>

    <SelectSearchField
        v-else
        v-bind="passThrough"
        @update:modelValue="forwardUpdate"
    />
</template>