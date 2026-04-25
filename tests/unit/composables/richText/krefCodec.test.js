/* eslint-env browser */
/* global document */
import { describe, it, expect } from "vitest";
import {
    encodeKrefTitle,
    decodeKrefTitle,
    decodeLegacyKrefAttributes,
    decodeKrefElement,
    normalizeKrefType,
    isSupportedKrefType,
    isKrefPayloadComplete,
} from "@/Composables/richText/krefCodec";

describe("krefCodec", () => {
    it("encode/decode title roundtrip", () => {
        const title = encodeKrefTitle({
            krefType: "pageSection",
            krefPayload: JSON.stringify({ pageSlug: "guide", sectionId: 12 }),
            label: "Section guide",
        });

        const decoded = decodeKrefTitle(title);
        expect(decoded).not.toBeNull();
        expect(decoded.krefType).toBe("pageSection");
        expect(decoded.label).toBe("Section guide");
    });

    it("normalise page_section et supporte uniquement les types whitelistés", () => {
        expect(normalizeKrefType("page_section")).toBe("pageSection");
        expect(isSupportedKrefType("page_section")).toBe(true);
        expect(isSupportedKrefType("unknown")).toBe(false);
    });

    it("rejette un title trop long", () => {
        const decoded = decodeKrefTitle("a".repeat(5000));
        expect(decoded).toBeNull();
    });

    it("décode les attributs legacy data-kref-*", () => {
        const el = document.createElement("span");
        el.className = "kref";
        el.setAttribute("data-kref-type", "page_section");
        el.setAttribute("data-kref-payload", JSON.stringify({ pageSlug: "journal", sectionId: 8 }));
        el.textContent = "Journal";

        const decoded = decodeLegacyKrefAttributes(el);
        expect(decoded).not.toBeNull();
        expect(decoded.krefType).toBe("pageSection");
    });

    it("rejette un payload legacy trop long", () => {
        const el = document.createElement("span");
        el.className = "kref";
        el.setAttribute("data-kref-type", "page_section");
        el.setAttribute("data-kref-payload", "x".repeat(2500));

        const decoded = decodeLegacyKrefAttributes(el);
        expect(decoded).toBeNull();
    });

    it("priorise title puis fallback legacy via decodeKrefElement", () => {
        const titleEl = document.createElement("span");
        titleEl.className = "kref";
        titleEl.setAttribute(
            "title",
            encodeKrefTitle({
                krefType: "characteristic",
                krefPayload: JSON.stringify({ key: "d" }),
                label: "Défense",
            }),
        );
        const decodedTitle = decodeKrefElement(titleEl);
        expect(decodedTitle?.krefType).toBe("characteristic");

        const legacyEl = document.createElement("span");
        legacyEl.className = "kref";
        legacyEl.setAttribute("data-kref-type", "entity");
        legacyEl.setAttribute("data-kref-payload", JSON.stringify({ entityType: "spells", id: 3 }));
        const decodedLegacy = decodeKrefElement(legacyEl);
        expect(decodedLegacy?.krefType).toBe("entity");
    });

    it("valide le payload minimal par type", () => {
        expect(isKrefPayloadComplete("characteristic", { key: "d" })).toBe(true);
        expect(isKrefPayloadComplete("characteristic", {})).toBe(false);
        expect(isKrefPayloadComplete("entity", { entityType: "spells", id: 2 })).toBe(true);
        expect(isKrefPayloadComplete("entity", { entityType: "spells" })).toBe(false);
        expect(isKrefPayloadComplete("unknown", { x: 1 })).toBe(false);
    });
});
