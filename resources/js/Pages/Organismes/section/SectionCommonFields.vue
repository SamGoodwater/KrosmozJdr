<script setup>
/**
 * SectionCommonFields Component
 * 
 * @description
 * Composant réutilisable pour les champs communs des formulaires de sections :
 * - Titre
 * - Slug
 * - Lecture (min.)
 * - Écriture (min.)
 * - État
 * - Ordre (optionnel)
 * 
 * Utilisé dans CreateSectionModal et SectionParamsModal pour éviter la duplication.
 * 
 * @props {Object} form - Objet formulaire Inertia
 * @props {Boolean} showOrder - Afficher le champ ordre
 * @props {Boolean} showAdvanced - Afficher les champs avancés (write_level, state)
 * @props {Array} roleOptions - Options pour les selects read_level/write_level
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
    roleOptions: {
        type: Array,
        default: () => [],
    },
    stateOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits([
    'slug-input',
    'update:title',
    'update:slug',
    'update:order',
    'update:readLevel',
    'update:writeLevel',
    'update:state'
]);

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

const readLevel = computed({
    get: () => props.form.read_level,
    set: (value) => emit('update:readLevel', value)
});

const writeLevel = computed({
    get: () => props.form.write_level,
    set: (value) => emit('update:writeLevel', value)
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
    
    <!-- Lecture (min.) -->
    <SelectField
        v-model="readLevel"
        label="Lecture (min.)"
        :options="roleOptions"
        helper="Qui peut voir cette section ?"
    />
    
    <!-- Champs avancés -->
    <template v-if="showAdvanced">
        <!-- Écriture (min.) -->
        <SelectField
            v-model="writeLevel"
            label="Écriture (min.)"
            :options="roleOptions"
            helper="Rôle minimum requis pour modifier cette section"
        />
        
        <!-- État -->
        <SelectField
            v-model="state"
            label="État"
            :options="stateOptions"
            helper="Cycle de vie de la section"
        />
    </template>
</template>

