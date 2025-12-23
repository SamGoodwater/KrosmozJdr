<script setup>
/**
 * Section UX: revue des nouveaux typeId DofusDB détectés pour les ressources.
 *
 * Permet de décider "utiliser / ne pas utiliser / en attente" sans changer le code.
 */
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import Card from '@/Pages/Atoms/data-display/Card.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Loading from '@/Pages/Atoms/feedback/Loading.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const notificationStore = useNotificationStore();
const { success, error: showError, info } = notificationStore;

const loading = ref(false);
const items = ref([]);
const examplesByTypeId = ref({});
const examplesLoading = ref({});
const expanded = ref({});

const { isAdmin } = usePermissions();

const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

const loadPending = async () => {
    loading.value = true;
    try {
        const response = await fetch('/api/scrapping/resource-types/pending', {
            headers: { 'Accept': 'application/json' },
        });
        const data = await response.json();
        if (response.ok && data.success) {
            items.value = data.data || [];
        } else {
            showError(data.message || 'Impossible de charger les types en attente');
        }
    } catch (e) {
        showError('Erreur lors du chargement : ' + e.message);
    } finally {
        loading.value = false;
    }
};

const updateDecision = async (resourceTypeId, decision) => {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError('Token CSRF introuvable. Veuillez recharger la page.');
        return;
    }

    try {
        const response = await fetch(`/api/scrapping/resource-types/${resourceTypeId}/decision`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                decision,
                // UX: quand on "Utilise", on rejoue automatiquement les items mémorisés
                replay_pending: decision === 'used',
                replay_limit: 500,
            }),
        });
        const data = await response.json();

        if (response.ok && data.success) {
            success('Statut enregistré', { duration: 2500 });
            if (decision === 'used' && data.replay?.summary) {
                const s = data.replay.summary;
                success(`Réimport auto: ${s.imported} importés, ${s.pivots_applied} pivots, ${s.errors} erreurs`, { duration: 4500 });
            }
            // Retirer de la liste si ce n’est plus pending
            if (decision !== 'pending') {
                items.value = items.value.filter(i => i.id !== resourceTypeId);
            }
        } else {
            showError(data.message || 'Erreur lors de la mise à jour');
        }
    } catch (e) {
        showError('Erreur lors de la mise à jour : ' + e.message);
    }
};

const decisionToUx = (decision) => {
    if (decision === 'allowed') return 'used';
    if (decision === 'blocked') return 'unused';
    return 'pending';
};

const replayPending = async (resourceTypeId) => {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError('Token CSRF introuvable. Veuillez recharger la page.');
        return;
    }

    try {
        info('Réimport en cours…', { duration: 2000 });
        const response = await fetch(`/api/scrapping/resource-types/${resourceTypeId}/replay`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ limit: 200 }),
        });
        const data = await response.json();
        if (response.ok && data.success) {
            success(`Réimport terminé: ${data.summary.success}/${data.summary.total}`, { duration: 3500 });
        } else {
            showError(data.message || 'Erreur lors du réimport');
        }
    } catch (e) {
        showError('Erreur lors du réimport : ' + e.message);
    }
};

const toggleExamples = async (resourceTypeId) => {
    expanded.value[resourceTypeId] = !expanded.value[resourceTypeId];
    if (expanded.value[resourceTypeId]) {
        await loadExamples(resourceTypeId);
    }
};

const loadExamples = async (resourceTypeId) => {
    if (examplesLoading.value[resourceTypeId]) return;
    examplesLoading.value[resourceTypeId] = true;
    try {
        const response = await fetch(`/api/scrapping/resource-types/${resourceTypeId}/pending-items?limit=5&with_preview=1`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await response.json();
        if (response.ok && data.success) {
            examplesByTypeId.value[resourceTypeId] = data.data?.items || [];
        } else {
            showError(data.message || 'Impossible de charger les exemples');
        }
    } catch (e) {
        showError('Erreur lors du chargement des exemples : ' + e.message);
    } finally {
        examplesLoading.value[resourceTypeId] = false;
    }
};

const formatExampleLine = (ex) => {
    const parts = [];
    if (ex.context) parts.push(ex.context);
    if (ex.source_entity_type && ex.source_entity_dofusdb_id) {
        parts.push(`${ex.source_entity_type}#${ex.source_entity_dofusdb_id}`);
    }
    if (ex.quantity) parts.push(`x${ex.quantity}`);
    return parts.length ? parts.join(' · ') : '—';
};

const formatDate = (iso) => {
    if (!iso) return '-';
    try {
        return new Date(iso).toLocaleString('fr-FR');
    } catch {
        return iso;
    }
};

onMounted(async () => {
    await loadPending();
});
</script>

<template>
    <Card class="p-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-bold text-primary-100">Types DofusDB détectés (Ressources)</h2>
                <p class="text-sm text-primary-300 mt-1">
                    Ces typeId ont été détectés pendant le scrapping dans des recettes/drops. Indique si on les utilise.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Badge :content="String(items.length)" color="warning" />
                <Btn size="sm" variant="ghost" @click="loadPending" :disabled="loading">
                    <Loading v-if="loading" class="mr-2" />
                    <Icon v-else source="fa-solid fa-rotate" alt="Rafraîchir" pack="solid" class="mr-2" />
                    Rafraîchir
                </Btn>
            </div>
        </div>

        <div v-if="loading" class="py-8 flex items-center justify-center">
            <Loading />
            <span class="ml-3 text-primary-300">Chargement…</span>
        </div>

        <div v-else-if="items.length === 0" class="text-center py-8 text-primary-300">
            <Icon source="fa-solid fa-check" alt="OK" pack="solid" size="3xl" class="mb-3 opacity-60" />
            <p>Aucun type en attente.</p>
        </div>

        <div v-else class="overflow-x-auto rounded-lg border border-base-300">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>typeId</th>
                        <th>Nom</th>
                        <th>Détections</th>
                        <th>Dernière détection</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in items" :key="t.id">
                        <td class="font-mono">{{ t.dofusdb_type_id }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span>{{ t.name }}</span>
                                <Btn
                                    size="xs"
                                    variant="ghost"
                                    @click="toggleExamples(t.id)"
                                    :disabled="examplesLoading[t.id]"
                                    :title="expanded[t.id] ? 'Masquer les exemples' : 'Voir des exemples d’items détectés'"
                                >
                                    <Loading v-if="examplesLoading[t.id]" class="mr-2" />
                                    <span v-else class="text-xs underline opacity-80">
                                        {{ expanded[t.id] ? 'Masquer' : 'Exemples' }}
                                    </span>
                                </Btn>
                            </div>
                            <div v-if="expanded[t.id]" class="mt-2 text-xs text-primary-300 space-y-1">
                                <div v-if="(examplesByTypeId[t.id] || []).length === 0" class="opacity-70">
                                    Aucun exemple disponible.
                                </div>
                                <div v-else>
                                    <div
                                        v-for="it in examplesByTypeId[t.id]"
                                        :key="it.dofusdb_item_id"
                                        class="font-mono"
                                    >
                                        #{{ it.dofusdb_item_id }}
                                        <span v-if="it.preview?.name" class="not-italic font-sans opacity-90">
                                            — {{ it.preview.name }}
                                        </span>
                                        <div class="ml-4 opacity-80" v-if="(it.examples || []).length">
                                            <div v-for="(ex, idx) in it.examples.slice(0, 2)" :key="idx">
                                                {{ formatExampleLine(ex) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ t.seen_count }}</td>
                        <td>{{ formatDate(t.last_seen_at) }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <select
                                    class="select select-bordered select-sm"
                                    :value="decisionToUx(t.decision)"
                                    :disabled="!isAdmin"
                                    title="Statut"
                                    @change="(e) => updateDecision(t.id, e.target.value)"
                                >
                                    <option value="pending">En attente</option>
                                    <option value="used">Utiliser</option>
                                    <option value="unused">Ne pas utiliser</option>
                                </select>
                                <Btn
                                    size="sm"
                                    variant="outline"
                                    @click="replayPending(t.id)"
                                    :title="'Réimport des items mémorisés pour ce typeId (si utilisé)'"
                                    :disabled="t.decision !== 'allowed'"
                                >
                                    Réimport
                                </Btn>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-xs text-primary-300">
            <p>
                <strong>Comportement:</strong> tant qu’un type est en <code>pending</code>, il est ignoré par le scrapping.
            </p>
        </div>
    </Card>
</template>


