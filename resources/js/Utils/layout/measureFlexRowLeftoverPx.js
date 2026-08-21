/**
 * Largeur restante dans une rangée flex, après les frères (titre, badges…).
 *
 * @description
 * Pour un titre tronqué (`flex-1`), on prend la largeur *souhaitée* du texte
 * (`scrollWidth`) afin de réserver l’espace entre le titre et le bord.
 *
 * @param {HTMLElement | null} el Élément dont on cherche la place restante.
 * @returns {number} Pixels, ≥ 0.
 */
export function measureFlexRowLeftoverPx(el) {
    if (!el || typeof window === "undefined") {
        return 0;
    }

    const found = findFlexRowWithSiblings(el);
    if (!found) {
        return el.getBoundingClientRect().width;
    }

    const { row, selfChild } = found;
    const rowBox = row.getBoundingClientRect();
    if (rowBox.width <= 1) {
        return 0;
    }

    const cs = getComputedStyle(row);
    const padX =
        (parseFloat(cs.paddingLeft) || 0) + (parseFloat(cs.paddingRight) || 0);
    const gap = parseFloat(cs.columnGap || cs.gap) || 0;
    const kids = Array.from(row.children);
    let others = 0;
    for (const child of kids) {
        if (child === selfChild) continue;
        others += preferredSiblingWidth(child);
    }
    const gaps = gap * Math.max(0, kids.length - 1);
    return Math.max(0, rowBox.width - padX - others - gaps);
}

/**
 * @param {HTMLElement} el
 * @returns {{ row: HTMLElement, selfChild: HTMLElement } | null}
 */
function findFlexRowWithSiblings(el) {
    let current = el;
    let node = el.parentElement;
    while (node) {
        const style = getComputedStyle(node);
        const display = style.display || "";
        const dir = style.flexDirection || "row";
        const isRow =
            (display === "flex" || display === "inline-flex") &&
            !String(dir).startsWith("column");
        if (isRow) {
            const kids = Array.from(node.children);
            const selfChild = kids.find(
                (child) => child === current || child.contains(el),
            );
            const others = kids.filter((child) => child !== selfChild);
            if (selfChild && others.length > 0) {
                return { row: node, selfChild };
            }
        }
        current = node;
        node = node.parentElement;
    }
    return null;
}

/**
 * @param {HTMLElement} el
 * @returns {number}
 */
function preferredSiblingWidth(el) {
    const title = el.querySelector(
        "h1, h2, h3, h4, .entity-minimal-title, .font-semibold.truncate",
    );
    if (title) {
        let extra = 0;
        el.querySelectorAll(".shrink-0, .flex-shrink-0").forEach((badge) => {
            if (title.contains(badge) || badge.contains(title)) return;
            extra += badge.getBoundingClientRect().width;
        });
        return Math.max(title.scrollWidth, title.getBoundingClientRect().width) + extra;
    }
    return el.getBoundingClientRect().width;
}
