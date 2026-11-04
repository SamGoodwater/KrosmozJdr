import { describe, expect, it } from "vitest";
import {
    buildUnifiedSubEffectModel,
    formatPivotRowValue,
} from "@/Composables/entity/useSpellSubEffectPresentation";

describe("formatPivotRowValue", () => {
    it("affiche le montant d’un retrait quand il n’y a pas de formule", () => {
        expect(
            formatPivotRowValue({
                params: { characteristic: "pa", value: "1" },
            }),
        ).toBe("1");
        expect(
            formatPivotRowValue({
                params: { characteristic: "pm", value: "1 par attaque réussie" },
            }),
        ).toBe("1 par attaque réussie");
    });

    it("garde la formule quand elle est renseignée", () => {
        expect(
            formatPivotRowValue({
                params: { value: "1", value_formula: "2d6+[chance]" },
            }),
        ).toBe("2d6+[chance]");
    });
});

describe("buildUnifiedSubEffectModel", () => {
    it("porte le nombre retiré sur la ligne de Flèche ralentissante", () => {
        const model = buildUnifiedSubEffectModel({
            source: "row",
            layout: "large",
            row: {
                duration_formula: "1",
                params: { characteristic: "pa", value: "2" },
                sub_effect: { slug: "retirer" },
            },
        });

        expect(model.actionSlug).toBe("retirer");
        expect(model.characteristic).toBe("pa");
        expect(model.valueDisplay).toBe("2");
        expect(model.valueFormula).toBeNull();
    });
});
