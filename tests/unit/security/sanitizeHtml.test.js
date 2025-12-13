import { describe, it, expect } from 'vitest';
import { sanitizeHtml } from '@/Utils/security/sanitizeHtml';

describe('sanitizeHtml', () => {
  it('removes script tags and dangerous attributes', () => {
    const input = '<p>ok</p><script>alert(1)</script><img src="x" onerror="alert(2)" />';
    const output = sanitizeHtml(input);

    expect(output).toContain('<p>ok</p>');
    expect(output.toLowerCase()).not.toContain('<script');
    expect(output.toLowerCase()).not.toContain('onerror');
  });
});


