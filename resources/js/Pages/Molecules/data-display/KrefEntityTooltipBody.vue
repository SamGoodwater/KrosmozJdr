<script setup>
/**
 * Corps d’infobulle pour une référence kref « entité » (aperçu léger, chargé au survol puis mis en cache).
 *
 * @props {string} entityType — ex. spells, items
 * @props {string|number} id — identifiant numérique
 */
import { ref, watch, onMounted } from "vue";
import {
    getCachedKrefEntityPreview,
    loadKrefEntityPreview,
} from "@/Composables/richText/krefEntityPreviewCache";
import Image from "@/Pages/Atoms/data-display/Image.vue";

const props = defineProps({
    entityType: {
        type: String,
        required: true,
    },
    id: {
        type: [String, Number],
        required: true,
    },
});

const loading = ref(true);
const error = ref("");
const payload = ref(null);

async function resolvePayload() {
    error.value = "";
    const et = String(props.entityType || "").trim();
    const id = props.id;
    if (!et || id == null || id === "") {
        loading.value = false;
        error.value = "Référence incomplète.";
        payload.value = null;
        return;
    }

    const cached = getCachedKrefEntityPreview(et, id);
    if (cached) {
        payload.value = cached;
        loading.value = false;
        return;
    }

    loading.value = true;
    payload.value = null;
    try {
        payload.value = await loadKrefEntityPreview(et, id);
    } catch (e) {
        if (e?.code === 401) {
            error.value = "Connectez-vous pour voir l’aperçu.";
        } else {
            error.value = "Aperçu indisponible.";
        }
        payload.value = null;
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    void resolvePayload();
});

watch(
    () => [props.entityType, props.id],
    () => {
        void resolvePayload();
    },
);
</script>

<template>
    <div class="kref-entity-tooltip max-w-xs text-left text-sm">
        <div v-if="loading" class="text-base-content/60 italic">Chargement…</div>
        <p v-else-if="error" class="text-error text-xs">{{ error }}</p>
        <div v-else-if="payload" class="flex gap-2">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-md border border-base-300/60 bg-base-200"
            >
                <Image
                    v-if="payload.image"
                    :source="payload.image"
                    :alt="payload.name || 'Entité'"
                    fit="contain"
                    class="h-full w-full"
                />
                <i v-else class="fa-solid fa-cube text-base-content/35 text-sm" aria-hidden="true" />
            </div>
            <div class="min-w-0 flex-1 space-y-0.5">
                <p class="text-sm font-semibold leading-tight text-base-content">
                    {{ payload.name || "Entité" }}
                </p>
                <ul
                    v-if="Array.isArray(payload.meta) && payload.meta.length"
                    class="list-none space-y-0.5 text-xs text-base-content/75"
                >
                    <li v-for="(line, idx) in payload.meta" :key="`m-${idx}`">{{ line }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>
