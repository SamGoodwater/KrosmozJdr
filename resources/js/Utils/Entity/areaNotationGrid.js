/**
 * Conversion notation zone Krosmoz → ensemble de cases (grille discrète) pour aperçu visuel.
 *
 * Métrique : distance de Chebyshev pour cercles / anneaux (aligné damier tactique).
 * Croix : bras le long des axes uniquement.
 *
 * @see docs/features/effects/README.md
 */

/** @typedef {{ x: number, y: number }} Cell */

/**
 * @param {string} key
 * @returns {Cell}
 */
function parseKey(key) {
    const [x, y] = key.split(',').map(Number);
    return { x, y };
}

/**
 * @param {number} x
 * @param {number} y
 * @returns {string}
 */
function cellKey(x, y) {
    return `${x},${y}`;
}

/**
 * @param {Iterable<string>} keys
 * @returns {Cell[]}
 */
function keysToCells(keys) {
    return [...keys].map(parseKey);
}

/**
 * Notation → cases actives (repère : origine au centre de la forme ; ligne horizontale pour line).
 *
 * @param {string|null|undefined} area
 * @returns {Cell[]}
 */
export function parseAreaToCells(area) {
    if (area == null || typeof area !== 'string') return [];
    const raw = area.trim();
    if (!raw) return [];

    const dash = raw.indexOf('-');
    const shape = dash >= 0 ? raw.slice(0, dash) : raw;
    const rest = dash >= 0 ? raw.slice(dash + 1) : '';

    const out = new Set();

    const add = (x, y) => out.add(cellKey(x, y));

    switch (shape) {
        case 'point': {
            add(0, 0);
            break;
        }
        case 'line': {
            const m = /^1x(\d+)$/.exec(rest);
            const len = m ? Math.max(1, parseInt(m[1], 10)) : 1;
            for (let i = 0; i < len; i += 1) add(i, 0);
            break;
        }
        case 'rect': {
            const m = /^(\d+)x(\d+)$/.exec(rest);
            if (!m) break;
            const w = Math.max(1, parseInt(m[1], 10));
            const h = Math.max(1, parseInt(m[2], 10));
            for (let cx = 0; cx < w; cx += 1) {
                for (let cy = 0; cy < h; cy += 1) add(cx, cy);
            }
            break;
        }
        case 'circle': {
            const m = /^(\d+)-(\d+)$/.exec(rest);
            if (!m) break;
            const rMin = Math.max(0, parseInt(m[1], 10));
            const rMax = Math.max(rMin, parseInt(m[2], 10));
            for (let dx = -rMax; dx <= rMax; dx += 1) {
                for (let dy = -rMax; dy <= rMax; dy += 1) {
                    const d = Math.max(Math.abs(dx), Math.abs(dy));
                    if (d >= rMin && d <= rMax) add(dx, dy);
                }
            }
            break;
        }
        case 'cross': {
            const m = /^(\d+)-(\d+)$/.exec(rest);
            if (!m) break;
            const cMin = Math.max(0, parseInt(m[1], 10));
            const cMax = Math.max(cMin, parseInt(m[2], 10));
            for (let d = cMin; d <= cMax; d += 1) {
                if (d === 0) add(0, 0);
                else {
                    add(d, 0);
                    add(-d, 0);
                    add(0, d);
                    add(0, -d);
                }
            }
            break;
        }
        case 'shape':
            // Pas de géométrie connue côté client (forme DofusDB non mappée) → pas de schéma grille.
            break;
        default:
            break;
    }

    return keysToCells(out);
}

/**
 * Bounding box + marge + carré englobant (cases vides pour compléter le côté court).
 *
 * @param {Cell[]} cells
 * @param {number} [padding=1]
 * @returns {{ side: number, offsetX: number, offsetY: number, active: Set<string> }|null}
 */
export function buildSquareGridModel(cells, padding = 1) {
    if (!cells.length) return null;

    let minX = Infinity;
    let maxX = -Infinity;
    let minY = Infinity;
    let maxY = -Infinity;
    for (const { x, y } of cells) {
        minX = Math.min(minX, x);
        maxX = Math.max(maxX, x);
        minY = Math.min(minY, y);
        maxY = Math.max(maxY, y);
    }

    minX -= padding;
    maxX += padding;
    minY -= padding;
    maxY += padding;

    const w = maxX - minX + 1;
    const h = maxY - minY + 1;
    const side = Math.max(w, h);

    const padX = Math.floor((side - w) / 2);
    const padY = Math.floor((side - h) / 2);

    const offsetX = minX - padX;
    const offsetY = minY - padY;

    const active = new Set();
    for (const { x, y } of cells) {
        active.add(cellKey(x - offsetX, y - offsetY));
    }

    return { side, offsetX, offsetY, active };
}

/**
 * Taille de cellule en px pour tenir dans un viewport carré max.
 *
 * @param {number} sideCells
 * @param {number} maxViewportPx
 * @param {number} cellSizeMinPx
 * @returns {number}
 */
export function computeCellPx(sideCells, maxViewportPx, cellSizeMinPx) {
    if (sideCells < 1) return cellSizeMinPx;
    const needed = sideCells * cellSizeMinPx;
    if (needed <= maxViewportPx) return cellSizeMinPx;
    return maxViewportPx / sideCells;
}

export { cellKey };
