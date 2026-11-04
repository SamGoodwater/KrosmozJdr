/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import { buildSpellResolutionFormula } from "@/Utils/Entity/spellResolutionFormula";
import { detectSpellDodgeChecks } from "@/Utils/Entity/spellDodgeChecks";

const damage = { sub_effect: { slug: "frapper" }, params: { characteristic: "fire" } };
const removePm = { sub_effect: { slug: "retirer" }, params: { characteristic: "pm", value: "1" } };
const stealPa = { sub_effect: { slug: "voler-caracteristiques" }, params: { characteristic: "action_points" } };
const removePo = { sub_effect: { slug: "retirer" }, params: { characteristic: "po" } };

/**
 * @param {object} fields
 * @param {Array<object>} rows
 */
function spell(fields, rows) {
    return { ...fields, _active_degree_rows: rows };
}

describe("detectSpellDodgeChecks", () => {
    it("repère retraits et vols PA/PM, pas les autres retraits", () => {
        expect(detectSpellDodgeChecks(spell({}, [damage, removePm, stealPa, removePo]))).toEqual({
            kinds: ["pa", "pm"],
            hasOtherEffects: true,
            hasEffects: true,
        });
    });

    it("lit les chips API du plus petit degré sans lignes de degré", () => {
        const entity = {
            effect_usages_chips: [
                { degree: 2, action_slug: "retirer", characteristic: "pa" },
                { degree: 1, action_slug: "retirer", characteristic: "pm" },
            ],
        };
        expect(detectSpellDodgeChecks(entity).kinds).toEqual(["pm"]);
    });
});

describe("buildSpellResolutionFormula", () => {
    it("touche + retrait PM : même jet vs CA et Esquive PM", () => {
        const f = buildSpellResolutionFormula(
            spell({ resolution_mode: "attack_roll", attack_characteristic_key: "agi" }, [damage, removePm]),
        );
        expect(f.lines).toHaveLength(1);
        expect(f.headline).toBe("Jet d'attaque Air vs CA · Esquive PM");
        expect(f.valueKeys).toContain("dodge_movement_points_creature");
    });

    it("touche avec retrait seul : plus de CA", () => {
        const f = buildSpellResolutionFormula(
            spell({ resolution_mode: "attack_roll", attack_characteristic_key: "agi" }, [removePm]),
        );
        expect(f.headline).toBe("Jet d'attaque Air vs Esquive PM");
    });

    it("sauvegarde + retrait : jet d'attaque séparé, caractéristique du DD par défaut", () => {
        const f = buildSpellResolutionFormula(
            spell({ resolution_mode: "saving_throw", save_characteristic_key: "sagesse" }, [damage, stealPa]),
        );
        expect(f.lines.map((l) => l.kind)).toEqual(["save", "attack"]);
        expect(f.lines[1].headline).toBe("Jet d'attaque Sagesse vs Esquive PA");
        expect(f.lines[1].leftParts).toContainEqual({ type: "chip", key: "modifier_wisdom_creature" });
    });

    it("sauvegarde + retrait : caractéristique d'attaque prioritaire si renseignée", () => {
        const f = buildSpellResolutionFormula(
            spell(
                {
                    resolution_mode: "saving_throw",
                    save_characteristic_key: "sagesse",
                    attack_characteristic_key: "strong",
                },
                [damage, removePm],
            ),
        );
        expect(f.lines[1].leftParts).toContainEqual({ type: "chip", key: "modifier_strength_creature" });
    });

    it("sauvegarde avec retrait seul : seul le jet vs Esquive reste", () => {
        const f = buildSpellResolutionFormula(
            spell({ resolution_mode: "saving_throw", save_characteristic_key: "sagesse" }, [removePm]),
        );
        expect(f.lines.map((l) => l.kind)).toEqual(["attack"]);
    });

    it("réussite automatique : aucun jet, même avec un retrait", () => {
        const f = buildSpellResolutionFormula(spell({ resolution_mode: "auto_success" }, [removePm]));
        expect(f.lines.map((l) => l.kind)).toEqual(["auto"]);
        expect(f.headline).toBe("Réussite automatique");
    });

    it("sans retrait : comportement inchangé", () => {
        const f = buildSpellResolutionFormula(
            spell({ resolution_mode: "attack_roll", attack_characteristic_key: "intel" }, [damage]),
        );
        expect(f.headline).toBe("Jet d'attaque Feu vs CA");
    });
});
