import { describe, expect, it } from "vitest";
import {
    ALMANAX_MONTHS,
    buildAlmanaxMonthGrid,
    formatAlmanaxDayLabel,
    getAlmanaxDate,
    getAlmanaxMonth,
    shiftAlmanaxMonth,
    toWorldOfTwelveYear,
} from "@/Utils/layout/almanaxCalendar";

describe("almanaxCalendar", () => {
    it("mappe août vers Fraouctor avec le même numéro de jour", () => {
        const date = new Date(2026, 7, 6);
        const info = getAlmanaxDate(date);

        expect(info.day).toBe(6);
        expect(info.month.name).toBe("Fraouctor");
        expect(info.almanaxLabel).toBe("6 Fraouctor");
        expect(info.worldYear).toBe(646);
        expect(formatAlmanaxDayLabel(date)).toBe("6 Fraouctor");
        expect(getAlmanaxMonth(date).gregorianMonth).toBe("août");
        expect(info.tooltip).toContain("an 646");
    });

    it("convertit l’année civile en année du Monde des Douze", () => {
        expect(toWorldOfTwelveYear(2026)).toBe(646);
        expect(toWorldOfTwelveYear(2027)).toBe(647);
        expect(toWorldOfTwelveYear(2025)).toBe(645);
    });

    it("expose 12 mois Almanax", () => {
        expect(ALMANAX_MONTHS).toHaveLength(12);
        expect(ALMANAX_MONTHS[0].name).toBe("Javian");
        expect(ALMANAX_MONTHS[11].name).toBe("Descendre");
    });

    it("construit une grille lundi→dimanche avec aujourd’hui marqué", () => {
        const weeks = buildAlmanaxMonthGrid(2026, 7);
        const flat = weeks.flat();
        const sixth = flat.find((cell) => cell?.inMonth && cell.day === 6);

        expect(weeks.length).toBeGreaterThanOrEqual(4);
        expect(weeks[0]).toHaveLength(7);
        expect(sixth?.almanaxLabel).toBe("6 Fraouctor");
        expect(flat.every((cell) => cell != null)).toBe(true);
    });

    it("décale les mois sur l’année civile sous-jacente", () => {
        expect(shiftAlmanaxMonth(2026, 0, -1)).toEqual({ year: 2025, monthIndex: 11 });
        expect(shiftAlmanaxMonth(2026, 11, 1)).toEqual({ year: 2027, monthIndex: 0 });
        expect(toWorldOfTwelveYear(shiftAlmanaxMonth(2026, 11, 1).year)).toBe(647);
    });
});
