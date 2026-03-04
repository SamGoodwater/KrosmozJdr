import { computed } from "vue";

/**
 * Retourne l'état de configuration scrapping pour une entité.
 * Centralise la lecture de configError + warning bloquant + diagnostics.
 *
 * @param {Record<string, any>|null|undefined} configByEntity
 * @param {string} entityType
 * @returns {{
 *   config: any|null,
 *   diagnostics: any|null,
 *   configError: string,
 *   hasConfigError: boolean,
 *   blockingWarning: any|null
 * }}
 */
export function getEntityConfigStatus(configByEntity, entityType) {
    const type = String(entityType || "").trim();
    const config = type ? (configByEntity?.[type] ?? null) : null;
    const diagnostics = config?.mappingDiagnostics && typeof config.mappingDiagnostics === "object"
        ? config.mappingDiagnostics
        : null;
    const configError = String(config?.configError || "").trim();
    const warnings = Array.isArray(diagnostics?.warnings) ? diagnostics.warnings : [];
    const blockingWarning = warnings.find((w) => w?.severity === "blocking") || null;

    return {
        config,
        diagnostics,
        configError,
        hasConfigError: configError.length > 0,
        blockingWarning,
    };
}

/**
 * Version composable réactive pour une entité sélectionnée.
 *
 * @param {import('vue').Ref<Record<string, any>>} configByEntityRef
 * @param {import('vue').Ref<string>} entityTypeRef
 */
export function useScrappingEntityConfigStatus(configByEntityRef, entityTypeRef) {
    return computed(() =>
        getEntityConfigStatus(configByEntityRef?.value, entityTypeRef?.value)
    );
}

