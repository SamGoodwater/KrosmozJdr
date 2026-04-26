/**
 * Détecte si le HTML contient des marqueurs de référence kref (persistés ou legacy).
 * Permet d’activer RichTextReadonlyView / l’éditeur riche sans exiger `settings.enableRichReferences`.
 *
 * @param {string|null|undefined} html Contenu HTML (idéalement déjà passé par `sanitizeHtml`).
 * @returns {boolean} True si au moins un marqueur kref est présent.
 * @example
 * htmlContainsKrefMarkers('<span class="kref icon-ca" title="eyJ0IjoiYyJ9">Force</span>');
 * // true
 */
function classAttributeHasKrefToken(classValue) {
  return String(classValue || '')
    .trim()
    .split(/\s+/)
    .some((token) => token === 'kref');
}

/**
 * @param {string} s
 * @returns {boolean}
 */
function htmlHasSpanWithKrefClass(s) {
  const re = /<span\b[^>]*\bclass\s*=\s*(["'])([^"']*)\1/gi;
  let m = re.exec(s);
  while (m !== null) {
    if (classAttributeHasKrefToken(m[2])) {
      return true;
    }
    m = re.exec(s);
  }
  return false;
}

export function htmlContainsKrefMarkers(html) {
  const s = String(html ?? '');
  if (!s) {
    return false;
  }
  if (htmlHasSpanWithKrefClass(s)) {
    return true;
  }
  if (/\bdata-kref-type\s*=/i.test(s)) {
    return true;
  }
  return false;
}
