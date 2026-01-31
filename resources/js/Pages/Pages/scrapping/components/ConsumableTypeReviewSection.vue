<script setup>
/**
 * Section UX: revue des nouveaux typeId DofusDB détectés pour les consommables.
 *
 * Permet de décider "utiliser / ne pas utiliser / en attente" sans changer le code.
 */
import { ref, onMounted } from "vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

const notificationStore = useNotificationStore();
const { success, error: showError } = notificationStore;

const loading = ref(false);
const items = ref([]);

const { isAdmin } = usePermissions();

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

const loadPending = async () => {
    loading.value = true;
    try {
        const response = await fetch("/api/scrapping/consumable-types/pending", {
            headers: { Accept: "application/json" },
        });
        const data = await response.json();
        if (response.ok && data.success) {
            items.value = data.data || [];
        } else {
            showError(data.message || "Impossible de charger les types en attente");
        }
    } catch (e) {
        showError("Erreur lors du chargement : " + e.message);
    } finally {
        loading.value = false;
    }
};

const updateDecision = async (consumableTypeId, decision) => {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        showError("Token CSRF introuvable. Veuillez recharger la page.");
        return;
    }

    try {
        const response = await fetch(`/api/scrapping/consumable-types/${consumableTypeId}/decision`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify({ decision }),
        });
        const data = await response.json();
        if (response.ok && data.success) {
            success("Statut enregistré", { duration: 2500 });
            if (decision !== "pending") {
                items.value = items.value.filter((i) => i.id !== consumableTypeId);
            }
        } else {
            showError(data.message || "Erreur lors de la mise à jour");
        }
    } catch (e) {
        showError("Erreur lors de la mise à jour : " + e.message);
    }
};

const decisionToUx = (decision) => {
    if (decision === "allowed") return "used";
    if (decision === "blocked") return "unused";
    return "pending";
};

const formatDate = (iso) => {
    if (!iso) return "-";
    try {
        return new Date(iso).toLocaleString("fr-FR");
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
                <h2 class="text-xl font-bold text-primary-100">Types DofusDB détectés (Consommables)</h2>
                <p class="text-sm text-primary-300 mt-1">
                    Ces typeId ont été détectés pendant le scrapping des consommables. Indique si on les utilise.
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
                        <td>{{ t.name }}</td>
                        <td>{{ t.seen_count }}</td>
                        <td>{{ formatDate(t.last_seen_at) }}</td>
                        <td class="text-right">
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Card>
</template>

