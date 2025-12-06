<script setup>
/**
 * SectionEntityTableRead Template
 * 
 * @description
 * Template de section pour afficher un tableau d'entités en mode lecture.
 */
import { computed } from 'vue';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const entities = computed(() => props.data?.entities || []);
const entityType = computed(() => props.data?.entity_type || '');
</script>

<template>
  <div class="section-entity-table-content">
    <div v-if="entities.length > 0">
      <table class="table table-zebra w-full">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <!-- Colonnes dynamiques selon le type d'entité -->
          </tr>
        </thead>
        <tbody>
          <tr v-for="entity in entities" :key="entity.id">
            <td>{{ entity.id }}</td>
            <td>{{ entity.name || entity.title }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p v-else class="text-center text-base-content/50 italic py-8">
      Aucune entité à afficher
    </p>
  </div>
</template>

