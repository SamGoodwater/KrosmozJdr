<script setup>
/**
 * Section d'affichage de l'historique des imports
 */
import { computed } from 'vue';
import Card from '@/Pages/Atoms/data-display/Card.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const props = defineProps({
    results: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['clear']);

const notificationStore = useNotificationStore();
const { info: showInfo } = notificationStore;

const stringify = (obj) => JSON.stringify(obj, null, 2);

const formatTimestamp = (timestamp) => {
    return new Date(timestamp).toLocaleString('fr-FR');
};

const getEntityTypeLabel = (type) => {
    const labels = {
        class: 'Classe',
        monster: 'Monstre',
        item: 'Objet',
        spell: 'Sort',
        resource: 'Ressource',
    };
    return labels[type] || type;
};

const getIconForRelatedType = (type) => {
    const icons = {
        class: 'fa-user',
        monster: 'fa-dragon',
        item: 'fa-box',
        spell: 'fa-wand-magic-sparkles',
        resource: 'fa-gem',
    };
    return icons[type] || 'fa-question';
};

const clearHistory = () => {
    emit('clear');
    showInfo('Historique effacé');
};
</script>

<template>
    <Card class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-primary-100">Historique des imports</h2>
            <Btn
                v-if="results.length > 0"
                variant="ghost"
                size="sm"
                @click="clearHistory"
            >
                <Icon source="fa-solid fa-trash" alt="Effacer" pack="solid" class="mr-2" />
                Effacer
            </Btn>
        </div>

        <div v-if="results.length === 0" class="text-center py-8 text-primary-300">
            <Icon source="fa-solid fa-inbox" alt="Aucun import" pack="solid" size="3xl" class="mb-4 opacity-50" />
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
                    <div v-if="result.result" class="mt-2 text-sm space-y-2">
                        <p v-if="result.result.message">{{ result.result.message }}</p>
                        
                        <!-- Affichage des relations importées -->
                        <div v-if="result.result.related && result.result.related.length > 0" class="mt-3 p-2 bg-info/10 border border-info/30 rounded">
                            <p class="text-xs font-semibold text-info mb-2 flex items-center gap-2">
                                <Icon source="fa-solid fa-link" alt="Entités liées" pack="solid" class="text-xs" />
                                Entités liées importées ({{ result.result.related.length }})
                            </p>
                            <div class="space-y-1">
                                <div
                                    v-for="(related, idx) in result.result.related"
                                    :key="idx"
                                    class="text-xs text-primary-200 flex items-center gap-2"
                                >
                                    <Icon :source="`fa-solid ${getIconForRelatedType(related.type)}`" :alt="getEntityTypeLabel(related.type)" pack="solid" class="text-xs" />
                                    <span>{{ getEntityTypeLabel(related.type) }} #{{ related.id }}</span>
                                    <Badge
                                        v-if="related.result"
                                        :color="related.result.success ? 'success' : 'error'"
                                        size="xs"
                                    >
                                        {{ related.result.success ? 'OK' : 'Erreur' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                        
                        <pre v-if="result.result.error" class="mt-2 text-error text-xs bg-base-200 p-2 rounded overflow-auto max-w-full break-words whitespace-pre-wrap">{{ result.result.error }}</pre>
                        <details class="mt-3">
                            <summary class="cursor-pointer text-primary-300 text-xs uppercase tracking-wide">
                                Payload complet
                            </summary>
                            <pre class="mt-2 text-xs bg-base-200 p-3 rounded overflow-auto max-h-64 max-w-full break-words whitespace-pre-wrap">{{ stringify(result.result) }}</pre>
                        </details>
                    </div>
                    <p v-if="result.errorMessage" class="mt-2 text-error text-sm">{{ result.errorMessage }}</p>
                </div>

                <!-- Import par plage -->
                <div v-else-if="result.type === 'range'">
                    <p class="font-semibold">
                        Plage {{ getEntityTypeLabel(result.entityType) }} #{{ result.range?.start }} → #{{ result.range?.end }}
                    </p>
                    <div v-if="result.result?.summary" class="mt-2 text-sm">
                        <p>
                            Succès : <strong>{{ result.result.summary.success }}</strong> /
                            Total : <strong>{{ result.result.summary.total }}</strong> /
                            Erreurs : <strong>{{ result.result.summary.errors }}</strong>
                        </p>
                    </div>
                    <details class="mt-3">
                        <summary class="cursor-pointer text-primary-300 text-xs uppercase tracking-wide">
                            Détails de l'import
                        </summary>
                        <pre class="mt-2 text-xs bg-base-200 p-3 rounded overflow-auto max-h-64 max-w-full break-words whitespace-pre-wrap">{{ stringify(result.result) }}</pre>
                    </details>
                    <p v-if="result.errorMessage" class="mt-2 text-error text-sm">{{ result.errorMessage }}</p>
                </div>

                <!-- Import complet -->
                <div v-else-if="result.type === 'all'">
                    <p class="font-semibold">
                        Import complet {{ getEntityTypeLabel(result.entityType) }}
                    </p>
                    <div v-if="result.result?.summary" class="mt-2 text-sm">
                        <p>
                            Succès : <strong>{{ result.result.summary.success }}</strong> /
                            Total : <strong>{{ result.result.summary.total }}</strong> /
                            Erreurs : <strong>{{ result.result.summary.errors }}</strong>
                        </p>
                    </div>
                    <details class="mt-3">
                        <summary class="cursor-pointer text-primary-300 text-xs uppercase tracking-wide">
                            Détails de l'import
                        </summary>
                        <pre class="mt-2 text-xs bg-base-200 p-3 rounded overflow-auto max-h-64 max-w-full break-words whitespace-pre-wrap">{{ stringify(result.result) }}</pre>
                    </details>
                    <p v-if="result.errorMessage" class="mt-2 text-error text-sm">{{ result.errorMessage }}</p>
                </div>
            </div>
        </div>
    </Card>
</template>

