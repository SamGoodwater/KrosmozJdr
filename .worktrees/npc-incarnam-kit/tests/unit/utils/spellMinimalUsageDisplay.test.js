/**
 * @vitest-environment node
 * Évite l’init jsdom (problèmes ESM/html-encoding-sniffer sur certains setups).
 */
import { describe, expect, it } from "vitest";
import {
    buildCastUsageSummary,
    buildResolutionSummary,
    buildSpellCastUsagePresentation,
} from "@/Utils/Entity/spellMinimalUsageDisplay";

describe("spellMinimalUsageDisplay", () => {
    it("résumé lancers : masque max cible si C absent, 0 ou égal à N ; délai 0/1/null → « / tour »", () => {
        expect(buildCastUsageSummary({ cast_per_turn: "2", cast_per_target: "0", number_between_two_cast: "0" }).text).toBe(
            "2 lancers / tour",
        );
        expect(buildCastUsageSummary({ cast_per_turn: "2", cast_per_target: "2", number_between_two_cast: "0" }).text).toBe(
            "2 lancers / tour",
        );
        expect(buildCastUsageSummary({ cast_per_turn: "2", cast_per_target: "1", number_between_two_cast: "3" }).text).toBe(
            "2 lancers (max cible 1) / 3 tours",
        );
        expect(buildCastUsageSummary({ cast_per_turn: "1", cast_per_target: "0", number_between_two_cast: "1" }).text).toBe(
            "1 lancer / tour",
        );
    });

    it("présentation lancers : même texte / tooltip que buildCastUsageSummary", () => {
        const entity = { cast_per_turn: "2", cast_per_target: "1", number_between_two_cast: "3" };
        const a = buildCastUsageSummary(entity);
        const b = buildSpellCastUsagePresentation(entity);
        expect(b.text).toBe(a.text);
        expect(b.tooltip).toBe(a.tooltip);
        expect(b.n).toBe(2);
        expect(b.c).toBe(1);
        expect(b.t).toBe(3);
        expect(b.showPerTarget).toBe(true);
        expect(b.showCooldownSegment).toBe(true);
        expect(b.cooldownShowNumeric).toBe(true);
        expect(b.metas).not.toBeNull();
    });

    it("résolution : modes et caracs", () => {
        expect(buildResolutionSummary({ resolution_mode: "auto_success" }).text).toBe(
            "Réussite automatique",
        );
        expect(
            buildResolutionSummary({
                resolution_mode: "attack_roll",
                attack_characteristic_key: "chance",
            }).text,
        ).toBe("Jet d'attaque vs CA (Chance)");
        expect(
            buildResolutionSummary({
                resolution_mode: "saving_throw",
                save_characteristic_key: "strong",
                save_dc_formula: "8 + mod FOR",
            }).text,
        ).toBe("Jet de sauvegarde (Force) · DD 8 + mod FOR");
    });
});
