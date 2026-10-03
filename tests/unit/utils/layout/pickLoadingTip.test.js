// @vitest-environment node
import { describe, expect, it } from "vitest";
import {
    pickLoadingTip,
    resolveLoadingTipHoldMs,
} from "../../../../resources/js/Utils/layout/pickLoadingTip.js";

describe("resolveLoadingTipHoldMs", () => {
    it("convertit les secondes en millisecondes et borne les valeurs", () => {
        expect(resolveLoadingTipHoldMs({ duration_seconds: 10 })).toBe(10000);
        expect(resolveLoadingTipHoldMs({ duration_seconds: 1 })).toBe(2000);
        expect(resolveLoadingTipHoldMs({ duration_seconds: 99 })).toBe(30000);
        expect(resolveLoadingTipHoldMs({})).toBe(8000);
        expect(resolveLoadingTipHoldMs(null)).toBe(8000);
    });
});

describe("pickLoadingTip", () => {
    it("retourne null si la liste est vide", () => {
        expect(pickLoadingTip([])).toBeNull();
        expect(pickLoadingTip(null)).toBeNull();
    });

    it("retourne la seule astuce disponible", () => {
        const tip = { body: "Seule", featured: false };
        expect(pickLoadingTip([tip])).toBe(tip);
        expect(pickLoadingTip([tip], tip)).toBe(tip);
    });

    it("évite de répéter immédiatement la même astuce", () => {
        const a = { body: "A", featured: false };
        const b = { body: "B", featured: false };
        const next = pickLoadingTip([a, b], a, () => 0);
        expect(next).toBe(b);
    });

    it("donne plus de poids aux astuces mises en avant", () => {
        const featured = { body: "Featured", featured: true };
        const normal = { body: "Normal", featured: false };
        const tips = [featured, normal];

        // Poids 3 + 1 = 4. Ticket 0.0 → featured ; ticket juste sous 0.75 → featured ;
        // ticket ≥ 0.75 → normal.
        expect(pickLoadingTip(tips, null, () => 0)).toBe(featured);
        expect(pickLoadingTip(tips, null, () => 0.74)).toBe(featured);
        expect(pickLoadingTip(tips, null, () => 0.75)).toBe(normal);
        expect(pickLoadingTip(tips, null, () => 0.99)).toBe(normal);
    });
});
