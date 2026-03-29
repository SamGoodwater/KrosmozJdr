<script setup>
/* eslint-disable vue/no-mutating-props -- `form` est la même référence que `useForm()` du parent (objet réactif partagé). */
/**
 * Rendu d’un champ du formulaire d’entité (extrait pour réutilisation dans EntityEditForm).
 */
import { computed } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import FileField from '@/Pages/Molecules/data-input/FileField.vue';
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue';
import SpellElementPrimariesField from '@/Pages/Molecules/entity/spell/SpellElementPrimariesField.vue';
import SpellTypesMultiField from '@/Pages/Molecules/entity/spell/SpellTypesMultiField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getByDbColumn } from '@/Composables/store/useCharacteristicsStore';
import { getCharacteristicColorStyle, getCharacteristicContainerStyle } from '@/Utils/color/Color';

const props = defineProps({
    field: { type: Object, required: true },
    form: { type: Object, required: true },
    isMultiEdit: { type: Boolean, default: false },
    differentFields: { type: Array, default: () => [] },
    fieldDirty: { type: Object, default: () => ({}) },
    checkboxDirty: { type: Object, default: () => ({}) },
    getFieldLabel: { type: Function, required: true },
    getFieldHelper: { type: Function, required: true },
    getFieldValidation: { type: Function, required: true },
    getFieldPlaceholder: { type: Function, required: true },
    getFieldRenderType: { type: Function, required: true },
    getFileCurrentPath: { type: Function, required: true },
    getFileAccept: { type: Function, required: true },
    formatDisplayValue: { type: Function, required: true },
    markDirty: { type: Function, required: true },
    resetFieldMultiEdit: { type: Function, required: true },
    resetBoolMultiEdit: { type: Function, required: true },
    /** @type {(fieldKey: string, value: boolean) => void} */
    onCheckboxUpdate: { type: Function, required: true },
    /**
     * Groupe Characteristics (ex. `spell`) pour icônes / couleurs BDD.
     * @type {string|null}
     */
    characteristicsGroup: {
        type: String,
        default: null,
    },
});

const charMeta = computed(() => {
    if (!props.characteristicsGroup || !props.field?.key) {
        return null;
    }
    return getByDbColumn(props.characteristicsGroup, props.field.key);
});

const checkboxColorHex = computed(() => {
    const fromDb = charMeta.value?.color;
    const fromCfg = props.field?.config?.uiColor;
    const raw = (typeof fromDb === 'string' && fromDb.startsWith('#') ? fromDb : null) || fromCfg || null;
    return raw;
});

const checkboxIconUrl = computed(() => {
    const icon = charMeta.value?.icon;
    if (!icon || typeof icon !== 'string') return null;
    if (icon.startsWith('fa-') || icon.includes('fa-')) return null;
    const name = icon.includes('/') ? icon.split('/').pop() : icon;
    return `/storage/images/icons/caracteristics/${name}`;
});

const checkboxFaSource = computed(() => {
    const fromDb = charMeta.value?.icon;
    if (typeof fromDb === 'string' && (fromDb.startsWith('fa-') || fromDb.includes('fa-solid'))) {
        return fromDb;
    }
    const fromCfg = props.field?.config?.uiIcon;
    if (typeof fromCfg === 'string' && fromCfg.includes('fa-')) {
        return fromCfg;
    }
    return 'fa-solid fa-toggle-on';
});

const checkboxContainerStyle = computed(() => {
    const hex = checkboxColorHex.value;
    if (!hex || !hex.startsWith('#')) return {};
    return getCharacteristicContainerStyle(hex);
});

const checkboxLabelStyle = computed(() => {
    const hex = checkboxColorHex.value;
    if (!hex) return {};
    return getCharacteristicColorStyle(hex) || {};
});

const fieldLabelText = computed(() => props.getFieldLabel(props.field.key, props.field.config));
const fieldHelperText = computed(() => props.getFieldHelper(props.field.key, props.field.config) || '');

/** Options select : support `options` fonction (résolution lazy après chargement Inertia). */
const resolvedSelectOptions = computed(() => {
    const opts = props.field?.config?.options;
    if (typeof opts === 'function') {
        try {
            return opts() || [];
        } catch {
            return [];
        }
    }
    return Array.isArray(opts) ? opts : [];
});
</script>

<template>
    <!-- Lecture seule -->
    <div
        v-if="field?.config && getFieldRenderType(field.key, field.config) === 'display'"
        class="rounded-(--radius-field) border border-base-300/60 bg-base-200/20 px-3 py-2"
    >
        <div class="text-xs font-medium uppercase tracking-wide opacity-60">
            {{ fieldLabelText }}
        </div>
        <div class="text-sm mt-1">
            {{ formatDisplayValue(form[field.key]) }}
        </div>
    </div>

    <!-- Booléen : libellé visible + métadonnées Characteristics -->
    <div
        v-else-if="field?.config && getFieldRenderType(field.key, field.config) === 'checkbox'"
        class="w-full"
    >
        <div
            class="flex flex-col gap-2 rounded-lg border border-base-300/70 p-2.5 sm:flex-row sm:items-center sm:justify-between"
            :style="checkboxContainerStyle"
        >
            <div class="flex min-w-0 flex-1 items-start gap-2.5">
                <img
                    v-if="checkboxIconUrl"
                    :src="checkboxIconUrl"
                    :alt="fieldLabelText"
                    class="h-9 w-9 shrink-0 object-contain"
                />
                <Icon
                    v-else
                    :source="checkboxFaSource"
                    :alt="fieldLabelText"
                    size="lg"
                    class="shrink-0 opacity-90"
                    :style="checkboxLabelStyle"
                />
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold leading-snug text-base-content" :style="checkboxLabelStyle">
                        {{ fieldLabelText }}
                    </div>
                    <p v-if="fieldHelperText" class="mt-0.5 text-xs leading-snug text-base-content/75">
                        {{ fieldHelperText }}
                    </p>
                </div>
            </div>
            <div class="flex shrink-0 items-center justify-end gap-3 sm:pl-2">
                <ToggleCore
                    variant="glass"
                    size="sm"
                    color="primary"
                    :model-value="Boolean(form[field.key])"
                    :indeterminate="differentFields.includes(field.key) && !checkboxDirty?.[field.key]"
                    @update:model-value="(v) => onCheckboxUpdate(field.key, v)"
                />
                <span
                    class="text-sm transition-colors duration-200 flex min-w-[2.5rem] items-center gap-1"
                    :class="{
                        'opacity-80': !(differentFields.includes(field.key) && !checkboxDirty?.[field.key]),
                        'text-warning font-semibold':
                            differentFields.includes(field.key) && !checkboxDirty?.[field.key],
                    }"
                >
                    <template v-if="differentFields.includes(field.key) && !checkboxDirty?.[field.key]">
                        <Icon source="fa-solid fa-exclamation-triangle" alt="Valeurs différentes" size="xs" />
                        Valeurs différentes
                    </template>
                    <template v-else>
                        {{ Boolean(form[field.key]) ? 'Oui' : 'Non' }}
                    </template>
                </span>
                <Btn
                    v-if="differentFields.includes(field.key) && checkboxDirty?.[field.key]"
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetBoolMultiEdit(field.key)"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </div>
        </div>
    </div>

    <!-- Pas de Tooltip DaisyUI ici : data-tip/::before recouvrait les textarea et bloquait saisie/focus. -->
    <div v-else-if="field?.config" class="w-full min-w-0">
        <!-- Éléments primaires (sort) : multi + valeur encodée 0–29 -->
        <div
            v-if="getFieldRenderType(field.key, field.config) === 'elementPrimaries'"
            class="flex w-full items-start gap-2"
        >
            <SpellElementPrimariesField
                class="min-w-0 flex-1"
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                :helper="getFieldHelper(field.key, field.config)"
                :validation="getFieldValidation(field.key)"
            />
            <Btn
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                size="xs"
                variant="ghost"
                class="mt-6 shrink-0"
                title="Annuler la modification (ne pas modifier ce champ)"
                @click.stop="resetFieldMultiEdit(field.key, 'elementPrimaries')"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </Btn>
        </div>

        <!-- Types de sort (multi) -->
        <div
            v-else-if="getFieldRenderType(field.key, field.config) === 'spellTypesMulti'"
            class="flex w-full items-start gap-2"
        >
            <SpellTypesMultiField
                class="min-w-0 flex-1"
                v-model="form[field.key]"
                @update:model-value="() => markDirty(field.key)"
                :label="getFieldLabel(field.key, field.config)"
                :helper="getFieldHelper(field.key, field.config)"
                :options="field.config.options || []"
                :validation="getFieldValidation(field.key)"
            />
            <Btn
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                size="xs"
                variant="ghost"
                class="mt-6 shrink-0"
                title="Annuler la modification (ne pas modifier ce champ)"
                @click.stop="resetFieldMultiEdit(field.key, 'spellTypesMulti')"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </Btn>
        </div>

        <!-- InputField -->
        <InputField
            v-else-if="
                getFieldRenderType(field.key, field.config) === 'text' ||
                !['textarea', 'select', 'file', 'number', 'checkbox', 'display', 'elementPrimaries', 'spellTypesMulti'].includes(
                    getFieldRenderType(field.key, field.config),
                )
            "
            v-model="form[field.key]"
            @update:model-value="() => markDirty(field.key)"
            :label="getFieldLabel(field.key, field.config)"
            :type="field.config.type || 'text'"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config)"
        >
            <template
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                #overEnd
            >
                <Btn
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetFieldMultiEdit(field.key, field.config.type || 'text')"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </template>
        </InputField>

        <TextareaField
            v-else-if="getFieldRenderType(field.key, field.config) === 'textarea'"
            v-model="form[field.key]"
            @update:model-value="() => markDirty(field.key)"
            :label="getFieldLabel(field.key, field.config)"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config)"
        >
            <template
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                #overEnd
            >
                <Btn
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetFieldMultiEdit(field.key, 'textarea')"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </template>
        </TextareaField>

        <SelectField
            v-else-if="getFieldRenderType(field.key, field.config) === 'select'"
            v-model="form[field.key]"
            @update:model-value="() => markDirty(field.key)"
            :label="getFieldLabel(field.key, field.config)"
            :options="resolvedSelectOptions"
            :option-badge="field.config.optionBadge || null"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config)"
        >
            <template
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                #overEnd
            >
                <Btn
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetFieldMultiEdit(field.key, 'select')"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </template>
        </SelectField>

        <FileField
            v-else-if="getFieldRenderType(field.key, field.config) === 'file'"
            v-model="form[field.key]"
            :current-path="getFileCurrentPath(field.key)"
            :accept="getFileAccept(field.key, field.config)"
            :label="getFieldLabel(field.key, field.config)"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
        />

        <InputField
            v-else-if="getFieldRenderType(field.key, field.config) === 'number'"
            v-model="form[field.key]"
            @update:model-value="() => markDirty(field.key)"
            :label="getFieldLabel(field.key, field.config)"
            type="number"
            :required="field.config.required"
            :helper="getFieldHelper(field.key, field.config)"
            :validation="getFieldValidation(field.key)"
            :placeholder="getFieldPlaceholder(field.key, field.config)"
        >
            <template
                v-if="isMultiEdit && differentFields.includes(field.key) && fieldDirty?.[field.key]"
                #overEnd
            >
                <Btn
                    size="xs"
                    variant="ghost"
                    title="Annuler la modification (ne pas modifier ce champ)"
                    @click.stop="resetFieldMultiEdit(field.key, 'number')"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </Btn>
            </template>
        </InputField>
    </div>
</template>
