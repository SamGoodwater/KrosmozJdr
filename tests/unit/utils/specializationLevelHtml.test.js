import { describe, it, expect } from "vitest";
import {
    decorateSpecializationStyleHtml,
    extractSpecializationLevelPicks,
    prepareSpecializationLevelHtml,
    specializationCapabilityPivotLevel,
} from "@/Utils/entity/specializationLevelHtml";

describe("specializationLevelHtml", () => {
    it("extrait les picks Aptitudes / Capacités", () => {
        const html =
            "<h2>Aptitudes — choisis-en 1</h2><ul><li>A</li></ul>" +
            "<h2>Capacités — choisis-en 2</h2><ul><li>B</li></ul>";
        expect(extractSpecializationLevelPicks(html)).toEqual({
            aptitudePick: 1,
            capacityPick: 2,
        });
    });

    it("garde maîtrises et aptitudes, retire les capacités simples", () => {
        const html =
            "<h2>Maîtrises</h2><ul><li><strong>Outils</strong> : Un accessoire.</li></ul>" +
            "<h2>Aptitudes — choisis-en 1</h2><ul><li><strong>Présence</strong> — effet.</li></ul>" +
            "<h2>Capacités — choisis-en 2</h2><ul><li><strong>[[kref:entity:capabilities:1:Applaudissement]]</strong> — effet.</li></ul>";
        const { narrativeHtml } = prepareSpecializationLevelHtml(html);
        expect(narrativeHtml).toContain("Maîtrises");
        expect(narrativeHtml).toContain("spe-masteries-list");
        expect(narrativeHtml).toContain("Outils");
        expect(narrativeHtml).not.toMatch(/Outils<\/strong>\s*:/);
        expect(narrativeHtml).toContain("Aptitudes");
        expect(narrativeHtml).not.toContain("Capacités — choisis-en");
        expect(narrativeHtml).not.toContain("Applaudissement");
    });

    it("extrait les variantes même avec krefs résolus (span.kref)", () => {
        const html =
            "<h2>Capacités — choisis-en 1</h2><ul>" +
            '<li><strong><span class="kref">Numéro de scène</span></strong> — selon ton style :' +
            "<ul>" +
            '<li><strong><span class="kref">Contre-refrain</span></strong> — effet M.</li>' +
            '<li><strong><span class="kref">Culbute</span></strong> — effet S.</li>' +
            "</ul></li>" +
            '<li><strong><span class="kref">Déguisement</span></strong> — effet simple.</li>' +
            "</ul>";
        const { styleVariants, excludeCapabilityNames } = prepareSpecializationLevelHtml(html);
        expect(styleVariants).toEqual(
            expect.arrayContaining([
                expect.objectContaining({
                    styleKey: "musicien",
                    capabilityName: "Contre-refrain",
                }),
                expect.objectContaining({
                    styleKey: "saltimbanque",
                    capabilityName: "Culbute",
                }),
            ]),
        );
        expect(excludeCapabilityNames).toEqual(
            expect.arrayContaining(["numéro de scène", "contre-refrain", "culbute"]),
        );
    });

    it("pose des pastilles spe-style sur les titres Musicien / etc.", () => {
        const html = "<h2>Choix de style</h2><h3>Musicien</h3><p>Blurb</p><h3>Voyant</h3>";
        const out = decorateSpecializationStyleHtml(html, "artiste");
        expect(out).toContain('data-spe-style="musicien"');
        expect(out).toContain('data-spe-style="voyant"');
        expect(out).toContain("spe-style");
    });

    it("retire le récap, les maîtrises vides et le rituel oui/non", () => {
        const html =
            "<p><strong>À ce niveau</strong> : 2 compétences.</p>" +
            "<h2>Maîtrises</h2><ul>" +
            "<li><strong>Outils</strong> : Un accessoire.</li>" +
            "<li><strong>Langues</strong> : Aucune.</li>" +
            "</ul>" +
            "<p><strong>2ᵉ sauvegarde</strong> : Sagesse.</p>" +
            "<p><strong>Rituel</strong> : oui (à partir du niveau 6).</p>";
        const { narrativeHtml } = prepareSpecializationLevelHtml(html);
        expect(narrativeHtml).not.toContain("À ce niveau");
        expect(narrativeHtml).not.toContain("Langues");
        expect(narrativeHtml).not.toContain("Rituel");
        expect(narrativeHtml).toContain("Tu maîtrises les jets de sauvegarde liés à Sagesse.");
        expect(narrativeHtml).toContain("Un accessoire.");
        expect(narrativeHtml).not.toMatch(/Outils<\/strong>\s*:/);
    });

    it("lit le palier depuis pivot.level", () => {
        expect(specializationCapabilityPivotLevel({ pivot: { level: 6 } })).toBe(6);
        expect(specializationCapabilityPivotLevel({ _data: { pivot: { level: 12 } } })).toBe(12);
        expect(specializationCapabilityPivotLevel({ level: 3 })).toBe(0);
    });
});
