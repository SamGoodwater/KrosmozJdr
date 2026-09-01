/**
 * Charte unique des filtres tableau (toutes les entités).
 */

import { describe, expect, it } from "vitest";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import { getResourceFieldDescriptors } from "@/Entities/resource/resource-descriptors";
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { getNpcFieldDescriptors } from "@/Entities/npc/npc-descriptors";
import { getConditionFieldDescriptors } from "@/Entities/condition/condition-descriptors";
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";
import { getPanoplyFieldDescriptors } from "@/Entities/panoply/panoply-descriptors";
import { getShopFieldDescriptors } from "@/Entities/shop/shop-descriptors";
import { getCampaignFieldDescriptors } from "@/Entities/campaign/campaign-descriptors";
import { getScenarioFieldDescriptors } from "@/Entities/scenario/scenario-descriptors";
import { getCreatureTraitFieldDescriptors } from "@/Entities/creature-trait/creature-trait-descriptors";
import { getResourceTypeFieldDescriptors } from "@/Entities/resource-type/resource-type-descriptors";
import { CONDITION_CATALOG_STATE_DEFAULT } from "@/Composables/condition/conditionDisplay";

const factories = {
    item: getItemFieldDescriptors,
    resource: getResourceFieldDescriptors,
    consumable: getConsumableFieldDescriptors,
    spell: getSpellFieldDescriptors,
    capability: getCapabilityFieldDescriptors,
    monster: getMonsterFieldDescriptors,
    npc: getNpcFieldDescriptors,
    condition: getConditionFieldDescriptors,
    breed: getBreedFieldDescriptors,
    specialization: getSpecializationFieldDescriptors,
    panoply: getPanoplyFieldDescriptors,
    shop: getShopFieldDescriptors,
    campaign: getCampaignFieldDescriptors,
    scenario: getScenarioFieldDescriptors,
    creatureTrait: getCreatureTraitFieldDescriptors,
    resourceType: getResourceTypeFieldDescriptors,
};

function collectFilterables(descriptors) {
    return Object.values(descriptors)
        .map((desc) => desc?.table?.filterable)
        .filter((f) => f?.id && f?.type);
}

describe("charte des filtres tableau", () => {
    it("n’expose aucun filtre texte (doublon de la recherche)", () => {
        for (const [name, factory] of Object.entries(factories)) {
            const texts = collectFilterables(factory()).filter((f) => f.type === "text");
            expect(texts, name).toEqual([]);
        }
    });

    it("filtre le niveau en plage visible (slider min/max)", () => {
        const cases = [
            ["item", getItemFieldDescriptors, "level"],
            ["resource", getResourceFieldDescriptors, "level"],
            ["consumable", getConsumableFieldDescriptors, "level"],
            ["spell", getSpellFieldDescriptors, "level"],
            ["capability", getCapabilityFieldDescriptors, "level"],
            ["monster", getMonsterFieldDescriptors, "creature_level"],
            ["npc", getNpcFieldDescriptors, "creature_level"],
        ];
        for (const [name, factory, id] of cases) {
            const filter = collectFilterables(factory()).find((f) => f.id === id);
            expect(filter, name).toMatchObject({
                type: "range",
                defaultVisible: true,
            });
        }
    });

    it("filtre l’état en multi, Jouable par défaut (sauf conditions)", () => {
        for (const [name, factory] of Object.entries(factories)) {
            const filter = collectFilterables(factory()).find((f) => f.id === "state");
            if (!filter) continue;
            expect(filter.type, name).toBe("multi");
            expect(filter.defaultVisible, name).toBe(true);
            expect(Array.isArray(filter.options) ? filter.options.map((o) => o.value) : [], name)
                .toEqual(expect.arrayContaining(["playable", "draft", "raw"]));
            if (name === "condition") {
                expect(filter.defaultValue).toEqual([...CONDITION_CATALOG_STATE_DEFAULT]);
                expect(filter.defaultValue).not.toContain("raw");
            } else {
                expect(filter.defaultValue, name).toEqual(["playable"]);
            }
        }
    });

    it("expose le nombre de pièces et les types d’objets sur les panoplies", () => {
        const filters = collectFilterables(getPanoplyFieldDescriptors());
        expect(filters.find((f) => f.id === "items_count")).toMatchObject({
            type: "range",
            defaultVisible: true,
        });
        expect(filters.find((f) => f.id === "item_type_id")).toMatchObject({
            type: "multi",
            defaultVisible: true,
            defaultByCatalog: true,
        });
    });

    it("filtre dissipable (états) en interrupteur, pas en liste", () => {
        const filter = collectFilterables(getConditionFieldDescriptors()).find((f) => f.id === "dissipable");
        expect(filter).toMatchObject({
            type: "boolean",
            defaultVisible: true,
        });
    });

    it("ne filtre plus creature_state (doublon de l’état d’entité)", () => {
        for (const factory of [getMonsterFieldDescriptors, getNpcFieldDescriptors]) {
            const ids = collectFilterables(factory()).map((f) => f.id);
            expect(ids).not.toContain("creature_state");
        }
    });
});
