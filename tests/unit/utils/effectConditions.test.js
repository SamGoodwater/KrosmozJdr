import { describe, expect, it } from "vitest";
import {
    buildActorSuffixedSuggestions,
    draftConditions,
    draftValueRules,
    emptyCondition,
    emptyValueRule,
    formatConditionText,
    formatConditionsListText,
    formatValueRuleText,
    normalizeAppliesTo,
    normalizeConditions,
    normalizeValueRules,
    normalizeValueUpTo,
    parseBracketToken,
} from "@/Utils/Entity/effectConditions.js";
import { getActiveIdentifierBounds } from "@/Utils/characteristic/formulaAutocomplete.js";

describe("effectConditions", () => {
    it("parse les jetons clé[:acteur] et l’ancienne forme", () => {
        expect(parseBracketToken("agi")).toEqual({ key: "agi", actor: null });
        expect(parseBracketToken("agi:target")).toEqual({ key: "agi", actor: "target" });
        expect(parseBracketToken("agi:caster")).toEqual({ key: "agi", actor: "caster" });
        expect(parseBracketToken("caster:agi")).toEqual({ key: "agi", actor: "caster" });
    });

    it("normalise pour persistance et conserve le brouillon", () => {
        const conditions = normalizeConditions([
            { left: "[agi]", op: ">=", right: "3" },
            { join: "or", left: "[intel]", op: ">", right: "[chance:target]" },
            { left: "", op: ">=", right: "" },
        ]);
        expect(conditions).toHaveLength(2);
        expect(conditions[1].join).toBe("OR");

        const draft = draftConditions([{ left: "", op: ">=", right: "" }, emptyCondition(1)]);
        expect(draft).toHaveLength(2);
        expect(draft[0].left).toBe("");

        const rules = normalizeValueRules([
            {
                action: "add",
                formula: "1",
                conditions: [{ left: "[agi]", op: ">=", right: "3" }],
            },
            { action: "nope", formula: "2" },
        ]);
        expect(rules).toHaveLength(1);

        const draftRules = draftValueRules([emptyValueRule()]);
        expect(draftRules).toHaveLength(1);
        expect(draftRules[0].formula).toBe("");

        expect(normalizeAppliesTo("caster")).toBe("caster");
        expect(normalizeValueUpTo(true)).toBe(true);
        expect(normalizeValueUpTo(false)).toBe(false);
    });

    it("formate les conditions avec suffixe cible", () => {
        const resolve = (k) => (k.includes("agi") ? "Agi" : k.includes("intel") ? "Intel" : k);
        expect(
            formatConditionText(
                { left: "[modifier_agility_creature:target]", op: ">=", right: "3" },
                resolve,
            ),
        ).toMatch(/cible/);
        expect(
            formatConditionsListText(
                [
                    { left: "[agi]", op: ">=", right: "3" },
                    { join: "OR", left: "[intel]", op: ">", right: "2" },
                ],
                resolve,
            ),
        ).toMatch(/ou/i);
        expect(
            formatValueRuleText(
                {
                    action: "add",
                    formula: "1",
                    conditions: [{ left: "[agi]", op: ">=", right: "3" }],
                },
                resolve,
            ),
        ).toMatch(/\+1 si/);
    });

    it("prépare les suggestions clé et clé:target", () => {
        const list = buildActorSuffixedSuggestions([{ id: "agi", name: "Agilité", short_name: "Agi" }]);
        expect(list).toEqual(
            expect.arrayContaining([
                expect.objectContaining({ id: "agi" }),
                expect.objectContaining({ id: "agi:target" }),
                expect.objectContaining({ id: "agi:caster" }),
            ]),
        );
    });
});

describe("formulaAutocomplete avec suffixe", () => {
    it("détecte clé:target dans l’identifiant actif", () => {
        const value = "[agi:target]";
        const { token } = getActiveIdentifierBounds(value, 8);
        expect(token).toContain("agi");
        expect(token).toContain("target");
    });
});
