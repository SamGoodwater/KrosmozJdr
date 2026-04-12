<script setup>
/**
 * Modal d’édition capacité : même contenu que {@link Pages/entity/capability/Edit}
 * (données chargées via JSON pour rester sur la liste).
 */
import { ref, watch } from "vue";
import axios from "axios";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import CapabilityEditFormContent from "@/Pages/Organismes/entity/CapabilityEditFormContent.vue";

const props = defineProps({
    open: { type: Boolean, default: false },
    capabilityId: { type: [Number, String], default: null },
});

const emit = defineEmits(["close", "saved"]);

const loading = ref(false);
const loadError = ref("");
const payload = ref(null);

function resetState() {
    loadError.value = "";
    payload.value = null;
}

async function fetchPayload(id) {
    if (id == null || id === "") {
        return;
    }
    loading.value = true;
    loadError.value = "";
    payload.value = null;
    try {
        const { data } = await axios.get(route("entities.capabilities.edit-payload", { capability: id }), {
            headers: { Accept: "application/json" },
        });
        payload.value = data;
    } catch (e) {
        loadError.value =
            e?.response?.data?.message ||
            e?.message ||
            "Impossible de charger la fiche d’édition de la capacité.";
    } finally {
        loading.value = false;
    }
}

watch(
    () => [props.open, props.capabilityId],
    ([isOpen, id]) => {
        if (!isOpen) {
            resetState();
            return;
        }
        fetchPayload(id);
    },
    { immediate: true },
);

const handleClose = () => {
    emit("close");
};
</script>

<template>
    <Modal
        :open="open"
        size="full"
        placement="middle-center"
        close-on-esc
        :close-on-outside-click="false"
        :draggable="false"
        @close="handleClose"
    >
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-2 pr-8">
                <h3 class="text-xl font-bold text-primary-100">
                    Modifier la capacité
                    <span v-if="payload?.capability?.name" class="text-base font-semibold text-base-content/80">
                        : {{ payload.capability.name }}
                    </span>
                </h3>
                <Btn color="neutral" variant="ghost" size="sm" type="button" @click="handleClose">
                    Fermer
                </Btn>
            </div>
        </template>

        <div class="max-h-[min(85vh,920px)] overflow-y-auto pr-1 pb-28 md:pb-32">
            <div v-if="loading" class="py-12 text-center text-sm text-base-content/70">
                Chargement de l’éditeur…
            </div>
            <div
                v-else-if="loadError"
                class="rounded-lg border border-error/40 bg-error/10 px-4 py-3 text-sm text-error"
            >
                {{ loadError }}
            </div>
            <CapabilityEditFormContent
                v-else-if="payload"
                :capability="payload.capability"
                embedded-in-modal
                redirect-after-update="stay"
                @cancel="handleClose"
                @saved="emit('saved')"
            />
        </div>
    </Modal>
</template>
