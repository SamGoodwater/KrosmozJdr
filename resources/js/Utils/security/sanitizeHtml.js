/**
 * HTML sanitization util (frontend defense-in-depth).
 *
 * @description
 * Utilise DOMPurify pour nettoyer du HTML non fiable avant rendu via `v-html`.
 * Le backend reste la source de vérité (sanitization à la persistance), mais ce helper
 * protège aussi contre du contenu historique non nettoyé.
 *
 * @example
 * import { sanitizeHtml } from '@/Utils/security/sanitizeHtml'
 * const safeHtml = sanitizeHtml(userHtml)
 *
 * @param {string} html
 * @returns {string}
 */
import DOMPurify from 'dompurify';

export function sanitizeHtml(html) {
  if (typeof html !== 'string' || html.length === 0) return '';
  return DOMPurify.sanitize(html, {
    USE_PROFILES: { html: true },
  });
}


