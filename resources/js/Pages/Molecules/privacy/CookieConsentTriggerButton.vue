<script setup>
/**
 * Bouton discret pour rouvrir le panneau de préférences cookies (hors position fixed).
 */
import { useCookieConsent } from '@/Composables/privacy/useCookieConsent';

const { shouldRenderCookiePanel, openCookiePreferences } = useCookieConsent();
</script>

<template>
    <button
        v-if="!shouldRenderCookiePanel"
        type="button"
        class="btn btn-ghost btn-xs opacity-80"
        @click="openCookiePreferences"
        aria-label="Ouvrir les preferences cookies"
        title="Preferences cookies"
    >
        <i class="fa-solid fa-cookie-bite sm:hidden" aria-hidden="true"></i>
        <span class="hidden sm:inline">Cookies</span>
    </button>
</template>
