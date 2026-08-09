/**
 * Store Pinia — modal Favoris (accès header sans changer de page).
 */
import { defineStore } from "pinia";

export const useFavoritesUiStore = defineStore("favoritesUi", {
    state: () => ({
        isOpen: false,
    }),
    actions: {
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        },
        toggle() {
            this.isOpen = !this.isOpen;
        },
    },
});
