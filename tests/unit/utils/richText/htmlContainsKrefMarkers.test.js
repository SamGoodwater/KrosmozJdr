import { describe, expect, it } from 'vitest';
import { htmlContainsKrefMarkers } from '@/Utils/richText/htmlContainsKrefMarkers';

describe('htmlContainsKrefMarkers', () => {
  it('retourne false pour chaîne vide ou null', () => {
    expect(htmlContainsKrefMarkers('')).toBe(false);
    expect(htmlContainsKrefMarkers(null)).toBe(false);
    expect(htmlContainsKrefMarkers(undefined)).toBe(false);
  });

  it('détecte span avec classe kref', () => {
    expect(
      htmlContainsKrefMarkers(
        '<p><span class="kref icon-ca" title="e30=">Force</span></p>',
      ),
    ).toBe(true);
    expect(htmlContainsKrefMarkers('<span class="foo kref bar">x</span>')).toBe(true);
  });

  it('détecte data-kref-type (legacy)', () => {
    expect(htmlContainsKrefMarkers('<span data-kref-type="spell">x</span>')).toBe(true);
  });

  it('retourne false sans marqueur', () => {
    expect(htmlContainsKrefMarkers('<p>Texte seul</p>')).toBe(false);
    expect(htmlContainsKrefMarkers('<span class="not-kref">x</span>')).toBe(false);
  });
});
