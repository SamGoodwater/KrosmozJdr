import { describe, it, expect } from "vitest";
import { useOverlayContentResolver } from "@/Composables/overlay/useOverlayContentResolver";

describe("useOverlayContentResolver", () => {
    it("resout du texte brut", async () => {
        const resolver = useOverlayContentResolver({
            content: "Bonjour",
            cache: false,
        });
        const out = await resolver.resolve();
        expect(out?.kind).toBe("text");
        expect(out?.value).toBe("Bonjour");
    });

    it("resout du html explicite", async () => {
        const resolver = useOverlayContentResolver({
            content: { html: "<b>ok</b>" },
            cache: false,
        });
        const out = await resolver.resolve();
        expect(out?.kind).toBe("html");
        expect(out?.value).toContain("<b>ok</b>");
    });

    it("dedoublonne les loaders par cle de cache", async () => {
        let calls = 0;
        const loader = async () => {
            calls += 1;
            return "payload";
        };
        const a = useOverlayContentResolver({
            content: { loader, key: "overlay-test-key" },
            cache: { key: "overlay-test-key", ttlMs: 1000, maxEntries: 10 },
        });
        const b = useOverlayContentResolver({
            content: { loader, key: "overlay-test-key" },
            cache: { key: "overlay-test-key", ttlMs: 1000, maxEntries: 10 },
        });

        const [one, two] = await Promise.all([a.resolve(), b.resolve()]);
        expect(one?.value).toBe("payload");
        expect(two?.value).toBe("payload");
        expect(calls).toBe(1);
    });
});
