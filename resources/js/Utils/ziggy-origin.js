/**
 * Déduit `url` / `port` Ziggy depuis une URL courante (string, Location, ou fenêtre).
 *
 * @param {string|{href?: string}|undefined} location
 * @returns {{ url?: string, port?: number|false, location?: string }}
 *
 * @example
 * resolveZiggyOrigin('http://127.0.0.1:8000/pages/accueil');
 * // → { url: 'http://127.0.0.1:8000', port: 8000, location: '...' }
 */
export function resolveZiggyOrigin(location) {
    let href = null;
    if (typeof location === 'string' && location.trim() !== '') {
        href = location;
    } else if (location && typeof location === 'object' && typeof location.href === 'string') {
        href = location.href;
    } else if (typeof window !== 'undefined') {
        href = window.location.href;
    }

    if (!href) {
        return {};
    }

    try {
        const base =
            typeof window !== 'undefined' ? window.location.origin : 'http://localhost';
        const parsed = new URL(href, base);
        const hasExplicitPort = parsed.port !== '';
        const port = hasExplicitPort
            ? Number(parsed.port)
            : parsed.protocol === 'https:'
              ? 443
              : 80;
        const url = hasExplicitPort
            ? `${parsed.protocol}//${parsed.hostname}:${parsed.port}`
            : `${parsed.protocol}//${parsed.hostname}`;

        return {
            url,
            port: hasExplicitPort ? port : false,
            location: href,
        };
    } catch {
        return { location: href };
    }
}
