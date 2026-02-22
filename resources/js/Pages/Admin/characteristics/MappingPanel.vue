<script setup>
/**
 * Panneau 3 — Mapping : lien DofusDB ↔ Krosmoz par entité.
 * Modal : à gauche choix de l'objet (breed, item, monster, panoply, spell), à droite propriétés (chemins) avec recherche.
 * @see docs/50-Fonctionnalités/Scrapping/SIMPLIFICATION_UI_MAPPING_DEPUIS_CARACTERISTIQUE.md
 */
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    scrappingMappingsUsingThis: { type: Array, default: () => [] },
    characteristicKey: { type: String, default: '' },
    mappingEntities: { type: Array, default: () => [] },
    entityLabels: { type: Object, default: () => ({}) },
});

const tableRows = computed(() =>
    (props.mappingEntities || []).map((entity) => {
        const mapping = (props.scrappingMappingsUsingThis || []).find((m) => m.entity === entity);
        return { entity, mapping };
    })
);

const showLinkModal = ref(false);
const entitiesList = ref([]);
const entitiesLoading = ref(false);
const selectedEntity = ref('');
const paths = ref([]);
const pathsLoading = ref(false);
const pathsError = ref('');
const pathSearch = ref('');
const selectedPathEntry = ref(null);
const linkSaving = ref(false);
const unlinkSaving = ref(null);

/** Ouverture avec entité pré-sélectionnée (ex. depuis la ligne "Classes") */
let preselectedEntity = '';

const filteredPaths = computed(() => {
    const q = (pathSearch.value || '').trim().toLowerCase();
    if (!q) return paths.value;
    return paths.value.filter(
        (p) =>
            (p.path || '').toLowerCase().includes(q) ||
            (p.key || '').toLowerCase().includes(q)
    );
});

function openLinkModal(entityFromRow) {
    preselectedEntity = entityFromRow || '';
    selectedEntity.value = '';
    selectedPathEntry.value = null;
    pathSearch.value = '';
    paths.value = [];
    pathsError.value = '';
    showLinkModal.value = true;
    entitiesLoading.value = true;
    entitiesList.value = [];
    const url = route('admin.characteristics.scrapping-mapping-options', {
        characteristic_key: props.characteristicKey,
    });
    axios
        .get(url)
        .then((res) => {
            entitiesList.value = res.data?.entities ?? [];
            if (preselectedEntity && entitiesList.value.some((e) => e.id === preselectedEntity)) {
                selectedEntity.value = preselectedEntity;
            } else if (entitiesList.value.length) {
                selectedEntity.value = entitiesList.value[0].id;
            }
        })
        .catch(() => {
            pathsError.value = 'Impossible de charger la liste des objets.';
        })
        .finally(() => {
            entitiesLoading.value = false;
        });
}

watch(selectedEntity, (entity) => {
    selectedPathEntry.value = null;
    pathSearch.value = '';
    if (!entity) {
        paths.value = [];
        return;
    }
    pathsLoading.value = true;
    pathsError.value = '';
    const url = route('admin.characteristics.scrapping-mapping-options', {
        characteristic_key: props.characteristicKey,
    });
    axios
        .get(url, { params: { entity } })
        .then((res) => {
            paths.value = res.data?.paths ?? [];
        })
        .catch((err) => {
            pathsError.value = err.response?.data?.message ?? 'Impossible de charger les propriétés.';
        })
        .finally(() => {
            pathsLoading.value = false;
        });
}, { immediate: false });

function closeLinkModal() {
    showLinkModal.value = false;
    selectedEntity.value = '';
    paths.value = [];
}

function saveLink() {
    const entry = selectedPathEntry.value;
    if (!entry || !props.characteristicKey || !selectedEntity.value) return;
    linkSaving.value = true;
    pathsError.value = '';
    const url = route('admin.characteristics.store-scrapping-mapping', {
        characteristic_key: props.characteristicKey,
    });
    axios
        .post(url, { entity: selectedEntity.value, from_path: entry.path })
        .then(() => {
            closeLinkModal();
            router.reload();
        })
        .catch((err) => {
            pathsError.value = err.response?.data?.message ?? 'Erreur lors de l\'enregistrement.';
        })
        .finally(() => {
            linkSaving.value = false;
        });
}

function unlink(mappingId) {
    if (!mappingId || !props.characteristicKey) return;
    unlinkSaving.value = mappingId;
    const url = route('admin.characteristics.unlink-scrapping-mapping', {
        characteristic_key: props.characteristicKey,
    });
    axios
        .post(url, { mapping_id: mappingId })
        .then(() => router.reload())
        .finally(() => { unlinkSaving.value = null; });
}

function entityLabel(entity) {
    if (!entity) return '';
    const found = entitiesList.value.find((e) => e.id === entity);
    return found?.label ?? props.entityLabels?.[entity] ?? entity;
}
</script>

<template>
    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-base-content border-b border-base-300 pb-2">Panneau 3 — Mapping</h2>
        <div class="card shadow border border-base-200 border-glass-sm">
            <div class="card-body bg-base-100 rounded-lg">
                <p class="text-sm text-base-content/70 mb-4">
                    Lien entre une propriété DofusDB et cette caractéristique. Cliquez sur <strong>Lier</strong> pour choisir l’objet (à gauche) puis la propriété (à droite).
                </p>

                <div v-if="tableRows.length" class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                        <thead>
                            <tr>
                                <th class="bg-base-300">Entité</th>
                                <th class="bg-base-300">Règle actuelle</th>
                                <th class="bg-base-300">Cibles</th>
                                <th class="bg-base-300 w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in tableRows" :key="row.entity">
                                <td>{{ entityLabel(row.entity) || (entityLabels?.[row.entity] ?? row.entity) }}</td>
                                <td>
                                    <template v-if="row.mapping">
                                        <code class="text-xs">{{ row.mapping.mapping_key }}</code>
                                        <span class="text-base-content/60 ml-1">({{ row.mapping.from_path }})</span>
                                    </template>
                                    <span v-else class="text-base-content/50 italic">—</span>
                                </td>
                                <td>
                                    <template v-if="row.mapping?.targets?.length">
                                        <span
                                            v-for="(t, i) in row.mapping.targets"
                                            :key="i"
                                            class="badge badge-ghost badge-sm mr-1"
                                        >{{ t.model }}.{{ t.field }}</span>
                                    </template>
                                    <span v-else class="text-base-content/50">—</span>
                                </td>
                                <td>
                                    <button
                                        v-if="row.mapping"
                                        type="button"
                                        class="btn btn-ghost btn-xs btn-error"
                                        :disabled="unlinkSaving === row.mapping.id"
                                        @click="unlink(row.mapping.id)"
                                    >
                                        {{ unlinkSaving === row.mapping.id ? '…' : 'Délier' }}
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="btn btn-primary btn-xs"
                                        @click="openLinkModal(row.entity)"
                                    >
                                        Lier
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-sm text-base-content/60 italic">Aucune entité à afficher pour ce groupe.</p>
            </div>
        </div>

        <!-- Modal : gauche = objet, droite = propriétés -->
        <dialog class="modal" :class="{ 'modal-open': showLinkModal }">
            <div class="modal-box max-w-4xl">
                <h3 class="font-semibold text-lg">Lier une propriété DofusDB</h3>
                <p class="text-sm text-base-content/70 mt-1">
                    Choisissez l’objet à gauche, puis la propriété à droite. Le mapping sera créé ou mis à jour à l’enregistrement.
                </p>

                <div v-if="entitiesLoading" class="flex justify-center py-8">
                    <span class="loading loading-spinner loading-md" />
                </div>
                <div v-else-if="pathsError && !selectedEntity" class="alert alert-warning mt-4">
                    {{ pathsError }}
                </div>
                <template v-else>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] gap-4">
                        <!-- Gauche : choix de l'objet -->
                        <div class="border border-base-200 rounded-lg p-3 bg-base-200/30">
                            <p class="label-text font-medium mb-2">Objet DofusDB</p>
                            <div class="flex flex-col gap-1">
                                <label
                                    v-for="e in entitiesList"
                                    :key="e.id"
                                    class="flex items-center gap-2 p-2 rounded cursor-pointer transition"
                                    :class="selectedEntity === e.id ? 'bg-primary/15 border border-primary' : 'hover:bg-base-200 border border-transparent'"
                                >
                                    <input
                                        v-model="selectedEntity"
                                        type="radio"
                                        :value="e.id"
                                        class="radio radio-primary radio-sm"
                                    />
                                    <span>{{ e.label }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Droite : propriétés (chemins) + recherche -->
                        <div class="border border-base-200 rounded-lg p-3 flex flex-col min-h-0">
                            <p class="label-text font-medium mb-2">Propriété (chemin)</p>
                            <input
                                v-model="pathSearch"
                                type="text"
                                placeholder="Rechercher (ex. level, life, name…)"
                                class="input input-bordered input-sm w-full mb-2"
                            />
                            <div v-if="pathsLoading" class="flex justify-center py-6">
                                <span class="loading loading-spinner loading-md" />
                            </div>
                            <div v-else-if="pathsError" class="alert alert-warning text-sm">
                                {{ pathsError }}
                            </div>
                            <div v-else class="overflow-x-auto flex-1 min-h-0 border border-base-200 rounded">
                                <table class="table table-sm table-zebra">
                                    <thead>
                                        <tr>
                                            <th class="bg-base-300 w-8" />
                                            <th class="bg-base-300">Clé</th>
                                            <th class="bg-base-300">Chemin DofusDB</th>
                                            <th class="bg-base-300">Cibles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(p, idx) in filteredPaths"
                                            :key="idx"
                                            class="cursor-pointer transition"
                                            :class="selectedPathEntry === p ? 'bg-primary/10' : 'hover:bg-base-200'"
                                            @click="selectedPathEntry = p"
                                        >
                                            <td>
                                                <input
                                                    v-model="selectedPathEntry"
                                                    type="radio"
                                                    :value="p"
                                                    class="radio radio-primary radio-sm"
                                                />
                                            </td>
                                            <td><code class="text-xs">{{ p.key }}</code></td>
                                            <td><code class="text-xs break-all">{{ p.path }}</code></td>
                                            <td>
                                                <span
                                                    v-for="(t, i) in (p.targets || [])"
                                                    :key="i"
                                                    class="badge badge-ghost badge-xs mr-0.5"
                                                >{{ t.model }}.{{ t.field }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-if="!pathsLoading && selectedEntity && filteredPaths.length === 0" class="text-sm text-base-content/60 italic mt-2">
                                Aucune propriété{{ pathSearch.trim() ? ' ne correspond à la recherche' : '' }}.
                            </p>
                        </div>
                    </div>
                </template>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="closeLinkModal">Annuler</button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        :disabled="!selectedPathEntry || !selectedEntity || linkSaving"
                        @click="saveLink"
                    >
                        {{ linkSaving ? 'Enregistrement…' : 'Enregistrer' }}
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @submit="closeLinkModal">
                <button type="submit">fermer</button>
            </form>
        </dialog>
    </section>
</template>
