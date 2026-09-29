import { describe, expect, it } from "vitest";
import {
    isEmptySectionHtml,
    isSectionContentDeferred,
    sanitizeSectionUpdatePayload,
} from "@/Utils/section/sectionUpdatePayload";

describe("isSectionContentDeferred", () => {
    it("détecte le flag racine ou dans data", () => {
        expect(isSectionContentDeferred({ content_deferred: true })).toBe(true);
        expect(isSectionContentDeferred({ data: { content_deferred: true } })).toBe(true);
        expect(isSectionContentDeferred({ data: { content: "<p>ok</p>" } })).toBe(false);
        expect(isSectionContentDeferred(null)).toBe(false);
    });
});

describe("isEmptySectionHtml", () => {
    it("traite null, vide et paragraphe TipTap vide", () => {
        expect(isEmptySectionHtml(null)).toBe(true);
        expect(isEmptySectionHtml("")).toBe(true);
        expect(isEmptySectionHtml("<p></p>")).toBe(true);
        expect(isEmptySectionHtml("<p>Chapitre</p>")).toBe(false);
    });
});

describe("sanitizeSectionUpdatePayload", () => {
    it("retire content_deferred et ne transmet pas content null", () => {
        expect(
            sanitizeSectionUpdatePayload({
                title: "Intro",
                data: { content: null, content_deferred: true, align: "left" },
            }),
        ).toEqual({
            title: "Intro",
            data: { align: "left" },
        });
    });

    it("écarte un HTML vide tant que le contenu est encore différé", () => {
        expect(
            sanitizeSectionUpdatePayload({
                data: { content: "<p></p>", content_deferred: true },
            }),
        ).toEqual({ data: {} });
    });

    it("laisse passer un vidage volontaire déjà hydraté", () => {
        expect(
            sanitizeSectionUpdatePayload({
                data: { content: "" },
            }),
        ).toEqual({ data: { content: "" } });
    });

    it("conserve un vrai HTML", () => {
        expect(
            sanitizeSectionUpdatePayload({
                data: { content: "<p>Nouveau</p>" },
            }),
        ).toEqual({ data: { content: "<p>Nouveau</p>" } });
    });
});
