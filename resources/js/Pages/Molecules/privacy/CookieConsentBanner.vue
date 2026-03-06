<script setup>
import { useCookieConsent } from '@/Composables/privacy/useCookieConsent';

const {
  shouldRenderCookiePanel,
  shouldShowCookieBanner,
  thirdPartyCookiesAccepted,
  isCookiePreferencesOpen,
  acceptThirdPartyCookies,
  declineThirdPartyCookies,
  openCookiePreferences,
  closeCookiePreferences,
  resetCookieConsentChoice,
} = useCookieConsent();
</script>

<template>
  <transition name="fade-slide">
    <aside
      v-if="shouldRenderCookiePanel"
      class="fixed bottom-2 sm:bottom-4 left-1/2 z-70 w-[min(98vw,760px)] sm:w-[min(96vw,760px)] -translate-x-1/2 rounded-xl border border-base-300 bg-base-100/95 p-2.5 sm:p-3 shadow-xl backdrop-blur"
      aria-live="polite"
    >
      <p class="text-xs font-semibold uppercase tracking-wide text-base-content/60">
        Parametres cookies
      </p>
      <p class="text-sm leading-snug text-base-content/85">
        Ce site utilise des cookies techniques necessaires (session, securite), sans ciblage publicitaire.
        Les cookies tiers ne s'activent que pour les contenus externes (YouTube/Vimeo), avec ton accord.
        <a class="link link-primary ml-1" href="/pages/cgu">CGU</a>
        <span aria-hidden="true">·</span>
        <a class="link link-primary ml-1" href="/pages/politique-donnees">Politique de donnees</a>
      </p>

      <p
        v-if="!shouldShowCookieBanner && thirdPartyCookiesAccepted !== null"
        class="mt-2 text-xs text-base-content/65"
      >
        Choix actuel :
        <strong>{{ thirdPartyCookiesAccepted ? "cookies tiers autorises" : "cookies tiers refuses" }}</strong>
      </p>

      <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="button" class="btn btn-xs btn-primary" @click="acceptThirdPartyCookies">
          Autoriser les cookies tiers
        </button>
        <button type="button" class="btn btn-xs btn-ghost" @click="declineThirdPartyCookies">
          Refuser
        </button>
        <button
          v-if="thirdPartyCookiesAccepted !== null"
          type="button"
          class="btn btn-xs btn-ghost"
          @click="resetCookieConsentChoice"
        >
          Reinitialiser
        </button>
        <button
          v-if="isCookiePreferencesOpen && !shouldShowCookieBanner"
          type="button"
          class="btn btn-xs btn-ghost ml-auto"
          @click="closeCookiePreferences"
        >
          Fermer
        </button>
      </div>
    </aside>
  </transition>

  <button
    v-if="!shouldRenderCookiePanel"
    type="button"
    class="btn btn-ghost btn-xs fixed bottom-2.5 right-2.5 sm:bottom-3 sm:right-3 z-60 opacity-80"
    @click="openCookiePreferences"
    aria-label="Ouvrir les preferences cookies"
    title="Preferences cookies"
  >
    <i class="fa-solid fa-cookie-bite sm:hidden" aria-hidden="true"></i>
    <span class="hidden sm:inline">Cookies</span>
  </button>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translate(-50%, 8px);
}
</style>
