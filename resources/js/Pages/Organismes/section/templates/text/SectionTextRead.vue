<script setup>
/**
 * SectionTextRead Template
 * 
 * @description
 * Template de section pour afficher du texte riche en mode lecture.
 * - Affiche le contenu HTML
 * - Gère l'alignement et la taille via settings
 * 
 * @props {Object} section - Données complètes de la section
 * @props {Object} data - Données de contenu (section.data)
 * @props {Object} settings - Paramètres (section.settings)
 */
import { computed } from 'vue';
import { SectionStyleService } from '@/Utils/Services';
import { sanitizeHtml } from '@/Utils/security/sanitizeHtml';
import { htmlContainsKrefMarkers } from '@/Utils/richText/htmlContainsKrefMarkers';
import RichTextReadonlyView from '@/Pages/Molecules/data-display/RichTextReadonlyView.vue';

const props = defineProps({
  section: {
    type: Object,
    required: true
  },
  data: {
    type: Object,
    default: () => ({})
  },
  settings: {
    type: Object,
    default: () => ({})
  }
});

/**
 * Retire les 2 premiers niveaux d'une numérotation de plan.
 * Exemples:
 * - "1.1.1 Titre" => "1 Titre"
 * - "1.1.1.2 Sous-titre" => "1.2 Sous-titre"
 * - "2.4.3- Mon titre" => "3 Mon titre"
 */
const stripFirstTwoPlanLevels = (label) => {
  const text = String(label || '').trim();
  if (!text) return text;
  const match = text.match(/^(\d+(?:\.\d+)+)(?:\s*[-:]\s*|\s+)?(.*)$/);
  if (!match) return text;

  const fullNumber = String(match[1] || '');
  const rest = String(match[2] || '').trim();
  const parts = fullNumber.split('.');
  if (parts.length <= 2) return text;

  const trimmedNumber = parts.slice(2).join('.');
  return rest ? `${trimmedNumber} ${rest}` : trimmedNumber;
};

/**
 * Contenu HTML
 */
const content = computed(() => {
  const sanitized = sanitizeHtml(props.data?.content || '');
  if (!sanitized || typeof DOMParser === 'undefined') return sanitized;
  try {
    const parser = new DOMParser();
    const doc = parser.parseFromString(sanitized, 'text/html');
    const baseAnchor = String(props.section?.slug || props.section?.id || '').trim();
    const prefix = baseAnchor ? `ssec-${baseAnchor}` : `section-${String(props.section?.id || '')}`;
    const usedIds = new Set();
    const slugify = (text) =>
      String(text || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');

    Array.from(doc.body.querySelectorAll('h3, h4, h5, h6')).forEach((heading, idx) => {
      const label = String(heading.textContent || '').trim();
      const displayLabel = stripFirstTwoPlanLevels(label);
      if (displayLabel && displayLabel !== label) {
        heading.textContent = displayLabel;
      }
      const local = slugify(displayLabel || label) || `heading-${idx + 1}`;
      let id = `${prefix}-${local}`;
      let n = 2;
      while (usedIds.has(id)) {
        id = `${prefix}-${local}-${n}`;
        n += 1;
      }
      usedIds.add(id);
      heading.setAttribute('id', id);
    });

    return doc.body.innerHTML;
  } catch {
    return sanitized;
  }
});

const enableRichReferences = computed(() =>
  Boolean(
    props.settings?.enableRichReferences ||
      props.settings?.enableReferenceMapper ||
      htmlContainsKrefMarkers(content.value),
  ),
);

/**
 * Classes CSS depuis les settings (utilise le service)
 */
const containerClasses = computed(() => {
  return SectionStyleService.getContainerClasses(props.settings || {});
});
</script>

<template>
  <div class="section-text-content" :class="containerClasses">
    <template v-if="content">
      <RichTextReadonlyView
        v-if="enableRichReferences"
        :key="'rte-read-' + String(enableRichReferences)"
        class="prose prose-invert max-w-none"
        :html="content"
        :enable-rich-references="true"
      />
      <!-- eslint-disable-next-line vue/no-v-html -- contenu sanitizé (DOMPurify) via `sanitizeHtml()` -->
      <div v-else class="prose prose-invert max-w-none" v-html="content" />
    </template>
    <p v-else class="text-base-content/50 italic">
      Aucun contenu disponible.
    </p>
  </div>
</template>

<style scoped lang="scss">
.section-text-content {
  // Styles par défaut pour le texte
  :deep(p) {
    margin-bottom: 1rem;
    
    &:last-child {
      margin-bottom: 0;
    }
  }
  
  :deep(h3, h4, h5, h6) {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
  }
  
  :deep(ul, ol) {
    margin-left: 1.5rem;
    margin-bottom: 1rem;
  }
  
  :deep(li) {
    margin-bottom: 0.5rem;
  }
  
  :deep(a) {
    color: hsl(var(--p));
    text-decoration: underline;
    
    &:hover {
      color: hsl(var(--pf));
    }
  }
  
  :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
  }
}
</style>

