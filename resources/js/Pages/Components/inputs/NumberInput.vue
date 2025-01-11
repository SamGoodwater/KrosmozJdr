<script setup>
import { computed, defineProps, ref, onMounted } from 'vue';

const emit = defineEmits(['update:value']);

const props = defineProps({
    theme: {
        type: String,
        default: ''
    },
    placeholder: {
        type: Number,
        default: 0,
    },
    value: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 100000000,
    },
    min: {
        type: Number,
        default: 0,
    },
    step: {
        type: Number,
        default: 1,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);
const isFocused = ref(false);

const classes = computed(() => {
    let classes = ['input', 'w-full', 'max-w-xs'];
    let match;

    if (props.theme) {
        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
            classes.push(`border-${match.groups.capture}`);
        } else {
            classes.push('text-main-500');
            classes.push('border-main-500');
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
            props.autofocus = true;
        }

        // Required
        const regexRequired = /(?:^|\s)(?<capture>required)(?:\s|$)/;
        match = regexRequired.exec(props.theme);
        if (match && match?.groups?.capture) {
            props.required = true;
        }
    } else {
        classes.push('text-main-500');
        classes.push('border-main-500');
        classes.push('input-md');
    }

    return classes.join(' ');
});

const updateValue = (event) => {
    emit('update:value', Number(event.target.value));
};

onMounted(() => {
    if (input.value && props.autofocus) {
        input.value.focus();
    }
});
</script>

<template>
    <input
        ref="input"
        type="number"
        :value="props.value"
        @input="updateValue"
        :placeholder="props.placeholder"
        :max="props.max"
        :min="props.min"
        :step="props.step"
        :autofocus="props.autofocus"
        :required="props.required"
        :class="classes"
    />
</template>
