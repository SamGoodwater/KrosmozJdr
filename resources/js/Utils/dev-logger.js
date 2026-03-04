/**
 * Dev logger helpers
 *
 * @description
 * Centralise les logs frontend afin d'eviter le bruit en production.
 * Les fonctions `logDev` et `warnDev` sont no-op hors mode developpement.
 */

const isDev = import.meta.env.DEV;

export function logDev(...args) {
    if (!isDev) return;
    console.log(...args);
}

export function warnDev(...args) {
    if (!isDev) return;
    console.warn(...args);
}
