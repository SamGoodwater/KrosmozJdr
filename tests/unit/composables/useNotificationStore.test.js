import { describe, expect, it, vi, beforeEach, afterEach } from "vitest";

describe("useNotificationStore timing", () => {
    beforeEach(() => {
        vi.useFakeTimers();
        vi.resetModules();
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it("utilise une durée par défaut plus longue (14 s)", async () => {
        const { useNotificationStore } = await import("@/Composables/store/useNotificationStore");
        const store = useNotificationStore();
        expect(store.DEFAULT_DURATION).toBe(14000);
        const id = store.success("Test");
        expect(id).toBeTruthy();
        vi.advanceTimersByTime(13000);
        expect(store.notifications.value.some((n) => n.id === id)).toBe(true);
        vi.advanceTimersByTime(2000);
        expect(store.notifications.value.some((n) => n.id === id)).toBe(false);
    });

    it("met en pause le compte à rebours au survol", async () => {
        const { useNotificationStore } = await import("@/Composables/store/useNotificationStore");
        const store = useNotificationStore();
        const id = store.info("Pause", { duration: 5000 });
        vi.advanceTimersByTime(4000);
        store.pauseNotification(id);
        vi.advanceTimersByTime(10000);
        expect(store.notifications.value.some((n) => n.id === id)).toBe(true);
        store.resumeNotification(id);
        vi.advanceTimersByTime(2000);
        expect(store.notifications.value.some((n) => n.id === id)).toBe(false);
    });
});
