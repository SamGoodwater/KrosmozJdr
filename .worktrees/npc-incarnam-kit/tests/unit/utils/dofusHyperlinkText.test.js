import { describe, expect, it } from "vitest";
import { toDisplayLabel } from "@/Utils/dofus/dofusHyperlinkText";

describe("dofusHyperlinkText.toDisplayLabel", () => {
    it("extrait le libellé d’un hyperlien spell", () => {
        expect(toDisplayLabel("{{spell,32891,1::Évadé}}")).toBe("Évadé");
    });

    it("laisse un libellé plain inchangé", () => {
        expect(toDisplayLabel("Pesanteur")).toBe("Pesanteur");
        expect(toDisplayLabel(null)).toBe("");
        expect(toDisplayLabel("  ")).toBe("");
    });

    it("remplace dans une phrase", () => {
        expect(toDisplayLabel("S'applique l'état {{spell,32891,1::Évadé}}.")).toBe(
            "S'applique l'état Évadé.",
        );
    });
});
