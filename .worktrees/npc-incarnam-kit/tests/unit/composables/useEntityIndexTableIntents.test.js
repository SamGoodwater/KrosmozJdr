import { describe, expect, it, vi, beforeEach } from "vitest";

const warningMock = vi.fn();

vi.mock("@inertiajs/vue3", () => ({
    router: { visit: vi.fn() },
}));

vi.mock("@/Composables/store/useNotificationStore", () => ({
    useNotificationStore: () => ({ warning: warningMock }),
}));

import { router } from "@inertiajs/vue3";
import { useEntityIndexTableIntents } from "@/Composables/entity/useEntityIndexTableIntents";

class FakeModel {
    constructor(data) {
        Object.assign(this, data);
    }

    static fromArray(rows) {
        return rows.map((r) => (r instanceof FakeModel ? r : new FakeModel(r)));
    }
}

describe("useEntityIndexTableIntents", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it("notifie sans ouvrir l’édition si Alt+clic sans droit update", () => {
        const openEdit = vi.fn();
        const { handleKeyboardIntent } = useEntityIndexTableIntents({
            ModelClass: FakeModel,
            routeShowName: "entities.spells.show",
            routeShowParam: "spell",
            canModify: () => false,
            openFullModal: vi.fn(),
            openEdit,
            noEditMessage: "Pas de droit",
        });

        handleKeyboardIntent({
            type: "open-edit",
            row: { rowParams: { entity: { id: 1, name: "Test" } } },
        });

        expect(warningMock).toHaveBeenCalledWith("Pas de droit", expect.any(Object));
        expect(openEdit).not.toHaveBeenCalled();
    });

    it("ouvre la page show sur open-show-page", () => {
        const { handleKeyboardIntent } = useEntityIndexTableIntents({
            ModelClass: FakeModel,
            routeShowName: "entities.spells.show",
            routeShowParam: "spell",
            canModify: () => true,
            openFullModal: vi.fn(),
            openEdit: vi.fn(),
        });

        handleKeyboardIntent({
            type: "open-show-page",
            row: { rowParams: { entity: { id: 42 } } },
        });

        expect(router.visit).toHaveBeenCalled();
        const url = String(router.visit.mock.calls[0][0]);
        expect(url).toMatch(/spell=42|spell%22?:42|"spell":42/);
    });
});
