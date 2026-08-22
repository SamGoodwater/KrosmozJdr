import { describe, expect, it } from "vitest";
import {
    parsePanoplyBonus,
    serializePanoplyBonus,
    shortBonusKey,
    visiblePanoplyBonusTiers,
    panoplyTierStatMap,
} from "@/Utils/entity/panoplyBonus";

describe("panoplyBonus", () => {
    it("raccourcit les clés *_object", () => {
        expect(shortBonusKey("strength_object")).toBe("strength");
        expect(shortBonusKey("vitality")).toBe("vitality");
    });

    it("parse un bonus par palier de pièces", () => {
        const tiers = parsePanoplyBonus('{"2":{"strength":1},"3":{"vitality":2}}');
        expect(tiers).toEqual([
            { pieceCount: 2, rows: [{ key: "strength", value: "1" }] },
            { pieceCount: 3, rows: [{ key: "vitality", value: "2" }] },
        ]);
    });

    it("sérialise en omettant les valeurs vides ou nulles", () => {
        const json = serializePanoplyBonus([
            { pieceCount: 2, rows: [{ key: "strength", value: "1" }, { key: "", value: "4" }] },
            { pieceCount: 4, rows: [{ key: "vitality", value: "0" }] },
        ]);
        expect(JSON.parse(json)).toEqual({ 2: { strength: 1 } });
    });

    it("replace un objet plat en palier 2 pièces", () => {
        const tiers = parsePanoplyBonus({ strength: 2, vitality: 1 });
        expect(tiers[0].pieceCount).toBe(2);
        expect(tiers[0].rows).toEqual([
            { key: "strength", value: "2" },
            { key: "vitality", value: "1" },
        ]);
    });

    it("n’expose que les paliers avec un bonus non nul", () => {
        const tiers = visiblePanoplyBonusTiers({
            2: { strength: 1 },
            3: { vitality: 0 },
            4: {},
        });
        expect(tiers).toEqual([{ pieceCount: 2, rows: [{ key: "strength", value: "1" }] }]);
    });

    it("construit un objet de stats pour un palier", () => {
        expect(panoplyTierStatMap({
            pieceCount: 2,
            rows: [{ key: "strength", value: "1" }, { key: "", value: "9" }],
        })).toEqual({ strength: "1" });
    });
});
