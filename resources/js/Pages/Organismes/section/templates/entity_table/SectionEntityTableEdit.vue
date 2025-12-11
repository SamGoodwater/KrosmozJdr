<script setup>
/**
 * SectionEntityTableEdit Template
 * 
 * @description
 * Template de section pour éditer un tableau d'entités en mode écriture.
 * - Formulaire d'édition pour les tableaux d'entités
 * - Auto-save avec debounce
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 * 
 * @emits data-updated - Émis quand les données sont mises à jour
 */
import { ref, watch } from 'vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import { useSectionSave } from '../../composables/useSectionSave';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['data-updated']);

const { saveSection } = useSectionSave();

const localData = ref({
  entity: props.data?.entity || '',
  filters: props.data?.filters || {},
  columns: props.data?.columns || []
});

// Synchroniser avec les props
watch(() => props.data, (newData) => {
  if (newData) {
    localData.value = {
      entity: newData.entity || '',
      filters: newData.filters || {},
      columns: newData.columns || []
    };
  }
}, { deep: true });

// Auto-save avec debounce
watch(localData, (newVal) => {
  const newData = {
    ...props.data,
    ...newVal
  };
  
  // Sauvegarder via le composable
  saveSection(props.section.id, { data: newData });
  
  // Émettre l'événement pour mettre à jour le parent
  emit('data-updated', newData);
}, { deep: true });
</script>

<template>
  <div class="section-entity-table-edit space-y-4">
    <InputField
      v-model="localData.entity"
      label="Type d'entité"
      type="text"
      placeholder="campaign, scenario, character, etc."
      helper="Type d'entité à afficher dans le tableau"
    />
    
    <div class="alert alert-info">
      <i class="fa-solid fa-info-circle"></i>
      <div>
        <p class="text-sm">
          La configuration complète des tableaux d'entités (filtres, colonnes) sera disponible prochainement.
          Pour l'instant, indiquez uniquement le type d'entité.
        </p>
      </div>
    </div>
  </div>
</template>

