<script setup>
/**
 * SectionLegalMarkdownRead Template
 *
 * @description
 * Charge un fichier markdown via URL (same-origin), le convertit en HTML
 * puis le sanitise avant affichage.
 */
import { computed, ref, watch } from 'vue';
import { marked } from 'marked';
import { sanitizeHtml } from '@/Utils/security/sanitizeHtml';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) },
});

const isLoading = ref(false);
const errorMessage = ref('');
const htmlContent = ref('');

const sourceUrl = computed(() => {
  const raw = props.data?.sourceUrl || props.settings?.sourceUrl || '';
  return String(raw || '').trim();
});

const sectionTitle = computed(() => {
  const raw = props.data?.title || props.settings?.title || props.section?.title || '';
  return String(raw || '').trim();
});

function getSafeSameOriginUrl(rawUrl) {
  if (!rawUrl || typeof window === 'undefined') return null;

  try {
    const url = new URL(rawUrl, window.location.origin);
    if (url.origin !== window.location.origin) {
      return null;
    }
    return url.toString();
  } catch {
    return null;
  }
}

async function loadMarkdown() {
  errorMessage.value = '';
  htmlContent.value = '';

  const safeUrl = getSafeSameOriginUrl(sourceUrl.value);
  if (!safeUrl) {
    errorMessage.value = 'URL du document invalide (meme origine requise).';
    return;
  }

  isLoading.value = true;
  try {
    const response = await fetch(safeUrl, { credentials: 'same-origin' });
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const markdown = await response.text();
    const rendered = marked.parse(markdown, { gfm: true, breaks: true });
    htmlContent.value = sanitizeHtml(typeof rendered === 'string' ? rendered : '');
  } catch {
    errorMessage.value = 'Impossible de charger le document markdown.';
  } finally {
    isLoading.value = false;
  }
}

watch(sourceUrl, () => {
  loadMarkdown();
}, { immediate: true });
</script>

<template>
  <div class="section-legal-markdown space-y-4">
    <h2 v-if="sectionTitle" class="text-2xl font-semibold">
      {{ sectionTitle }}
    </h2>

    <div v-if="isLoading" class="alert alert-info">
      <span class="loading loading-spinner loading-sm"></span>
      <span>Chargement du document legal...</span>
    </div>

    <div v-else-if="errorMessage" class="alert alert-warning">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </div>

    <!-- eslint-disable-next-line vue/no-v-html -- contenu markdown rendu puis sanitise -->
    <article v-else-if="htmlContent" class="prose prose-invert max-w-none legal-markdown-prose" v-html="htmlContent"></article>

    <p v-else class="text-base-content/60 italic">Aucun contenu legal a afficher.</p>
  </div>
</template>

<style scoped lang="scss">
.legal-markdown-prose {
  :deep(h1),
  :deep(h2),
  :deep(h3) {
    margin-top: 1.25rem;
    margin-bottom: 0.75rem;
  }
}
</style>
