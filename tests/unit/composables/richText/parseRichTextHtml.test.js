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
});
