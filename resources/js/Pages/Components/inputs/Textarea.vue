<script setup>
import { computed, defineProps, ref, onMounted } from 'vue';

const emit = defineEmits(['update:value']);

const props = defineProps({
    theme: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: '',
    },
    value: {
        type: String,
        default: '',
    },
    maxlength: {
        type: Number,
        default: 255,
    },
    color: {
        type: String,
        default: 'primary',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg'].includes(value),
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

const classes = computed(() => {
    let classes = ['textarea', 'w-full', 'max-w-xs'];
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
            classes.push(`textarea-${match.groups.capture}`);
        } else {
            classes.push('textarea-md');
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
        classes.push('textarea-md');
    }

    return classes.join(' ');
});

const updateValue = (event) => {
    emit('update:value', event.target.value);
};

onMounted(() => {
    if (input.value && props.autofocus) {
        input.value.focus();
    }
});
</script>

<template>
    <textarea
        ref="input"
        :value="props.value"
        @input="updateValue"
        :placeholder="props.placeholder"
        :maxlength="props.maxlength"
        :autofocus="props.autofocus"
        :required="props.required"
        :class="classes"
    ></textarea>
</template>
