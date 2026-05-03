import { Capability } from "@/Models/Entity/Capability";

/**
 * @param {object[]|null|undefined} rawList
 * @returns {{ passiveCapabilities: Capability[], otherCapabilities: Capability[] }}
 */
export function partitionBreedCapabilities(rawList) {
    const list = Array.isArray(rawList) ? rawList : [];
    const passiveCapabilities = [];
    const otherCapabilities = [];
    for (const raw of list) {
        const c = raw instanceof Capability ? raw : new Capability(raw);
        if (c.isPassive) {
            passiveCapabilities.push(c);
        } else {
            otherCapabilities.push(c);
        }
    }
    return { passiveCapabilities, otherCapabilities };
}
