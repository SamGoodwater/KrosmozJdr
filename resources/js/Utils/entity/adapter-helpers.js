/**
 * Helpers communs pour les adapters
 *
 * @description
 * Fonctions utilitaires partagées entre les adapters pour réduire la duplication de code.
 */

/**
 * Convertit une valeur en nombre, ou retourne null si invalide.
 *
 * @param {any} v
 * @returns {number|null}
 */
export function toNumber(v) {
  const n = typeof v === "string" ? Number(v) : v;
  return typeof n === "number" && Number.isFinite(n) ? n : null;
}

/**
 * Formate une date ISO en format français (DD/MM/YYYY HH:mm).
 *
 * @param {string|null} isoString
 * @returns {string}
 */
export function formatDateFr(isoString) {
  if (!isoString) return "-";
  const ms = Date.parse(String(isoString));
  if (!Number.isFinite(ms)) return "-";
  const d = new Date(ms);
  const pad2 = (n) => String(n).padStart(2, "0");
  return `${pad2(d.getDate())}/${pad2(d.getMonth() + 1)}/${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
}

/**
 * Labels de visibilité standardisés.
 */
export const VISIBILITY_LABELS = Object.freeze({
  guest: "Invité",
  user: "Utilisateur",
  player: "Joueur",
  game_master: "Maître du jeu",
  admin: "Administrateur",
  super_admin: "Super administrateur",
});

/**
 * Retourne la couleur DaisyUI pour un niveau de visibilité.
 *
 * @param {string} v
 * @returns {string}
 */
export function visibilityColor(v) {
  const s = String(v ?? "");
  if (s === "admin" || s === "super_admin") return "error";
  if (s === "game_master") return "warning";
  if (s === "user" || s === "player") return "info";
  return "neutral";
}

/**
 * Labels de rareté standardisés.
 */
export const RARITY_LABELS = Object.freeze({
  0: "Commun",
  1: "Peu commun",
  2: "Rare",
  3: "Très rare",
  4: "Légendaire",
  5: "Unique",
});

/**
 * Retourne la couleur DaisyUI pour un niveau de rareté.
 *
 * @param {number|string} v
 * @returns {string}
 */
export function rarityColor(v) {
  const n = toNumber(v);
  if (n === 0) return "success";
  if (n === 1) return "info";
  if (n === 2) return "primary";
  if (n === 3) return "warning";
  if (n === 4) return "error";
  if (n === 5) return "neutral";
  return "primary";
}

/**
 * Génère une cellule texte standardisée.
 *
 * @param {any} value
 * @param {Object} [opts]
 * @param {any} [opts.sortValue]
 * @param {string} [opts.searchValue]
 * @param {string} [opts.filterValue]
 * @returns {{type: string, value: string, params: Object}}
 */
export function buildTextCell(value, opts = {}) {
  const v = value ?? null;
  const text = v === null || typeof v === "undefined" || v === "" ? "-" : String(v);
  return {
    type: "text",
    value: text,
    params: {
      sortValue: opts.sortValue ?? text,
      searchValue: opts.searchValue ?? (text === "-" ? "" : text),
      filterValue: opts.filterValue,
    },
  };
}

/**
 * Génère une cellule badge standardisée.
 *
 * @param {string} label
 * @param {string} color
 * @param {Object} [opts]
 * @param {any} [opts.sortValue]
 * @param {string} [opts.filterValue]
 * @returns {{type: string, value: string, params: Object}}
 */
export function buildBadgeCell(label, color, opts = {}) {
  return {
    type: "badge",
    value: label,
    params: {
      color,
      sortValue: opts.sortValue ?? label,
      filterValue: opts.filterValue,
    },
  };
}

/**
 * Génère une cellule route standardisée.
 *
 * @param {string} label
 * @param {string|null} href
 * @param {Object} [opts]
 * @param {string} [opts.tooltip]
 * @param {Object} [opts.truncate]
 * @param {string} [opts.searchValue]
 * @param {string} [opts.sortValue]
 * @returns {{type: string, value: string, params: Object}}
 */
export function buildRouteCell(label, href, opts = {}) {
  const text = label || "-";
  return {
    type: "route",
    value: text,
    params: {
      href: href || null,
      tooltip: opts.tooltip ?? (text === "-" ? "" : String(text)),
      truncate: opts.truncate,
      searchValue: opts.searchValue ?? (text === "-" ? "" : String(text)),
      sortValue: opts.sortValue ?? (text === "-" ? "" : String(text)),
    },
  };
}

/**
 * Génère une cellule pour un champ booléen.
 *
 * @param {any} value
 * @param {string} [mode] - "badge" ou "text"
 * @returns {{type: string, value: string, params: Object}}
 */
export function buildBoolCell(value, mode = "badge") {
  const boolValue = value === 1 || value === true || String(value) === "1";
  const label = boolValue ? "Oui" : "Non";
  if (mode === "badge") {
    return buildBadgeCell(label, boolValue ? "success" : "neutral", {
      sortValue: boolValue ? 1 : 0,
    });
  }
  return buildTextCell(label, {
    sortValue: boolValue ? 1 : 0,
  });
}

/**
 * Génère une cellule pour un champ created_by (relation User).
 *
 * @param {Object|null} user
 * @returns {{type: string, value: string, params: Object}}
 */
export function buildCreatedByCell(user) {
  const label = user?.name || user?.email || "-";
  return buildTextCell(label, {
    searchValue: label === "-" ? "" : label,
    sortValue: label,
  });
}

/**
 * Génère une cellule pour un champ de date (created_at, updated_at).
 *
 * @param {string|null} isoString
 * @returns {{type: string, value: string, params: Object}}
 */
export function buildDateCell(isoString) {
  const label = formatDateFr(isoString);
  const sortValue = isoString ? new Date(isoString).getTime() : 0;
  return buildTextCell(label, {
    searchValue: label === "-" ? "" : label,
    sortValue,
  });
}

