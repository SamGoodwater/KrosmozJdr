import { describe, expect, it } from "vitest";
import { entityDisplayName, portableEntityRef, IA_EXAMPLE_STATE_FILTER } from "@/Utils/entity/portableEntityRef";

describe("portableEntityRef", () => {
    it("préfère official_id au nom et ignore l’id SQL", () => {
        expect(
            portableEntityRef({
                id: 99,
                official_id: "jdr:item:cape-piou",
                name: "Cape du Piou Vert",
            }),
        ).toBe("jdr:item:cape-piou");
    });

    it("retombe sur le nom si official_id est vide", () => {
        expect(portableEntityRef({ id: 3, official_id: "  ", name: "Pression" })).toBe("Pression");
        expect(portableEntityRef({ id: 4, creature: { name: "Piou Vert" } })).toBe("Piou Vert");
    });

    it("ne renvoie pas l’id SQL", () => {
        expect(portableEntityRef({ id: 12, official_id: "", name: "" })).toBeNull();
        expect(portableEntityRef(null)).toBeNull();
    });
});

describe("entityDisplayName", () => {
    it("lit name ou creature.name", () => {
        expect(entityDisplayName({ name: "Anneau du Tofu" })).toBe("Anneau du Tofu");
        expect(entityDisplayName({ creature: { name: "Ganymède" } })).toBe("Ganymède");
    });
});

describe("IA_EXAMPLE_STATE_FILTER", () => {
    it("cible uniquement l’état jouable", () => {
        expect(IA_EXAMPLE_STATE_FILTER).toEqual({ state: "playable" });
    });
});
