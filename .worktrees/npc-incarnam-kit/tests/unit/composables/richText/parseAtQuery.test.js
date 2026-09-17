import { describe, it, expect } from "vitest";
import { parseAtQuery } from "@/Composables/richText/parseAtQuery";

describe("parseAtQuery", () => {
    it("retourne isMatch false sans trigger @ final", () => {
        const parsed = parseAtQuery("bonjour");
        expect(parsed.isMatch).toBe(false);
    });

    it("parse la recherche globale", () => {
        const parsed = parseAtQuery("test @vita");
        expect(parsed.isMatch).toBe(true);
        expect(parsed.mode).toBe("all");
        expect(parsed.query).toBe("vita");
    });

    it("parse les préfixes carac et section", () => {
        expect(parseAtQuery("@carac:force").mode).toBe("characteristic");
        expect(parseAtQuery("@section:intro").mode).toBe("section");
    });

    it("parse un type d'entité", () => {
        const parsed = parseAtQuery("@monstre:bouftou");
        expect(parsed.mode).toBe("entityType");
        expect(parsed.entityType).toBe("monsters");
        expect(parsed.query).toBe("bouftou");
    });

    it("retombe en mode global si préfixe inconnu", () => {
        const parsed = parseAtQuery("@inconnu:abc");
        expect(parsed.mode).toBe("all");
        expect(parsed.query).toBe("abc");
    });
});
