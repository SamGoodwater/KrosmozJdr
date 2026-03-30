<script setup>
/**
 * Aide : raccourcis clavier et clics souris du tableau TanStack.
 *
 * @description
 * Liste complète : trop d’entrées pour un seul tooltip → bouton + modal.
 */
import { computed } from "vue";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    TANSTACK_TABLE_KEYBOARD_SHORTCUTS,
    TANSTACK_TABLE_POINTER_SHORTCUTS,
} from "@/Composables/table/useTanStackTableKeyboard.js";
import TanStackTableShortcutKeysDisplay from "@/Pages/Molecules/table/TanStackTableShortcutKeysDisplay.vue";

const props = defineProps({
    open: { type: Boolean, default: false },
    /** xs | sm | md */
    uiSize: { type: String, default: "xs" },
    uiColor: { type: String, default: "primary" },
});

const emit = defineEmits(["close"]);

const btnSize = computed(() => (props.uiSize === "xs" ? "xs" : props.uiSize === "sm" ? "sm" : "md"));

const close = () => emit("close");
</script>

<template>
    <Modal
        :open="open"
        size="lg"
        variant="glass"
        placement="middle-center"
        close-on-esc
        @close="close"
    >
        <template #header>
            <h3 class="text-lg font-semibold text-primary-100 flex items-center gap-2">
                <Icon source="fa-solid fa-keyboard" class="opacity-90" alt="" />
                Raccourcis tableau
            </h3>
        </template>

        <div class="space-y-6 text-sm text-base-content/90 max-h-[min(70vh,520px)] overflow-y-auto pr-1">
            <section>
                <h4 class="font-medium text-primary-200 mb-2">Clavier (focus sur une ligne ou la zone tableau)</h4>
                <table class="table table-sm w-full">
                    <thead>
                        <tr class="border-b border-base-300">
                            <th class="text-left font-normal text-base-content/70 w-[40%]">Touches</th>
                            <th class="text-left font-normal">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, idx) in TANSTACK_TABLE_KEYBOARD_SHORTCUTS" :key="'k' + idx">
                            <td class="align-top">
                                <TanStackTableShortcutKeysDisplay :keys="row.keys" />
                            </td>
                            <td>{{ row.action }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <section>
                <h4 class="font-medium text-primary-200 mb-2">Souris</h4>
                <table class="table table-sm w-full">
                    <thead>
                        <tr class="border-b border-base-300">
                            <th class="text-left font-normal text-base-content/70 w-[40%]">Interaction</th>
                            <th class="text-left font-normal">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, idx) in TANSTACK_TABLE_POINTER_SHORTCUTS" :key="'p' + idx">
                            <td class="align-top">
                                <TanStackTableShortcutKeysDisplay :keys="row.keys" />
                            </td>
                            <td>{{ row.action }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>

        <template #actions>
            <Btn :size="btnSize" variant="primary" :color="uiColor" @click="close">Fermer</Btn>
        </template>
    </Modal>
</template>
