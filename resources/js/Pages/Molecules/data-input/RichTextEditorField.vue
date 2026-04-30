<script setup>
/**
 * RichTextEditorField Molecule (TipTap, DaisyUI, Atomic Design)
 *
 * @description
 * Champ de saisie WYSIWYG basé sur TipTap, intégré au design system.
 * - Utilise TipTap avec toutes les extensions disponibles
 * - Fonctionne comme un Field : label, helper, validation externe
 * - v-model = contenu HTML (string)
 * 
 * **Extensions disponibles :**
 * - Formatage : Gras, Italique, Souligné, Barré, Indice, Exposant
 * - Titres : H1, H2, H3, H4
 * - Listes : Puces, Numérotées, Tâches
 * - Alignement : Gauche, Centre, Droite, Justifié
 * - Couleurs : Texte, Surlignage
 * - Éléments : Liens, Images, Tableaux, Citations, Code, Ligne horizontale
 * - Utilitaires : Annuler/Refaire, Compteur de caractères, Placeholder
 *
 * @example
 * <RichTextEditorField
 *   v-model="content"
 *   label="Contenu"
 *   helper="Texte riche de la section"
 *   :validation="{ state: 'error', message: 'Contenu requis' }"
 * />
 */
import { computed, onBeforeUnmount, onMounted, watch, ref, nextTick } from 'vue'
import { searchRichReferenceItems } from '@/Composables/richText/useRichReferenceSearch'
import { parseAtQuery } from '@/Composables/richText/parseAtQuery'
import { KREF_ENTITY_CONFIGS } from '@/Composables/richText/krefEntityRegistry'
import { sanitizeHtml } from '@/Utils/security/sanitizeHtml'
import { useEditor, EditorContent } from '@tiptap/vue-3'

import { createRichTextExtensions } from '@/Composables/richText/richTextExtensions'
import RichTextKrefInteractions from '@/Pages/Molecules/data-display/RichTextKrefInteractions.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

const props = defineProps({
  ...getInputPropsDefinition('textarea', 'field'),
  modelValue: {
    type: String,
    default: ''
  },
  // Hauteur du contenu éditable
  height: {
    type: String,
    default: 'min-h-[260px]'
  },
  // Placeholder pour l'éditeur
  placeholder: {
    type: String,
    default: 'Commencez à écrire...'
  },
  // Afficher le compteur de caractères
  showCharacterCount: {
    type: Boolean,
    default: false
  },
  // Limite de caractères (optionnel)
  maxCharacters: {
    type: Number,
    default: null
  },
  // Afficher un bouton d'enregistrement manuel dans la toolbar
  showSaveButton: {
    type: Boolean,
    default: false
  },
  // Libellé du bouton d'enregistrement manuel
  saveButtonLabel: {
    type: String,
    default: 'Enregistrer'
  },
  // Handler d'upload local (retourne { url, name, mime_type })
  uploadFileHandler: {
    type: Function,
    default: null
  },
  /** Références riches (@) : nœud TipTap + UI de saisie (sections texte). */
  enableRichReferences: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'save-request'])

// Validation externe simplifiée (comme les autres fields)
const validationState = computed(() => {
  if (!props.validation || typeof props.validation !== 'object') return null
  return props.validation.state || null
})

const validationMessage = computed(() => {
  if (!props.validation || typeof props.validation !== 'object') return ''
  return props.validation.message || ''
})

// État pour les modals (couleur, lien, image)
const showLinkModal = ref(false)
const showImageModal = ref(false)
const isUploadingFile = ref(false)
const linkUrl = ref('')
const linkText = ref('')
const imageUrl = ref('')
const imageAlt = ref('')
const textColor = ref('#000000')
const highlightColor = ref('#ffff00')
const lastEmittedHtml = ref(props.modelValue || '')
const pendingExternalHtml = ref(null)
const isFullscreen = ref(false)
const isFocusMode = ref(false)
const isPreviewSplit = ref(false)
const showTocPanel = ref(false)
const tocItems = ref([])
const showSlashMenu = ref(false)
const slashQuery = ref('')
const slashCommandRange = ref(null)
const slashActiveIndex = ref(0)
const localFileInputRef = ref(null)
const imageFileInputRef = ref(null)

/** Mentions @ (références riches) */
const showAtMenu = ref(false)
const atQuery = ref('')
const atCommandRange = ref(null)
const atActiveIndex = ref(0)
const atItems = ref([])
const atLoading = ref(false)
let atSearchTimer = null
let atAbortController = null
const atMenuTop = ref(0)
const atMenuLeft = ref(0)
const atMenuWidth = ref(320)
/** Élément du popover @ (téléporté) pour mesurer la hauteur (flip vertical). */
const atMenuElRef = ref(null)

const showRefPickerModal = ref(false)
const refPickerQuery = ref('')
const refPickerItems = ref([])
const refPickerLoading = ref(false)
let refPickerSearchTimer = null

const richEditorRootRef = ref(null)
const atParsedQuery = ref(parseAtQuery(''))

const atScopeLabel = computed(() => {
  const parsed = atParsedQuery.value
  if (!parsed?.isMatch) return 'Recherche globale'
  if (parsed.mode === 'characteristic') return 'Recherche de caractéristiques'
  if (parsed.mode === 'section') return 'Recherche de sections'
  if (parsed.mode === 'entityType') {
    const cfg = KREF_ENTITY_CONFIGS.find((item) => item.entityType === parsed.entityType)
    return cfg ? `Recherche d’entités: ${cfg.label}` : 'Recherche d’entités'
  }
  return 'Recherche globale'
})

const atHintText = computed(() => {
  const parsed = atParsedQuery.value
  if (!parsed?.isMatch) return 'Tapez @ pour commencer une référence.'
  if (!parsed.raw) return 'Exemples: @carac:force, @section:intro, @monstre:bouftou'
  if (parsed.raw.endsWith(':')) return `${atScopeLabel.value}: saisissez au moins 2 caractères`
  if (parsed.query.trim().length < 2) return 'Saisissez au moins 2 caractères.'
  return `${atScopeLabel.value}...`
})

const atInsertPresets = computed(() => [
  { key: 'all', label: 'Recherche globale', trigger: '@' },
  { key: 'carac', label: 'Caractéristiques', trigger: '@carac:' },
  { key: 'section', label: 'Sections', trigger: '@section:' },
  ...KREF_ENTITY_CONFIGS.map((cfg) => ({
    key: cfg.entityType,
    label: `Entités: ${cfg.label}`,
    trigger: `@${cfg.atPrefix}:`,
  })),
])

/**
 * Nettoie le HTML collé depuis Word/Google Docs pour limiter les styles inline parasites.
 *
 * @param {string} html
 * @returns {string}
 */
const normalizePastedHtml = (html) => {
  if (!html || typeof html !== 'string') return ''
  if (typeof DOMParser === 'undefined') return html

  const parser = new DOMParser()
  const doc = parser.parseFromString(html, 'text/html')
  const allElements = Array.from(doc.body.querySelectorAll('*'))

  allElements.forEach((el) => {
    // Préserver les références riches (payload dans `title` base64url, ou legacy data-*).
    if (
      el.tagName === 'SPAN' &&
      el.classList?.contains('kref') &&
      (el.hasAttribute('title') || el.hasAttribute('data-kref-type'))
    ) {
      return
    }
    // Nettoyage des attributs purement décoratifs
    el.removeAttribute('class')
    el.removeAttribute('id')
    el.removeAttribute('data-start')
    el.removeAttribute('data-end')
    el.removeAttribute('data-stringify-type')

    const styleAttr = el.getAttribute('style') || ''
    const normalizedStyle = styleAttr
      .replace(/mso-[^:;]+:[^;]+;?/gi, '')
      .replace(/font-family:[^;]+;?/gi, '')
      .replace(/line-height:[^;]+;?/gi, '')
      .replace(/margin[^:]*:[^;]+;?/gi, '')
      .trim()

    if (normalizedStyle) {
      el.setAttribute('style', normalizedStyle)
    } else {
      el.removeAttribute('style')
    }
  })

  // Supprime les commentaires conditionnels Office.
  if (typeof NodeFilter !== 'undefined' && typeof doc.createNodeIterator === 'function') {
    const comments = Array.from(doc.createNodeIterator(doc.body, NodeFilter.SHOW_COMMENT))
    comments.forEach((commentNode) => {
      commentNode.parentNode?.removeChild(commentNode)
    })
  }

  return doc.body.innerHTML
}

const slugifyHeading = (text) => {
  return String(text || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
}

const buildAnchoredHtmlAndToc = (html) => {
  if (!html || typeof html !== 'string' || typeof DOMParser === 'undefined') {
    return { html: html || '', toc: [] }
  }

  const parser = new DOMParser()
  const doc = parser.parseFromString(html, 'text/html')
  const headings = Array.from(doc.body.querySelectorAll('h1, h2, h3, h4, h5, h6'))
  const usedIds = new Set()
  const toc = []

  headings.forEach((heading, index) => {
    const text = String(heading.textContent || '').trim()
    const level = Number(heading.tagName?.replace('H', '') || 1)
    const base = slugifyHeading(text) || `section-${index + 1}`
    let id = base
    let n = 2
    while (usedIds.has(id)) {
      id = `${base}-${n}`
      n += 1
    }
    usedIds.add(id)
    heading.setAttribute('id', id)
    toc.push({ id, text: text || `Titre ${index + 1}`, level })
  })

  return { html: doc.body.innerHTML, toc }
}

// Éditeur TipTap avec toutes les extensions
const editor = useEditor({
  content: props.modelValue || '',
  extensions: createRichTextExtensions({
    placeholder: props.placeholder,
    maxCharacters: props.maxCharacters,
    enableReferenceInline: props.enableRichReferences,
  }),
  onUpdate: ({ editor }) => {
    const { html, toc } = buildAnchoredHtmlAndToc(editor.getHTML())
    tocItems.value = toc
    lastEmittedHtml.value = html
    emit('update:modelValue', html)
    syncSlashMenu()
    syncAtMenu()
  },
  onSelectionUpdate: () => {
    syncSlashMenu()
    syncAtMenu()
  },
  onBlur: ({ editor }) => {
    if (typeof pendingExternalHtml.value !== 'string') return
    const nextHtml = pendingExternalHtml.value
    pendingExternalHtml.value = null
    if (nextHtml !== editor.getHTML()) {
      editor.commands.setContent(nextHtml || '', false)
      lastEmittedHtml.value = nextHtml || ''
    }
  },
  editorProps: {
    transformPastedHTML(html) {
      return normalizePastedHtml(html)
    },
    handleDrop(view, event) {
      if (!canUploadLocalFile.value) return false
      const droppedFiles = Array.from(event?.dataTransfer?.files || [])
      if (!droppedFiles.length) return false

      const dropPos = view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos
      if (typeof dropPos === 'number') {
        editor.value?.chain().focus().setTextSelection(dropPos).run()
      }

      event.preventDefault()
      const target = droppedFiles.every((file) => String(file.type || '').startsWith('image/')) ? 'image' : 'file'
      void handleEditorFilesUpload(droppedFiles, target)
      return true
    },
    handlePaste(view, event) {
      if (!canUploadLocalFile.value) return false
      const pastedFiles = Array.from(event?.clipboardData?.files || [])
      if (!pastedFiles.length) return false
      event.preventDefault()
      const target = pastedFiles.every((file) => String(file.type || '').startsWith('image/')) ? 'image' : 'file'
      void handleEditorFilesUpload(pastedFiles, target)
      return true
    },
    handleKeyDown(_view, event) {
      if (props.enableRichReferences && showAtMenu.value) {
        if (event.key === 'Escape') {
          event.preventDefault()
          closeAtMenu()
          return true
        }
        if (event.key === 'ArrowDown' || event.key === 'Tab') {
          event.preventDefault()
          if (!atItems.value.length) return true
          atActiveIndex.value = (atActiveIndex.value + 1) % atItems.value.length
          return true
        }
        if (event.key === 'ArrowUp') {
          event.preventDefault()
          if (!atItems.value.length) return true
          atActiveIndex.value = (atActiveIndex.value - 1 + atItems.value.length) % atItems.value.length
          return true
        }
        if (event.key === 'Enter') {
          const selected = atItems.value[atActiveIndex.value] || atItems.value[0]
          if (!selected) return false
          event.preventDefault()
          runAtItem(selected)
          return true
        }
        return false
      }
      if (!showSlashMenu.value) return false
      if (event.key === 'Escape') {
        event.preventDefault()
        closeSlashMenu()
        return true
      }
      if (event.key === 'ArrowDown' || event.key === 'Tab') {
        event.preventDefault()
        if (!slashCommands.value.length) return true
        slashActiveIndex.value = (slashActiveIndex.value + 1) % slashCommands.value.length
        return true
      }
      if (event.key === 'ArrowUp') {
        event.preventDefault()
        if (!slashCommands.value.length) return true
        slashActiveIndex.value = (slashActiveIndex.value - 1 + slashCommands.value.length) % slashCommands.value.length
        return true
      }
      if (event.key === 'Enter') {
        const selected = slashCommands.value[slashActiveIndex.value] || slashCommands.value[0]
        if (!selected) return false
        event.preventDefault()
        runSlashCommand(selected)
        return true
      }
      return false
    }
  }
})

// Mettre à jour le contenu si modelValue change de l'extérieur
watch(
  () => props.modelValue,
  (newValue) => {
    if (!editor.value) return
    const incomingHtml = String(newValue || '')
    const tocPayload = buildAnchoredHtmlAndToc(incomingHtml)
    tocItems.value = tocPayload.toc
    // Cas nominal: retour du v-model émis par cet éditeur -> ne rien faire.
    if (incomingHtml === String(lastEmittedHtml.value || '')) return

    // Éviter les retours arrière pendant la saisie.
    if (editor.value.isFocused) {
      pendingExternalHtml.value = incomingHtml
      return
    }

    const currentHtml = editor.value.getHTML()
    if (incomingHtml !== currentHtml) {
      editor.value.commands.setContent(incomingHtml, false)
      lastEmittedHtml.value = incomingHtml
    }
  }
)

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
  window.removeEventListener('keydown', handleEscapeForFullscreen)
  window.removeEventListener('resize', handleAtMenuViewportUpdate)
  window.removeEventListener('scroll', handleAtMenuViewportUpdate, true)
  if (atAbortController) {
    atAbortController.abort()
    atAbortController = null
  }
})

const handleEscapeForFullscreen = (event) => {
  const isToggleFocusShortcut = (event.ctrlKey || event.metaKey) && event.shiftKey && event.key.toLowerCase() === 'f'

  if (isToggleFocusShortcut) {
    event.preventDefault()
    isFocusMode.value = !isFocusMode.value
    return
  }

  if (event.key === 'Escape') {
    if (isFocusMode.value) {
      isFocusMode.value = false
      return
    }
    if (isFullscreen.value) {
      isFullscreen.value = false
    }
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleEscapeForFullscreen)
  window.addEventListener('resize', handleAtMenuViewportUpdate)
  window.addEventListener('scroll', handleAtMenuViewportUpdate, true)
  tocItems.value = buildAnchoredHtmlAndToc(String(props.modelValue || '')).toc
})

watch(showAtMenu, (opened) => {
  if (!opened) return
  nextTick(() => {
    updateAtMenuPosition()
  })
})

watch([atItems, atLoading], () => {
  if (!showAtMenu.value) return
  nextTick(() => {
    requestAnimationFrame(() => updateAtMenuPosition(true))
  })
})

// Classes du container (reprend la logique des autres fields)
const containerClasses = computed(() => {
  return [
    'form-control w-full',
    props.class
  ].filter(Boolean).join(' ')
})

const labelConfig = computed(() => {
  // On utilise un label simple en haut
  return {
    top: props.label || ''
  }
})

// Compteur de caractères
const characterCount = computed(() => {
  if (!editor.value || !props.showCharacterCount) return null
  return editor.value.storage.characterCount?.characters() || 0
})

const characterCountLimit = computed(() => {
  return props.maxCharacters || null
})

const canUploadLocalFile = computed(() => typeof props.uploadFileHandler === 'function')

// Fonctions pour les modals
const insertLink = () => {
  if (!editor.value) return
  const url = linkUrl.value.trim()
  const text = linkText.value.trim()
  
  if (url) {
    // Si du texte est sélectionné, créer un lien avec ce texte
    if (editor.value.state.selection.empty) {
      if (text) {
        // Insérer le texte avec le lien
        editor.value.chain().focus().insertContent(`<a href="${url}">${text}</a>`).run()
      } else {
        // Insérer juste l'URL comme texte de lien
        editor.value.chain().focus().insertContent(`<a href="${url}">${url}</a>`).run()
      }
    } else {
      // Appliquer le lien au texte sélectionné
      editor.value.chain().focus().setLink({ href: url }).run()
    }
  }
  showLinkModal.value = false
  linkUrl.value = ''
  linkText.value = ''
}

const insertImage = () => {
  if (!editor.value) return
  const url = imageUrl.value.trim()
  
  if (url) {
    editor.value.chain().focus().setImage({ 
      src: url,
      alt: imageAlt.value || ''
    }).run()
  }
  showImageModal.value = false
  imageUrl.value = ''
  imageAlt.value = ''
}

const openLocalFilePicker = (target = 'file') => {
  if (!canUploadLocalFile.value) return
  if (target === 'image') {
    imageFileInputRef.value?.click()
    return
  }
  localFileInputRef.value?.click()
}

const escapeHtml = (unsafe) => {
  return String(unsafe || '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;')
}

const insertUploadedFileIntoEditor = async (file, preferredTarget = 'file') => {
  if (!editor.value || !file || !canUploadLocalFile.value) return

  try {
    isUploadingFile.value = true
    const uploaded = await props.uploadFileHandler(file, { target: preferredTarget })

    const url = uploaded?.url || uploaded?.file || uploaded?.original_url || ''
    if (!url) return

    const mimeType = String(uploaded?.mime_type || uploaded?.mime || file.type || '')
    const fileName = String(uploaded?.name || uploaded?.title || file.name || 'Fichier')

    const shouldInsertAsImage = preferredTarget === 'image' || mimeType.startsWith('image/')
    if (shouldInsertAsImage) {
      editor.value.chain().focus().setImage({
        src: url,
        alt: fileName,
      }).run()
      return
    }

    const safeUrl = escapeHtml(url)
    const safeLabel = escapeHtml(fileName)
    editor.value.chain().focus().insertContent(
      `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">${safeLabel}</a>`
    ).run()
  } finally {
    isUploadingFile.value = false
  }
}

const handleEditorFilesUpload = async (files, preferredTarget = 'file') => {
  if (!Array.isArray(files) || files.length === 0) return
  for (const file of files) {
    await insertUploadedFileIntoEditor(file, preferredTarget)
  }
}

const syncSlashMenu = () => {
  if (!editor.value) return
  const state = editor.value.state
  const selection = state.selection
  if (!selection?.empty) {
    showSlashMenu.value = false
    return
  }

  const parent = selection.$from.parent
  const offset = selection.$from.parentOffset
  const textBefore = parent.textBetween(0, offset, '\0', '\0')
  const match = textBefore.match(/\/([a-z0-9_-]*)$/i)
  if (!match) {
    showSlashMenu.value = false
    slashQuery.value = ''
    slashCommandRange.value = null
    return
  }

  showAtMenu.value = false
  showSlashMenu.value = true
  slashQuery.value = String(match[1] || '').toLowerCase()
  slashCommandRange.value = {
    from: selection.from - match[0].length,
    to: selection.from,
  }
}

const syncAtMenu = () => {
  if (!props.enableRichReferences || !editor.value) return
  const state = editor.value.state
  const selection = state.selection
  if (!selection?.empty) {
    showAtMenu.value = false
    return
  }

  const parent = selection.$from.parent
  const offset = selection.$from.parentOffset
  const textBefore = parent.textBetween(0, offset, '\0', '\0')
  const parsed = parseAtQuery(textBefore)
  if (!parsed.isMatch) {
    showAtMenu.value = false
    atQuery.value = ''
    atParsedQuery.value = parseAtQuery('')
    atCommandRange.value = null
    return
  }

  showSlashMenu.value = false
  showAtMenu.value = true
  atQuery.value = String(parsed.query || '')
  atParsedQuery.value = parsed
  atCommandRange.value = {
    from: selection.from - parsed.trigger.length,
    to: selection.from,
  }
  updateAtMenuPosition()
}

const closeAtMenu = () => {
  showAtMenu.value = false
  atQuery.value = ''
  atParsedQuery.value = parseAtQuery('')
  atCommandRange.value = null
  atActiveIndex.value = 0
  atItems.value = []
}

const AT_MENU_VIEWPORT_GAP = 8
const AT_MENU_MAX_VH = 0.52

/**
 * @param {boolean} [isRecalc] — second passage après layout (mesure réelle de hauteur)
 */
const updateAtMenuPosition = (isRecalc = false) => {
  if (!editor.value || !showAtMenu.value) return
  const selection = editor.value.state?.selection
  if (!selection?.empty) return
  try {
    const coords = editor.value.view.coordsAtPos(selection.from)
    const viewportWidth = window.innerWidth
    const viewportHeight = window.innerHeight
    const preferredWidth = Math.min(420, Math.max(300, Math.round(viewportWidth * 0.35)))
    const left = Math.max(8, Math.min(coords.left, viewportWidth - preferredWidth - 8))
    atMenuLeft.value = Math.round(left)
    atMenuWidth.value = preferredWidth

    const el = atMenuElRef.value
    const measured = el?.isConnected ? el.offsetHeight : 0
    const fallbackMax = Math.round(Math.min(viewportHeight * AT_MENU_MAX_VH, 400))
    const menuHeight = measured > 24 ? measured : fallbackMax

    const spaceBelow = viewportHeight - coords.bottom - AT_MENU_VIEWPORT_GAP
    const spaceAbove = coords.top - AT_MENU_VIEWPORT_GAP

    let placeAbove = false
    if (menuHeight <= spaceBelow) {
      placeAbove = false
    } else if (menuHeight <= spaceAbove) {
      placeAbove = true
    } else {
      placeAbove = spaceAbove > spaceBelow
    }

    let top = placeAbove
      ? Math.round(coords.top - menuHeight - AT_MENU_VIEWPORT_GAP)
      : Math.round(coords.bottom + AT_MENU_VIEWPORT_GAP)

    const minTop = AT_MENU_VIEWPORT_GAP
    const maxTop = viewportHeight - menuHeight - AT_MENU_VIEWPORT_GAP
    if (maxTop >= minTop) {
      top = Math.max(minTop, Math.min(top, maxTop))
    } else {
      top = minTop
    }
    atMenuTop.value = top

    if (!isRecalc && (!measured || measured <= 24)) {
      nextTick(() => {
        requestAnimationFrame(() => updateAtMenuPosition(true))
      })
    }
  } catch {
    // no-op
  }
}

const handleAtMenuViewportUpdate = () => {
  if (!showAtMenu.value) return
  updateAtMenuPosition()
}

const insertReferenceTrigger = (triggerText) => {
  if (!editor.value) return
  const trigger = String(triggerText || '@')
  editor.value.chain().focus().insertContent(trigger).run()
  syncAtMenu()
}

const insertRichReference = (item, options = {}) => {
  const replaceTriggerRange = options.replaceTriggerRange !== false
  if (!editor.value || !item) return
  if (replaceTriggerRange && atCommandRange.value) {
    editor.value.chain().focus().deleteRange(atCommandRange.value).run()
  }
  editor.value.chain().focus().insertReferenceInline({
    krefType: item.krefType,
    payload: item.krefPayload,
    label: item.label,
  }).run()
}

const runAtItem = (item) => {
  if (!editor.value || !item) return
  insertRichReference(item, { replaceTriggerRange: true })
  closeAtMenu()
}

const openReferencePicker = () => {
  if (!props.enableRichReferences || !editor.value) return
  editor.value.chain().focus().run()
  showRefPickerModal.value = true
  refPickerQuery.value = ''
  refPickerItems.value = []
}

const closeReferencePicker = () => {
  showRefPickerModal.value = false
  refPickerQuery.value = ''
  refPickerItems.value = []
}

const runRefPickerItem = (item) => {
  if (!item) return
  insertRichReference(item, { replaceTriggerRange: false })
  closeReferencePicker()
}

watch([atQuery, showAtMenu], () => {
  if (!props.enableRichReferences) return
  if (!showAtMenu.value) {
    atItems.value = []
    return
  }
  clearTimeout(atSearchTimer)
  atSearchTimer = setTimeout(async () => {
    const parsed = atParsedQuery.value
    const q = String(parsed?.query || '').trim()
    if (q.length < 2) {
      if (atAbortController) {
        atAbortController.abort()
        atAbortController = null
      }
      atLoading.value = false
      atItems.value = []
      return
    }
    if (atAbortController) {
      atAbortController.abort()
    }
    atAbortController = typeof AbortController !== 'undefined' ? new AbortController() : null
    atLoading.value = true
    try {
      atItems.value = await searchRichReferenceItems(q, {
        mode: parsed?.mode || 'all',
        entityType: parsed?.entityType || null,
        maxResults: 12,
        signal: atAbortController?.signal || null,
      })
      if (atActiveIndex.value >= atItems.value.length) {
        atActiveIndex.value = 0
      }
    } catch (error) {
      if (error?.name === 'AbortError') return
      atItems.value = []
    } finally {
      atLoading.value = false
      atAbortController = null
    }
  }, 200)
})

watch([refPickerQuery, showRefPickerModal], () => {
  if (!props.enableRichReferences) return
  if (!showRefPickerModal.value) {
    refPickerItems.value = []
    return
  }
  clearTimeout(refPickerSearchTimer)
  refPickerSearchTimer = setTimeout(async () => {
    const q = String(refPickerQuery.value || '').trim()
    if (q.length < 2) {
      refPickerItems.value = []
      return
    }
    refPickerLoading.value = true
    try {
      refPickerItems.value = await searchRichReferenceItems(q)
    } finally {
      refPickerLoading.value = false
    }
  }, 250)
})

const closeSlashMenu = () => {
  showSlashMenu.value = false
  slashQuery.value = ''
  slashCommandRange.value = null
  slashActiveIndex.value = 0
}

const slashCommands = computed(() => {
  const commands = [
    { key: 'h1', label: 'Titre 1', tokens: ['h1', 'titre1', 'title1'], run: () => editor.value?.chain().focus().toggleHeading({ level: 1 }).run() },
    { key: 'h2', label: 'Titre 2', tokens: ['h2', 'titre2', 'title2'], run: () => editor.value?.chain().focus().toggleHeading({ level: 2 }).run() },
    { key: 'h3', label: 'Titre 3', tokens: ['h3', 'titre3', 'title3'], run: () => editor.value?.chain().focus().toggleHeading({ level: 3 }).run() },
    { key: 'p', label: 'Paragraphe', tokens: ['p', 'paragraph', 'texte'], run: () => editor.value?.chain().focus().setParagraph().run() },
    { key: 'ul', label: 'Liste a puces', tokens: ['ul', 'liste', 'puces'], run: () => editor.value?.chain().focus().toggleBulletList().run() },
    { key: 'ol', label: 'Liste numerotee', tokens: ['ol', 'liste', 'numero'], run: () => editor.value?.chain().focus().toggleOrderedList().run() },
    { key: 'quote', label: 'Citation', tokens: ['quote', 'citation'], run: () => editor.value?.chain().focus().toggleBlockquote().run() },
    { key: 'info', label: 'Bloc info', tokens: ['info', 'bloc', 'note'], run: () => insertCalloutBlock('info') },
    { key: 'warning', label: 'Bloc avertissement', tokens: ['warning', 'alerte', 'bloc'], run: () => insertCalloutBlock('warning') },
    { key: 'note', label: 'Bloc note', tokens: ['note', 'bloc', 'memo'], run: () => insertCalloutBlock('note') },
    { key: 'table', label: 'Tableau', tokens: ['table', 'tableau'], run: () => insertTable() },
    { key: 'hr', label: 'Separateur', tokens: ['hr', 'separator', 'ligne'], run: () => editor.value?.chain().focus().setHorizontalRule().run() },
    { key: 'image', label: 'Uploader image', tokens: ['image', 'photo', 'upload'], run: () => openLocalFilePicker('image'), hidden: !canUploadLocalFile.value },
    { key: 'file', label: 'Uploader fichier', tokens: ['file', 'fichier', 'upload'], run: () => openLocalFilePicker('file'), hidden: !canUploadLocalFile.value },
  ]

  const query = slashQuery.value.trim()
  return commands
    .filter((c) => !c.hidden)
    .filter((c) => {
      if (!query) return true
      return c.label.toLowerCase().includes(query) || c.tokens.some((token) => token.includes(query))
    })
    .slice(0, 7)
})

const runSlashCommand = (command) => {
  if (!editor.value || !command) return
  if (slashCommandRange.value) {
    editor.value.chain().focus().deleteRange(slashCommandRange.value).run()
  }
  command.run?.()
  closeSlashMenu()
}

const insertCalloutBlock = (kind = 'info') => {
  if (!editor.value) return
  const normalizedKind = String(kind || 'info').toLowerCase()
  const label = normalizedKind === 'warning'
    ? 'Avertissement'
    : normalizedKind === 'note'
      ? 'Note'
      : 'Info'
  editor.value.chain().focus().insertContent(
    `<blockquote><p><strong>${label} :</strong> Votre contenu...</p></blockquote><p></p>`
  ).run()
}

watch(slashCommands, (commands) => {
  if (!Array.isArray(commands) || commands.length === 0) {
    slashActiveIndex.value = 0
    return
  }
  if (slashActiveIndex.value >= commands.length) {
    slashActiveIndex.value = 0
  }
})

const focusHeadingByIndex = (index) => {
  if (!editor.value || Number(index) < 0) return
  const headings = Array.from(editor.value.view.dom.querySelectorAll('h1, h2, h3, h4, h5, h6'))
  const targetHeading = headings[index]
  if (!targetHeading) {
    editor.value.commands.focus()
    return
  }
  targetHeading.scrollIntoView({ behavior: 'smooth', block: 'center' })
  editor.value.commands.focus()
}

const handleLocalFileSelected = async (event, preferredTarget = 'file') => {
  const file = event?.target?.files?.[0]
  if (!file) return
  await insertUploadedFileIntoEditor(file, preferredTarget)
  event.target.value = ''
}

const setTextColor = () => {
  if (!editor.value) return
  editor.value.chain().focus().setColor(textColor.value).run()
}

const setHighlightColor = () => {
  if (!editor.value) return
  // Si déjà surligné avec cette couleur, désactiver, sinon activer
  if (editor.value.isActive('highlight', { color: highlightColor.value })) {
    editor.value.chain().focus().unsetHighlight().run()
  } else {
    editor.value.chain().focus().toggleHighlight({ color: highlightColor.value }).run()
  }
}

const insertTable = () => {
  if (!editor.value) return
  editor.value.chain().focus().insertTable({ 
    rows: 3, 
    cols: 3, 
    withHeaderRow: true 
  }).run()
}

const addColumnBefore = () => {
  editor.value?.chain().focus().addColumnBefore().run()
}

const addColumnAfter = () => {
  editor.value?.chain().focus().addColumnAfter().run()
}

const deleteColumn = () => {
  editor.value?.chain().focus().deleteColumn().run()
}

const addRowBefore = () => {
  editor.value?.chain().focus().addRowBefore().run()
}

const addRowAfter = () => {
  editor.value?.chain().focus().addRowAfter().run()
}

const deleteRow = () => {
  editor.value?.chain().focus().deleteRow().run()
}

const deleteTable = () => {
  editor.value?.chain().focus().deleteTable().run()
}

const requestSave = () => {
  emit('save-request')
}

const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value
}

const toggleFocusMode = () => {
  isFocusMode.value = !isFocusMode.value
}

const togglePreviewSplit = () => {
  isPreviewSplit.value = !isPreviewSplit.value
}

const sanitizedPreviewHtml = computed(() => {
  if (!editor.value) return ''
  return sanitizeHtml(editor.value.getHTML() || '')
})
</script>

<template>
  <FieldTemplate
    :container-classes="containerClasses"
    :label-config="labelConfig"
    :input-attrs="{}"
    :listeners="{}"
    :input-ref="null"
    :actions-to-display="[]"
    :style-properties="{}"
    :validation-state="validationState"
    :validation-message="validationMessage"
    :helper="props.helper"
  >
    <template #core>
      <div
        class="w-full"
        :class="{
          'fixed inset-0 z-50 bg-base-100 p-4 overflow-auto': isFullscreen,
        }"
      >
        <!-- Toolbar complète -->
        <div
          class="flex flex-wrap gap-2 mb-2 items-center text-sm border-b border-base-300 pb-2"
          :class="{ 'sticky top-0 z-10 bg-base-100 pt-2': isFullscreen }"
        >
          <!-- Formatage de texte -->
          <div class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1">
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('bold') }"
              @click="editor?.chain().focus().toggleBold().run()"
              title="Gras (Ctrl/Cmd + B)"
            >
              <i class="fa-solid fa-bold" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('italic') }"
              @click="editor?.chain().focus().toggleItalic().run()"
              title="Italique (Ctrl/Cmd + I)"
            >
              <i class="fa-solid fa-italic" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('underline') }"
              @click="editor?.chain().focus().toggleUnderline().run()"
              title="Souligné (Ctrl/Cmd + U)"
            >
              <i class="fa-solid fa-underline" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('strike') }"
              @click="editor?.chain().focus().toggleStrike().run()"
              title="Barré (Ctrl/Cmd + Shift + X)"
            >
              <i class="fa-solid fa-strikethrough" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('subscript') }"
              @click="editor?.chain().focus().toggleSubscript().run()"
              title="Indice"
            >
              <i class="fa-solid fa-subscript" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('superscript') }"
              @click="editor?.chain().focus().toggleSuperscript().run()"
              title="Exposant"
            >
              <i class="fa-solid fa-superscript" />
            </button>
          </div>

          <div v-if="!isFocusMode" class="divider divider-horizontal mx-1" />

          <!-- Titres -->
          <div class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1">
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('paragraph') }"
              @click="editor?.chain().focus().setParagraph().run()"
              title="Paragraphe"
            >
              P
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('heading', { level: 1 }) }"
              @click="editor?.chain().focus().toggleHeading({ level: 1 }).run()"
              title="Titre 1"
            >
              H1
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('heading', { level: 2 }) }"
              @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
              title="Titre 2"
            >
              H2
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('heading', { level: 3 }) }"
              @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
              title="Titre 3"
            >
              H3
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('heading', { level: 4 }) }"
              @click="editor?.chain().focus().toggleHeading({ level: 4 }).run()"
              title="Titre 4"
            >
              H4
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('heading', { level: 5 }) }"
              @click="editor?.chain().focus().toggleHeading({ level: 5 }).run()"
              title="Titre 5"
            >
              H5
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('heading', { level: 6 }) }"
              @click="editor?.chain().focus().toggleHeading({ level: 6 }).run()"
              title="Titre 6"
            >
              H6
            </button>
          </div>

          <div v-if="!isFocusMode" class="divider divider-horizontal mx-1" />

          <!-- Listes -->
          <div
            v-if="!isFocusMode"
            class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1"
          >
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('bulletList') }"
              @click="editor?.chain().focus().toggleBulletList().run()"
              title="Liste à puces"
            >
              <i class="fa-solid fa-list-ul" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('orderedList') }"
              @click="editor?.chain().focus().toggleOrderedList().run()"
              title="Liste numérotée"
            >
              <i class="fa-solid fa-list-ol" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('taskList') }"
              @click="editor?.chain().focus().toggleTaskList().run()"
              title="Liste de tâches"
            >
              <i class="fa-solid fa-square-check" />
            </button>
          </div>

          <div v-if="!isFocusMode" class="divider divider-horizontal mx-1" />

          <!-- Alignement -->
          <div
            v-if="!isFocusMode"
            class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1"
          >
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive({ textAlign: 'left' }) }"
              @click="editor?.chain().focus().setTextAlign('left').run()"
              title="Aligner à gauche"
            >
              <i class="fa-solid fa-align-left" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive({ textAlign: 'center' }) }"
              @click="editor?.chain().focus().setTextAlign('center').run()"
              title="Centrer"
            >
              <i class="fa-solid fa-align-center" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive({ textAlign: 'right' }) }"
              @click="editor?.chain().focus().setTextAlign('right').run()"
              title="Aligner à droite"
            >
              <i class="fa-solid fa-align-right" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive({ textAlign: 'justify' }) }"
              @click="editor?.chain().focus().setTextAlign('justify').run()"
              title="Justifier"
            >
              <i class="fa-solid fa-align-justify" />
            </button>
          </div>

          <div v-if="!isFocusMode" class="divider divider-horizontal mx-1" />

          <!-- Couleurs et surlignage -->
          <div
            v-if="!isFocusMode"
            class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1"
          >
            <div class="dropdown dropdown-end">
              <button
                type="button"
                tabindex="0"
                class="btn btn-xs btn-ghost"
                :class="{ 'btn-active': editor?.isActive('textStyle') }"
                title="Couleur du texte"
              >
                <i class="fa-solid fa-palette" />
              </button>
              <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow border border-base-300">
                <li>
                  <input
                    type="color"
                    v-model="textColor"
                    @change="setTextColor"
                    class="w-full h-8 cursor-pointer"
                    title="Couleur du texte"
                  />
                </li>
              </ul>
            </div>
            <div class="dropdown dropdown-end">
              <button
                type="button"
                tabindex="0"
                class="btn btn-xs btn-ghost"
                :class="{ 'btn-active': editor?.isActive('highlight') }"
                title="Surlignage"
              >
                <i class="fa-solid fa-highlighter" />
              </button>
              <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow border border-base-300">
                <li>
                  <input
                    type="color"
                    v-model="highlightColor"
                    @change="setHighlightColor"
                    class="w-full h-8 cursor-pointer"
                    title="Couleur de surlignage"
                  />
                </li>
              </ul>
            </div>
          </div>

          <div v-if="!isFocusMode" class="divider divider-horizontal mx-1" />

          <!-- Éléments spéciaux -->
          <div
            v-if="!isFocusMode"
            class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1"
          >
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="showLinkModal = true"
              title="Insérer un lien"
            >
              <i class="fa-solid fa-link" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="showImageModal = true"
              title="Insérer une image"
            >
              <i class="fa-solid fa-image" />
            </button>
            <button
              v-if="canUploadLocalFile"
              type="button"
              class="btn btn-xs btn-ghost"
              :disabled="isUploadingFile"
              @click="openLocalFilePicker('image')"
              title="Uploader une image locale"
            >
              <i class="fa-solid fa-file-image" />
            </button>
            <button
              v-if="canUploadLocalFile"
              type="button"
              class="btn btn-xs btn-ghost"
              :disabled="isUploadingFile"
              @click="openLocalFilePicker('file')"
              title="Uploader un fichier local"
            >
              <i class="fa-solid fa-file-arrow-up" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="insertTable"
              title="Insérer un tableau"
            >
              <i class="fa-solid fa-table" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('blockquote') }"
              @click="editor?.chain().focus().toggleBlockquote().run()"
              title="Citation"
            >
              <i class="fa-solid fa-quote-right" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('codeBlock') }"
              @click="editor?.chain().focus().toggleCodeBlock().run()"
              title="Bloc de code"
            >
              <i class="fa-solid fa-code" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="editor?.chain().focus().setHorizontalRule().run()"
              title="Ligne horizontale"
            >
              <i class="fa-solid fa-minus" />
            </button>
            <div v-if="enableRichReferences" class="dropdown dropdown-end">
              <button
                type="button"
                tabindex="0"
                class="btn btn-xs btn-ghost"
                title="Insérer une référence (mention @)"
              >
                <i class="fa-solid fa-at" />
              </button>
              <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-56 p-2 shadow border border-base-300 max-h-72 overflow-auto">
                <li>
                  <button type="button" @click="openReferencePicker">Ouvrir le sélecteur complet</button>
                </li>
                <li class="menu-title"><span>Insérer un déclencheur</span></li>
                <li v-for="preset in atInsertPresets" :key="preset.key">
                  <button type="button" @click="insertReferenceTrigger(preset.trigger)">
                    <span class="font-mono">{{ preset.trigger }}</span>
                    <span class="opacity-70">{{ preset.label }}</span>
                  </button>
                </li>
              </ul>
            </div>
          </div>

          <div v-if="!isFocusMode" class="divider divider-horizontal mx-1" />

          <!-- Historique -->
          <div
            v-if="!isFocusMode"
            class="flex gap-1 items-center rounded-md border border-base-300/70 px-1 py-1"
          >
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="editor?.chain().focus().undo().run()"
              :disabled="!editor?.can().undo()"
              title="Annuler (Ctrl/Cmd + Z)"
            >
              <i class="fa-solid fa-rotate-left" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="editor?.chain().focus().redo().run()"
              :disabled="!editor?.can().redo()"
              title="Refaire (Ctrl/Cmd + Shift + Z)"
            >
              <i class="fa-solid fa-rotate-right" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="editor?.chain().focus().unsetAllMarks().clearNodes().run()"
              title="Nettoyer le formatage"
            >
              <i class="fa-solid fa-eraser" />
            </button>
          </div>

          <div class="ml-auto flex items-center gap-2">
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="togglePreviewSplit"
              :title="isPreviewSplit ? 'Masquer la previsualisation' : 'Activer la previsualisation split'"
            >
              <i :class="isPreviewSplit ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="showTocPanel = !showTocPanel"
              :title="showTocPanel ? 'Masquer la table des matieres' : 'Afficher la table des matieres'"
            >
              <i class="fa-solid fa-list" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="toggleFocusMode"
              :title="isFocusMode ? 'Quitter le mode focus (Esc)' : 'Mode focus rédaction (Ctrl/Cmd + Shift + F)'"
            >
              <i :class="isFocusMode ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'" />
            </button>
            <span v-if="isUploadingFile" class="text-xs text-base-content/70">Upload...</span>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="toggleFullscreen"
              :title="isFullscreen ? 'Quitter le plein écran (Esc)' : 'Plein écran (Esc pour quitter)'"
            >
              <i :class="isFullscreen ? 'fa-solid fa-compress' : 'fa-solid fa-expand'" />
            </button>
            <button
              v-if="showSaveButton"
              type="button"
              class="btn btn-xs btn-primary"
              @click="requestSave"
              title="Enregistrer les modifications"
            >
              <i class="fa-solid fa-floppy-disk" />
              {{ saveButtonLabel }}
            </button>

            <!-- Compteur de caractères -->
            <div v-if="showCharacterCount" class="text-xs text-base-content/60">
              {{ characterCount }}{{ characterCountLimit ? ` / ${characterCountLimit}` : '' }}
            </div>
          </div>
        </div>

        <div
          v-if="showSlashMenu && slashCommands.length"
          class="mb-2 rounded-box border border-base-300 bg-base-100 p-2 shadow"
        >
          <div class="mb-1 text-[11px] text-base-content/60">
            Commandes slash (`↑`/`↓`, `Tab`, `Entrée`, `Esc`)
          </div>
          <div class="flex flex-wrap gap-1">
            <button
              v-for="(command, idx) in slashCommands"
              :key="command.key"
              type="button"
              class="btn btn-xs"
              :class="idx === slashActiveIndex ? 'btn-primary' : 'btn-outline'"
              @click="runSlashCommand(command)"
            >
              /{{ command.key }} - {{ command.label }}
            </button>
          </div>
        </div>

        <details
          v-if="enableRichReferences && !isFocusMode"
          class="mb-2 rounded-box border border-base-300/70 bg-base-100/70 px-2 py-1.5 text-xs text-base-content/75"
        >
          <summary class="cursor-pointer select-none font-medium text-base-content/80">
            Aide rapide des références @
          </summary>
          <div class="mt-2 space-y-1 leading-relaxed">
            <p>
              Tape <span class="font-mono">@</span> puis ton terme pour une recherche globale.
            </p>
            <p>
              Tu peux filtrer avec
              <span class="font-mono">@carac:</span>,
              <span class="font-mono">@section:</span>
              ou <span class="font-mono">@monstre:</span> (et autres types d’entités).
            </p>
            <p>
              Le bouton <span class="font-mono">@</span> de la toolbar permet d’insérer ces préfixes automatiquement.
            </p>
          </div>
        </details>

        <Teleport to="body">
          <div
            v-if="enableRichReferences && showAtMenu"
            ref="atMenuElRef"
            class="fixed z-[1200] rounded-box border border-base-300 bg-base-100 p-2 shadow-2xl"
            :style="{
              top: `${atMenuTop}px`,
              left: `${atMenuLeft}px`,
              width: `${atMenuWidth}px`,
              maxHeight: '52vh',
            }"
          >
            <div class="mb-1 text-[11px] text-base-content/60">
              Références @ — {{ atScopeLabel }} (`↑`/`↓`, `Tab`, `Entrée`, `Esc`)
            </div>
            <div v-if="atLoading" class="text-xs text-base-content/60 py-1">Recherche…</div>
            <div v-else-if="atQuery.trim().length < 2" class="text-xs text-base-content/60 py-1">
              {{ atHintText }}
            </div>
            <div v-else-if="!atItems.length" class="text-xs text-base-content/60 py-1">Aucun résultat</div>
            <div v-else class="flex flex-col gap-0.5 max-h-56 overflow-auto">
              <button
                v-for="(item, idx) in atItems"
                :key="item.key"
                type="button"
                class="btn btn-xs btn-ghost justify-start h-auto min-h-8 py-1 text-left normal-case"
                :class="idx === atActiveIndex ? 'btn-primary text-primary-content' : ''"
                @click="runAtItem(item)"
              >
                <span class="inline-flex w-full items-start gap-2">
                  <img
                    v-if="item.iconUrl"
                    :src="item.iconUrl"
                    :alt="item.subtitle || item.label || 'Référence'"
                    class="mt-0.5 h-4 w-4 shrink-0 object-contain opacity-90"
                    loading="lazy"
                  />
                  <i v-else-if="item.icon" :class="item.icon" class="mt-0.5 shrink-0 opacity-80" />
                  <span class="min-w-0">
                    <span class="font-medium block truncate">{{ item.label }}</span>
                    <span v-if="item.subtitle" class="text-[10px] opacity-80 block truncate">{{ item.subtitle }}</span>
                  </span>
                </span>
              </button>
            </div>
          </div>
        </Teleport>

        <div
          v-if="showTocPanel && tocItems.length"
          class="mb-2 rounded-box border border-base-300 bg-base-100/80 p-2"
        >
          <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-base-content/70">Table des matieres</div>
          <div class="max-h-40 space-y-1 overflow-auto pr-1">
            <a
              v-for="(heading, idx) in tocItems"
              :key="`${heading.id}-${idx}`"
              :href="`#${heading.id}`"
              class="block text-sm text-primary hover:underline"
              :class="{
                'pl-0': heading.level === 1,
                'pl-3': heading.level === 2,
                'pl-6': heading.level === 3,
                'pl-9': heading.level >= 4,
              }"
              @click.prevent="focusHeadingByIndex(idx)"
            >
              {{ heading.text }}
            </a>
          </div>
        </div>

        <!-- Menu contextuel pour les tableaux -->
        <div
          v-if="editor?.isActive('table') && !isFocusMode"
          class="flex flex-wrap gap-1 mb-2 items-center text-sm border-b border-base-300 pb-2"
        >
          <span class="text-xs text-base-content/60 mr-2">Tableau :</span>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="addColumnBefore"
            title="Ajouter une colonne avant"
          >
            <i class="fa-solid fa-columns" /> + Avant
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="addColumnAfter"
            title="Ajouter une colonne après"
          >
            <i class="fa-solid fa-columns" /> + Après
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="deleteColumn"
            title="Supprimer la colonne"
          >
            <i class="fa-solid fa-columns" /> Suppr
          </button>
          <div class="divider divider-horizontal mx-1" />
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="addRowBefore"
            title="Ajouter une ligne avant"
          >
            <i class="fa-solid fa-grip-lines" /> + Avant
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="addRowAfter"
            title="Ajouter une ligne après"
          >
            <i class="fa-solid fa-grip-lines" /> + Après
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="deleteRow"
            title="Supprimer la ligne"
          >
            <i class="fa-solid fa-grip-lines" /> Suppr
          </button>
          <div class="divider divider-horizontal mx-1" />
          <button
            type="button"
            class="btn btn-xs btn-ghost btn-error"
            @click="deleteTable"
            title="Supprimer le tableau"
          >
            <i class="fa-solid fa-trash" /> Tableau
          </button>
          <div class="divider divider-horizontal mx-1" />
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="editor?.chain().focus().toggleHeaderRow().run()"
            title="Basculer en-tête de ligne"
          >
            Entête ligne
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="editor?.chain().focus().toggleHeaderColumn().run()"
            title="Basculer en-tête de colonne"
          >
            Entête colonne
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :disabled="!editor?.can()?.mergeCells?.()"
            @click="editor?.chain().focus().mergeCells().run()"
            title="Fusionner cellules"
          >
            Fusionner
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="editor?.chain().focus().splitCell().run()"
            title="Scinder cellule"
          >
            Scinder
          </button>
        </div>

        <!-- Zone éditable -->
        <div
          class="grid w-full gap-3"
          :class="isPreviewSplit ? 'grid-cols-1 xl:grid-cols-2' : 'grid-cols-1'"
        >
          <div
            ref="richEditorRootRef"
            class="section-rich-editor w-full rounded-box border border-base-300 bg-base-100 px-3 py-2 prose prose-sm max-w-none focus-within:border-primary transition-colors"
            :class="[
              height,
              isFullscreen ? 'min-h-[calc(100vh-8rem)]' : '',
              isFocusMode ? 'w-full' : '',
            ]"
          >
            <EditorContent :editor="editor" />
            <RichTextKrefInteractions
              v-if="enableRichReferences"
              :root-element="richEditorRootRef"
              :enabled="enableRichReferences"
            />
          </div>
          <div
            v-if="isPreviewSplit"
            class="section-rich-editor-preview w-full rounded-box border border-base-300 bg-base-100 px-3 py-2"
            :class="[
              height,
              isFullscreen ? 'min-h-[calc(100vh-8rem)]' : '',
            ]"
          >
            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-base-content/70">
              Apercu
            </div>
            <!-- eslint-disable-next-line vue/no-v-html -->
            <article class="prose prose-sm max-w-none" v-html="sanitizedPreviewHtml" />
          </div>
        </div>
      </div>

      <!-- Modal pour insérer un lien -->
      <dialog :class="{ 'modal-open': showLinkModal }" class="modal">
        <div class="modal-box">
          <h3 class="font-bold text-lg mb-4">Insérer un lien</h3>
          <div class="form-control w-full mb-4">
            <label class="label">
              <span class="label-text">URL</span>
            </label>
            <input
              type="url"
              v-model="linkUrl"
              placeholder="https://example.com"
              class="input input-bordered w-full"
              @keyup.enter="insertLink"
            />
          </div>
          <div class="form-control w-full mb-4">
            <label class="label">
              <span class="label-text">Texte du lien (optionnel)</span>
            </label>
            <input
              type="text"
              v-model="linkText"
              placeholder="Texte à afficher"
              class="input input-bordered w-full"
              @keyup.enter="insertLink"
            />
          </div>
          <div class="modal-action">
            <button class="btn btn-ghost" @click="showLinkModal = false">Annuler</button>
            <button class="btn btn-primary" @click="insertLink">Insérer</button>
          </div>
        </div>
        <form method="dialog" class="modal-backdrop" @click="showLinkModal = false">
          <button>close</button>
        </form>
      </dialog>

      <!-- Modal pour insérer une image -->
      <dialog :class="{ 'modal-open': showImageModal }" class="modal">
        <div class="modal-box">
          <h3 class="font-bold text-lg mb-4">Insérer une image</h3>
          <div class="form-control w-full mb-4">
            <label class="label">
              <span class="label-text">URL de l'image</span>
            </label>
            <input
              type="url"
              v-model="imageUrl"
              placeholder="https://example.com/image.jpg"
              class="input input-bordered w-full"
              @keyup.enter="insertImage"
            />
          </div>
          <div class="form-control w-full mb-4">
            <label class="label">
              <span class="label-text">Texte alternatif (alt)</span>
            </label>
            <input
              type="text"
              v-model="imageAlt"
              placeholder="Description de l'image"
              class="input input-bordered w-full"
              @keyup.enter="insertImage"
            />
          </div>
          <div class="modal-action">
            <button class="btn btn-ghost" @click="showImageModal = false">Annuler</button>
            <button class="btn btn-primary" @click="insertImage">Insérer</button>
          </div>
        </div>
        <form method="dialog" class="modal-backdrop" @click="showImageModal = false">
          <button>close</button>
        </form>
      </dialog>

      <dialog v-if="enableRichReferences" :class="{ 'modal-open': showRefPickerModal }" class="modal">
        <div class="modal-box max-w-lg">
          <h3 class="font-bold text-lg mb-2">Insérer une référence</h3>
          <p class="text-sm text-base-content/70 mb-3">
            Recherche (min. 2 caractères) : caractéristiques, entités, pages et sections.
          </p>
          <input
            v-model="refPickerQuery"
            type="search"
            class="input input-bordered w-full mb-2"
            placeholder="Ex. vitalité, monstre, règles…"
            autofocus
          />
          <div v-if="refPickerLoading" class="text-sm text-base-content/60 py-2">Recherche…</div>
          <div v-else-if="refPickerQuery.trim().length < 2" class="text-sm text-base-content/60 py-2">
            Saisissez au moins 2 caractères.
          </div>
          <div v-else-if="!refPickerItems.length" class="text-sm text-base-content/60 py-2">Aucun résultat</div>
          <ul v-else class="menu menu-sm rounded-box border border-base-300 bg-base-200/40 max-h-64 overflow-y-auto p-0">
            <li v-for="item in refPickerItems" :key="item.key">
              <button type="button" class="flex w-full items-start gap-2 text-left" @click="runRefPickerItem(item)">
                <img
                  v-if="item.iconUrl"
                  :src="item.iconUrl"
                  :alt="item.subtitle || item.label || 'Référence'"
                  class="mt-1 h-4 w-4 shrink-0 object-contain opacity-90"
                  loading="lazy"
                />
                <i v-else-if="item.icon" :class="item.icon" class="mt-1 shrink-0 opacity-80" />
                <span class="min-w-0">
                  <span class="font-medium block truncate">{{ item.label }}</span>
                  <span v-if="item.subtitle" class="text-xs opacity-70 block truncate">{{ item.subtitle }}</span>
                </span>
              </button>
            </li>
          </ul>
          <div class="modal-action">
            <button type="button" class="btn btn-ghost" @click="closeReferencePicker">Fermer</button>
          </div>
        </div>
        <form method="dialog" class="modal-backdrop" @click="closeReferencePicker">
          <button type="button">close</button>
        </form>
      </dialog>

      <input
        v-if="canUploadLocalFile"
        ref="imageFileInputRef"
        type="file"
        accept="image/*"
        class="hidden"
        @change="(event) => handleLocalFileSelected(event, 'image')"
      />
      <input
        v-if="canUploadLocalFile"
        ref="localFileInputRef"
        type="file"
        class="hidden"
        @change="(event) => handleLocalFileSelected(event, 'file')"
      />
    </template>
  </FieldTemplate>
</template>



