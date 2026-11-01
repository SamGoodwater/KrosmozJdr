import { Capability } from "@/Models/Entity/Capability";

/**
 * Sépare les passifs des capacités de classe ; ces dernières sont triées par niveau croissant.
 *
 * @param {object[]|null|undefined} rawList
 * @returns {{ passiveCapabilities: Capability[], otherCapabilities: Capability[] }}
 * @example partitionBreedCapabilities(breed.capabilities).otherCapabilities.map((c) => c.level) // ['3', '9', '14']
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
    otherCapabilities.sort((a, b) => (Number(a.level) || 0) - (Number(b.level) || 0));
    return { passiveCapabilities, otherCapabilities };
}
