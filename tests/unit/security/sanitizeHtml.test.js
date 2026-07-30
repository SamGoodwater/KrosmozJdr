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

  it('preserves kref title payloads used by rich reference navigation', () => {
    const input = '<p><span class="kref kref--nav" title="eyJ0IjoicGFnZSJ9">Guide</span></p>';
    const output = sanitizeHtml(input);

    expect(output).toContain('class="kref kref--nav"');
    expect(output).toContain('title="eyJ0IjoicGFnZSJ9"');
  });
});


