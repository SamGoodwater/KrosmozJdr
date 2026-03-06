import { computed, ref } from 'vue';

const STORAGE_KEY = 'krosmoz_cookie_consent_v1';

const consentLoaded = ref(false);
const thirdPartyCookiesAccepted = ref(null); // null | true | false
const thirdPartyRequesters = new Set();
const hasThirdPartyConsentRequest = ref(false);
const isCookiePreferencesOpen = ref(false);

function updateThirdPartyRequestFlag() {
  hasThirdPartyConsentRequest.value = thirdPartyRequesters.size > 0;
}

function loadConsentOnce() {
  if (consentLoaded.value || typeof window === 'undefined') return;
  consentLoaded.value = true;

  try {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    if (!raw) return;
    const parsed = JSON.parse(raw);
    if (typeof parsed?.thirdPartyCookiesAccepted === 'boolean') {
      thirdPartyCookiesAccepted.value = parsed.thirdPartyCookiesAccepted;
    }
  } catch {
    thirdPartyCookiesAccepted.value = null;
  }
}

function persistConsent() {
  if (typeof window === 'undefined') return;
  try {
    window.localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify({
        thirdPartyCookiesAccepted: thirdPartyCookiesAccepted.value,
        updatedAt: new Date().toISOString(),
      })
    );
  } catch {
    // ignore quota/storage errors
  }
}

export function useCookieConsent() {
  loadConsentOnce();

  function registerThirdPartyRequester(requesterId) {
    if (!requesterId) return;
    thirdPartyRequesters.add(String(requesterId));
    updateThirdPartyRequestFlag();
  }

  function unregisterThirdPartyRequester(requesterId) {
    if (!requesterId) return;
    thirdPartyRequesters.delete(String(requesterId));
    updateThirdPartyRequestFlag();
  }

  function acceptThirdPartyCookies() {
    thirdPartyCookiesAccepted.value = true;
    isCookiePreferencesOpen.value = false;
    persistConsent();
  }

  function declineThirdPartyCookies() {
    thirdPartyCookiesAccepted.value = false;
    isCookiePreferencesOpen.value = false;
    persistConsent();
  }

  function resetCookieConsentChoice() {
    thirdPartyCookiesAccepted.value = null;
    isCookiePreferencesOpen.value = true;
    persistConsent();
  }

  function openCookiePreferences() {
    isCookiePreferencesOpen.value = true;
  }

  function closeCookiePreferences() {
    isCookiePreferencesOpen.value = false;
  }

  const shouldShowCookieBanner = computed(() => {
    return hasThirdPartyConsentRequest.value && thirdPartyCookiesAccepted.value === null;
  });

  const shouldRenderCookiePanel = computed(() => {
    return shouldShowCookieBanner.value || isCookiePreferencesOpen.value;
  });

  return {
    thirdPartyCookiesAccepted,
    hasThirdPartyConsentRequest,
    shouldShowCookieBanner,
    shouldRenderCookiePanel,
    isCookiePreferencesOpen,
    registerThirdPartyRequester,
    unregisterThirdPartyRequester,
    acceptThirdPartyCookies,
    declineThirdPartyCookies,
    resetCookieConsentChoice,
    openCookiePreferences,
    closeCookiePreferences,
  };
}
