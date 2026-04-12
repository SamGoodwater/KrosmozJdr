<script setup>
/**
 * SelectField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Facade for select fields. Routes everything through SelectSearchField which
 * supports single select, multi-select with chips, search, deduplication, etc.
 *
 * SelectFieldNative is kept as a fallback import but is no longer the default
 * for `multiple` — SelectSearchField now handles both modes.
 */
import { useAttrs } from "vue";
import { computed } from "vue";
import { getInputPropsDefinition } from "@/Utils/atomic-design/inputHelper";
import SelectSearchField from "@/Pages/Molecules/data-input/SelectSearchField.vue";

const props = defineProps(getInputPropsDefinition("select", "field"));
const emit = defineEmits(["update:modelValue"]);
const $attrs = useAttrs();

const forwardUpdate = (v) => emit("update:modelValue", v);

const passThrough = computed(() => {
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
    <SelectSearchField
        v-bind="passThrough"
        @update:modelValue="forwardUpdate"
    />
</template>
