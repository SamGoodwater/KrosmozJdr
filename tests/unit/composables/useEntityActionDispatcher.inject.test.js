/**
 * Tests ciblés submitAiInject du dispatcher entité.
 */
import { describe, expect, it, vi, beforeEach } from "vitest";
import { defineComponent, nextTick } from "vue";
import { mount } from "@vue/test-utils";
import axios from "axios";

vi.mock("axios", () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
    },
}));

vi.mock("@inertiajs/vue3", () => ({
    router: { visit: vi.fn(), reload: vi.fn() },
}));

vi.mock("@/Composables/permissions/usePermissions", () => ({
    usePermissions: () => ({
        isAdmin: { value: true },
        canUpdateAny: vi.fn(() => true),
        authUser: { value: { id: 1 } },
    }),
}));

vi.mock("@/Composables/utils/useCopyToClipboard", () => ({
    useCopyToClipboard: () => ({ copyToClipboard: vi.fn() }),
}));

vi.mock("@/Composables/entity/useEntityDofusdbRefresh", () => ({
    useEntityDofusdbRefresh: () => ({
        previewRefresh: vi.fn(),
        applyRefresh: vi.fn(),
    }),
}));

vi.mock("@/Composables/store/useNotificationStore", () => ({
    useNotificationStore: () => ({
        success: vi.fn(),
        error: vi.fn(),
    }),
}));

import { useEntityActionDispatcher } from "@/Composables/entity/useEntityActionDispatcher";

function mountDispatcher(entityType = "spells") {
    let api;
    const Comp = defineComponent({
        setup() {
            api = useEntityActionDispatcher(entityType);
            return api;
        },
        template: "<div />",
    });
    mount(Comp);
    return api;
}

describe("useEntityActionDispatcher submitAiInject", () => {
    beforeEach(() => {
        axios.post.mockReset();
        axios.get.mockReset();
    });

    it("poste sur ia-inject et ouvre le diff", async () => {
        axios.post.mockResolvedValue({
            data: {
                success: true,
                message: "JSON injecté",
                diff: { source: "ia", snapshot_id: "snap-1", changed_count: 1 },
            },
        });

        const api = mountDispatcher("spells");
        api.refreshConfirm.value = {
            ...api.refreshConfirm.value,
            open: true,
            entity: { id: 42, name: "Sort" },
            aiAction: "spell",
            playable: false,
            aiSubmitting: false,
        };

        const ok = await api.submitAiInject({ payload: { effect: "1d6" } });
        await nextTick();

        expect(ok).toBe(true);
        expect(axios.post).toHaveBeenCalledWith(
            "/api/entities/spells/42/ia-inject",
            { action: "spell", payload: { effect: "1d6" }, force: false },
            expect.objectContaining({ headers: { Accept: "application/json" } }),
        );
        expect(api.refreshConfirm.value.diff?.snapshot_id).toBe("snap-1");
        expect(api.refreshConfirm.value.aiSubmitting).toBe(false);
    });

    it("refuse un payload non objet", async () => {
        const api = mountDispatcher("spells");
        api.refreshConfirm.value = {
            ...api.refreshConfirm.value,
            entity: { id: 7 },
            aiAction: "spell",
        };

        const ok = await api.submitAiInject({ payload: "[1]" });
        expect(ok).toBe(false);
        expect(api.refreshConfirm.value.aiError).toContain("Payload JSON invalide");
        expect(axios.post).not.toHaveBeenCalled();
    });

    it("surface l’erreur 423 password.confirm", async () => {
        axios.post.mockRejectedValue({ response: { status: 423 } });

        const api = mountDispatcher("items");
        api.refreshConfirm.value = {
            ...api.refreshConfirm.value,
            entity: { id: 9 },
            aiAction: "item",
        };

        const ok = await api.submitAiInject({
            payload: { name: "a", description: "b", bonus: "c", effect: "d" },
        });
        expect(ok).toBe(false);
        expect(api.refreshConfirm.value.aiError).toContain("mot de passe");
    });
});
