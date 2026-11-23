import { describe, expect, it } from "vitest";
import {
    adjacentCellClass,
    adjacentCellStyle,
    groupSectionsIntoRows,
    normalizeLayoutWidth,
} from "@/Utils/section/sectionLayout";

describe("sectionLayout adjacent rows", () => {
    it("normalise les largeurs connues", () => {
        expect(normalizeLayoutWidth("half")).toEqual({ mode: "half" });
        expect(normalizeLayoutWidth("40")).toEqual({ mode: "percent", percent: 40 });
        expect(normalizeLayoutWidth(25)).toEqual({ mode: "percent", percent: 25 });
        expect(normalizeLayoutWidth("auto")).toEqual({ mode: "auto" });
    });

    it("groupe les sections consécutives au même layoutRowId", () => {
        const rows = groupSectionsIntoRows([
            { id: 1, settings: {} },
            { id: 2, settings: { layoutRowId: "a", layoutWidth: "half" } },
            { id: 3, settings: { layoutRowId: "a", layoutWidth: "half" } },
            { id: 4, settings: { layoutRowId: "b", layoutWidth: "third" } },
            { id: 5, settings: { layoutRowId: "b", layoutWidth: "twoThirds" } },
            { id: 6, settings: { layoutRowId: "b", layoutWidth: "fill" } },
        ]);

        expect(rows).toHaveLength(3);
        expect(rows[0].items).toHaveLength(1);
        expect(rows[0].items[0].section.id).toBe(1);
        expect(rows[1].rowId).toBe("a");
        expect(rows[1].items.map((i) => i.section.id)).toEqual([2, 3]);
        expect(rows[2].items).toHaveLength(3);
    });

    it("ne fusionne pas deux rangées d’id différent même voisines", () => {
        const rows = groupSectionsIntoRows([
            { id: 1, settings: { layoutRowId: "x" } },
            { id: 2, settings: { layoutRowId: "y" } },
        ]);
        expect(rows).toHaveLength(2);
    });

    it("expose des classes / styles de cellule", () => {
        expect(adjacentCellClass("fill")).toContain("flex-1");
        expect(adjacentCellStyle(40)).toEqual({
            flex: "1 1 40%",
            maxWidth: "100%",
        });
        expect(adjacentCellStyle("half")).toBeUndefined();
    });
});
