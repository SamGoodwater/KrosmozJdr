import { describe, it, expect, beforeEach } from "vitest";
import {
    ENTITY_ACTION_CONTEXT_PRESETS,
    MINIMAL_EXPANDED_ACTION_KEYS,
} from "@/Entities/entity-actions-config";
import {
    isEntityPinned,
    listPinnedWindows,
    toggleEntityPin,
    updatePinnedWindowPosition,
} from "@/Composables/entity/usePinnedEntityIds";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { CREATURE_CHARACTERISTIC_SUMMARY_KEYS } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";

describe("ENTITY_ACTION_CONTEXT_PRESETS", () => {
    it("ordonne le minimal comme convenu", () => {
        expect(ENTITY_ACTION_CONTEXT_PRESETS.minimalLine).toEqual([
            "state",
            "pin",
            "quick-view",
            "quick-edit",
            "view-dofusdb",
            "favorite",
            "copy-link",
            "view",
            "edit",
        ]);
        expect(MINIMAL_EXPANDED_ACTION_KEYS).toEqual(ENTITY_ACTION_CONTEXT_PRESETS.minimalLine);
    });

    it("n’inclut pas quick-edit / quick-view en modal", () => {
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).not.toContain("quick-view");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).not.toContain("quick-edit");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).toContain("refresh");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).toContain("delete");
    });

    it("n’inclut pas view (agrandir) en page", () => {
        expect(ENTITY_ACTION_CONTEXT_PRESETS.pageDetail).not.toContain("view");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.pageDetail).toEqual([
            "state",
            "favorite",
            "copy-link",
            "view-dofusdb",
            "edit",
            "refresh",
            "delete",
        ]);
    });
});

describe("usePinnedEntityIds fenêtres", () => {
    beforeEach(() => {
        localStorage.clear();
    });

    it("crée une fenêtre avec payload et position", () => {
        const pinned = toggleEntityPin("monsters", 7, {
            entity: { id: 7, name: "Bouftou", creature: { pa: 6 } },
        });
        expect(pinned).toBe(true);
        expect(isEntityPinned("monsters", 7)).toBe(true);
        const wins = listPinnedWindows();
        expect(wins).toHaveLength(1);
        expect(wins[0].entity?.name).toBe("Bouftou");
        expect(wins[0].x).toBeTypeOf("number");
    });

    it("met à jour la position et désépingle", () => {
        toggleEntityPin("spells", 3, { entity: { id: 3, name: "Sort" } });
        updatePinnedWindowPosition("spells", 3, 120, 200);
        expect(listPinnedWindows()[0].x).toBe(120);
        expect(listPinnedWindows()[0].y).toBe(200);
        expect(toggleEntityPin("spells", 3)).toBe(false);
        expect(listPinnedWindows()).toHaveLength(0);
    });
});

describe("buildCreatureCharacteristicGroups", () => {
    const creature = {
        level: 8,
        pa: 6,
        pm: 3,
        po: 1,
        life: 40,
        ini: 12,
        strong: 14,
        intel: 10,
        agi: 12,
        chance: 10,
        vitality: 12,
        sagesse: 11,
    };

    it("mode summary = 5 clés plates", () => {
        const groups = buildCreatureCharacteristicGroups(creature, {
            mode: "summary",
            byDbColumn: Object.fromEntries(
                CREATURE_CHARACTERISTIC_SUMMARY_KEYS.map((k) => [
                    k,
                    { key: `${k}_creature`, name: k, short_name: k, db_column: k },
                ]),
            ),
        });
        expect(groups).toHaveLength(1);
        expect(groups[0].title).toBe("");
        expect(groups[0].characteristics.map((c) => c.def.db_column || c.def.key.replace(/_creature$/, ""))).toEqual(
            expect.arrayContaining([...CREATURE_CHARACTERISTIC_SUMMARY_KEYS]),
        );
        expect(groups[0].characteristics).toHaveLength(5);
    });

    it("mode full expose Combat puis Stats", () => {
        const allKeys = [
            "pa",
            "pm",
            "po",
            "life",
            "ini",
            "invocation",
            "strong",
            "intel",
            "agi",
            "chance",
            "vitality",
            "sagesse",
            "ca",
            "dodge_pa",
            "dodge_pm",
            "fuite",
            "tacle",
            "critical_hit",
            "heal_bonus",
        ];
        const byDbColumn = Object.fromEntries(
            allKeys.map((k) => [k, { key: `${k}_creature`, name: k, short_name: k, db_column: k }]),
        );
        const groups = buildCreatureCharacteristicGroups(creature, { mode: "full", byDbColumn });
        expect(groups[0].title).toBe("Combat");
        expect(groups[1].title).toBe("Stats");
    });
});
