<script setup>
/**
 * Rendu public d'une section CharacteristicNorms.
 * Charge les normes via l'API et affiche le NormsViewer interactif.
 */
import { computed, onMounted, ref, watch } from 'vue';
import NormsViewer from '@/Pages/Organismes/data-display/NormsViewer.vue';
import axios from 'axios';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const loading = ref(false);
const error = ref(null);
const normsData = ref(null);

const characteristicKey = computed(() => props.settings?.characteristic_key || '');
const entity = computed(() => props.settings?.entity || '*');

async function fetchNorms() {
    if (!characteristicKey.value) {
        error.value = 'Aucune caractéristique configurée.';
        return;
    }
    loading.value = true;
    error.value = null;
    try {
        const url = `/api/characteristics/${encodeURIComponent(characteristicKey.value)}/norms/${encodeURIComponent(entity.value)}`;
        const { data } = await axios.get(url);
        normsData.value = data;
    } catch (e) {
        error.value = e.response?.data?.error || 'Erreur lors du chargement des normes.';
    } finally {
        loading.value = false;
    }
}

onMounted(fetchNorms);
watch([characteristicKey, entity], fetchNorms);
</script>

<template>
    <div class="section-characteristic-norms">
        <div v-if="loading" class="flex items-center justify-center py-8">
            <span class="loading loading-spinner loading-md" />
        </div>

        <div v-else-if="error" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>{{ error }}</span>
        </div>

        <div v-else-if="!normsData?.norms" class="alert alert-info">
            <i class="fa-solid fa-info-circle" />
            <span>Aucune norme définie pour cette caractéristique.</span>
        </div>

        <NormsViewer
            v-else
            :grid="normsData.norms.grid"
            :conditions="normsData.norms.conditions || []"
            :description="normsData.norms.description || ''"
            :min-limit="normsData.norms.limits?.min ?? null"
            :max-limit="normsData.norms.limits?.max ?? null"
            :characteristic-name="normsData.characteristic?.name || ''"
            :characteristic-color="normsData.characteristic?.color || '#6366f1'"
            :available-characteristics="normsData.available_characteristics || {}"
        />
    </div>
</template>
