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
    
    // Pool options pour éviter les problèmes avec jsdom
    pool: 'threads',
    poolOptions: {
      threads: {
        singleThread: false,
      },
    },
    
    // Coverage
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
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
    include: ['tests/unit/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}'],
    exclude: ['node_modules', 'dist', '.idea', '.git', '.cache'],
    
    // Timeout pour les tests
    testTimeout: 10000,
  },
  
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
});

