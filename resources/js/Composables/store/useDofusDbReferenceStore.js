/**
 * Store Pinia — panneau de référence DofusDB.
 *
 * @description
 * Une seule source de vérité app-wide (évite singleton module / HMR cassé).
 * Ouverture depuis EntityActions ; rendu dans le layout Main.
 */
import { defineStore } from 'pinia';
import {
    buildDofusDbEntityUrl,
    getEntityDofusDbId,
} from '@/Utils/dofusdb/buildDofusDbEntityUrl';

const WINDOW_NAME = 'krosmoz-dofusdb-ref';

export const useDofusDbReferenceStore = defineStore('dofusDbReference', {
    state: () => ({
        isOpen: false,
        entityType: '',
        /** @type {Object|null} */
        entity: null,
        popupBlocked: false,
    }),

    getters: {
        dofusdbId: (state) => getEntityDofusDbId(state.entity),
        dofusDbUrl: (state) =>
            buildDofusDbEntityUrl(state.entityType, getEntityDofusDbId(state.entity)),
        entityLabel: (state) => {
            const target = state.entity;
            if (!target) return '';
            return (
                target.name
                || target.title
                || target._data?.name
                || target._data?.title
                || ''
            );
        },
    },

    actions: {
        /**
         * @param {string} entityType
         * @param {Object|null} entity
         */
        openPanel(entityType, entity = null) {
            this.entityType = String(entityType || '');
            this.entity = entity;
            this.popupBlocked = false;
            this.isOpen = true;
        },

        closePanel() {
            this.isOpen = false;
        },

        /**
         * @returns {boolean}
         */
        openExternalWindow() {
            const url = this.dofusDbUrl;
            if (!url || typeof window === 'undefined') return false;

            const win = window.open(url, WINDOW_NAME, 'width=1100,height=800');
            if (!win) {
                this.popupBlocked = true;
                return false;
            }

            try {
                win.opener = null;
            } catch {
                // ignore
            }

            this.popupBlocked = false;
            try {
                win.focus();
            } catch {
                // ignore
            }
            return true;
        },
    },
});
