<script setup>
import { computed, defineProps, ref, defineEmits } from "vue";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    checked: {
        type: [String, Boolean],
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "primary-500",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["", "xs", "sm", "md", "lg"].includes(value),
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

const isChecked = ref(props.checked);

const classes = computed(() => {
    let classes = ["checkbox"];
    let match;

    if (props.theme) {
        // COLOR
        const regexColor =
            /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
        } else {
            classes.push(`text-${props.color}`);
        }

        // SIZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`checkbox-${match.groups.capture}`);
        } else {
            classes.push(`checkbox-${props.size}`);
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
        classes.push(`text-${props.color}`);
        classes.push(`checkbox-${props.size}`);
    }

    return classes.join(" ");
});

const updateChecked = (event) => {
    isChecked.value = event.target.checked;
    emit("update:checked", isChecked.value);
};
</script>

<template>
    <div class="form-control">
        <label class="label cursor-pointer">
            <input
                :class="classes"
                class="checkbox"
                type="checkbox"
                :checked="isChecked"
                @change="updateChecked"
                :autofocus="props.autofocus"
                :required="props.required"
            />
            <span class="label-text">{{ props.label }}</span>
        </label>
    </div>
</template>
