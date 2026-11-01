/* global document */
import { describe, it, expect } from "vitest";
import { encodeKrefTitle } from "@/Composables/richText/krefCodec";
import { parseRichTextHtml } from "@/Composables/richText/parseRichTextHtml";

describe("parseRichTextHtml", () => {
    it("remplace un span.kref par un nœud puce, sans title base64", () => {
        const title = encodeKrefTitle({
            krefType: "characteristic",
            krefPayload: JSON.stringify({ key: "action_points_creature" }),
            label: "PA",
        });
        const tree = parseRichTextHtml(`<p>Coût <span class="kref" title="${title}">PA</span>.</p>`);

        expect(tree).toHaveLength(1);
        expect(tree[0].tag).toBe("p");
        const kref = tree[0].children.find((child) => child.kind === "kref");
        expect(kref).toMatchObject({
            kind: "kref",
            krefType: "characteristic",
            label: "PA",
        });
        expect(kref.krefPayload).toContain("action_points_creature");
    });

    it("conserve les styles de couleur sur les spans (Wakfu, Dofus, etc.)", () => {
        const tree = parseRichTextHtml(
            '<p>Le <span style="color:#5eead4"><strong>Wakfu</strong></span> et la <span style="color:#c4b5fd"><strong>Stasis</strong></span>.</p>',
        );

        expect(tree).toHaveLength(1);
        const spans = tree[0].children.filter((child) => child.kind === "el" && child.tag === "span");
        expect(spans).toHaveLength(2);
        expect(spans[0].attrs.style).toContain("color:#5eead4");
        expect(spans[1].attrs.style).toContain("color:#c4b5fd");
    });
});
