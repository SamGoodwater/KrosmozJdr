<script setup>
/**
 * Page principale de gestion du Scrapping
 * 
 * Interface restructurée avec composants modulaires pour une meilleure maintenabilité
 */
import { Head } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

import Container from '@/Pages/Atoms/data-display/Container.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';

// Composants modulaires
import EntityTypeSelector from './components/EntityTypeSelector.vue';
import SearchPreviewSection from './components/SearchPreviewSection.vue';
import ImportOptionsSection from './components/ImportOptionsSection.vue';
import HistorySection from './components/HistorySection.vue';

const { setPageTitle } = usePageTitle();
const notificationStore = useNotificationStore();
const { success, error: showError } = notificationStore;

// État réactif
const loading = ref(false);
const metaLoading = ref(true);
const entityTypes = ref([]);
const selectedEntityType = ref('class');
const importOptions = ref({
    skipCache: false,
    forceUpdate: false,
    dryRun: false,
    validateOnly: false,
    includeRelations: true, // Import des relations imbriquées (sorts, ressources, recettes, etc.)
});
const results = ref([]);

// Computed
const selectedEntity = computed(() => {
    return entityTypes.value.find(e => e.value === selectedEntityType.value);
});

const maxId = computed(() => {
    return selectedEntity.value?.maxId || 0;
});

// Méthodes
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

const loadEntityMeta = async () => {
    metaLoading.value = true;
    try {
        const response = await fetch('/api/scrapping/meta', {
            headers: {
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        if (import.meta.env.DEV) {
            console.log('Index.vue - API response:', data);
        }

        if (response.ok && data.success) {
            // Transformer les données pour correspondre au format attendu
            entityTypes.value = data.data.map(item => ({
                value: item.type,
                label: item.label,
                icon: getIconForType(item.type),
                maxId: item.maxId,
            }));

            if (import.meta.env.DEV) {
                console.log('Index.vue - entityTypes after mapping:', entityTypes.value);
            }

            // Définir le type par défaut si disponible
            if (entityTypes.value.length > 0 && !entityTypes.value.find(e => e.value === selectedEntityType.value)) {
                selectedEntityType.value = entityTypes.value[0].value;
            }
        } else {
            if (import.meta.env.DEV) {
                console.error('Index.vue - API error:', data);
            }
            showError('Impossible de charger les métadonnées des entités');
            // Fallback sur les valeurs par défaut
            entityTypes.value = [
                { value: 'class', label: 'Classe', icon: 'fa-user', maxId: 19 },
                { value: 'monster', label: 'Monstre', icon: 'fa-dragon', maxId: 5000 },
                { value: 'item', label: 'Objet', icon: 'fa-box', maxId: 30000 },
                { value: 'spell', label: 'Sort', icon: 'fa-wand-magic-sparkles', maxId: 20000 },
            ];
        }
    } catch (err) {
        if (import.meta.env.DEV) {
            console.error('Index.vue - Fetch error:', err);
        }
        showError('Erreur lors du chargement des métadonnées : ' + err.message);
        // Fallback sur les valeurs par défaut
        entityTypes.value = [
            { value: 'class', label: 'Classe', icon: 'fa-user', maxId: 19 },
            { value: 'monster', label: 'Monstre', icon: 'fa-dragon', maxId: 5000 },
            { value: 'item', label: 'Objet', icon: 'fa-box', maxId: 30000 },
            { value: 'spell', label: 'Sort', icon: 'fa-wand-magic-sparkles', maxId: 20000 },
        ];
    } finally {
        metaLoading.value = false;
        if (import.meta.env.DEV) {
            console.log('Index.vue - metaLoading set to false, entityTypes:', entityTypes.value);
        }
    }
};

const getIconForType = (type) => {
    const icons = {
        class: 'fa-user',
        monster: 'fa-dragon',
        item: 'fa-box',
        spell: 'fa-wand-magic-sparkles',
        panoply: 'fa-layer-group',
    };
    return icons[type] || 'fa-question';
};

const handlePreview = (data) => {
    // La prévisualisation est gérée dans SearchPreviewSection
    // On peut ajouter des actions supplémentaires ici si nécessaire
};

const handleSimulate = async (params) => {
    loading.value = true;
    const csrfToken = getCsrfToken();

    if (!csrfToken) {
        showError('Token CSRF introuvable. Veuillez recharger la page.');
        loading.value = false;
        return;
    }

    try {
        let url = '';
        let payload = {
            ...importOptions.value,
            skip_cache: importOptions.value.skipCache,
            force_update: importOptions.value.forceUpdate,
            dry_run: true, // Force le dry-run pour la simulation
            validate_only: importOptions.value.validateOnly,
            include_relations: importOptions.value.includeRelations,
        };

        if (params.mode === 'single') {
            url = `/api/scrapping/import/${params.entityType}/${params.singleId}`;
        } else if (params.mode === 'range') {
            url = '/api/scrapping/import/range';
            payload = {
                ...payload,
                type: params.entityType,
                start_id: params.rangeStart,
                end_id: params.rangeEnd,
            };
        } else if (params.mode === 'all') {
            url = '/api/scrapping/import/all';
            payload = {
                ...payload,
                type: params.entityType,
            };
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (response.ok) {
            const summary = data.summary || { total: 1, success: data.success ? 1 : 0, errors: data.success ? 0 : 1 };
            success(`Simulation terminée : ${summary.success}/${summary.total} entités réussies`);
            
            results.value.unshift({
                type: params.mode === 'single' ? 'individual' : params.mode,
                entityType: params.entityType,
                entityId: params.singleId,
                range: params.mode === 'range' ? { start: params.rangeStart, end: params.rangeEnd } : null,
                result: data,
                timestamp: new Date().toISOString(),
                error: !data.success,
                simulated: true,
            });
        } else {
            showError(data.message || 'Erreur lors de la simulation');
        }
    } catch (err) {
        showError('Erreur lors de la simulation : ' + err.message);
    } finally {
        loading.value = false;
    }
};

const handleImport = async (params) => {
    loading.value = true;
    const csrfToken = getCsrfToken();

    if (!csrfToken) {
        showError('Token CSRF introuvable. Veuillez recharger la page.');
        loading.value = false;
        return;
    }

    try {
        let url = '';
        let payload = {
            skip_cache: importOptions.value.skipCache,
            force_update: importOptions.value.forceUpdate,
            dry_run: importOptions.value.dryRun,
            validate_only: importOptions.value.validateOnly,
            include_relations: importOptions.value.includeRelations,
        };

        if (params.mode === 'single') {
            url = `/api/scrapping/import/${params.entityType}/${params.singleId}`;
        } else if (params.mode === 'range') {
            url = '/api/scrapping/import/range';
            payload = {
                ...payload,
                type: params.entityType,
                start_id: params.rangeStart,
                end_id: params.rangeEnd,
            };
        } else if (params.mode === 'all') {
            url = '/api/scrapping/import/all';
            payload = {
                ...payload,
                type: params.entityType,
            };
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (response.ok && data.success !== false) {
            success(data.message || 'Import réussi');
            
            results.value.unshift({
                type: params.mode === 'single' ? 'individual' : params.mode,
                entityType: params.entityType,
                entityId: params.singleId,
                range: params.mode === 'range' ? { start: params.rangeStart, end: params.rangeEnd } : null,
                result: data,
                timestamp: new Date().toISOString(),
                error: false,
            });
        } else {
            showError(data.message || 'Erreur lors de l\'import');
            
            results.value.unshift({
                type: params.mode === 'single' ? 'individual' : params.mode,
                entityType: params.entityType,
                entityId: params.singleId,
                range: params.mode === 'range' ? { start: params.rangeStart, end: params.rangeEnd } : null,
                result: data,
                timestamp: new Date().toISOString(),
                error: true,
            });
        }
    } catch (err) {
        showError('Erreur lors de l\'import : ' + err.message);
        
        results.value.unshift({
            type: params.mode === 'single' ? 'individual' : params.mode,
            entityType: params.entityType,
            entityId: params.singleId,
            range: params.mode === 'range' ? { start: params.rangeStart, end: params.rangeEnd } : null,
            error: true,
            errorMessage: err.message,
            timestamp: new Date().toISOString(),
        });
    } finally {
        loading.value = false;
    }
};

const clearHistory = () => {
    results.value = [];
};

onMounted(async () => {
    setPageTitle('Gestion du Scrapping');
    await loadEntityMeta();
});
</script>

<template>
    <Head title="Gestion du Scrapping" />
    
    <Container class="space-y-6 pb-8">
        <!-- En-tête -->
        <div class="flex flex-col gap-2 md:flex-row md:justify-between md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Gestion du Scrapping</h1>
                <p class="text-primary-200 mt-2">Importez des données depuis DofusDB vers KrosmozJDR</p>
            </div>
        </div>

        <!-- Loading initial -->
        <div v-if="metaLoading" class="flex justify-center items-center py-12">
            <Loading />
            <span class="ml-3 text-primary-300">Chargement des métadonnées...</span>
        </div>

        <!-- Contenu principal -->
        <div v-else class="space-y-4">
            <!-- Sélecteur de type d'entité et options -->
            <div class="space-y-3">
                <EntityTypeSelector
                    v-model="selectedEntityType"
                    :entity-types="entityTypes"
                    :loading="loading"
                />

                <!-- Options d'import globales -->
                <ImportOptionsSection
                    v-model="importOptions"
                />
            </div>

            <!-- Section recherche et prévisualisation -->
            <SearchPreviewSection
                :entity-type="selectedEntityType"
                :max-id="maxId"
                :loading="loading"
                @preview="handlePreview"
                @simulate="handleSimulate"
                @import="handleImport"
            />

            <!-- Historique des imports -->
            <HistorySection
                :results="results"
                @clear="clearHistory"
            />
        </div>
    </Container>
</template>
