<script setup>
/**
 * Page de gestion du Scrapping
 * 
 * Interface pour importer des données depuis DofusDB vers KrosmozJDR
 * avec toutes les options disponibles (skip_cache, force_update, dry_run, validate_only)
 * 
 * @description
 * - Import individuel : classe, monstre, objet, sort
 * - Import en lot depuis un fichier JSON
 * - Options d'import configurables
 * - Affichage des résultats en temps réel
 * - Historique des imports
 */
import { Head, usePage, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

// Atoms & Molecules
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Card from '@/Pages/Atoms/data-display/Card.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import CheckboxField from '@/Pages/Molecules/data-input/CheckboxField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import Tab from '@/Pages/Molecules/navigation/Tab.vue';
import TabItem from '@/Pages/Atoms/navigation/TabItem.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

const page = usePage();
const { setPageTitle } = usePageTitle();

// Store de notifications
const notificationStore = useNotificationStore();
const { success, error, info } = notificationStore;

// État réactif
const activeTab = ref('individual');
const loading = ref(false);
const results = ref([]);

// Formulaire d'import individuel
const individualForm = ref({
    entityType: 'class',
    entityId: '',
    skipCache: false,
    forceUpdate: false,
    dryRun: false,
    validateOnly: false,
});

// Formulaire d'import en lot
const batchForm = ref({
    entitiesJson: '',
    skipCache: false,
    forceUpdate: false,
    dryRun: false,
    validateOnly: false,
});

// Options d'entités
const entityTypes = [
    { value: 'class', label: 'Classe', icon: 'fa-user', maxId: 19 },
    { value: 'monster', label: 'Monstre', icon: 'fa-dragon', maxId: 5000 },
    { value: 'item', label: 'Objet', icon: 'fa-box', maxId: 30000 },
    { value: 'spell', label: 'Sort', icon: 'fa-wand-magic-sparkles', maxId: 20000 },
];

// Computed
const currentEntityType = computed(() => {
    return entityTypes.find(e => e.value === individualForm.value.entityType);
});

const isValidEntityId = computed(() => {
    const id = parseInt(individualForm.value.entityId);
    if (!currentEntityType.value || isNaN(id)) return false;
    return id >= 1 && id <= currentEntityType.value.maxId;
});

const isValidBatchJson = computed(() => {
    if (!batchForm.value.entitiesJson.trim()) return false;
    try {
        const parsed = JSON.parse(batchForm.value.entitiesJson);
        return Array.isArray(parsed) && parsed.length > 0;
    } catch {
        return false;
    }
});

// Méthodes
const importIndividual = async () => {
    if (!isValidEntityId.value) {
        error('ID invalide pour ce type d\'entité');
        return;
    }

    loading.value = true;
    const options = {
        skip_cache: individualForm.value.skipCache,
        force_update: individualForm.value.forceUpdate,
        dry_run: individualForm.value.dryRun,
        validate_only: individualForm.value.validateOnly,
    };

    try {
        // Récupérer le token CSRF depuis le meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            error('Token CSRF introuvable. Veuillez recharger la page.');
            loading.value = false;
            return;
        }
        
        const response = await fetch(`/api/scrapping/import/${individualForm.value.entityType}/${individualForm.value.entityId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(options),
        });

        const data = await response.json();
        
        if (data.success) {
            success(data.message || 'Import réussi');
            results.value.unshift({
                type: 'individual',
                entityType: individualForm.value.entityType,
                entityId: individualForm.value.entityId,
                result: data,
                timestamp: new Date().toISOString(),
            });
        } else {
            error(data.message || 'Erreur lors de l\'import');
            results.value.unshift({
                type: 'individual',
                entityType: individualForm.value.entityType,
                entityId: individualForm.value.entityId,
                result: data,
                error: true,
                timestamp: new Date().toISOString(),
            });
        }
    } catch (err) {
        error('Erreur lors de l\'import : ' + err.message);
        results.value.unshift({
            type: 'individual',
            entityType: individualForm.value.entityType,
            entityId: individualForm.value.entityId,
            error: true,
            errorMessage: err.message,
            timestamp: new Date().toISOString(),
        });
    } finally {
        loading.value = false;
    }
};

const importBatch = async () => {
    if (!isValidBatchJson.value) {
        error('JSON invalide. Format attendu : [{"type": "class", "id": 1}, ...]');
        return;
    }

    loading.value = true;
    const options = {
        skip_cache: batchForm.value.skipCache,
        force_update: batchForm.value.forceUpdate,
        dry_run: batchForm.value.dryRun,
        validate_only: batchForm.value.validateOnly,
    };

    let entities;
    try {
        entities = JSON.parse(batchForm.value.entitiesJson);
    } catch (err) {
        error('Erreur de parsing JSON : ' + err.message);
        loading.value = false;
        return;
    }

    try {
        // Récupérer le token CSRF depuis le meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            error('Token CSRF introuvable. Veuillez recharger la page.');
            loading.value = false;
            return;
        }
        
        const response = await fetch('/api/scrapping/import/batch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                entities,
                ...options,
            }),
        });

        const data = await response.json();
        
        if (data.success) {
            success(`Import en lot réussi : ${data.summary?.success || 0}/${data.summary?.total || 0} entités`);
            results.value.unshift({
                type: 'batch',
                entities,
                result: data,
                timestamp: new Date().toISOString(),
            });
        } else {
            error(data.message || 'Erreur lors de l\'import en lot');
            results.value.unshift({
                type: 'batch',
                entities,
                result: data,
                error: true,
                timestamp: new Date().toISOString(),
            });
        }
    } catch (err) {
        error('Erreur lors de l\'import en lot : ' + err.message);
        results.value.unshift({
            type: 'batch',
            entities,
            error: true,
            errorMessage: err.message,
            timestamp: new Date().toISOString(),
        });
    } finally {
        loading.value = false;
    }
};

const clearResults = () => {
    results.value = [];
    info('Historique effacé');
};

const formatTimestamp = (timestamp) => {
    return new Date(timestamp).toLocaleString('fr-FR');
};

const getEntityTypeLabel = (type) => {
    return entityTypes.find(e => e.value === type)?.label || type;
};

onMounted(() => {
    setPageTitle('Gestion du Scrapping');
});
</script>

<template>
    <Head title="Gestion du Scrapping" />
    
    <Container class="space-y-6">
        <!-- En-tête -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-primary-100">Gestion du Scrapping</h1>
                <p class="text-primary-200 mt-2">Importez des données depuis DofusDB vers KrosmozJDR</p>
            </div>
            <div v-if="results.length > 0">
                <Btn color="ghost" size="sm" @click="clearResults">
                    <Icon icon="fa-trash" pack="solid" class="mr-2" />
                    Effacer l'historique
                </Btn>
            </div>
        </div>

        <!-- Onglets -->
        <Tab variant="lift" size="md" class="mb-6">
            <TabItem 
                :active="activeTab === 'individual'" 
                label="Import individuel" 
                icon="fa-plus"
                @click.prevent="activeTab = 'individual'"
                style="cursor: pointer;"
            />
            <TabItem 
                :active="activeTab === 'batch'" 
                label="Import en lot" 
                icon="fa-layer-group"
                @click.prevent="activeTab = 'batch'"
                style="cursor: pointer;"
            />
            <TabItem 
                :active="activeTab === 'results'" 
                icon="fa-list"
                @click.prevent="activeTab = 'results'"
                style="cursor: pointer;"
            >
                <template #label>
                    Résultats
                    <Badge v-if="results.length > 0" color="primary" class="ml-2">{{ results.length }}</Badge>
                </template>
            </TabItem>
        </Tab>

        <!-- Import individuel -->
        <Card v-show="activeTab === 'individual'" class="p-6">
            <h2 class="text-xl font-bold mb-4">Import d'une entité</h2>
            
            <div class="space-y-4">
                <!-- Type d'entité -->
                <SelectField
                    v-model="individualForm.entityType"
                    label="Type d'entité"
                    :options="entityTypes.map(e => ({ value: e.value, label: e.label }))"
                />

                <!-- ID de l'entité -->
                <InputField
                    v-model="individualForm.entityId"
                    type="number"
                    :label="`ID ${currentEntityType?.label || 'Entité'} (1-${currentEntityType?.maxId || '?'})`"
                    :min="1"
                    :max="currentEntityType?.maxId || 99999"
                    placeholder="Ex: 1"
                />

                <!-- Options -->
                <div class="space-y-2">
                    <h3 class="font-semibold text-primary-100">Options d'import</h3>
                    
                    <CheckboxField
                        v-model="individualForm.skipCache"
                        label="Ignorer le cache"
                    >
                        <template #helper>
                            <Tooltip content="Force la récupération depuis DofusDB sans utiliser le cache">
                                <Icon icon="fa-circle-question" pack="solid" class="text-primary-300" />
                            </Tooltip>
                        </template>
                    </CheckboxField>

                    <CheckboxField
                        v-model="individualForm.forceUpdate"
                        label="Forcer la mise à jour"
                    >
                        <template #helper>
                            <Tooltip content="Met à jour l'entité même si elle existe déjà">
                                <Icon icon="fa-circle-question" pack="solid" class="text-primary-300" />
                            </Tooltip>
                        </template>
                    </CheckboxField>

                    <CheckboxField
                        v-model="individualForm.dryRun"
                        label="Mode simulation (dry-run)"
                    >
                        <template #helper>
                            <Tooltip content="Simule l'import sans sauvegarder en base de données">
                                <Icon icon="fa-circle-question" pack="solid" class="text-primary-300" />
                            </Tooltip>
                        </template>
                    </CheckboxField>

                    <CheckboxField
                        v-model="individualForm.validateOnly"
                        label="Validation uniquement"
                    >
                        <template #helper>
                            <Tooltip content="Valide les données sans les importer">
                                <Icon icon="fa-circle-question" pack="solid" class="text-primary-300" />
                            </Tooltip>
                        </template>
                    </CheckboxField>
                </div>

                <!-- Bouton d'import -->
                <Btn
                    color="primary"
                    size="lg"
                    :disabled="!isValidEntityId || loading"
                    @click="importIndividual"
                    class="w-full"
                >
                    <Loading v-if="loading" class="mr-2" />
                    <Icon v-else icon="fa-download" pack="solid" class="mr-2" />
                    {{ loading ? 'Import en cours...' : 'Importer' }}
                </Btn>
            </div>
        </Card>

        <!-- Import en lot -->
        <Card v-show="activeTab === 'batch'" class="p-6">
            <h2 class="text-xl font-bold mb-4">Import en lot</h2>
            
            <div class="space-y-4">
                <!-- JSON des entités -->
                <TextareaField
                    v-model="batchForm.entitiesJson"
                    label="Entités à importer (JSON)"
                    placeholder='[{"type": "class", "id": 1}, {"type": "monster", "id": 31}]'
                    rows="8"
                >
                    <template #helper>
                        <div class="text-sm text-primary-300">
                            Format : <code class="bg-base-200 px-1 rounded">[{"type": "class|monster|item|spell", "id": number}, ...]</code>
                        </div>
                    </template>
                </TextareaField>

                <!-- Options -->
                <div class="space-y-2">
                    <h3 class="font-semibold text-primary-100">Options d'import</h3>
                    
                    <CheckboxField
                        v-model="batchForm.skipCache"
                        label="Ignorer le cache"
                    />
                    <CheckboxField
                        v-model="batchForm.forceUpdate"
                        label="Forcer la mise à jour"
                    />
                    <CheckboxField
                        v-model="batchForm.dryRun"
                        label="Mode simulation (dry-run)"
                    />
                    <CheckboxField
                        v-model="batchForm.validateOnly"
                        label="Validation uniquement"
                    />
                </div>

                <!-- Bouton d'import -->
                <Btn
                    color="primary"
                    size="lg"
                    :disabled="!isValidBatchJson || loading"
                    @click="importBatch"
                    class="w-full"
                >
                    <Loading v-if="loading" class="mr-2" />
                    <Icon v-else icon="fa-layer-group" pack="solid" class="mr-2" />
                    {{ loading ? 'Import en cours...' : 'Importer en lot' }}
                </Btn>
            </div>
        </Card>

        <!-- Résultats -->
        <Card v-show="activeTab === 'results'" class="p-6">
            <h2 class="text-xl font-bold mb-4">Historique des imports</h2>
            
            <div v-if="results.length === 0" class="text-center py-8 text-primary-300">
                <Icon icon="fa-inbox" pack="solid" size="3xl" class="mb-4 opacity-50" />
                <p>Aucun import effectué</p>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="(result, index) in results"
                    :key="index"
                    class="border border-base-300 rounded-lg p-4"
                    :class="result.error ? 'bg-error/10 border-error' : 'bg-success/10 border-success'"
                >
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <Badge :color="result.error ? 'error' : 'success'">
                                {{ result.error ? 'Erreur' : 'Succès' }}
                            </Badge>
                            <span class="text-sm text-primary-300">
                                {{ formatTimestamp(result.timestamp) }}
                            </span>
                        </div>
                    </div>

                    <!-- Import individuel -->
                    <div v-if="result.type === 'individual'">
                        <p class="font-semibold">
                            {{ getEntityTypeLabel(result.entityType) }} #{{ result.entityId }}
                        </p>
                        <div v-if="result.result" class="mt-2 text-sm">
                            <p v-if="result.result.message">{{ result.result.message }}</p>
                            <pre v-if="result.result.error" class="mt-2 text-error text-xs bg-base-200 p-2 rounded overflow-auto">{{ result.result.error }}</pre>
                        </div>
                        <p v-if="result.errorMessage" class="mt-2 text-error text-sm">{{ result.errorMessage }}</p>
                    </div>

                    <!-- Import en lot -->
                    <div v-else-if="result.type === 'batch'">
                        <p class="font-semibold">Import en lot ({{ result.entities?.length || 0 }} entités)</p>
                        <div v-if="result.result?.summary" class="mt-2 text-sm">
                            <p>
                                Succès : <strong>{{ result.result.summary.success }}</strong> /
                                Total : <strong>{{ result.result.summary.total }}</strong> /
                                Erreurs : <strong>{{ result.result.summary.errors }}</strong>
                            </p>
                        </div>
                        <p v-if="result.errorMessage" class="mt-2 text-error text-sm">{{ result.errorMessage }}</p>
                    </div>
                </div>
            </div>
        </Card>
    </Container>
</template>

