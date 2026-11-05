import { describe, expect, it } from "vitest";
import {
    buildActorPrefixedSuggestions,
    formatConditionText,
    formatConditionsListText,
    formatValueRuleText,
    normalizeAppliesTo,
    normalizeConditions,
    normalizeValueRules,
} from "@/Utils/Entity/effectConditions.js";
import { getActiveIdentifierBounds } from "@/Utils/characteristic/formulaAutocomplete.js";

describe("effectConditions", () => {
    it("normalise conditions et règles de valeur", () => {
        const conditions = normalizeConditions([
            { left: "[caster:agi]", op: ">=", right: "3" },
            { join: "or", left: "[caster:intel]", op: ">", right: "[target:chance]" },
            { left: "", op: ">=", right: "" },
        ]);
        expect(conditions).toHaveLength(2);
        expect(conditions[1].join).toBe("OR");

        const rules = normalizeValueRules([
            {
                action: "add",
                formula: "1",
                conditions: [{ left: "[caster:agi]", op: ">=", right: "3" }],
            },
            { action: "nope", formula: "2" },
        ]);
        expect(rules).toHaveLength(1);
        expect(rules[0].action).toBe("add");
        expect(normalizeAppliesTo("caster")).toBe("caster");
        expect(normalizeAppliesTo("x")).toBe("targets");
    });

    it("formate les conditions et règles de façon lisible", () => {
        const resolve = (k) => (k.includes("agi") ? "Agi" : k.includes("intel") ? "Intel" : k);
        expect(
            formatConditionText(
                { left: "[caster:modifier_agility_creature]", op: ">=", right: "3" },
                resolve,
            ),
        ).toContain("Agi");
        expect(
            formatConditionsListText(
                [
                    { left: "[caster:agi]", op: ">=", right: "3" },
                    { join: "OR", left: "[caster:intel]", op: ">", right: "2" },
                ],
                resolve,
            ),
        ).toMatch(/ou/i);
        expect(
            formatValueRuleText(
                {
                    action: "add",
                    formula: "1",
                    conditions: [{ left: "[caster:agi]", op: ">=", right: "3" }],
                },
                resolve,
            ),
        ).toMatch(/\+1 si/);
    });

    it("préfixe les suggestions lanceur / cible", () => {
        const list = buildActorPrefixedSuggestions([{ id: "agi", name: "Agilité", short_name: "Agi" }]);
        expect(list).toEqual(
            expect.arrayContaining([
                expect.objectContaining({ id: "caster:agi", name: "Lanceur · Agilité" }),
                expect.objectContaining({ id: "target:agi", name: "Cible · Agilité" }),
            ]),
        );
    });
});

describe("formulaAutocomplete avec préfixe", () => {
    it("détecte caster: dans l’identifiant actif", () => {
        const value = "[caster:agi]";
        const { token } = getActiveIdentifierBounds(value, 8);
        expect(token).toContain("caster");
        expect(token).toContain("agi");
    });
});
