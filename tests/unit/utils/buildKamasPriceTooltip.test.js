/**
 * @vitest-environment node
 */
import { describe, expect, it } from "vitest";
import { buildKamasPriceTooltip } from "@/Utils/Entity/buildKamasPriceTooltip";

describe("buildKamasPriceTooltip", () => {
    it("ressource : prix Dofus", () => {
        expect(buildKamasPriceTooltip({ entityType: "resource" })).toBe("Prix proposé par Dofus.");
    });

    it("équipement : formule et montant calculé", () => {
        expect(
            buildKamasPriceTooltip({
                entityType: "item",
                priceCalculated: 3400,
                priceCustom: 0,
            }),
        ).toBe(
            "Prix automatique : 3400 kamas. Bonus de caractéristiques + 150 kamas × niveau + 200 kamas × rareté.",
        );
    });

    it("consommable : mentionne l’ajustement manuel", () => {
        expect(
            buildKamasPriceTooltip({
                entityType: "consumable",
                priceCalculated: 250,
                priceCustom: -50,
            }),
        ).toBe(
            "Prix automatique : 250 kamas. Somme des prix des ressources de la recette. Le total affiché a été ajusté manuellement.",
        );
    });
});
