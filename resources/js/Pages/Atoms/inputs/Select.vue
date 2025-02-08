<script setup>
import { computed, defineProps, ref, onMounted, defineEmits } from "vue";

const emit = defineEmits(["update:value"]);

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    value: {
        type: [String, Number],
        default: "",
    },
    options: {
        type: Array,
        required: true,
    },
    color: {
        type: String,
        default: "primary",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["", "xs", "sm", "md", "lg"].includes(value),
    },
    label: {
        type: String,
        default: "Sélectionner une option",
    },
    autofocus: {
        type: [Boolean, String],
        default: false,
    },
    required: {
        type: [Boolean, String],
        default: false,
    },
});

const input = ref(null);

const classes = computed(() => {
    let classes = ["select", "w-full", "max-w-xs"];
    let match;

    if (props.theme) {
        // COLOR
        const regexColor =
            /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
            classes.push(`border-${match.groups.capture}`);
        } else {
            classes.push("text-primary-500");
            classes.push("border-primary-500");
        }

        // SIZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`select-${match.groups.capture}`);
        } else {
            classes.push("select-md");
        }

        // Autofocus
        const regexAutofocus = /(?:^|\s)(?<capture>autofocus)(?:\s|$)/;
        match = regexAutofocus.exec(props.theme);
        if (match && match?.groups?.capture) {
            props.autofocus = true;
        } else {
            props.autofocus = props.autofocus ? props.autofocus : false;
        }

        // Required
        const regexRequired = /(?:^|\s)(?<capture>required)(?:\s|$)/;
        match = regexRequired.exec(props.theme);
        if (match && match?.groups?.capture) {
            props.required = true;
        } else {
            props.required = props.required ? props.required : false;
        }
    } else {
        classes.push("text-primary-500");
        classes.push("border-primary-500");
        classes.push("select-md");
    }

    return classes.join(" ");
});

const updateValue = (event) => {
    emit("update:value", event.target.value);
};

onMounted(() => {
    if (input.value && props.autofocus) {
        input.value.focus();
    }
});
</script>

<template>
    <label :class="classes">
        <span>{{ props.label }}</span>
        <select
            ref="input"
            :value="props.value"
            @change="updateValue"
            :autofocus="props.autofocus"
            :required="props.required"
            :class="classes"
        >
            <option
                v-for="option in props.options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
    </label>
</template>
