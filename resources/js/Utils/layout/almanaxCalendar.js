/**
 * Mapping Almanax/Dofus basé sur le mois réel.
 *
 * @example
 * getAlmanaxMonth(new Date("2026-07-04")).name // "Joullier"
 */
export const ALMANAX_MONTHS = [
    { name: "Javian", file: "javian.png" },
    { name: "Flovor", file: "flovor.png" },
    { name: "Martalo", file: "martalo.png" },
    { name: "Aperirel", file: "aperirel.png" },
    { name: "Maisial", file: "maisial.png" },
    { name: "Juinssidor", file: "juinssidor.png" },
    { name: "Joullier", file: "joullier.png" },
    { name: "Fraouctor", file: "fraouctor.png" },
    { name: "Septange", file: "septange.png" },
    { name: "Octolliard", file: "octolliard.png" },
    { name: "Novamaire", file: "novamaire.png" },
    { name: "Descendre", file: "descendre.png" },
];

/**
 * Retourne le mois Almanax associé à une date réelle.
 *
 * @param {Date} date
 * @returns {{ name: string, file: string }}
 */
export function getAlmanaxMonth(date = new Date()) {
    const monthIndex = Number.isFinite(date?.getMonth?.()) ? date.getMonth() : 0;
    return ALMANAX_MONTHS[monthIndex] ?? ALMANAX_MONTHS[0];
}

/**
 * Construit l'URL publique de l'icône de mois Almanax.
 *
 * @param {{ file: string }} month
 * @returns {string}
 */
export function getAlmanaxMonthIconUrl(month) {
    return `/storage/images/calendar/${month.file}`;
}
