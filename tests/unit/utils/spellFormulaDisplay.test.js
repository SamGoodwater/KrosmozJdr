import { describe, expect, it } from "vitest";
import {
    abbreviateDamageFormula,
    doubleDiceInFormula,
    expandShortAbilityTokens,
    formatSpellDegreeTabLabel,
    parseFormulaToParts,
    resolveCritFormula,
} from "@/Utils/Entity/spellFormulaDisplay";
import { spellDegreeTabLabel } from "@/Composables/entity/useSpellDisplayDegrees";

describe("spellFormulaDisplay", () => {
    it("expandShortAbilityTokens remplace [intel] par le modificateur", () => {
        expect(expandShortAbilityTokens("1d4+[intel]")).toBe(
            "1d4+[modifier_intelligence_creature]",
        );
        expect(expandShortAbilityTokens("2d6+[strong]")).toBe(
            "2d6+[modifier_strength_creature]",
        );
    });

    it("doubleDiceInFormula double chaque dé", () => {
        expect(doubleDiceInFormula("1d4+[intel]")).toBe("2d4+[intel]");
        expect(doubleDiceInFormula("2d6 + [strong]")).toBe("4d6 + [strong]");
    });

    it("abbreviateDamageFormula affiche dés + mod + Bonus pour Flèche enflammée", () => {
        const result = abbreviateDamageFormula("1d4+[intel]", "fire");
        expect(result.hasBonus).toBe(true);
        expect(result.fullFormula).toContain("[modifier_intelligence_creature]");
        expect(result.fullFormula).toContain("[fixed_damage_fire_creature]");
        expect(result.fullFormula).toContain("[fixed_damage_multiple_creature]");
        expect(result.fullFormula).toContain("[fixed_damage_fire_object]");
        expect(result.fullFormula).toContain("[fixed_damage_fire_consumable]");
        expect(result.visible.some((p) => p.type === "bonus")).toBe(true);
        expect(result.visible.some((p) => p.type === "chip" && p.key === "modifier_intelligence_creature")).toBe(
            true,
        );
        expect(result.visible.some((p) => p.type === "text" && /1d4/.test(p.text))).toBe(true);
    });

    it("resolveCritFormula double les dés si Frapper sans critique", () => {
        expect(
            resolveCritFormula({
                actionSlug: "frapper",
                valueFormula: "1d4+[intel]",
                valueFormulaCrit: "",
            }),
        ).toBe("2d4+[intel]");
        expect(
            resolveCritFormula({
                actionSlug: "frapper",
                valueFormula: "1d4+[intel]",
                valueFormulaCrit: "2d8+[intel]",
            }),
        ).toBe("2d8+[intel]");
        expect(
            resolveCritFormula({
                actionSlug: "déplacer",
                valueFormula: "1",
                valueFormulaCrit: "",
            }),
        ).toBe("");
    });

    it("parseFormulaToParts découpe texte et puces", () => {
        const parts = parseFormulaToParts("1d4 + [modifier_intelligence_creature] + [fixed_damage_fire_creature]");
        expect(parts[0]).toEqual({ type: "text", text: "1d4 + " });
        expect(parts[1]).toEqual({ type: "chip", key: "modifier_intelligence_creature" });
        expect(parts[2]).toEqual({ type: "text", text: " + " });
        expect(parts[3]).toEqual({ type: "chip", key: "fixed_damage_fire_creature" });
    });

    it("formatSpellDegreeTabLabel et spellDegreeTabLabel lisent le niveau du degré", () => {
        expect(formatSpellDegreeTabLabel({ required_level: 5 }, 0)).toBe("Niv 5");
        expect(formatSpellDegreeTabLabel({ required_level: 13 }, 1)).toBe("Niv 13");
        expect(spellDegreeTabLabel({ required_level: 1 }, 0)).toBe("Niv 1");
        expect(spellDegreeTabLabel({ required_level: 20 }, 3)).toBe("Niv 20");
        expect(spellDegreeTabLabel({ position: 2 }, 0)).toBe("Niv 2");
        expect(spellDegreeTabLabel({}, 4)).toBe("Niv 5");
    });
});
