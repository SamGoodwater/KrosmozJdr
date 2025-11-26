<script setup>
/**
 * Section de recherche et prévisualisation d'entités
 */
import { ref, computed } from 'vue';
import Card from '@/Pages/Atoms/data-display/Card.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import EntityDiffTable from './EntityDiffTable.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const props = defineProps({
    entityType: {
        type: String,
        required: true,
    },
    maxId: {
        type: Number,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['preview', 'import', 'simulate']);

const notificationStore = useNotificationStore();
const { error: showError } = notificationStore;

// Mode de recherche
const searchMode = ref('single'); // 'single', 'range', 'all'
const singleId = ref('');
const rangeStart = ref('');
const rangeEnd = ref('');

// État de prévisualisation
const previewData = ref(null);
const previewLoading = ref(false);

const isValidSingleId = computed(() => {
    const id = parseInt(singleId.value);
    return !isNaN(id) && id >= 1 && id <= props.maxId;
});

const isValidRange = computed(() => {
    const start = parseInt(rangeStart.value);
    const end = parseInt(rangeEnd.value);
    if (isNaN(start) || isNaN(end)) return false;
    if (start < 1 || end > props.maxId) return false;
    return start <= end;
});

const rangeCount = computed(() => {
    if (!isValidRange.value) return 0;
    const start = parseInt(rangeStart.value);
    const end = parseInt(rangeEnd.value);
    return end - start + 1;
});

const stringify = (obj) => JSON.stringify(obj, null, 2);

const handlePreview = async () => {
    if (searchMode.value === 'single' && !isValidSingleId.value) {
        showError('ID invalide pour ce type d\'entité');
        return;
    }

    previewLoading.value = true;
    previewData.value = null;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            showError('Token CSRF introuvable');
            return;
        }

        let url = '';
        if (searchMode.value === 'single') {
            url = `/api/scrapping/preview/${props.entityType}/${singleId.value}`;
        } else {
            // Pour les plages, on prévisualise le premier ID
            url = `/api/scrapping/preview/${props.entityType}/${rangeStart.value}`;
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (response.ok && data.success) {
            previewData.value = data.data;
            emit('preview', data.data);
        } else {
            showError(data.message || 'Impossible de prévisualiser cette entité');
        }
    } catch (err) {
        showError('Erreur lors de la prévisualisation : ' + err.message);
    } finally {
        previewLoading.value = false;
    }
};

const handleSimulate = () => {
    if (searchMode.value === 'single' && !isValidSingleId.value) {
        showError('ID invalide pour la simulation');
        return;
    }
    if (searchMode.value === 'range' && !isValidRange.value) {
        showError('Plage invalide pour la simulation');
        return;
    }

    emit('simulate', {
        mode: searchMode.value,
        entityType: props.entityType,
        singleId: searchMode.value === 'single' ? parseInt(singleId.value) : null,
        rangeStart: searchMode.value === 'range' ? parseInt(rangeStart.value) : null,
        rangeEnd: searchMode.value === 'range' ? parseInt(rangeEnd.value) : null,
    });
};

const handleImport = () => {
    if (searchMode.value === 'single' && !isValidSingleId.value) {
        showError('ID invalide pour l\'import');
        return;
    }
    if (searchMode.value === 'range' && !isValidRange.value) {
        showError('Plage invalide pour l\'import');
        return;
    }

    emit('import', {
        mode: searchMode.value,
        entityType: props.entityType,
        singleId: searchMode.value === 'single' ? parseInt(singleId.value) : null,
        rangeStart: searchMode.value === 'range' ? parseInt(rangeStart.value) : null,
        rangeEnd: searchMode.value === 'range' ? parseInt(rangeEnd.value) : null,
    });
};
</script>

<template>
    <Card class="p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-primary-100 mb-2">Recherche & Prévisualisation</h2>
            <p class="text-sm text-primary-300">
                Recherchez une entité, prévisualisez-la et comparez-la avec la version en base avant d'importer.
            </p>
        </div>

        <!-- Mode de recherche -->
        <div class="space-y-4">
            <div class="flex gap-2">
                <Btn
                    :color="searchMode === 'single' ? 'primary' : 'ghost'"
                    size="sm"
                    @click="searchMode = 'single'"
                >
                    ID unique
                </Btn>
                <Btn
                    :color="searchMode === 'range' ? 'primary' : 'ghost'"
                    size="sm"
                    @click="searchMode = 'range'"
                >
                    Plage d'ID
                </Btn>
                <Btn
                    :color="searchMode === 'all' ? 'primary' : 'ghost'"
                    size="sm"
                    @click="searchMode = 'all'"
                >
                    Import complet
                </Btn>
            </div>

            <!-- ID unique -->
            <div v-if="searchMode === 'single'" class="space-y-4">
                <InputField
                    v-model="singleId"
                    type="number"
                    label="ID de l'entité"
                    :min="1"
                    :max="maxId"
                    placeholder="Ex: 1"
                />
                <div class="flex gap-2">
                    <Btn
                        color="primary"
                        :disabled="!isValidSingleId || previewLoading"
                        @click="handlePreview"
                    >
                        <Loading v-if="previewLoading" class="mr-2" />
                        <Icon v-else icon="fa-eye" pack="solid" class="mr-2" />
                        Prévisualiser
                    </Btn>
                    <Btn
                        color="secondary"
                        :disabled="!isValidSingleId || loading"
                        @click="handleSimulate"
                    >
                        <Icon icon="fa-flask" pack="solid" class="mr-2" />
                        Simuler
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="!isValidSingleId || loading"
                        @click="handleImport"
                    >
                        <Icon icon="fa-download" pack="solid" class="mr-2" />
                        Importer
                    </Btn>
                </div>
            </div>

            <!-- Plage d'ID -->
            <div v-if="searchMode === 'range'" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <InputField
                        v-model="rangeStart"
                        type="number"
                        label="ID de début"
                        :min="1"
                        :max="maxId"
                        placeholder="Ex: 10"
                    />
                    <InputField
                        v-model="rangeEnd"
                        type="number"
                        label="ID de fin"
                        :min="1"
                        :max="maxId"
                        placeholder="Ex: 20"
                    />
                </div>
                <div v-if="isValidRange" class="rounded-lg border border-base-300 bg-base-200/40 p-3 text-sm text-primary-200">
                    {{ rangeCount }} entité(s) seront importées
                </div>
                <div class="flex gap-2">
                    <Btn
                        color="secondary"
                        :disabled="!isValidRange || loading"
                        @click="handleSimulate"
                    >
                        <Icon icon="fa-flask" pack="solid" class="mr-2" />
                        Simuler
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="!isValidRange || loading"
                        @click="handleImport"
                    >
                        <Icon icon="fa-download" pack="solid" class="mr-2" />
                        Importer la plage
                    </Btn>
                </div>
            </div>

            <!-- Import complet -->
            <div v-if="searchMode === 'all'" class="space-y-4">
                <Alert color="warning" class="text-sm">
                    Cette opération importera toutes les entités de type <strong>{{ entityType }}</strong> ({{ maxId }} entités max).
                    Cela peut prendre plusieurs minutes.
                </Alert>
                <div class="flex gap-2">
                    <Btn
                        color="secondary"
                        :disabled="loading"
                        @click="handleSimulate"
                    >
                        <Icon icon="fa-flask" pack="solid" class="mr-2" />
                        Simuler l'import complet
                    </Btn>
                    <Btn
                        color="success"
                        :disabled="loading"
                        @click="handleImport"
                    >
                        <Icon icon="fa-database" pack="solid" class="mr-2" />
                        Importer toutes les entités
                    </Btn>
                </div>
            </div>
        </div>

            <!-- Résultat de prévisualisation -->
        <div v-if="previewData" class="space-y-4 border-t border-base-300 pt-4">
            <h3 class="font-semibold text-primary-100">Résultat de la prévisualisation</h3>

            <!-- Informations sur les relations -->
            <div v-if="previewData.raw" class="mb-4 p-3 bg-info/10 border border-info/30 rounded text-sm">
                <p class="font-semibold text-info mb-2 flex items-center gap-2">
                    <Icon icon="fa-info-circle" pack="solid" class="text-xs" />
                    Relations détectées
                </p>
                <div class="space-y-1 text-xs text-primary-200">
                    <div v-if="previewData.raw.spells && previewData.raw.spells.length > 0" class="flex items-center gap-2">
                        <Icon icon="fa-wand-magic-sparkles" pack="solid" class="text-xs" />
                        <span>{{ previewData.raw.spells.length }} sort(s) associé(s)</span>
                    </div>
                    <div v-if="previewData.raw.drops && previewData.raw.drops.length > 0" class="flex items-center gap-2">
                        <Icon icon="fa-gem" pack="solid" class="text-xs" />
                        <span>{{ previewData.raw.drops.length }} ressource(s) droppée(s)</span>
                    </div>
                    <div v-if="previewData.raw.recipe && previewData.raw.recipe.length > 0" class="flex items-center gap-2">
                        <Icon icon="fa-book" pack="solid" class="text-xs" />
                        <span>{{ previewData.raw.recipe.length }} ressource(s) dans la recette</span>
                    </div>
                    <div v-if="previewData.raw.summon" class="flex items-center gap-2">
                        <Icon icon="fa-dragon" pack="solid" class="text-xs" />
                        <span>Monstre invoqué (ID: {{ previewData.raw.summon.id }})</span>
                    </div>
                    <p v-if="!previewData.raw.spells?.length && !previewData.raw.drops?.length && !previewData.raw.recipe?.length && !previewData.raw.summon" class="text-primary-300 italic">
                        Aucune relation détectée
                    </p>
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-2">
                <div>
                    <h4 class="font-semibold text-primary-100 mb-2 text-sm">Version DofusDB convertie</h4>
                    <pre class="text-xs bg-base-300/50 p-3 rounded max-h-80 overflow-auto max-w-full break-words whitespace-pre-wrap">{{ stringify(previewData.converted) }}</pre>
                </div>
                <div>
                    <h4 class="font-semibold text-primary-100 mb-2 text-sm">Version actuelle (base Krosmoz)</h4>
                    <template v-if="previewData.existing">
                        <pre class="text-xs bg-base-300/50 p-3 rounded max-h-80 overflow-auto max-w-full break-words whitespace-pre-wrap">{{ stringify(previewData.existing) }}</pre>
                    </template>
                    <p v-else class="text-sm text-primary-300 italic">
                        Aucune donnée existante. L'import créera une nouvelle entrée.
                    </p>
                </div>
            </div>

            <!-- Comparaison -->
            <div v-if="previewData.existing" class="mt-4">
                <h4 class="font-semibold text-primary-100 mb-2 text-sm">Comparaison</h4>
                <EntityDiffTable
                    :existing="previewData.existing.record"
                    :incoming="previewData.converted"
                />
            </div>

            <!-- Actions depuis la prévisualisation -->
            <div class="flex gap-2 pt-4 border-t border-base-300">
                <Btn
                    color="success"
                    :disabled="loading"
                    @click="handleImport"
                >
                    <Icon icon="fa-arrow-rotate-right" pack="solid" class="mr-2" />
                    Importer cette version
                </Btn>
            </div>
        </div>
    </Card>
</template>

