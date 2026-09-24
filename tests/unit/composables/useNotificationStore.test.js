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

    it("passe en contracted après la moitié de la durée (tous types, y compris success/error)", async () => {
        const { useNotificationStore } = await import("@/Composables/store/useNotificationStore");
        const store = useNotificationStore();
        const now = Date.now();
        const successNotif = {
            type: "success",
            duration: 6000,
            createdAt: now - 10000,
            fullDisplayTime: 3000,
        };
        const errorNotif = {
            type: "error",
            duration: 6000,
            createdAt: now - 4000,
            fullDisplayTime: 3000,
        };
        const infoFull = {
            type: "info",
            duration: 6000,
            createdAt: now - 1000,
            fullDisplayTime: 3000,
        };
        const infoContracted = {
            type: "info",
            duration: 6000,
            createdAt: now - 10000,
            fullDisplayTime: 3000,
        };

        expect(store.getNotificationState(successNotif)).toBe("contracted");
        expect(store.getNotificationState(errorNotif)).toBe("contracted");
        expect(store.getNotificationState(infoFull)).toBe("full");
        expect(store.getNotificationState(infoContracted)).toBe("contracted");
    });

    it("diminue la barre de progression avec le temps écoulé", async () => {
        const { useNotificationStore } = await import("@/Composables/store/useNotificationStore");
        const store = useNotificationStore();
        const id = store.info("Progress", { duration: 10000 });
        const notif = store.notifications.value.find((n) => n.id === id);

        const atStart = store.getProgressPercentage(notif);
        expect(atStart).toBeCloseTo(100, 0);

        vi.advanceTimersByTime(2500);
        const afterQuarter = store.getProgressPercentage(notif);
        expect(afterQuarter).toBeCloseTo(75, 0);
        expect(afterQuarter).toBeLessThan(atStart);

        vi.advanceTimersByTime(2500);
        const afterHalf = store.getProgressPercentage(notif);
        expect(afterHalf).toBeCloseTo(50, 0);
        expect(afterHalf).toBeLessThan(afterQuarter);
    });

    it("fige la progression pendant la pause", async () => {
        const { useNotificationStore } = await import("@/Composables/store/useNotificationStore");
        const store = useNotificationStore();
        const id = store.success("Pause progress", { duration: 10000 });
        const notif = store.notifications.value.find((n) => n.id === id);

        // Passer la moitié de durée → contracted, puis pause
        vi.advanceTimersByTime(6000);
        expect(store.getNotificationState(notif)).toBe("contracted");
        store.pauseNotification(id);
        const pausedProgress = store.getProgressPercentage(notif);

        vi.advanceTimersByTime(5000);
        expect(store.getProgressPercentage(notif)).toBe(pausedProgress);
        expect(store.getNotificationState(notif)).toBe("contracted");
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

    it("ne ré-applique pas pause si déjà en pause (évite reset createdAt)", async () => {
        const { useNotificationStore } = await import("@/Composables/store/useNotificationStore");
        const store = useNotificationStore();
        const id = store.info("Stable pause", { duration: 8000 });
        const notif = store.notifications.value.find((n) => n.id === id);

        vi.advanceTimersByTime(2000);
        store.pauseNotification(id);
        const createdAtAfterFirstPause = notif.createdAt;
        const elapsedAtPause = notif.elapsedAtPause;

        store.pauseNotification(id);
        store.pauseNotification(id);
        expect(notif.createdAt).toBe(createdAtAfterFirstPause);
        expect(notif.elapsedAtPause).toBe(elapsedAtPause);
        expect(notif.paused).toBe(true);
    });
});
