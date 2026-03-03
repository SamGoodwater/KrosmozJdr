<script setup>
/**
 * SectionEntityTableRead Template
 *
 * Affiche un tableau d'entités chargé depuis l'API tables (api.tables.{entity}).
 * Utilise settings.entity, settings.filters et settings.limit pour la requête.
 */
import { ref, computed, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
});

const entities = ref([]);
const loading = ref(false);
const error = ref(null);

const entityType = computed(() => {
  return props.settings?.entity || props.data?.entity || 'spells';
});

const filters = computed(() => {
  const raw = props.settings?.filters ?? props.data?.filters;
  if (typeof raw === 'string') {
    try {
      return raw.trim() ? JSON.parse(raw) : {};
    } catch {
      return {};
    }
  }
  return typeof raw === 'object' && raw !== null ? raw : {};
});

const limit = computed(() => {
  const n = props.settings?.limit ?? props.data?.limit ?? 50;
  const num = Number(n);
  return Number.isFinite(num) ? Math.min(500, Math.max(1, num)) : 50;
});

const tableRoute = computed(() => {
  const name = `api.tables.${entityType.value}`;
  try {
    return route(name);
  } catch {
    return null;
  }
});

const entityShowRouteName = computed(() => {
  const map = {
    spells: 'entities.spells.show',
    monsters: 'entities.monsters.show',
    npcs: 'entities.npcs.show',
    campaigns: 'entities.campaigns.show',
    scenarios: 'entities.scenarios.show',
    shops: 'entities.shops.show',
    breeds: 'entities.breeds.show',
    specializations: 'entities.specializations.show',
    attributes: 'entities.attributes.show',
    capabilities: 'entities.capabilities.show',
    consumables: 'entities.consumables.show',
    items: 'entities.items.show',
    resources: 'entities.resources.show',
    panoplies: 'entities.panoplies.show',
  };
  return map[entityType.value] || null;
});

function fetchEntities() {
  if (!tableRoute.value) {
    error.value = 'Type d\'entité inconnu';
    return;
  }
  loading.value = true;
  error.value = null;
  axios
    .get(tableRoute.value, {
      params: {
        format: 'entities',
        limit: limit.value,
        filters: Object.keys(filters.value).length ? filters.value : undefined,
      },
    })
    .then((res) => {
      entities.value = res.data?.entities ?? [];
    })
    .catch((err) => {
      error.value = err.response?.data?.message || err.message || 'Erreur lors du chargement';
      entities.value = [];
    })
    .finally(() => {
      loading.value = false;
    });
}

onMounted(fetchEntities);
watch([entityType, filters, limit], fetchEntities, { deep: true });

function entityHref(entity) {
  if (!entityShowRouteName.value || !entity?.id) return null;
  try {
    return route(entityShowRouteName.value, entity.id);
  } catch {
    return null;
  }
}

function entityLabel(entity) {
  return entity?.name ?? entity?.title ?? `#${entity?.id ?? ''}`;
}
</script>

<template>
  <div class="section-entity-table-content">
    <div v-if="loading" class="flex justify-center py-8">
      <span class="loading loading-spinner loading-lg"></span>
    </div>
    <div v-else-if="error" class="alert alert-warning">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span>{{ error }}</span>
    </div>
    <div v-else-if="entities.length > 0" class="overflow-x-auto">
      <table class="table table-zebra table-sm w-full">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="entity in entities" :key="entity.id">
            <td>{{ entity.id }}</td>
            <td>
              <a
                v-if="entityHref(entity)"
                :href="entityHref(entity)"
                class="link link-hover"
                @click.prevent="router.visit(entityHref(entity))"
              >
                {{ entityLabel(entity) }}
              </a>
              <span v-else>{{ entityLabel(entity) }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <p v-else class="text-center text-base-content/50 italic py-8">
      Aucune entité à afficher
    </p>
  </div>
</template>
