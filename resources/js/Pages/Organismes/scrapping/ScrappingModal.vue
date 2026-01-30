<script setup>
/**
 * ScrappingModal (Organism)
 *
 * @description
 * Wrapper modal autour de `ScrappingSection` pour le cas "mettre à jour un élément précis"
 * depuis une autre page (ex: page show/edit d'une entité).
 */
import { computed } from "vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import ScrappingSection from "@/Pages/Organismes/scrapping/ScrappingSection.vue";

const props = defineProps({
    open: { type: Boolean, default: false },
    entityType: { type: String, required: true },
    dofusdbId: { type: [String, Number], required: true },
    title: { type: String, default: "Mettre à jour via scrapping" },
});

const emit = defineEmits(["close", "import-success"]);

const idStr = computed(() => String(props.dofusdbId ?? "").trim());
</script>

<template>
    <Modal
        :open="open"
        size="xl"
        variant="glass"
        placement="middle-center"
        :close-on-esc="true"
        @close="emit('close')"
    >
        <template #header>
            <div class="flex items-center justify-between gap-3 w-full">
                <div class="font-semibold text-primary-100">
                    {{ title }}
                </div>
                <Btn size="sm" variant="ghost" data-no-row-select @click="emit('close')">
                    Fermer
                </Btn>
            </div>
        </template>

        <ScrappingSection
            variant="modal"
            :locked-entity-type="entityType"
            :initial-id="idStr"
            :available-modes="['single']"
            :lock-search-inputs="true"
            :auto-preview="true"
            :show-history="false"
            @import-success="(payload) => emit('import-success', payload)"
        />
    </Modal>
</template>

