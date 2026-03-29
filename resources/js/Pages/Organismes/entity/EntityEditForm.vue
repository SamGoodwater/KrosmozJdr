<script setup>
/**
 * EntityEditForm Organism
 * 
 * @description
 * Composant réutilisable pour la création/édition des contenus.
 * 
 * @props {Object} entity - Données de l'entité à éditer
 * @props {String} entityType - Type d'entité (item, spell, monster, etc.)
 * @props {Object} fieldsConfig - Configuration des champs à afficher
 * @props {Boolean} isUpdating - Mode édition (true) ou création (false)
 */
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import FormulaHelpHint from '@/Pages/Molecules/entity/FormulaHelpHint.vue';
import EntityEditFormFieldBody from '@/Pages/Molecules/entity/EntityEditFormFieldBody.vue';
import { FORMULA_PLACEHOLDER } from '@/Utils/entity/formula-help';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    fieldsConfig: {
        type: Object,
        default: () => ({})
    },
    isUpdating: {
        type: Boolean,
        default: true
    },
    differentFields: {
        type: Array,
        default: () => []
    },
    /**
     * Override optionnel pour les routes (utile pour les "types" ou routes non standards).
     *
     * @example
     * routeNameBase="entities.resource-types"
     * routeParamKey="resourceType"
     */
    routeNameBase: {
        type: String,
        default: null
    },
    routeParamKey: {
        type: String,
        default: null
    },
    /**
     * Clés masquées (non affichées). Par défaut : auto_update, dofus_version.
     * Ex. pour les sorts : `['dofus_version']` pour afficher l’auto-update dans une section dédiée.
     */
    hiddenFieldKeys: {
        type: Array,
        default: null,
    },
    /**
     * Sections du formulaire (cartes avec titre). Ordre des `fieldKeys` = ordre d’affichage.
     * @type {Array<{ id: string, title: string, subtitle?: string, fieldKeys: string[] }>|null}
     */
    fieldSections: {
        type: Array,
        default: null,
    },
    /** Affiche l’état (workflow) dans la barre du haut. */
    showStateToolbar: {
        type: Boolean,
        default: true,
    },
    /** Affiche lecture / écriture dans le pied de formulaire. */
    showAccessLevelsInFooter: {
        type: Boolean,
        default: true,
    },
    /**
     * Groupe Characteristics (ex. `spell`) pour enrichir les booléens (icône / couleur BDD).
     */
    characteristicsGroup: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['submit', 'cancel']);

const notificationStore = useNotificationStore();

/**
 * Multi-edit quand on n'a pas d'ID et qu'on est en mode update.
 * Dans ce mode, on ne doit envoyer que les champs explicitement modifiés.
 */
const isMultiEdit = computed(() => {
    const entityId = props.entity?.id ?? null;
    return !entityId && Boolean(props.isUpdating);
});

// Configuration par défaut des champs selon le type d'entité
const defaultFieldsConfig = computed(() => {
    const baseFields = {
        name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
        description: { type: 'textarea', label: 'Description', required: false, showInCompact: false },
    };

    // Configuration spécifique par type d'entité
    const entitySpecificFields = {
        item: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            rarity: { type: 'select', label: 'Rareté', required: false, showInCompact: true, options: [
                { value: 'common', label: 'Commun' },
                { value: 'uncommon', label: 'Peu commun' },
                { value: 'rare', label: 'Rare' },
                { value: 'epic', label: 'Épique' },
                { value: 'legendary', label: 'Légendaire' }
            ]},
            image: { type: 'file', label: 'Image', required: false, showInCompact: false }
        },
        spell: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            ap_cost: { type: 'number', label: 'Coût PA', required: false, showInCompact: true },
            range: { type: 'number', label: 'Portée', required: false, showInCompact: false },
            image: { type: 'file', label: 'Image', required: false, showInCompact: false }
        },
        monster: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            life: { type: 'number', label: 'Vie', required: false, showInCompact: true },
            size: { type: 'number', label: 'Taille', required: false, showInCompact: false },
            is_boss: { type: 'checkbox', label: 'Boss', required: false, showInCompact: true }
        }
    };

    return {
        ...baseFields,
        ...(entitySpecificFields[props.entityType] || {})
    };
});

// Fusion de la configuration par défaut avec la configuration personnalisée
const fieldsConfig = computed(() => {
    return { ...defaultFieldsConfig.value, ...props.fieldsConfig };
});

const resolvedHiddenFieldKeys = computed(() => {
    if (props.hiddenFieldKeys !== null && props.hiddenFieldKeys !== undefined) {
        return new Set(props.hiddenFieldKeys);
    }
    return new Set(['auto_update', 'dofus_version']);
});
const topRowFieldKeys = ['name'];
const secondaryFieldKeys = new Set([
    'id', 'slug', 'read_level', 'write_level',
    'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at',
    'dofusdb_id', 'dofusdb_type_id',
    'source', 'source_url', 'source_ref',
]);

const isSecondaryField = (fieldKey, fieldConfig) => {
    if (secondaryFieldKeys.has(fieldKey)) return true;
    const group = String(fieldConfig?.group || '').toLowerCase();
    if (!group) return false;
    return group.includes('meta') || group.includes('statut') || group.includes('permission');
};

const visibleFields = computed(() => {
    const entries = Object.entries(fieldsConfig.value).filter(([fieldKey]) => !resolvedHiddenFieldKeys.value.has(fieldKey));

    if (props.fieldSections?.length) {
        const entryByKey = new Map(entries.map(([key, config]) => [key, { key, config }]));
        const ordered = [];
        const consumed = new Set();
        for (const sec of props.fieldSections) {
            for (const fieldKey of sec.fieldKeys || []) {
                const entry = entryByKey.get(fieldKey);
                if (entry && !consumed.has(fieldKey)) {
                    ordered.push(entry);
                    consumed.add(fieldKey);
                }
            }
        }
        for (const [fieldKey, fieldConfig] of entries) {
            if (!consumed.has(fieldKey)) {
                ordered.push({ key: fieldKey, config: fieldConfig });
            }
        }

        return ordered;
    }

    const entryByKey = new Map(entries.map(([key, config]) => [key, { key, config }]));
    const consumed = new Set();
    const ordered = [];

    for (const key of topRowFieldKeys) {
        const entry = entryByKey.get(key);
        if (!entry) continue;
        ordered.push(entry);
        consumed.add(key);
    }

    let descriptionEntry = null;
    const important = [];
    const secondary = [];

    for (const [fieldKey, fieldConfig] of entries) {
        if (consumed.has(fieldKey)) continue;
        if (fieldKey === 'description') {
            descriptionEntry = { key: fieldKey, config: fieldConfig };
            continue;
        }

        if (isSecondaryField(fieldKey, fieldConfig)) {
            secondary.push({ key: fieldKey, config: fieldConfig });
            continue;
        }

        important.push({ key: fieldKey, config: fieldConfig });
    }

    ordered.push(...important);
    if (descriptionEntry) ordered.push(descriptionEntry);
    ordered.push(...secondary);

    return ordered;
});

const stateField = computed(() => {
    if (!props.showStateToolbar) {
        return null;
    }
    const entry = visibleFields.value.find((field) => field?.key === 'state');
    return entry?.config ? entry : null;
});

const accessLevelFields = computed(() => {
    if (!props.showAccessLevelsInFooter) {
        return [];
    }
    return visibleFields.value.filter((field) => ['read_level', 'write_level'].includes(field?.key));
});

const mainFields = computed(() => {
    const exclude = [];
    if (props.showStateToolbar) {
        exclude.push('state');
    }
    if (props.showAccessLevelsInFooter) {
        exclude.push('read_level', 'write_level');
    }
    return visibleFields.value.filter((field) => !exclude.includes(field?.key));
});

// Initialisation du formulaire avec les données de l'entité
const initializeForm = () => {
    // Si c'est une création (pas d'ID), utiliser les valeurs par défaut
    if (!props.isUpdating || !props.entity?.id) {
        const formData = {};
        Object.keys(fieldsConfig.value).forEach(key => {
            // En multi-edit: si le champ fait partie des champs différents, valeur neutre
            // => affiche "Valeurs différentes" et évite de pré-remplir une valeur arbitraire.
            if (isMultiEdit.value && props.differentFields.includes(key)) {
                formData[key] = getDefaultValue(fieldsConfig.value[key].type);
                return;
            }
            // Utiliser la valeur de l'entité si fournie, sinon valeur par défaut
            formData[key] = props.entity[key] !== undefined 
                ? props.entity[key] 
                : getDefaultValue(fieldsConfig.value[key].type);
        });
        return formData;
    }
    
    // Si l'entité est une instance de modèle avec toFormData(), l'utiliser
    if (props.entity && typeof props.entity.toFormData === 'function') {
        const modelFormData = props.entity.toFormData();
        const formData = {};
        Object.keys(fieldsConfig.value).forEach(key => {
            // Utiliser les données du modèle si disponibles, sinon valeur par défaut
            formData[key] = modelFormData[key] !== undefined 
                ? modelFormData[key] 
                : (props.entity[key] !== undefined ? props.entity[key] : getDefaultValue(fieldsConfig.value[key].type));
        });
        return formData;
    }
    
    // Sinon, utiliser l'accès direct aux propriétés (compatibilité avec objets bruts)
    // Pour l'édition multiple, l'entité peut être un objet simple avec les valeurs communes
    const formData = {};
    Object.keys(fieldsConfig.value).forEach(key => {
        formData[key] = props.entity[key] !== undefined 
            ? props.entity[key] 
            : getDefaultValue(fieldsConfig.value[key].type);
    });
    return formData;
};

const getDefaultValue = (type) => {
    switch (type) {
        case 'number': return null;
        case 'checkbox': return false;
        case 'select': return null;
        case 'elementPrimaries': return null;
        case 'spellTypesMulti': return [];
        case 'file': return null;
        case 'display': return '';
        default: return '';
    }
};

const form = useForm(initializeForm());
/**
 * Snapshot des valeurs "initiales" du formulaire (au moment de l'ouverture / dernière synchro).
 * Utilisé par le bouton Reset.
 */
const initialSnapshot = ref(initializeForm());

/**
 * Visibilité conditionnelle (ex. résolution : champs selon `resolution_mode`).
 *
 * @param {string} fieldKey
 * @param {object} fieldConfig
 */
function isFieldVisible(fieldKey, fieldConfig) {
    const rule = fieldConfig?.visibleWhen;
    if (!rule) return true;
    const v = form[rule.field];
    if (Object.prototype.hasOwnProperty.call(rule, 'value')) return v === rule.value;
    if (Array.isArray(rule.in)) return rule.in.includes(v);
    return true;
}

const mainFieldSections = computed(() => {
    if (!props.fieldSections?.length) return null;
    const mf = mainFields.value;
    const byKey = new Map(mf.map((f) => [f.key, f]));
    return props.fieldSections.map((sec) => ({
        id: sec.id,
        title: sec.title,
        subtitle: sec.subtitle,
        fields: (sec.fieldKeys || [])
            .map((k) => byKey.get(k))
            .filter(Boolean)
            .filter((f) => isFieldVisible(f.key, f.config)),
    })).filter((sec) => sec.fields.length > 0);
});

const visibleMainFields = computed(() =>
    mainFields.value.filter((f) => isFieldVisible(f.key, f.config)),
);

/**
 * Track des bools modifiés en UI (utile pour gérer un état indeterminate en mode multi-edit).
 * @type {Record<string, boolean>}
 */
const checkboxDirty = ref({});

const onCheckboxUpdate = (key, v) => {
    checkboxDirty.value = { ...(checkboxDirty.value || {}), [key]: true };
    form[key] = Boolean(v);
};

/**
 * Track des champs non-bool modifiés en multi-edit.
 * @type {Record<string, boolean>}
 */
const fieldDirty = ref({});

/**
 * Marque un champ comme modifié (multi-edit).
 * @param {string} key
 */
const markDirty = (key) => {
    if (!isMultiEdit.value) return;
    if (!props.differentFields.includes(key)) return;
    fieldDirty.value = { ...(fieldDirty.value || {}), [key]: true };
};

/**
 * Annule la modification d'un champ (multi-edit) => ne pas modifier.
 * @param {string} key
 * @param {string} type
 */
const resetFieldMultiEdit = (key, type) => {
    if (!isMultiEdit.value) return;
    if (!props.differentFields.includes(key)) return;
    fieldDirty.value = { ...(fieldDirty.value || {}), [key]: false };
    form[key] = getDefaultValue(type);
};

/**
 * Reset d'un champ bool en mode multi-edit : revient à l'état indeterminate (non modifié).
 *
 * @param {string} key
 * @returns {void}
 */
const resetBoolMultiEdit = (key) => {
    if (!key) return;
    checkboxDirty.value = { ...(checkboxDirty.value || {}), [key]: false };
    // Important: en mode "valeurs différentes", `form[key]` n'est pas une valeur fiable.
    // L'état indeterminate (= ne pas modifier) est piloté par `checkboxDirty`.
    form[key] = false;
};

/**
 * Reset global du formulaire :
 * - revient aux valeurs chargées au moment de l'ouverture (ou dernière synchro entity->form)
 * - remet les champs multi-edit en mode "ne pas modifier" (dirty=false)
 */
const resetForm = () => {
    const snap = initialSnapshot.value || {};

    for (const key of Object.keys(fieldsConfig.value || {})) {
        if (Object.prototype.hasOwnProperty.call(snap, key)) {
            form[key] = snap[key];
        } else {
            form[key] = getDefaultValue(fieldsConfig.value?.[key]?.type);
        }
    }

    checkboxDirty.value = {};
    fieldDirty.value = {};
    form.clearErrors?.();
};

// Mise à jour du formulaire quand l'entité change
watch(() => props.entity, () => {
    const formData = initializeForm();
    initialSnapshot.value = formData;
    Object.keys(formData).forEach(key => {
        form[key] = formData[key];
    });
}, { deep: true });

/**
 * Raccourcis clavier
 */
const handleKeydown = (event) => {
    // Ctrl+S / Cmd+S : Sauvegarder
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault();
        if (!form.processing) {
            submit();
        }
        return;
    }

    // Esc : Annuler / Fermer
    if (event.key === 'Escape') {
        event.preventDefault();
        emit('cancel');
        return;
    }

    // Ctrl+Z / Cmd+Z : Réinitialiser (si possible)
    if ((event.ctrlKey || event.metaKey) && event.key === 'z' && !event.shiftKey) {
        // Ne pas intercepter si on est dans un input/textarea (laisser le navigateur gérer)
        const target = event.target;
        if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
            return;
        }
        event.preventDefault();
        resetForm();
        return;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

// Validation pour chaque champ
const getFieldValidation = (fieldKey) => {
    if (!form.errors[fieldKey]) return null;
    return {
        state: 'error',
        message: form.errors[fieldKey],
        showNotification: false
    };
};

const nonFormulaFieldKeys = new Set([
    'id', 'name', 'title', 'slug', 'description', 'image', 'icon', 'state', 'decision',
    'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at',
]);

const isFormulaFriendlyField = (fieldKey, fieldConfig) => {
    if (!fieldConfig || fieldConfig.type !== 'text') return false;
    if (nonFormulaFieldKeys.has(fieldKey)) return false;
    return true;
};

const getFieldPlaceholder = (fieldKey, fieldConfig) => {
    if (props.differentFields.includes(fieldKey)) return 'Valeurs différentes';
    if (fieldConfig?.placeholder) return fieldConfig.placeholder;
    if (isFormulaFriendlyField(fieldKey, fieldConfig)) return FORMULA_PLACEHOLDER;
    return undefined;
};

const isImageField = (fieldKey, fieldConfig) => {
    const key = String(fieldKey || '').toLowerCase();
    const label = String(fieldConfig?.label || '').toLowerCase();
    return key.includes('image') || key.includes('thumbnail') || label.includes('image');
};

const getFieldRenderType = (fieldKey, fieldConfig) => {
    if (fieldConfig?.type === 'display') return 'display';
    if (fieldConfig?.type === 'file') return 'file';
    if (fieldConfig?.type === 'text' && isImageField(fieldKey, fieldConfig)) return 'file';
    if (fieldConfig?.type === 'elementPrimaries') return 'elementPrimaries';
    if (fieldConfig?.type === 'spellTypesMulti') return 'spellTypesMulti';
    return fieldConfig?.type || 'text';
};

/**
 * @param {unknown} val
 */
const formatDisplayValue = (val) => {
    if (val === null || val === undefined || val === '') return '—';
    return String(val);
};

const getFileCurrentPath = (fieldKey) => {
    const value = form[fieldKey];
    return typeof value === 'string' ? value : null;
};

const getFileAccept = (fieldKey, fieldConfig) => {
    if (fieldConfig?.accept) return fieldConfig.accept;
    return isImageField(fieldKey, fieldConfig) ? 'image/*' : undefined;
};

const humanizeFieldKey = (fieldKey) => {
    const raw = String(fieldKey || '')
        .replace(/[_-]+/g, ' ')
        .trim();
    if (!raw) return '';
    return raw.charAt(0).toUpperCase() + raw.slice(1);
};

const getFieldLabel = (fieldKey, fieldConfig) => {
    if (fieldConfig?.label && String(fieldConfig.label).trim() !== '' && fieldConfig.label !== fieldKey) {
        return fieldConfig.label;
    }

    const fallbackLabels = {
        state: 'État',
        read_level: 'Lecture (min.)',
        write_level: 'Écriture (min.)',
        ap_cost: 'Coût en PA',
        pa: 'PA',
        pm: 'PM',
        po: 'PO',
        po_min: 'PO min',
        po_max: 'PO max',
        aoe: 'Zone d’effet',
    };

    return fallbackLabels[fieldKey] || humanizeFieldKey(fieldKey);
};

const getFieldHelper = (fieldKey, fieldConfig) => {
    if (fieldConfig?.help && String(fieldConfig.help).trim() !== '') return fieldConfig.help;

    if (fieldKey === 'read_level') {
        return "Niveau de privilège minimum requis pour consulter ce contenu.";
    }
    if (fieldKey === 'write_level') {
        return "Niveau de privilège minimum requis pour modifier ce contenu.";
    }

    return undefined;
};

const getFieldWrapperClass = (fieldKey) => ([
    'form-field',
    props.differentFields.includes(fieldKey) ? 'opacity-60' : '',
    fieldKey === 'name' ? 'form-field--wide' : '',
    fieldKey === 'description' ? 'form-field--full' : '',
    ['category', 'state'].includes(fieldKey) ? 'form-field--meta-pair' : '',
    fieldKey === 'element' ? 'form-field--full' : '',
].filter(Boolean));

// Soumission du formulaire
const submit = () => {
    // Si l'entité n'a pas d'ID (édition multiple), émettre directement les données
    const entityId = props.entity?.id ?? null;
    if (!entityId && props.isUpdating) {
        // Mode édition multiple : n'émettre que les champs explicitement modifiés.
        // (tri-state => indeterminate = ne pas modifier)
        const data = form.data();
        for (const key of (props.differentFields || [])) {
            const type = fieldsConfig.value?.[key]?.type;
            if (type === 'checkbox') {
                if (!checkboxDirty.value?.[key]) delete data[key];
            } else {
                if (!fieldDirty.value?.[key]) delete data[key];
            }
        }
        emit('submit', data);
        return;
    }

    // Construction du nom de route selon le type d'entité
    // Note: Par défaut, les routes utilisent le pluriel (items, spells, monsters, panoplies)
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    const base = props.routeNameBase || `entities.${entityTypePlural}`;
    const routeName = props.isUpdating ? `${base}.update` : `${base}.store`;
    
    // Paramètres de route selon le type d'entité
    const routeParamKey = props.routeParamKey || props.entityType;
    const routeParams = props.isUpdating ? { [routeParamKey]: entityId } : {};

    const method = props.isUpdating ? 'patch' : 'post';
    
    form[method](route(routeName, routeParams), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success(
                props.isUpdating ? 'Modifications enregistrées avec succès' : 'Création effectuée avec succès',
                { duration: 3000, placement: 'top-right' }
            );
            emit('submit', form.data());
        },
        onError: (errors) => {
            notificationStore.error(
                'Erreur lors de la sauvegarde',
                { duration: 5000, placement: 'top-right' }
            );
            console.error('Erreurs de validation:', errors);
        }
    });
};

// Annulation
const cancel = () => {
    emit('cancel');
    // Ne pas rediriger si c'est dans un modal (le modal gère la fermeture)
    // Seulement rediriger si c'est une page d'édition
    if (props.isUpdating) {
        const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
        const entityId = props.entity?.id ?? null;
        if (entityId) {
            router.visit(route(`entities.${entityTypePlural}.show`, { [props.entityType]: entityId }));
        }
    }
};

</script>

<template>
    <Container class="entity-edit-form">
        <div class="flex flex-col gap-3 mb-4">
            <div class="top-tools-row">
                <div class="top-tools-row__formula rounded-(--radius-field) border border-base-300 bg-base-100/60 px-3 py-2">
                    <FormulaHelpHint placement="bottom-start" />
                </div>

                <div
                    v-if="stateField?.config"
                    class="state-field w-full md:w-[180px] md:min-w-[180px]"
                >
                    <SelectField
                        v-model="form[stateField.key]"
                        @update:model-value="() => markDirty(stateField.key)"
                        :label="getFieldLabel(stateField.key, stateField.config)"
                        :options="stateField.config.options || []"
                        :option-badge="stateField.config.optionBadge || null"
                        :required="stateField.config.required"
                        :validation="getFieldValidation(stateField.key)"
                    />
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submit" class="space-y-5">
            <div v-if="mainFieldSections?.length" class="space-y-5">
                <section
                    v-for="sec in mainFieldSections"
                    :key="sec.id"
                    class="rounded-xl border border-base-300/70 bg-base-100/30 p-3 shadow-sm md:p-4"
                >
                    <div class="mb-3 border-b border-base-300/50 pb-2">
                        <h2 class="text-base font-semibold tracking-tight text-base-content">
                            {{ sec.title }}
                        </h2>
                        <p v-if="sec.subtitle" class="mt-0.5 text-xs leading-snug text-base-content/70 md:text-sm">
                            {{ sec.subtitle }}
                        </p>
                    </div>
                    <div class="form-fields">
                        <template v-for="field in sec.fields" :key="field.key">
                            <div v-if="field?.config" :class="getFieldWrapperClass(field.key)">
                                <EntityEditFormFieldBody
                                    :field="field"
                                    :form="form"
                                    :is-multi-edit="isMultiEdit"
                                    :different-fields="props.differentFields"
                                    :field-dirty="fieldDirty"
                                    :checkbox-dirty="checkboxDirty"
                                    :get-field-label="getFieldLabel"
                                    :get-field-helper="getFieldHelper"
                                    :get-field-validation="getFieldValidation"
                                    :get-field-placeholder="getFieldPlaceholder"
                                    :get-field-render-type="getFieldRenderType"
                                    :get-file-current-path="getFileCurrentPath"
                                    :get-file-accept="getFileAccept"
                                    :format-display-value="formatDisplayValue"
                                    :mark-dirty="markDirty"
                                    :reset-field-multi-edit="resetFieldMultiEdit"
                                    :reset-bool-multi-edit="resetBoolMultiEdit"
                                    :on-checkbox-update="onCheckboxUpdate"
                                    :characteristics-group="characteristicsGroup"
                                />
                            </div>
                        </template>
                    </div>
                </section>
            </div>
            <div v-else class="form-fields">
                <template v-for="field in visibleMainFields" :key="field.key">
                    <div v-if="field?.config" :class="getFieldWrapperClass(field.key)">
                        <EntityEditFormFieldBody
                            :field="field"
                            :form="form"
                            :is-multi-edit="isMultiEdit"
                            :different-fields="props.differentFields"
                            :field-dirty="fieldDirty"
                            :checkbox-dirty="checkboxDirty"
                            :get-field-label="getFieldLabel"
                            :get-field-helper="getFieldHelper"
                            :get-field-validation="getFieldValidation"
                            :get-field-placeholder="getFieldPlaceholder"
                            :get-field-render-type="getFieldRenderType"
                            :get-file-current-path="getFileCurrentPath"
                            :get-file-accept="getFileAccept"
                            :format-display-value="formatDisplayValue"
                            :mark-dirty="markDirty"
                            :reset-field-multi-edit="resetFieldMultiEdit"
                            :reset-bool-multi-edit="resetBoolMultiEdit"
                            :on-checkbox-update="onCheckboxUpdate"
                            :characteristics-group="characteristicsGroup"
                        />
                    </div>
                </template>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-glass-t-md">
                <div class="footer-tools-row">
                    <div
                        v-if="accessLevelFields.length"
                        class="access-levels"
                    >
                        <div
                            v-for="field in accessLevelFields"
                            :key="field.key"
                            class="access-levels__item rounded-(--radius-field) border border-base-300/70 bg-base-100/35 px-2.5 py-1.5"
                        >
                            <SelectField
                                v-model="form[field.key]"
                                @update:model-value="() => markDirty(field.key)"
                                :label="getFieldLabel(field.key, field.config)"
                                :options="field.config.options || []"
                                :required="field.config.required"
                                :helper="getFieldHelper(field.key, field.config)"
                                :validation="getFieldValidation(field.key)"
                            />
                        </div>
                    </div>

                    <div class="footer-actions flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                    <Btn
                        type="button"
                        variant="outline"
                        class="order-1"
                        @click="cancel"
                    >
                        Annuler
                    </Btn>

                    <Tooltip
                        content="Réinitialise le formulaire : revient aux valeurs chargées au moment de l’ouverture (ou dernière synchro). En multi‑édition, remet les champs ‘valeurs différentes’ en mode ‘ne pas modifier’."
                        placement="top"
                    >
                        <Btn
                            type="button"
                            variant="outline"
                            class="order-2"
                            @click="resetForm"
                        >
                            <i class="fa-solid fa-arrow-rotate-left mr-2"></i>
                            Reset
                        </Btn>
                    </Tooltip>

                    <Btn
                        type="submit"
                        color="primary"
                        class="order-3"
                        :disabled="form.processing"
                    >
                        <i class="fa-solid fa-save mr-2"></i>
                        {{ form.processing ? 'Enregistrement...' : (isUpdating ? 'Mettre à jour' : 'Créer') }}
                    </Btn>
                    </div>
                </div>
            </div>
        </form>
    </Container>
</template>

<style scoped lang="scss">
.entity-edit-form {
    .top-tools-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .top-tools-row__formula {
        flex: 1 1 320px;
        min-width: 0;
    }

    .form-fields {
        display: flex;
        flex-wrap: wrap;
        gap: 0.875rem;
    }

    .form-field {
        width: 100%;
    }

    .footer-tools-row {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .footer-actions {
        width: 100%;
    }

    .access-levels {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        width: 100%;
    }

    .access-levels__item {
        width: 100%;
    }

    @media (min-width: 768px) {
        .form-field {
            flex: 1 1 calc(50% - 0.625rem);
            max-width: calc(50% - 0.625rem);
        }

        .form-field--meta-pair {
            flex: 1 1 calc(50% - 0.4375rem);
            max-width: calc(50% - 0.4375rem);
        }

        .form-field--wide {
            flex-basis: calc(66.666% - 0.42rem);
            max-width: calc(66.666% - 0.42rem);
        }

        .form-field--full {
            flex-basis: 100%;
            max-width: 100%;
        }

        .footer-tools-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
        }

        .access-levels {
            width: auto;
            flex: 1 1 auto;
        }

        .access-levels__item {
            width: auto;
            min-width: 220px;
            max-width: 260px;
            flex: 1 1 220px;
        }

        .footer-actions {
            width: auto;
            flex: 0 0 auto;
            justify-content: flex-end;
        }
    }

    :deep(.field-template),
    :deep(.field-template > div),
    :deep(.input),
    :deep(.select),
    :deep(.textarea),
    :deep(.file-input),
    :deep(button.select) {
        width: 100%;
        max-width: none;
    }

    :deep(.input),
    :deep(.select),
    :deep(.file-input),
    :deep(button.select) {
        min-height: 2.75rem;
    }

    :deep(.textarea) {
        min-height: 8rem;
    }

    .state-field :deep(label) {
        display: none;
    }

    .access-levels :deep(label) {
        font-size: 0.75rem;
        opacity: 0.85;
    }

    .state-field :deep(.field-template) {
        gap: 0;
    }

    .state-field :deep(.helper),
    .state-field :deep(.field-template .text-xs),
    .state-field :deep(.field-template .text-sm) {
        display: none;
    }

    .state-field :deep(.select),
    .state-field :deep(input),
    .state-field :deep(button.select) {
        min-height: 2.5rem;
        height: 2.5rem;
        font-size: 0.85rem;
    }

    .access-levels :deep(.select),
    .access-levels :deep(input),
    .access-levels :deep(button.select) {
        min-height: 2rem;
        height: 2rem;
        font-size: 0.8rem;
    }
}
</style>

