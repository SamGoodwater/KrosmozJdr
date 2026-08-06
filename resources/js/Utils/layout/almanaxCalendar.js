/**
 * Mapping Almanax / Monde des Douze basé sur la date réelle.
 * Le numéro du jour et la semaine restent ceux du calendrier grégorien ;
 * seuls les noms de mois changent (janvier → Javian, etc.).
 *
 * @example
 * getAlmanaxMonth(new Date("2026-08-06")).name // "Fraouctor"
 * formatAlmanaxDayLabel(new Date("2026-08-06")) // "6 Fraouctor"
 */

export const ALMANAX_MONTHS = [
    { name: "Javian", file: "javian.png", gregorianMonth: "janvier" },
    { name: "Flovor", file: "flovor.png", gregorianMonth: "février" },
    { name: "Martalo", file: "martalo.png", gregorianMonth: "mars" },
    { name: "Aperirel", file: "aperirel.png", gregorianMonth: "avril" },
    { name: "Maisial", file: "maisial.png", gregorianMonth: "mai" },
    { name: "Juinssidor", file: "juinssidor.png", gregorianMonth: "juin" },
    { name: "Joullier", file: "joullier.png", gregorianMonth: "juillet" },
    { name: "Fraouctor", file: "fraouctor.png", gregorianMonth: "août" },
    { name: "Septange", file: "septange.png", gregorianMonth: "septembre" },
    { name: "Octolliard", file: "octolliard.png", gregorianMonth: "octobre" },
    { name: "Novamaire", file: "novamaire.png", gregorianMonth: "novembre" },
    { name: "Descendre", file: "descendre.png", gregorianMonth: "décembre" },
];

/** Libellés courts des jours (lundi → dimanche), alignés sur getDay() JS (0 = dimanche). */
export const ALMANAX_WEEKDAY_SHORT = ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"];

/** Ordre d’affichage calendrier (lundi en premier). */
export const ALMANAX_WEEKDAY_HEADERS = ["Lu", "Ma", "Me", "Je", "Ve", "Sa", "Di"];

/**
 * @param {Date} [date]
 * @returns {{ name: string, file: string, gregorianMonth: string, index: number }}
 */
export function getAlmanaxMonth(date = new Date()) {
    const monthIndex = Number.isFinite(date?.getMonth?.()) ? date.getMonth() : 0;
    const month = ALMANAX_MONTHS[monthIndex] ?? ALMANAX_MONTHS[0];
    return { ...month, index: monthIndex };
}

/**
 * @param {{ file: string }} month
 * @returns {string}
 */
export function getAlmanaxMonthIconUrl(month) {
    return `/storage/images/calendar/${month.file}`;
}

/**
 * Date Almanax dérivée d’une date réelle (même jour / même année civile).
 *
 * @param {Date} [date]
 * @returns {{
 *   day: number,
 *   year: number,
 *   weekdayIndex: number,
 *   month: ReturnType<typeof getAlmanaxMonth>,
 *   almanaxLabel: string,
 *   gregorianLabel: string,
 *   tooltip: string,
 * }}
 */
export function getAlmanaxDate(date = new Date()) {
    const safe = date instanceof Date && !Number.isNaN(date.getTime()) ? date : new Date();
    const day = safe.getDate();
    const year = safe.getFullYear();
    const month = getAlmanaxMonth(safe);
    const almanaxLabel = `${day} ${month.name}`;
    const gregorianLabel = safe.toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });

    return {
        day,
        year,
        weekdayIndex: safe.getDay(),
        month,
        almanaxLabel,
        gregorianLabel,
        tooltip: `Aujourd’hui : ${almanaxLabel} (${gregorianLabel})`,
    };
}

/**
 * @param {Date} [date]
 * @returns {string}
 */
export function formatAlmanaxDayLabel(date = new Date()) {
    return getAlmanaxDate(date).almanaxLabel;
}

/**
 * Construit la grille d’un mois (lundi → dimanche) avec jours du mois voisin en padding.
 *
 * @param {number} year
 * @param {number} monthIndex 0–11
 * @returns {Array<Array<{ day: number, inMonth: boolean, date: Date, almanaxLabel: string, isToday: boolean }|null>>}
 */
export function buildAlmanaxMonthGrid(year, monthIndex) {
    const first = new Date(year, monthIndex, 1);
    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
    // getDay(): 0 dimanche → décalage pour commencer lundi
    const startOffset = (first.getDay() + 6) % 7;
    const today = new Date();
    const todayKey = `${today.getFullYear()}-${today.getMonth()}-${today.getDate()}`;

    /** @type {Array<{ day: number, inMonth: boolean, date: Date, almanaxLabel: string, isToday: boolean }|null>} */
    const cells = [];

    for (let i = 0; i < startOffset; i += 1) {
        const d = new Date(year, monthIndex, 1 - (startOffset - i));
        cells.push(makeCell(d, false, todayKey));
    }

    for (let day = 1; day <= daysInMonth; day += 1) {
        cells.push(makeCell(new Date(year, monthIndex, day), true, todayKey));
    }

    while (cells.length % 7 !== 0) {
        const last = cells[cells.length - 1]?.date;
        const next = last
            ? new Date(last.getFullYear(), last.getMonth(), last.getDate() + 1)
            : new Date(year, monthIndex + 1, 1);
        cells.push(makeCell(next, false, todayKey));
    }

    /** @type {Array<Array<{ day: number, inMonth: boolean, date: Date, almanaxLabel: string, isToday: boolean }|null>>} */
    const weeks = [];
    for (let i = 0; i < cells.length; i += 7) {
        weeks.push(cells.slice(i, i + 7));
    }
    return weeks;
}

/**
 * @param {Date} date
 * @param {boolean} inMonth
 * @param {string} todayKey
 */
function makeCell(date, inMonth, todayKey) {
    const key = `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
    const month = getAlmanaxMonth(date);
    return {
        day: date.getDate(),
        inMonth,
        date,
        almanaxLabel: `${date.getDate()} ${month.name}`,
        isToday: key === todayKey,
    };
}

/**
 * Décale un mois Almanax (année civile, sans inventer d’année Dofus).
 *
 * @param {number} year
 * @param {number} monthIndex
 * @param {number} delta
 * @returns {{ year: number, monthIndex: number }}
 */
export function shiftAlmanaxMonth(year, monthIndex, delta) {
    const total = year * 12 + monthIndex + delta;
    return {
        year: Math.floor(total / 12),
        monthIndex: ((total % 12) + 12) % 12,
    };
}
