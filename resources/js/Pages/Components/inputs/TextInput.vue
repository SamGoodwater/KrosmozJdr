<script setup>
import { ref, onMounted, defineExpose, computed } from 'vue';
import { useAttrs } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    maxlength: {
        type: [String, Number],
        default: null,
    },
    minlength: {
        type: [String, Number],
        default: null,
    },
    pattern: {
        type: String,
        default: null,
    },
    tooltip: {
        type: String,
        default: '',
    },
    labelInside: {
        type: Boolean,
        default: false,
    },
    theme: {
        type: String,
        default: '',
    },
});

const input = ref(null);
const attrs = useAttrs();
let colorRef = ref('gray-600');
let typeRef = ref(props.type);
let autofocusRef = ref(props.autofocus);
let requiredRef = ref(props.required);
let maxRef = ref(props.maxlength);
let minRef = ref(props.minlength);

const getClasses = computed(() => {
    let classes = ['input', 'w-full', 'max-w-xs'];
    let match;

    if (props.theme) {
        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
            classes.push(`border-${match.groups.capture}`);
            colorRef.value = match.groups.capture;
        } else {
            classes.push('text-main-500');
            classes.push('border-main-500');
        }

        // TYPE
        const regexType = /(?:^|\s)(?<capture>text|email|password|tel|url)(?:\s|$)/;
        match = regexType.exec(props.theme);
        if (match && match?.groups?.capture) {
            typeRef.value = match.groups.capture;
        } else {
            typeRef.value = 'text';
        }

        // SIZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`input-${match.groups.capture}`);
        } else {
            classes.push('input-md');
        }

        // Autofocus
        const regexAutofocus = /(?:^|\s)(?<capture>autofocus)(?:\s|$)/;
        match = regexAutofocus.exec(props.theme);
        if (match && match?.groups?.capture) {
            autofocusRef.value = true;
        }

        // Required
        const regexRequired = /(?:^|\s)(?<capture>required)(?:\s|$)/;
        match = regexRequired.exec(props.theme);
        if (match && match?.groups?.capture) {
            requiredRef.value = true;
        }

        // Bordered
        const regexBordered = /(?:^|\s)(?<capture>border|bordered)(?:\s|$)/;
        match = regexBordered.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push('input-bordered');
        }

        // Max
        const regexMax = /(?:^|\s)max:(?<capture>[0-9]+)(?:\s|$)/; // max:1000
        match = regexMax.exec(props.theme);
        if (match && match?.groups?.capture) {
            maxRef.value = match.groups.capture;
        }

        // Min
        const regexMin = /(?:^|\s)min:(?<capture>[0-9]+)(?:\s|$)/; // min:0
        match = regexMin.exec(props.theme);
        if (match && match?.groups?.capture) {
            minRef.value = match.groups.capture;
        }
    }

    return classes.join(' ');
});

const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};

onMounted(() => {
    if (input.value && autofocusRef.value) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <label v-if="labelInside" :class="`input border-${colorRef} text-${colorRef} input-bordered flex items-center gap-2`">
        <slot v-if="labelInside" name="before" />
        <input
            v-bind="attrs"
            :required="requiredRef"
            :autofocus="autofocusRef"
            :value="modelValue"
            @input="updateValue"
            ref="input"
            :type="typeRef"
            :placeholder="placeholder"
            :maxlength="maxRef"
            :minlength="minRef"
            :pattern="pattern"
            :data-tip="tooltip"
            :class="getClasses"
        />
        <slot v-if="labelInside" name="after" />
    </label>
    <input
        v-else
        v-bind="attrs"
        :required="requiredRef"
        :autofocus="autofocusRef"
        :value="modelValue"
        @input="updateValue"
        ref="input"
        :type="typeRef"
        :placeholder="placeholder"
        :maxlength="maxRef"
        :minlength="minRef"
        :pattern="pattern"
        :data-tip="tooltip"
        :class="getClasses"
    />
</template>
