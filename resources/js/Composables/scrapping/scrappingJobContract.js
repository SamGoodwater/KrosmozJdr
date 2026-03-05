/**
 * Contrat d'etat pour les jobs de scrapping.
 *
 * @description
 * Centralise les statuts et valeurs par defaut utilises par le manager de job
 * et les ecrans scrapping pour conserver un comportement coherent.
 */

/** @type {readonly string[]} */
export const SCRAPPING_JOB_STATUSES = [
    "idle",
    "running",
    "cancelling",
    "success",
    "error",
    "cancelled",
];

/**
 * @typedef {Object} ScrappingJobProgress
 * @property {string} phase
 * @property {number} done
 * @property {number} total
 * @property {number} percent
 * @property {string} label
 */

/**
 * @typedef {Object} ScrappingJobState
 * @property {string|null} id
 * @property {string|null} kind
 * @property {string} status
 * @property {string} label
 * @property {ScrappingJobProgress} progress
 * @property {string|null} runId
 * @property {string|null} backendJobId
 * @property {Record<string, any>|null} unknownCharacteristics
 * @property {string|null} error
 * @property {boolean} canCancel
 * @property {number|null} startedAt
 * @property {number|null} endedAt
 */

/**
 * @returns {ScrappingJobState}
 */
export function createIdleScrappingJobState() {
    return {
        id: null,
        kind: null,
        status: "idle",
        label: "",
        progress: {
            phase: "idle",
            done: 0,
            total: 0,
            percent: 0,
            label: "",
        },
        runId: null,
        backendJobId: null,
        unknownCharacteristics: null,
        error: null,
        canCancel: false,
        startedAt: null,
        endedAt: null,
    };
}

/**
 * @param {string} status
 * @returns {boolean}
 */
export function isTerminalScrappingJobStatus(status) {
    return status === "success" || status === "error" || status === "cancelled";
}
