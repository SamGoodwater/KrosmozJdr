<script setup>
/**
 * FavoritesModal — Modal global des favoris (ouvert depuis le header).
 */
import { storeToRefs } from "pinia";
import Modal from "@/Pages/Molecules/action/Modal.vue";
import FavoritesPanel from "@/Pages/Organismes/entity/FavoritesPanel.vue";
import { useFavoritesUiStore } from "@/Composables/store/useFavoritesUiStore";

const store = useFavoritesUiStore();
const { isOpen } = storeToRefs(store);
</script>

<template>
    <Modal
        :open="isOpen"
        size="lg"
        variant="glass"
        color="primary"
        placement="middle-center"
        :close-on-esc="true"
        :close-on-outside-click="true"
        :draggable="true"
        data-cy="favorites-modal"
        @close="store.close()"
    >
        <FavoritesPanel surface="modal" @close="store.close()" />
    </Modal>
</template>
