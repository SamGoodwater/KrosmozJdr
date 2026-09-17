import { describe, expect, it } from "vitest";
import { Panoply } from "@/Models/Entity/Panoply";

describe("Panoply.level", () => {
    it("utilise le niveau serveur s’il est fourni", () => {
        const panoply = new Panoply({
            id: 1,
            name: "Set",
            level: 40,
            items: [{ id: 1, level: "9" }],
        });
        expect(panoply.level).toBe(40);
    });

    it("dérive le max numérique des pièces si le payload n’a pas de level", () => {
        const panoply = new Panoply({
            id: 1,
            name: "Set",
            items: [{ id: 1, level: "9" }, { id: 2, level: "80" }, { id: 3, level: "20" }],
        });
        expect(panoply.level).toBe(80);
    });

    it("retourne null sans pièces ni niveau", () => {
        const panoply = new Panoply({ id: 1, name: "Vide", items: [] });
        expect(panoply.level).toBeNull();
    });
});
