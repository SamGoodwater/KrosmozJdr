<script setup>
/**
 * TypeManagerTable (Organism)
 *
 * @description
 * Tableau générique de gestion des "types/races" :
 * - mode `decision` : registry DofusDB (pending/allowed/blocked)
 * - mode `state` : validation interne via `state` (raw/draft/playable/archived)
 *
 * Supporte sélection multiple + update en masse via un seul input.
 */
import { computed, onMounted, ref, watch } from "vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
import CheckboxField from "@/Pages/Molecules/data-input/CheckboxField.vue";
import ConfirmModal from "@/Pages/Molecules/action/ConfirmModal.vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { usePermissions } from "@/Composables/permissions/usePermissions";

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: "" },

    // GET -> { success:true, data:[] }
    listUrl: { type: String, required: true },
    // PATCH -> bulk endpoint (expects ids + decision/state)
    bulkUrl: { type: String, required: true },
    // DELETE -> suppression (optionnel)
    deleteUrlBase: { type: String, default: "" },

    // 'decision' | 'state'
    mode: { type: String, required: true, validator: (v) => ["decision", "state"].includes(String(v)) },
    // libellé affiché pour le champ principal
    fieldLabel: { type: String, default: "" },
});

const notificationStore = useNotificationStore();
const { success, error: showError, info } = notificationStore;
const { isAdmin } = usePermissions();

const loading = ref(false);
const rows = ref([]);

const querySearch = ref("");
const queryFilter = ref("all"); // 'all' | decision/state values

const selectedIds = ref(new Set());
const bulkValue = ref(""); // decision/state à appliquer

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

const confirmOpen = ref(false);
const confirmTarget = ref(null); // { id:number, name?:string } | null

const filterOptions = computed(() => {
    if (String(props.mode) === "decision") {
        return [
            { value: "all", label: "Tous" },
            { value: "pending", label: "En attente" },
            { value: "allowed", label: "Utiliser" },
            { value: "blocked", label: "Ne pas utiliser" },
        ];
    }
    return [
        { value: "all", label: "Tous" },
        { value: "raw", label: "Raw" },
        { value: "draft", label: "Brouillon" },
        { value: "playable", label: "Validé (playable)" },
        { value: "archived", label: "Archivé" },
    ];
});

const bulkOptions = computed(() => {
    if (String(props.mode) === "decision") {
        return [
            { value: "pending", label: "En attente" },
            { value: "used", label: "Utiliser" }, // alias UX (backend normalise)
            { value: "unused", label: "Ne pas utiliser" }, // alias UX
        ];
    }
    return [
        { value: "raw", label: "Raw" },
        { value: "draft", label: "Brouillon" },
        { value: "playable", label: "Validé (playable)" },
        { value: "archived", label: "Archivé" },
    ];
});

const fieldKey = computed(() => (String(props.mode) === "decision" ? "decision" : "state"));
const filterQueryKey = computed(() => (String(props.mode) === "decision" ? "decision" : "state"));

const selectedCount = computed(() => selectedIds.value.size);

const filteredRows = computed(() => {
    const list = Array.isArray(rows.value) ? rows.value : [];
    const q = String(querySearch.value || "").trim().toLowerCase();
    if (!q) return list;
    return list.filter((r) => {
        const hay = [
            r?.id,
            r?.name,
            r?.dofusdb_type_id,
            r?.decision,
            r?.state,
        ]
            .filter((v) => v !== null && typeof v !== "undefined")
            .map((v) => String(v).toLowerCase())
            .join(" ");
        return hay.includes(q);
    });
});

const allVisibleSelected = computed(() => {
    const list = filteredRows.value;
    if (!list.length) return false;
    return list.every((r) => selectedIds.value.has(Number(r?.id)));
});

const toggleSelectAllVisible = () => {
    const next = new Set(selectedIds.value);
    const list = filteredRows.value;
    const shouldSelect = !allVisibleSelected.value;
    for (const r of list) {
        const id = Number(r?.id);
        if (!Number.isFinite(id)) continue;
        if (shouldSelect) next.add(id);
        else next.delete(id);
    }
    selectedIds.value = next;
};

const toggleRow = (id) => {
    const n = Number(id);
    if (!Number.isFinite(n)) return;
    const next = new Set(selectedIds.value);
    if (next.has(n)) next.delete(n);
    else next.add(n);
    selectedIds.value = next;
};

const buildListUrl = () => {
    const u = new URL(props.listUrl, window.location.origin);
    const f = String(queryFilter.value || "all");
    if (f !== "all") {
        u.searchParams.set(filterQueryKey.value, f);
    }
    return u.pathname + u.search;
};

const load = async () => {
    loading.value = true;
    try {
        const response = await fetch(buildListUrl(), { headers: { Accept: "application/json" } });
        const json = await response.json();
        if (!response.ok || !json?.success) {
            throw new Error(json?.message || "Chargement impossible");
        }
        rows.value = Array.isArray(json.data) ? json.data : [];
    } catch (e) {
        showError(`${props.title} : ${e.message}`);
    } finally {
        loading.value = false;
    }
};

const requestDelete = (row) => {
    const base = String(props.deleteUrlBase || "").trim();
    if (!base) return;
    const id = Number(row?.id);
    if (!Number.isFinite(id)) return;
    confirmTarget.value = { id, name: row?.name ? String(row.name) : "" };
    confirmOpen.value = true;
};

const deleteOne = async (id) => {
    if (!isAdmin.value) return;
    const base = String(props.deleteUrlBase || "").trim();
    if (!base) return;
    const n = Number(id);
    if (!Number.isFinite(n)) return;

    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        return;
    }

    try {
        const url = `${base}/${n}`;
        const res = await fetch(url, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
        });
        const json = await res.json().catch(() => null);
        if (!res.ok || (json && json.success === false)) {
            throw new Error(json?.message || "Suppression impossible");
        }
        success("Supprimé", { duration: 2000 });
        await load();
    } catch (e) {
        showError("Suppression : " + e.message);
    }
};

const bulkApply = async () => {
    if (!isAdmin.value) return;
    if (!selectedIds.value.size) return;
    const v = String(bulkValue.value || "").trim();
    if (!v) return;

    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        return;
    }

    info("Mise à jour en masse…", { duration: 1500 });
    try {
        const payload = { ids: Array.from(selectedIds.value) };
        payload[fieldKey.value] = v;

        const res = await fetch(props.bulkUrl, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (!res.ok || !json?.success) {
            throw new Error(json?.message || "Bulk update échoué");
        }
        success("Mise à jour enregistrée", { duration: 2500 });
        await load();
    } catch (e) {
        showError("Bulk : " + e.message);
    }
};

const updateSingle = async (id, value) => {
    if (!isAdmin.value) return;
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        return;
    }

    // On réutilise le bulk pour rester DRY côté API
    selectedIds.value = new Set([Number(id)]);
    bulkValue.value = String(value);
    await bulkApply();
};

watch(
    () => queryFilter.value,
    async () => {
        await load();
    }
);

onMounted(async () => {
    await load();
});
</script>

<template>
    <Card class="p-6 space-y-4">
        <ConfirmModal
            :open="confirmOpen"
            title="Supprimer"
            :message="`Supprimer définitivement cette entrée${confirmTarget?.name ? ` (« ${confirmTarget.name} »)` : ''} ?`"
            confirm-label="Supprimer"
            cancel-label="Annuler"
            confirm-color="error"
            confirm-icon="fa-solid fa-trash"
            @close="confirmOpen = false"
            @cancel="
                () => {
                    confirmOpen = false;
                    confirmTarget = null;
                }
            "
            @confirm="
                async () => {
                    const id = confirmTarget?.id;
                    confirmOpen = false;
                    confirmTarget = null;
                    if (Number.isFinite(Number(id))) {
                        await deleteOne(id);
                    }
                }
            "
        />

        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-primary-100">{{ title }}</h2>
                <p v-if="description" class="text-sm text-primary-300 mt-1">
                    {{ description }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Badge :content="String(filteredRows.length)" color="neutral" />
                <Btn size="sm" variant="ghost" :disabled="loading" @click="load">
                    <Loading v-if="loading" class="mr-2" />
                    Rafraîchir
                </Btn>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-3">
            <InputField v-model="querySearch" label="Recherche" placeholder="id, nom…" />
            <SelectField
                v-model="queryFilter"
                :label="String(mode) === 'decision' ? 'Filtre statut' : 'Filtre état'"
                :options="filterOptions"
            />
            <div class="space-y-1">
                <SelectField
                    v-model="bulkValue"
                    :label="fieldLabel || (String(mode) === 'decision' ? 'Valider (bulk)' : 'État (bulk)')"
                    :options="bulkOptions"
                    :disabled="!isAdmin || selectedCount === 0"
                    placeholder="Choisir…"
                />
                <div class="flex items-center justify-between gap-2">
                    <div class="text-xs text-primary-300">
                        Sélection: <span class="font-semibold">{{ selectedCount }}</span>
                    </div>
                    <Btn size="sm" color="primary" :disabled="!isAdmin || selectedCount === 0 || !bulkValue" @click="bulkApply">
                        Appliquer
                    </Btn>
                </div>
            </div>
        </div>

        <div v-if="loading" class="py-6 flex items-center gap-3 text-primary-300">
            <Loading />
            <span>Chargement…</span>
        </div>

        <div v-else class="overflow-x-auto rounded-lg border border-base-300">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th class="w-10">
                            <CheckboxField
                                :model-value="allVisibleSelected"
                                @update:modelValue="toggleSelectAllVisible"
                                label=""
                            />
                        </th>
                        <th class="w-20">ID</th>
                        <th v-if="String(mode) === 'decision'" class="w-28">typeId</th>
                        <th>Nom</th>
                        <th v-if="String(mode) === 'decision'" class="w-28">Détections</th>
                        <th class="w-52 text-right">Validation</th>
                        <th v-if="String(mode) === 'decision'" class="w-56">Dernière détection</th>
                        <th v-else class="w-56">Dernière maj</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in filteredRows" :key="r.id">
                        <td>
                            <input
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                :checked="selectedIds.has(Number(r.id))"
                                @change="toggleRow(r.id)"
                            />
                        </td>
                        <td class="font-mono">{{ r.id }}</td>
                        <td v-if="String(mode) === 'decision'" class="font-mono">{{ r.dofusdb_type_id }}</td>
                        <td class="min-w-[220px]">
                            {{ r.name || "—" }}
                        </td>
                        <td v-if="String(mode) === 'decision'">{{ r.seen_count ?? "—" }}</td>
                        <td class="text-right">
                            <div class="flex justify-end items-center gap-2">
                                <select
                                    class="select select-bordered select-sm"
                                    :disabled="!isAdmin"
                                    :value="String(mode) === 'decision' ? (r.decision === 'allowed' ? 'used' : r.decision === 'blocked' ? 'unused' : 'pending') : r.state"
                                    @change="(e) => updateSingle(r.id, e.target.value)"
                                >
                                    <template v-if="String(mode) === 'decision'">
                                        <option value="pending">En attente</option>
                                        <option value="used">Utiliser</option>
                                        <option value="unused">Ne pas utiliser</option>
                                    </template>
                                    <template v-else>
                                        <option value="raw">raw</option>
                                        <option value="draft">draft</option>
                                        <option value="playable">playable</option>
                                        <option value="archived">archived</option>
                                    </template>
                                </select>

                                <Btn
                                    v-if="deleteUrlBase"
                                    size="sm"
                                    variant="ghost"
                                    :disabled="!isAdmin"
                                    title="Supprimer"
                                    @click="requestDelete(r)"
                                >
                                    <Icon source="fa-solid fa-trash" alt="Supprimer" pack="solid" />
                                </Btn>
                            </div>
                        </td>
                        <td v-if="String(mode) === 'decision'">
                            <span class="text-sm text-primary-300">{{ r.last_seen_at ?? "—" }}</span>
                        </td>
                        <td v-else>
                            <span class="text-sm text-primary-300">{{ r.updated_at ?? "—" }}</span>
                        </td>
                    </tr>
                    <tr v-if="!filteredRows.length">
                        <td :colspan="String(mode) === 'decision' ? 7 : 6" class="text-center text-primary-300 italic py-6">
                            Aucun résultat.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Card>
</template>

