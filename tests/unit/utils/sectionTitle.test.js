import { describe, expect, it } from "vitest";
import {
    isPlaceholderSectionTitle,
    publicSectionTitle,
    sectionDisplayLabel,
} from "@/Utils/section/sectionTitle";

describe("publicSectionTitle", () => {
    it("garde un titre de contenu", () => {
        expect(publicSectionTitle("Niveau 1")).toBe("Niveau 1");
        expect(publicSectionTitle("  Repères ")).toBe("Repères");
        expect(publicSectionTitle("Texte public")).toBe("Texte public");
    });

    it("efface les libellés de secours", () => {
        expect(publicSectionTitle("")).toBe("");
        expect(publicSectionTitle("   ")).toBe("");
        expect(publicSectionTitle("Texte")).toBe("");
        expect(publicSectionTitle("SECTION")).toBe("");
        expect(publicSectionTitle("Sections")).toBe("");
        expect(publicSectionTitle("Section 2")).toBe("");
        expect(publicSectionTitle("Sans titre")).toBe("");
        expect(isPlaceholderSectionTitle("Niveau 3")).toBe(false);
    });

    it("reprend les premiers mots quand le titre est un bouche-trou", () => {
        expect(
            sectionDisplayLabel({
                title: "Texte",
                data: { content: "<p>En tant qu'Artiste tu tiens la salle et le public</p>" },
            }),
        ).toBe("En tant qu'Artiste tu tiens la salle...");
        expect(sectionDisplayLabel({ title: "Niveau 1", data: { content: "<p>Ignoré</p>" } })).toBe(
            "Niveau 1",
        );
        expect(sectionDisplayLabel({ title: "", data: { content: "" } })).toBe("");
    });
});
