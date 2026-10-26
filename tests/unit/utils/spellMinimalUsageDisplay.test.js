/**
 * @vitest-environment node
 * Évite l’init jsdom (problèmes ESM/html-encoding-sniffer sur certains setups).
 */
import { describe, expect, it } from "vitest";
import {
    buildCastUsageSummary,
    buildResolutionSummary,
    buildSpellCastingRitualPresentation,
    buildSpellCastUsagePresentation,
    normalizeSpellCastingTime,
} from "@/Utils/Entity/spellMinimalUsageDisplay";
import { buildSpellResolutionFormula } from "@/Utils/Entity/spellResolutionFormula";

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

    it("durée d’incantation : masque le défaut 1 tour / 6 s ; convertit les multiples", () => {
        expect(normalizeSpellCastingTime("").show).toBe(false);
        expect(normalizeSpellCastingTime("0").show).toBe(false);
        expect(normalizeSpellCastingTime("1").show).toBe(false);
        expect(normalizeSpellCastingTime("1 tour").show).toBe(false);
        expect(normalizeSpellCastingTime("6s").show).toBe(false);
        expect(normalizeSpellCastingTime("6 secondes").show).toBe(false);

        const twelve = normalizeSpellCastingTime("12");
        expect(twelve.show).toBe(true);
        expect(twelve.displayText).toBe("12 tours");

        const twelveSec = normalizeSpellCastingTime("12s");
        expect(twelveSec.show).toBe(true);
        expect(twelveSec.displayText).toBe("2 tours");

        // ≤ 6 s = défaut d’un tour : masqué (plan : « inférieur à 1 tour ou entre 0 et 6 s »)
        expect(normalizeSpellCastingTime("4s").show).toBe(false);

        const eightSec = normalizeSpellCastingTime("8s");
        expect(eightSec.show).toBe(true);
        expect(eightSec.displayText).toBe("8 s");

        const ritualOnly = buildSpellCastingRitualPresentation({
            casting_time: "6s",
            ritual_available: true,
        });
        expect(ritualOnly.showCasting).toBe(false);
        expect(ritualOnly.showRitual).toBe(true);
        expect(ritualOnly.show).toBe(true);

        const longCast = buildSpellCastingRitualPresentation({ casting_time: "12" });
        expect(longCast.showCasting).toBe(true);
        expect(longCast.castingText).toBe("12 tours");
    });

    it("formule publique : élément nommé, vs séparé, sans fiche de personnage", () => {
        const attack = buildSpellResolutionFormula({
            resolution_mode: "attack_roll",
            attack_characteristic_key: "agi",
        });
        expect(attack.headline).toBe("Jet d'attaque Air vs CA");
        expect(attack.elementLabel).toBe("Air");
        expect(attack.leftParts.map((part) => part.key || part.text).join("")).toBe(
            "1d20 + modifier_agility_creature + hit_bonus_creature",
        );
        expect(attack.rightParts.map((part) => part.key || part.text).join("")).toBe("CA");

        const save = buildSpellResolutionFormula({
            resolution_mode: "saving_throw",
            save_characteristic_key: "intel",
        });
        expect(save.headline).toBe("Degré de difficulté Feu vs jet de sauvegarde Feu");
        expect(save.elementLabel).toBe("Feu");
        expect(save.leftParts.map((part) => part.key || part.text).join("")).toBe(
            "DD save_dc_intelligence_creature",
        );
        expect(save.rightParts.map((part) => part.key || part.text).join("")).toBe(
            "1d20 + save_intelligence_creature",
        );
    });
});
