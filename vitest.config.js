import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

/**
 * Configuration Vitest pour les tests unitaires frontend
 * 
 * @description
 * Configuration pour tester les composants Vue, composables, et utilitaires.
 * Utilise la même configuration que Vite pour la cohérence.
 */
export default defineConfig({
  plugins: [vue()],
  
  test: {
    // Environnement de test
    environment: 'jsdom',
    
    // Globals pour éviter d'importer vi, describe, it, etc.
    globals: true,
    
    // Setup files
    setupFiles: ['./tests/setup.js'],
    
    // Pool (Vitest 4) : limiter le parallélisme pour éviter les timeouts workers sous charge
    pool: 'forks',
    maxWorkers: 4,
    
    // Coverage (Vitest 4 : include explicite, coverage.all retiré)
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      include: ['resources/js/**/*.{js,vue}'],
      exclude: [
        'node_modules/',
        'tests/',
        '**/*.config.js',
        '**/*.config.ts',
        '**/dist/',
        '**/build/',
      ],
    },
    
    // Patterns de fichiers de test
    include: [
      'tests/unit/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}',
      'tests/a11y/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}',
    ],
    // Vitest 4 n’exclut plus vendor/dist par défaut hors node_modules/.git
    exclude: [
      'node_modules',
      'dist',
      'vendor',
      'public/build',
      'storage',
      '.idea',
      '.git',
      '.cache',
    ],
    
    // Timeout pour les tests
    testTimeout: 10000,
  },
  
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
});

