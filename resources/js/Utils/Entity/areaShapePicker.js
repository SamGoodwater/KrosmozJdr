/**
 * Parse / sérialise la notation de zone pour le sélecteur de formes UI.
 *
 * @example
 * parseAreaShapeParams('circle-0-2') // { shape: 'circle', a: 0, b: 2 }
 * buildAreaNotation({ shape: 'line', length: 3 }) // 'line-1x3'
 */
import { getAreaShape } from '@/Utils/Entity/Areas.js';
import { isValidAreaNotation } from '@/Utils/Entity/areaNotation.js';

/** @typedef {{ shape: string, a?: number, b?: number, length?: number, width?: number, height?: number }} AreaShapeParams */

export const AREA_SHAPE_HELP = Object.freeze({
    point: 'Une seule case : la case ciblée.',
    line: 'Ligne d’une case de large et L cases de long (dans la direction du lancer).',
    cross: 'Croix. a = rayon intérieur (0 = case centrale incluse), b = rayon extérieur.',
    circle: 'Cercle / disque. a = rayon intérieur (0 = case centrale incluse), b = rayon extérieur.',
    rect: 'Rectangle W×H centré sur la cible (cases).',
});

/**
 * @param {string|null|undefined} area
 * @returns {AreaShapeParams}
 */
export function parseAreaShapeParams(area) {
    const shape = getAreaShape(area) || 'point';
    const s = typeof area === 'string' ? area.trim() : '';
    if (shape === 'point' || !s) {
        return { shape: 'point' };
    }
    let m = /^line-1x(\d+)$/.exec(s);
    if (m) return { shape: 'line', length: parseInt(m[1], 10) };
    m = /^cross-(\d+)-(\d+)$/.exec(s);
    if (m) return { shape: 'cross', a: parseInt(m[1], 10), b: parseInt(m[2], 10) };
    m = /^circle-(\d+)-(\d+)$/.exec(s);
    if (m) return { shape: 'circle', a: parseInt(m[1], 10), b: parseInt(m[2], 10) };
    m = /^rect-(\d+)x(\d+)$/.exec(s);
    if (m) {
        return {
            shape: 'rect',
            width: parseInt(m[1], 10),
            height: parseInt(m[2], 10),
        };
    }
    return { shape };
}

/**
 * @param {AreaShapeParams} params
 * @returns {string}
 */
export function buildAreaNotation(params) {
    const shape = params?.shape || 'point';
    if (shape === 'point') return 'point';
    if (shape === 'line') {
        const length = Math.max(1, Number(params.length) || 1);
        return `line-1x${length}`;
    }
    if (shape === 'cross' || shape === 'circle') {
        const a = Math.max(0, Number(params.a) || 0);
        const b = Math.max(a, Number(params.b) || a);
        return `${shape}-${a}-${b}`;
    }
    if (shape === 'rect') {
        const width = Math.max(1, Number(params.width) || 1);
        const height = Math.max(1, Number(params.height) || 1);
        return `rect-${width}x${height}`;
    }
    return 'point';
}

/**
 * @param {string|null|undefined} area
 * @returns {boolean}
 */
export function isKnownShapeNotation(area) {
    if (!isValidAreaNotation(area)) return false;
    const shape = getAreaShape(area);
    return shape != null && ['point', 'line', 'cross', 'circle', 'rect'].includes(shape);
}
