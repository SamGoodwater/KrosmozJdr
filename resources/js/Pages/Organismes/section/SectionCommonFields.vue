<script setup>
/**
 * SectionCommonFields Component
 * 
 * @description
 * Composant réutilisable pour les champs communs des formulaires de sections :
 * - Titre
 * - Slug
 * - Visibilité
 * - Rôle requis pour éditer
 * - État
 * - Ordre (optionnel)
 * 
 * Utilisé dans CreateSectionModal et SectionParamsModal pour éviter la duplication.
 * 
 * @props {Object} form - Objet formulaire Inertia
 * @props {Boolean} showOrder - Afficher le champ ordre
 * @props {Boolean} showAdvanced - Afficher les champs avancés (can_edit_role, state)
 * @props {Array} visibilityOptions - Options pour le select de visibilité
 * @props {Array} stateOptions - Options pour le select d'état
 */
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object, // Inertia form object
        required: true,
    },
    showOrder: {
        type: Boolean,
        default: false,
    },
    showAdvanced: {
        type: Boolean,
        default: false,
    },
    visibilityOptions: {
        type: Array,
        default: () => [],
    },
    stateOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['slug-input', 'update:title', 'update:slug', 'update:order', 'update:isVisible', 'update:canEditRole', 'update:state']);

// Computed avec getter/setter pour éviter les mutations directes de props
const title = computed({
    get: () => props.form.title,
    set: (value) => emit('update:title', value)
});

const slug = computed({
    get: () => props.form.slug,
    set: (value) => emit('update:slug', value)
});

const order = computed({
    get: () => props.form.order,
    set: (value) => emit('update:order', value)
});

const isVisible = computed({
    get: () => props.form.is_visible,
    set: (value) => emit('update:isVisible', value)
});

const canEditRole = computed({
    get: () => props.form.can_edit_role,
    set: (value) => emit('update:canEditRole', value)
});

const state = computed({
    get: () => props.form.state,
    set: (value) => emit('update:state', value)
});

// Validation computed pour chaque champ
const titleValidation = computed(() => {
    if (!props.form.errors.title) return null;
    return {
        state: 'error',
        message: props.form.errors.title,
        showNotification: false
    };
});

const slugValidation = computed(() => {
    if (!props.form.errors.slug) return null;
    return {
        state: 'error',
        message: props.form.errors.slug,
        showNotification: false
    };
});

const orderValidation = computed(() => {
    if (!props.form.errors.order) return null;
    return {
        state: 'error',
        message: props.form.errors.order,
        showNotification: false
    };
});

const handleSlugInput = () => {
    emit('slug-input');
};
</script>

<template>
    <!-- Titre -->
    <InputField
        v-model="title"
        label="Titre"
        type="text"
        :validation="titleValidation"
        placeholder="Titre de la section"
        helper="Le titre de la section (optionnel)"
    />
    
    <!-- Slug -->
    <InputField
        v-model="slug"
        label="Slug"
        type="text"
        :validation="slugValidation"
        placeholder="url-de-la-section"
        helper="L'URL de la section (généré automatiquement depuis le titre)"
        @input="handleSlugInput"
    />
    
    <!-- Ordre (optionnel) -->
    <InputField
        v-if="showOrder"
        v-model="order"
        label="Ordre"
        type="number"
        min="0"
        :validation="orderValidation"
        helper="Ordre d'affichage de la section (0 = premier)"
    />
    
    <!-- Visibilité -->
    <SelectField
        v-model="isVisible"
        label="Visibilité"
        :options="visibilityOptions"
        helper="Qui peut voir cette section ?"
    />
    
    <!-- Champs avancés -->
    <template v-if="showAdvanced">
        <!-- Rôle requis pour modifier -->
        <SelectField
            v-model="canEditRole"
            label="Rôle requis pour modifier"
            :options="visibilityOptions"
            helper="Rôle minimum requis pour modifier cette section"
        />
        
        <!-- État -->
        <SelectField
            v-model="state"
            label="État"
            :options="stateOptions"
            helper="État de publication de la section"
        />
    </template>
</template>

