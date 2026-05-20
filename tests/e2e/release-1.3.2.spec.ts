import { test, expect } from '@playwright/test';

/**
 * Smoke release 1.3.2 — nécessite `php artisan serve` sur PLAYWRIGHT_BASE_URL.
 */
test.describe('Release 1.3.2 smoke', () => {
    test('accueil charge le contenu public', async ({ page }) => {
        // Nécessite `php artisan serve` + assets front (`pnpm run dev` ou `pnpm run build`)
        const response = await page.goto('/', { waitUntil: 'load' });
        expect(response?.status()).toBeLessThan(400);
        // `/` → redirect `/pages/accueil` ; le contenu est rendu par Inertia après hydratation
        await expect(page).toHaveURL(/\/pages\/accueil/, { timeout: 20_000 });
        await expect(
            page.getByRole('heading', { name: /Bienvenue sur Krosmoz/i })
        ).toBeVisible({ timeout: 30_000 });
    });

    test('page légale CGU accessible', async ({ page }) => {
        const response = await page.goto('/legal/cgu');
        expect(response?.status()).toBeLessThan(500);
        await expect(page.locator('body')).toContainText(/conditions|CGU|Krosmoz/i);
    });

    test('recherche globale API répond', async ({ request }) => {
        const res = await request.get('/api/search/global?q=krosmoz&limit=3');
        expect(res.status()).toBeLessThan(500);
    });
});
