<script setup>
/**
 * Lecture du catalogue de fichiers téléchargeables.
 *
 * @example
 * <SectionDownloadCatalogRead :section="section" :settings="{}" />
 */
import { computed, onMounted, ref } from "vue";
import axios from "axios";

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const loading = ref(false);
const error = ref(null);
const groups = ref([]);

const allowedGroups = computed(() => {
    const raw = props.settings?.groups;
    if (Array.isArray(raw)) {
        return raw.map((g) => String(g).trim()).filter(Boolean);
    }
    if (typeof raw === "string" && raw.trim() !== "") {
        return raw.split(",").map((g) => g.trim()).filter(Boolean);
    }
    return [];
});

const visibleGroups = computed(() => {
    if (allowedGroups.value.length === 0) {
        return groups.value;
    }
    const allow = new Set(allowedGroups.value);
    return groups.value.filter((g) => allow.has(g.key));
});

async function fetchCatalog() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await axios.get("/api/game-downloads");
        groups.value = Array.isArray(data?.groups) ? data.groups : [];
    } catch (e) {
        error.value = e?.response?.data?.message || e?.message || "Impossible de charger les fichiers.";
        groups.value = [];
    } finally {
        loading.value = false;
    }
}

/**
 * @param {number|null} bytes
 * @returns {string}
 */
function formatSize(bytes) {
    if (bytes == null || !Number.isFinite(Number(bytes))) {
        return "";
    }
    const n = Number(bytes);
    if (n < 1024) {
        return `${n} o`;
    }
    if (n < 1024 * 1024) {
        return `${(n / 1024).toFixed(1)} Ko`;
    }
    return `${(n / (1024 * 1024)).toFixed(1)} Mo`;
}

/**
 * @param {string|null} iso
 * @returns {string}
 */
function formatDate(iso) {
    if (!iso) {
        return "";
    }
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) {
        return "";
    }
    return d.toLocaleDateString("fr-FR", { day: "numeric", month: "long", year: "numeric" });
}

onMounted(fetchCatalog);
</script>

<template>
    <div class="space-y-6">
        <p v-if="loading" class="text-sm opacity-70">Chargement des fichiers…</p>
        <p v-else-if="error" class="text-sm text-error">{{ error }}</p>
        <p v-else-if="visibleGroups.length === 0" class="text-sm opacity-70">
            Aucun fichier à afficher pour le moment.
        </p>

        <section v-for="group in visibleGroups" :key="group.key" class="space-y-3">
            <h3 class="text-lg font-semibold">{{ group.label }}</h3>
            <ul class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <li
                    v-for="item in group.items"
                    :key="item.key"
                    class="rounded-box border border-base-content/20 bg-base-100/40 p-4 flex flex-col gap-2"
                >
                    <p class="font-semibold flex items-center gap-2">
                        <i class="fa-solid" :class="item.icon" aria-hidden="true" />
                        <span>{{ item.label }}</span>
                    </p>
                    <p class="text-sm opacity-80">{{ item.description }}</p>
                    <p v-if="item.available" class="text-xs opacity-60">
                        {{ formatSize(item.size) }}
                        <span v-if="formatDate(item.updated_at)"> · {{ formatDate(item.updated_at) }}</span>
                    </p>
                    <p v-else class="text-xs opacity-60">
                        Pas encore compilé — un administrateur doit lancer la génération.
                    </p>
                    <a
                        v-if="item.available"
                        class="btn btn-primary btn-sm mt-auto w-fit"
                        :href="item.download_url"
                    >
                        Télécharger
                    </a>
                    <span v-else class="btn btn-disabled btn-sm mt-auto w-fit">Indisponible</span>
                </li>
            </ul>
        </section>
    </div>
</template>
