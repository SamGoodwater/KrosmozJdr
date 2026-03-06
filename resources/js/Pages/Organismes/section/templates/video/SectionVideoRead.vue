<script setup>
/**
 * SectionVideoRead Template
 * 
 * @description
 * Template de section pour afficher une vidéo en mode lecture.
 */
import { computed, onUnmounted, watch } from 'vue';
import { useCookieConsent } from '@/Composables/privacy/useCookieConsent';

const props = defineProps({
  section: { type: Object, required: true },
  data: { type: Object, default: () => ({}) },
  settings: { type: Object, default: () => ({}) }
});

const videoType = computed(() => String(props.data?.type || 'youtube').toLowerCase());
const rawVideoSource = computed(() => String(props.data?.src || props.data?.url || '').trim());
const autoplay = computed(() => Boolean(props.settings?.autoplay));
const controls = computed(() => props.settings?.controls !== false);
const directVideoDisplayMode = computed(() => String(props.settings?.directVideoDisplayMode || 'preview'));
const caption = computed(() => props.data?.caption || '');
const requesterId = `video-consent-${props.section?.id || Math.random().toString(36).slice(2)}`;

const {
  thirdPartyCookiesAccepted,
  registerThirdPartyRequester,
  unregisterThirdPartyRequester,
  acceptThirdPartyCookies,
} = useCookieConsent();

const hasUnsafeProtocol = (value = '') => /^\s*(javascript|data):/i.test(String(value));

const extractYoutubeId = (value = '') => {
  const raw = String(value).trim();
  if (!raw) return '';
  const idMatch = raw.match(/^[A-Za-z0-9_-]{6,20}$/);
  if (idMatch) return raw;
  const urlMatch = raw.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,20})/i);
  return urlMatch?.[1] || '';
};

const extractVimeoId = (value = '') => {
  const raw = String(value).trim();
  if (!raw) return '';
  const idMatch = raw.match(/^[0-9]{6,12}$/);
  if (idMatch) return raw;
  const urlMatch = raw.match(/vimeo\.com\/([0-9]{6,12})/i);
  return urlMatch?.[1] || '';
};

const resolvedEmbedUrl = computed(() => {
  const src = rawVideoSource.value;
  if (!src || hasUnsafeProtocol(src)) return '';

  if (videoType.value === 'youtube') {
    const id = extractYoutubeId(src);
    if (!id) return '';
    const params = new URLSearchParams({
      autoplay: autoplay.value ? '1' : '0',
      rel: '0',
      modestbranding: '1',
      controls: controls.value ? '1' : '0',
    });
    return `https://www.youtube.com/embed/${id}?${params.toString()}`;
  }

  if (videoType.value === 'vimeo') {
    const id = extractVimeoId(src);
    if (!id) return '';
    const params = new URLSearchParams({
      autoplay: autoplay.value ? '1' : '0',
    });
    return `https://player.vimeo.com/video/${id}?${params.toString()}`;
  }

  return '';
});

const resolvedDirectUrl = computed(() => {
  if (videoType.value !== 'direct') return '';
  const src = rawVideoSource.value;
  if (!src || hasUnsafeProtocol(src)) return '';
  return src;
});

const shouldForceDirectDownload = computed(() => {
  return videoType.value === 'direct' && Boolean(resolvedDirectUrl.value) && directVideoDisplayMode.value === 'download';
});

const needsThirdPartyCookies = computed(() => {
  return Boolean(resolvedEmbedUrl.value) && (videoType.value === 'youtube' || videoType.value === 'vimeo');
});

const canRenderEmbed = computed(() => {
  return Boolean(resolvedEmbedUrl.value) && (!needsThirdPartyCookies.value || thirdPartyCookiesAccepted.value === true);
});

watch(needsThirdPartyCookies, (required) => {
  if (required) {
    registerThirdPartyRequester(requesterId);
  } else {
    unregisterThirdPartyRequester(requesterId);
  }
}, { immediate: true });

onUnmounted(() => {
  unregisterThirdPartyRequester(requesterId);
});
</script>

<template>
  <div class="section-video-content">
    <div v-if="canRenderEmbed" class="aspect-video">
      <iframe
        :src="resolvedEmbedUrl"
        class="w-full h-full rounded-lg"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      ></iframe>
    </div>
    <div
      v-else-if="resolvedEmbedUrl && needsThirdPartyCookies"
      class="text-center text-base-content/75 py-8 border border-base-300 rounded-lg space-y-3"
    >
      <div class="inline-flex items-center gap-2 badge badge-soft badge-neutral">
        <i class="fa-solid fa-cookie-bite"></i>
        <span>Contenu tiers</span>
      </div>
      <p>Cette video externe peut deposer des cookies tiers (YouTube/Vimeo).</p>
      <div class="flex justify-center">
        <button type="button" class="btn btn-sm btn-primary" @click="acceptThirdPartyCookies">
          Autoriser et lire la video
        </button>
      </div>
    </div>
    <div v-else-if="shouldForceDirectDownload" class="text-center text-base-content/70 py-8 border border-base-300 rounded-lg space-y-3">
      <div class="inline-flex items-center gap-2 badge badge-soft badge-neutral">
        <i class="fa-solid fa-file-arrow-down"></i>
        <span>Vidéo directe</span>
      </div>
      <p>Lecture intégrée désactivée pour cette vidéo.</p>
      <div class="flex justify-center">
        <a :href="resolvedDirectUrl" download>
          <button class="btn btn-sm btn-outline">Télécharger la vidéo</button>
        </a>
      </div>
    </div>
    <div v-else-if="resolvedDirectUrl" class="aspect-video">
      <video
        :src="resolvedDirectUrl"
        class="w-full h-full rounded-lg"
        :autoplay="autoplay"
        :controls="controls"
      >
        Ton navigateur ne prend pas en charge la lecture vidéo.
      </video>
    </div>
    <p v-else class="text-center text-base-content/50 italic py-8">
      Aucune vidéo
    </p>
    <p v-if="caption" class="mt-2 text-sm text-base-content/70 text-center italic">
      {{ caption }}
    </p>
  </div>
</template>

