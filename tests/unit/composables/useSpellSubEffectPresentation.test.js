import { describe, expect, it } from "vitest";
import {
    buildUnifiedSubEffectModel,
    formatPivotRowValue,
    formatTriggerTurnGroupLabel,
    formatTriggerTurnLabel,
    normalizeTriggerTurn,
} from "@/Composables/entity/useSpellSubEffectPresentation";
import { groupSpellEffectRowsByTriggerTurn } from "@/Composables/entity/useSpellEffectRowSegments";

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

    it("expose triggerTurn depuis params ou colonne", () => {
        const fromParams = buildUnifiedSubEffectModel({
            source: "row",
            layout: "large",
            row: {
                params: { characteristic: "pm", value: "1", trigger_turn: 1 },
                sub_effect: { slug: "retirer" },
            },
        });
        expect(fromParams.triggerTurn).toBe(1);

        const fromColumn = buildUnifiedSubEffectModel({
            source: "row",
            layout: "large",
            row: {
                trigger_turn: 2,
                params: { characteristic: "pa", value: "1" },
                sub_effect: { slug: "booster" },
            },
        });
        expect(fromColumn.triggerTurn).toBe(2);

        const fromChip = buildUnifiedSubEffectModel({
            source: "chip",
            layout: "minimal",
            chip: { action_slug: "booster", trigger_turn: 1, text: "+1 PA" },
        });
        expect(fromChip.triggerTurn).toBe(1);
    });
});

describe("formatTriggerTurnLabel", () => {
    it("normalise et libelle les tours différés", () => {
        expect(normalizeTriggerTurn(null)).toBe(0);
        expect(normalizeTriggerTurn("")).toBe(0);
        expect(normalizeTriggerTurn(999)).toBe(255);
        expect(formatTriggerTurnLabel(0)).toBeNull();
        expect(formatTriggerTurnLabel(1)).toBe("Au tour suivant");
        expect(formatTriggerTurnLabel(3)).toBe("Dans 3 tours");
        expect(formatTriggerTurnGroupLabel(0)).toBe("Au lancer");
        expect(formatTriggerTurnGroupLabel(1)).toBe("Au tour suivant");
    });
});

describe("groupSpellEffectRowsByTriggerTurn", () => {
    it("groupe par tour croissant et segmente chaque groupe", () => {
        const groups = groupSpellEffectRowsByTriggerTurn([
            { order: 0, params: { trigger_turn: 1 }, logic_operator: null },
            { order: 1, params: {}, logic_operator: null },
            { order: 2, trigger_turn: 1, params: {}, logic_operator: null },
        ]);
        expect(groups).toHaveLength(2);
        expect(groups[0].triggerTurn).toBe(0);
        expect(groups[0].label).toBe("Au lancer");
        expect(groups[0].rows).toHaveLength(1);
        expect(groups[1].triggerTurn).toBe(1);
        expect(groups[1].label).toBe("Au tour suivant");
        expect(groups[1].rows).toHaveLength(2);
        expect(groups[1].segments.length).toBeGreaterThan(0);
    });
});
