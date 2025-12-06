<script setup>
/**
 * SectionEntityTableEdit Template
 * 
 * @description
 * Template de section pour éditer un tableau d'entités en mode écriture.
 * Formulaire d'édition pour les tableaux d'entités (non compatible auto-save pour l'instant).
 */
import { ref, watch } from 'vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const emit = defineEmits(['update:data']);

const localData = ref({
  entity: props.data.entity || '',
  filters: props.data.filters || {},
  columns: props.data.columns || []
});

watch(() => props.data, (newData) => {
  localData.value = {
    entity: newData.entity || '',
    filters: newData.filters || {},
    columns: newData.columns || []
  };
}, { deep: true });

watch(localData, (newVal) => {
  emit('update:data', { ...props.data, ...newVal });
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

