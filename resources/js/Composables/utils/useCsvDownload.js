/**
 * useCsvDownload
 *
 * @description
 * Utilitaire pour construire et télécharger un fichier CSV (UTF-8 avec BOM pour Excel).
 * Réutilisable pour export erreurs import batch et export prévisualisation batch.
 *
 * @example
 * downloadCsvFromRows([['ID','Nom','Statut']], [['1','Bouftou','OK']], 'export-2025-02-21.csv');
 */

const UTF8_BOM = "\uFEFF";

/**
 * Échappe une cellule CSV (guillemets si contient ; ou " ou \n).
 * @param {string|number} cell
 * @returns {string}
 */
function escapeCsvCell(cell) {
    const s = String(cell ?? "");
    if (s.includes(";") || s.includes('"') || s.includes("\n")) return `"${s.replace(/"/g, '""')}"`;
    return s;
}

/**
 * Construit une ligne CSV (séparateur ;).
 * @param {Array<string|number>} row
 * @returns {string}
 */
function csvLine(row) {
    return row.map(escapeCsvCell).join(";") + "\n";
}

/**
 * Télécharge un fichier CSV à partir de lignes (headers + rows).
 * @param {Array<string>} headers - En-têtes de colonnes.
 * @param {Array<Array<string|number>>} rows - Lignes de données.
 * @param {string} filename - Nom du fichier (ex. export-erreurs-2025-02-21.csv).
 */
export function downloadCsvFromRows(headers, rows, filename = "export.csv") {
    const line1 = csvLine(headers);
    const body = rows.map((row) => csvLine(row)).join("");
    const csv = UTF8_BOM + line1 + body;
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

/**
 * Construit le nom de fichier avec timestamp pour export erreurs batch.
 * @returns {string}
 */
export function filenameForBatchErrors() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, "0");
    return `scrapping-erreurs-import-${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}.csv`;
}

/**
 * Construit le nom de fichier avec timestamp pour export prévisualisation batch.
 * @returns {string}
 */
export function filenameForBatchPreview() {
    const d = new Date();
    const pad = (n) => String(n).padStart(2, "0");
    return `scrapping-previsualisation-${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}-${pad(d.getHours())}${pad(d.getMinutes())}.csv`;
}

/**
 * Convertit lastBatchErrorResults (API batch import) en lignes CSV.
 * @param {Array<{type?: string, id?: number, success?: boolean, error?: string, validation_errors?: Array<{path?: string, message?: string}>}>} results
 * @returns {{ headers: string[], rows: Array<string[]> }}
 */
export function buildCsvFromErrorResults(results) {
    const headers = ["type", "id", "statut", "message", "détails"];
    const rows = (results || []).map((r) => {
        const details = Array.isArray(r.validation_errors)
            ? r.validation_errors.map((ve) => `${ve.path ?? ""}: ${ve.message ?? ""}`).join(" | ")
            : "";
        return [r.type ?? "", r.id ?? "", "Erreur", r.error ?? "", details];
    });
    return { headers, rows };
}

/**
 * Convertit batchPreviewResults en lignes CSV.
 * @param {Array<{id: number, name: string, status: string, error?: string|null}>} results
 * @returns {{ headers: string[], rows: Array<string[]> }}
 */
export function buildCsvFromPreviewResults(results) {
    const headers = ["id", "nom", "statut", "message"];
    const rows = (results || []).map((r) => [r.id ?? "", r.name ?? "", r.status === "ok" ? "OK" : "Erreur", r.error ?? ""]);
    return { headers, rows };
}

export default {
    downloadCsvFromRows,
    filenameForBatchErrors,
    filenameForBatchPreview,
    buildCsvFromErrorResults,
    buildCsvFromPreviewResults,
};
