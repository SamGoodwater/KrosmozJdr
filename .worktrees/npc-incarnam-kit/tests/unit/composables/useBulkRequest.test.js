/**
 * Tests unitaires pour useBulkRequest (fetch + useUxFeedback).
 */

import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { useBulkRequest } from "@/Composables/entity/useBulkRequest";

const notifySuccess = vi.fn();
const notifyError = vi.fn();

vi.mock("@/Composables/utils/useUxFeedback", () => ({
  useUxFeedback: () => ({
    notifySuccess,
    notifyError,
  }),
}));

describe("useBulkRequest", () => {
  beforeEach(() => {
    vi.clearAllMocks();
    vi.stubGlobal("fetch", vi.fn());
    vi.spyOn(document, "querySelector").mockReturnValue({
      getAttribute: () => "csrf-test-token",
    });
  });

  afterEach(() => {
    vi.unstubAllGlobals();
    vi.restoreAllMocks();
  });

  describe("bulkPatchJson", () => {
    it("fait une requête PATCH avec le bon payload", async () => {
      global.fetch.mockResolvedValue({
        ok: true,
        json: async () => ({ success: true, summary: { updated: 2, requested: 2 } }),
      });

      const { bulkPatchJson } = useBulkRequest();
      const payload = { ids: [1, 2], level: "50" };

      await bulkPatchJson({ url: "/api/entities/spells/bulk", payload });

      expect(global.fetch).toHaveBeenCalledWith(
        "/api/entities/spells/bulk",
        expect.objectContaining({
          method: "PATCH",
          headers: expect.objectContaining({
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "csrf-test-token",
          }),
          body: JSON.stringify(payload),
        }),
      );
    });

    it("affiche une notification de succès", async () => {
      global.fetch.mockResolvedValue({
        ok: true,
        json: async () => ({ success: true, summary: { updated: 2, requested: 2 } }),
      });

      const { bulkPatchJson } = useBulkRequest();

      await bulkPatchJson({ url: "/api/entities/spells/bulk", payload: { ids: [1, 2] } });

      expect(notifySuccess).toHaveBeenCalled();
      expect(notifyError).not.toHaveBeenCalled();
    });

    it("affiche une notification d'erreur en cas d'échec HTTP", async () => {
      global.fetch.mockResolvedValue({
        ok: false,
        json: async () => ({ success: false, message: "Erreur de validation" }),
      });

      const { bulkPatchJson } = useBulkRequest();

      await bulkPatchJson({ url: "/api/entities/spells/bulk", payload: { ids: [1, 2] } });

      expect(notifyError).toHaveBeenCalled();
      expect(notifySuccess).not.toHaveBeenCalled();
    });

    it("gère les erreurs réseau", async () => {
      global.fetch.mockRejectedValue(new Error("Network Error"));

      const { bulkPatchJson } = useBulkRequest();

      await bulkPatchJson({ url: "/api/entities/spells/bulk", payload: { ids: [1, 2] } });

      expect(notifyError).toHaveBeenCalled();
    });

    it("retourne true en cas de succès", async () => {
      global.fetch.mockResolvedValue({
        ok: true,
        json: async () => ({ success: true, summary: { updated: 2, requested: 2 } }),
      });

      const { bulkPatchJson } = useBulkRequest();

      const result = await bulkPatchJson({ url: "/api/entities/spells/bulk", payload: { ids: [1, 2] } });

      expect(result).toBe(true);
    });

    it("retourne false en cas d'échec", async () => {
      global.fetch.mockResolvedValue({
        ok: true,
        json: async () => ({ success: false, errors: [{ id: 1, error: "Not found" }] }),
      });

      const { bulkPatchJson } = useBulkRequest();

      const result = await bulkPatchJson({ url: "/api/entities/spells/bulk", payload: { ids: [1, 2] } });

      expect(result).toBe(false);
    });

    it("gère les erreurs partielles", async () => {
      global.fetch.mockResolvedValue({
        ok: true,
        json: async () => ({
          success: false,
          summary: { requested: 2, updated: 1, errors: 1 },
          errors: [{ id: 2, error: "Not found" }],
        }),
      });

      const { bulkPatchJson } = useBulkRequest();

      const result = await bulkPatchJson({ url: "/api/entities/spells/bulk", payload: { ids: [1, 2] } });

      expect(result).toBe(false);
      expect(notifyError).toHaveBeenCalled();
    });
  });
});
