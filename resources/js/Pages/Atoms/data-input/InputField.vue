<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * InputField Atom (DaisyUI + Custom Utility + Edition réactive)
 *
 * @description
 * Composant atomique InputField conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les types d'input (text, email, password, number, url, tel, search, date, etc.)
 * - Props DaisyUI : color, size, variant, type
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Slots : #label, #icon, #right, #validator, #help, default
 * - v-model natif
 * - Edition réactive avancée via useFieldComposable/field/debounceTime (voir ci-dessous)
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <InputField type="text" color="primary" size="lg" label="Nom" v-model="name" />
 * <InputField type="email" variant="outline" label="Email" v-model="email" :validator="form.errors.email" />
 * <InputField type="password" color="error" size="md" label="Mot de passe" v-model="password">
 *   <template #icon><i class="fa fa-lock"></i></template>
 *   <template #right><Btn icon="fa-eye" /></template>
 * </InputField>
 *
 * // Edition réactive (champ modifiable avec bouton reset, debounce, etc.)
 * <InputField v-model="user.name" useFieldComposable :debounceTime="300" />
 *
 * @props {String} type - Type d'input (text, email, password, number, url, tel, search, date, etc.)
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} variant - Style DaisyUI ('', 'ghost', 'outline', 'bordered', 'glass')
 * @props {String} label - Label du champ (optionnel, sinon slot #label)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @props {Boolean} showPasswordToggle - Affiche l'icône pour afficher/masquer le mot de passe si type=password (défaut true)
 * @slot label - Label custom
 * @slot icon - Icône à gauche
 * @slot right - Icône ou bouton à droite
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Contenu custom dans l'input
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import {
    computed,
    ref,
    watch,
    onMounted,
    onUnmounted,
    
} from "vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import Validator from "@/Pages/Atoms/data-input/Validator.vue";
import {
    getCommonProps,
    getCommonAttrs,
    getCustomUtilityProps,
    getCustomUtilityClasses,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import {
    getInputAttrs,
    getInputProps,
} from "@/Utils/atomic-design/atomManager";
import InputLabel from "@/Pages/Atoms/data-input/InputLabel.vue";
import useEditableField from "@/Composables/form/useEditableField";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { colorList, sizeXlList, variantList } from "@/Pages/Atoms/atomMap";
import { typeList } from "./data-inputMap";

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps(),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: "",
        validator: (v) => colorList.includes(v),
    },
    size: {
        type: String,
        default: "",
        validator: (v) => sizeXlList.includes(v),
    },
    variant: {
        type: String,
        default: "",
        validator: (v) => variantList.includes(v),
    },
    type: {
        type: String,
        default: "text",
        validator: (v) => typeList.includes(v),
    },
    label: { type: String, default: "" },
    labelTop: { type: String, default: "" },
    labelLeft: { type: String, default: "" },
    labelRight: { type: String, default: "" },
    labelBottom: { type: String, default: "" },
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
    showPasswordToggle: {
        type: Boolean,
        default: undefined, // sera true si type=password, sinon false
    },
});

const emit = defineEmits(["update:modelValue"]);
const inputRef = ref(null);

// Gestion editableField (optionnel)
const editableField = computed(() => {
    if (props.useFieldComposable) {
        return useEditableField(props.modelValue, {
            field: props.field,
            debounce: props.debounceTime,
            onUpdate: (val) => emit("update:modelValue", val),
        });
    }
    return null;
});

const isFieldModified = computed(() =>
    props.useFieldComposable && editableField.value
        ? editableField.value.isModified.value
        : false,
);
const displayValue = computed(() => {
    if (props.useFieldComposable && editableField.value) {
        return editableField.value.value.value;
    }
    return props.modelValue;
});

function onInput(e) {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onInput(e);
    } else {
        emit("update:modelValue", e.target.value);
    }
}
function onBlur() {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onBlur();
    }
}
function handleReset() {
    if (
        props.useFieldComposable &&
        editableField.value &&
        typeof editableField.value.reset === "function"
    ) {
        editableField.value.reset();
        editableField.value.onBlur();
    }
}

onMounted(() => {
    if (inputRef.value && props.autofocus) {
        inputRef.value.focus();
    }
});
onUnmounted(() => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
});
defineExpose({ focus: () => inputRef.value && inputRef.value.focus() });

function getAtomClasses(props) {
    return mergeClasses(
        [
            "input",
            props.color === "neutral" && "input-neutral",
            props.color === "primary" && "input-primary",
            props.color === "secondary" && "input-secondary",
            props.color === "accent" && "input-accent",
            props.color === "info" && "input-info",
            props.color === "success" && "input-success",
            props.color === "warning" && "input-warning",
            props.color === "error" && "input-error",
            props.size === "xs" && "input-xs",
            props.size === "sm" && "input-sm",
            props.size === "md" && "input-md",
            props.size === "lg" && "input-lg",
            props.size === "xl" && "input-xl",
            props.variant === "ghost" && "input-ghost",
            props.variant === "outline" && "input-outline",
            props.variant === "bordered" && "input-bordered",
            props.variant === "glass" && "glass",
            (props.errorMessage || props.validator) && "validator input-error",
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class,
    );
}

const atomClasses = computed(() => getAtomClasses(props));
const showPassword = ref(false);

const isPasswordType = computed(() => props.type === "password");
const shouldShowPasswordToggle = computed(
    () =>
        isPasswordType.value &&
        (props.showPasswordToggle === undefined ||
            props.showPasswordToggle === true),
);

const inputType = computed(() => {
    if (isPasswordType.value && shouldShowPasswordToggle.value) {
        return showPassword.value ? "text" : "password";
    }
    return props.type;
});

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
    type: inputType.value,
    value: props.modelValue,
}));

const inputId = computed(
    () => props.id || `inputfield-${Math.random().toString(36).substr(2, 9)}`,
);
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Cas floating ou label inline (input dans le label) -->
            <InputLabel
                v-if="props.floating || $slots.leftLabel || $slots.rightLabel"
                :for="inputId"
                :value="label"
                :size="size"
                :color="color"
                :floating="props.floating"
            >
                <template #default>
                    <div class="relative flex items-center w-full">
                        <!-- Left label (ex: préfixe, icône) -->
                        <span
                            v-if="$slots.leftLabel"
                            class="absolute left-3 z-10 flex items-center h-full"
                        >
                            <slot name="leftLabel" />
                        </span>
                        <!-- Input principal -->
                        <input
                            ref="inputRef"
                            v-bind="attrs"
                            v-on="$attrs"
                            :id="inputId"
                            :class="[
                                atomClasses,
                                $slots.leftLabel ? 'pl-10' : '',
                                $slots.rightLabel ? 'pr-10' : '',
                            ]"
                            :value="displayValue"
                            @input="onInput"
                            @blur="onBlur"
                            :aria-invalid="
                                !!errorMessage || validator === 'error'
                            "
                        />
                        <!-- Right label (ex: suffixe, icône) -->
                        <span
                            v-if="$slots.rightLabel"
                            class="absolute right-3 z-10 flex items-center h-full"
                        >
                            <slot name="rightLabel" />
                        </span>
                        <!-- Password toggle -->
                        <button
                            v-if="shouldShowPasswordToggle"
                            type="button"
                            class="absolute right-3 z-20 flex items-center h-full text-base-600/80 hover:text-base-600/50"
                            @click="showPassword = !showPassword"
                        >
                            <i
                                :class="
                                    showPassword
                                        ? 'fa-solid fa-eye'
                                        : 'fa-solid fa-eye-slash'
                                "
                            ></i>
                        </button>
                        <!-- Bouton reset -->
                        <Btn
                            v-if="props.useFieldComposable && isFieldModified"
                            class="absolute right-10 z-20"
                            size="xs"
                            variant="glass"
                            circle
                            @click="handleReset"
                            :aria-label="'Réinitialiser'"
                        >
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                        </Btn>
                    </div>
                </template>
            </InputLabel>
            <template v-else>
                <!-- Label top -->
                <InputLabel
                    v-if="
                        labelTop ||
                        $slots.labelTop ||
                        (!labelTop &&
                            !label &&
                            !$slots.label &&
                            !$slots.labelTop)
                    "
                    :for="inputId"
                    :value="labelTop || label"
                    :size="size"
                    :color="color"
                >
                    <template v-if="$slots.labelTop || $slots.label" #default>
                        <slot name="labelTop">
                            <slot name="label" />
                        </slot>
                    </template>
                </InputLabel>
                <div class="relative flex items-center w-full">
                    <!-- Label left -->
                    <InputLabel
                        v-if="labelLeft || $slots.labelLeft"
                        :for="inputId"
                        :value="labelLeft"
                        :size="size"
                        :color="color"
                        class="mr-2"
                    >
                        <template v-if="$slots.labelLeft" #default>
                            <slot name="labelLeft" />
                        </template>
                    </InputLabel>
                    <!-- Input principal -->
                    <input
                        ref="inputRef"
                        v-bind="attrs"
                        v-on="$attrs"
                        :id="inputId"
                        :class="[
                            atomClasses,
                            $slots.leftLabel ? 'pl-10' : '',
                            $slots.rightLabel ? 'pr-10' : '',
                        ]"
                        :value="displayValue"
                        @input="onInput"
                        @blur="onBlur"
                        :aria-invalid="!!errorMessage || validator === 'error'"
                    />
                    <!-- Password toggle -->
                    <button
                        v-if="shouldShowPasswordToggle"
                        type="button"
                        class="absolute right-3 z-20 flex items-center h-full text-base-600/80 hover:text-base-600/50"
                        @click="showPassword = !showPassword"
                    >
                        <i
                            :class="
                                showPassword
                                    ? 'fa-solid fa-eye'
                                    : 'fa-solid fa-eye-slash'
                            "
                        ></i>
                    </button>
                    <!-- Label right -->
                    <InputLabel
                        v-if="labelRight || $slots.labelRight"
                        :for="inputId"
                        :value="labelRight"
                        :size="size"
                        :color="color"
                        class="ml-2"
                    >
                        <template v-if="$slots.labelRight" #default>
                            <slot name="labelRight" />
                        </template>
                    </InputLabel>
                    <!-- Bouton reset -->
                    <Btn
                        v-if="props.useFieldComposable && isFieldModified"
                        class="absolute right-10 z-20"
                        size="xs"
                        variant="ghost"
                        circle
                        @click="handleReset"
                        :aria-label="'Réinitialiser'"
                    >
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </Btn>
                </div>
                <!-- Label bottom -->
                <InputLabel
                    v-if="labelBottom || $slots.labelBottom"
                    :for="inputId"
                    :value="labelBottom"
                    :size="size"
                    :color="color"
                    class="mt-1"
                >
                    <template v-if="$slots.labelBottom" #default>
                        <slot name="labelBottom" />
                    </template>
                </InputLabel>
            </template>
            <!-- Validator -->
            <div v-if="validator || $slots.validator" class="mt-1">
                <slot name="validator">
                    <Validator
                        v-if="validator"
                        :state="
                            validator === true
                                ? 'success'
                                : validator === 'error'
                                  ? 'error'
                                  : validator
                        "
                        :message="errorMessage"
                    />
                </slot>
            </div>
            <!-- Help -->
            <div v-if="help || $slots.help" class="mt-1 text-xs text-base-400">
                <slot name="help">{{ help }}</slot>
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
