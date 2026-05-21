/**
 * Store Pinia — métadonnées caractéristiques (API /api/characteristics).
 *
 * @description
 * Chargement unique par session ; fallback sur props Inertia legacy si présentes.
 *
 * @example
 * const store = useCharacteristicsPiniaStore();
 * await store.fetchOnce();
 */
import { defineStore } from "pinia";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

export const useCharacteristicsPiniaStore = defineStore("characteristics", {
    state: () => ({
        /** @type {Record<string, object>|null} */
        data: null,
        loading: false,
        error: null,
        fetched: false,
    }),

    getters: {
        /**
         * @returns {Record<string, object>}
         */
        characteristics(state) {
            if (state.data && typeof state.data === "object") {
                return state.data;
            }

            try {
                const page = usePage();
                const legacy = page?.props?.characteristics;
                return legacy && typeof legacy === "object" ? legacy : {};
            } catch {
                return {};
            }
        },
    },

    actions: {
        /**
         * Charge les métadonnées une fois par session (idempotent).
         *
         * @returns {Promise<Record<string, object>>}
         */
        async fetchOnce() {
            if (this.fetched && this.data) {
                return this.data;
            }

            if (this.loading) {
                return new Promise((resolve) => {
                    const stop = this.$subscribe(() => {
                        if (!this.loading) {
                            stop();
                            resolve(this.data ?? {});
                        }
                    });
                });
            }

            try {
                const page = usePage();
                const legacy = page?.props?.characteristics;
                if (legacy && typeof legacy === "object" && Object.keys(legacy).length > 0) {
                    this.data = legacy;
                    this.fetched = true;

                    return legacy;
                }
            } catch {
                // hors contexte Inertia
            }

            this.loading = true;
            this.error = null;

            try {
                const { data } = await axios.get("/api/characteristics");
                this.data = data && typeof data === "object" ? data : {};
                this.fetched = true;

                return this.data;
            } catch (err) {
                this.error = err;
                this.data = {};

                throw err;
            } finally {
                this.loading = false;
            }
        },
    },
});
