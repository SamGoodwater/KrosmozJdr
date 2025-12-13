import js from '@eslint/js';
import vue from 'eslint-plugin-vue';
import globals from 'globals';
import prettier from 'eslint-config-prettier';

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

  {
    files: ['resources/js/**/*.{js,vue}'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        ...globals.node,
        // Ziggy / Inertia helpers exposés globalement dans le runtime
        route: 'readonly',
      },
    },
    rules: {
      /**
       * Sécurité : `v-html` est interdit par défaut.
       * Les exceptions doivent être explicites (commentaire) et idéalement passer par un sanitiseur.
       */
      'vue/no-v-html': 'error',

      // Réduction du bruit initial : le projet existant contient beaucoup de legacy.
      // On durcira progressivement une fois le baseline stabilisé.
      'no-unused-vars': 'warn',
      'no-undef': 'error',
      'vue/multi-word-component-names': 'off',
      'vue/attributes-order': 'off',
      'vue/first-attribute-linebreak': 'off',
      'vue/attribute-hyphenation': 'off',
      'vue/no-required-prop-with-default': 'off',
      'vue/no-dupe-keys': 'off',
    },
  },
];


