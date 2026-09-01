/**
 * Un seul dropdown ouvert à la fois (listes de filtres, menus).
 *
 * @example
 * claimExclusiveDropdown("a", closeA)
 */

const closers = new Map();

/**
 * @param {string} id
 * @param {() => void} close
 */
export function claimExclusiveDropdown(id, close) {
    const key = String(id || "");
    if (!key) return;
    for (const [otherId, otherClose] of [...closers.entries()]) {
        if (otherId === key) continue;
        try {
            otherClose();
        } catch {
            // ignore
        }
    }
    closers.set(key, close);
}

/**
 * @param {string} id
 */
export function releaseExclusiveDropdown(id) {
    closers.delete(String(id || ""));
}
