import { describe, expect, it } from "vitest";
import { itemPanopliesFrom, otherPanoplyItems } from "@/Utils/entity/itemPanoply";

describe("itemPanoply", () => {
    it("lit les panoplies sur l’entité ou _data", () => {
        expect(itemPanopliesFrom({ panoplies: [{ id: 1, name: "A" }] })).toEqual([
            { id: 1, name: "A" },
        ]);
        expect(itemPanopliesFrom({ _data: { panoplies: [{ id: 2 }] } })).toEqual([{ id: 2 }]);
        expect(itemPanopliesFrom({})).toEqual([]);
    });

    it("exclut l’équipement courant de la liste des autres pièces", () => {
        const panoply = {
            items: [
                { id: 1, name: "Coiffe" },
                { id: 2, name: "Cape" },
            ],
        };
        expect(otherPanoplyItems(panoply, 1).map((row) => row.id)).toEqual([2]);
        expect(otherPanoplyItems(panoply, null)).toHaveLength(2);
    });
});
