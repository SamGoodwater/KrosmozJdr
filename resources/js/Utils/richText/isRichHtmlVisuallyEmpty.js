/**
 * Indique si un fragment HTML d’éditeur riche n’a aucun texte visible
 * (par ex. `<p></p>`, `<p><br></p>`, espaces, &nbsp;).
 *
 * @param {string|null|undefined} html
 * @returns {boolean}
 */
export function isRichHtmlVisuallyEmpty(html) {
  if (html == null) {
    return true;
  }
  const raw = String(html);
  if (!raw.trim()) {
    return true;
  }
  const text = raw
    .replace(/<[^>]+>/g, " ")
    .replace(/&nbsp;/gi, " ")
    .replace(/\s+/g, " ")
    .trim();

  return text === "";
}
