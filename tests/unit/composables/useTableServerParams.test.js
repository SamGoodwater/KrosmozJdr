import { describe, it, expect } from "vitest";
import { buildFetchUrl } from "../../resources/js/Composables/table/useTableServerParams.js";

describe("buildFetchUrl", () => {
    it("ajoute sorts[i][field] et sorts[i][dir] sans conflit avec sort legacy", () => {
        const url = buildFetchUrl(
            {
                page: 1,
                pageSize: 25,
                sorts: [
                    { field: "state", dir: "asc" },
                    { field: "name", dir: "desc" },
                ],
                sort: "name",
                order: "asc",
            },
            "https://example.test/api/t",
            0,
        );
        expect(url).toContain("sorts%5B0%5D%5Bfield%5D=state");
        expect(url).toContain("sorts%5B0%5D%5Bdir%5D=asc");
        expect(url).toContain("sorts%5B1%5D%5Bfield%5D=name");
        expect(url).toContain("sorts%5B1%5D%5Bdir%5D=desc");
        expect(url).toContain("sort=name");
        expect(url).toContain("order=asc");
    });
});
