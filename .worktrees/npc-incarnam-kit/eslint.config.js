import js from '@eslint/js';
import vue from 'eslint-plugin-vue';
import globals from 'globals';
import prettier from 'eslint-config-prettier';
import { createRequire } from 'node:module';
import path from 'node:path';

const require = createRequire(import.meta.url);
const eslintPluginVueRoot = path.dirname(require.resolve('eslint-plugin-vue/package.json'));
const vueParser = require(require.resolve('vue-eslint-parser', { paths: [eslintPluginVueRoot] }));

const projectVueRules = {
  /**
   * Sécurité : `v-html` est interdit par défaut.
   * Les exceptions doivent être explicites (commentaire) et idéalement passer par un sanitiseur.
   */
  'vue/no-v-html': 'error',

  'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
  'no-undef': 'error',
  'vue/multi-word-component-names': 'off',
  'vue/attributes-order': 'off',
  'vue/first-attribute-linebreak': 'off',
  'vue/attribute-hyphenation': 'off',
  'vue/no-required-prop-with-default': 'off',
  'vue/no-dupe-keys': 'off',
};

export default [
  {
    ignores: [
      'vendor/**',
      'node_modules/**',
      'public/**',
      'storage/**',
      'bootstrap/**',
      'resources/css/**',
      'resources/views/**',
      'docs/**',
    ],
  },

  js.configs.recommended,
  ...vue.configs['flat/recommended'],
  prettier,

  /**
   * JS : options de langage complètes.
   * Vue : parser explicite (vue-eslint-parser n’est pas hoisted à la racine du monorepo pnpm ;
   * un bloc `languageOptions` sans `parser` peut aussi écraser celui de `eslint-plugin-vue`).
   */
  {
    files: ['resources/js/**/*.js'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
        route: 'readonly',
      },
    },
    rules: {
      'no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
      'no-undef': 'error',
    },
  },
  {
    files: ['resources/js/**/*.vue'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
      },
      globals: {
        ...globals.browser,
        ...globals.node,
        route: 'readonly',
      },
    },
    rules: projectVueRules,
  },
];


