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
import { computed, onBeforeUnmount, onMounted, watch, ref } from 'vue'
import DOMPurify from 'dompurify'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import TextAlign from '@tiptap/extension-text-align'
import Highlight from '@tiptap/extension-highlight'
import Color from '@tiptap/extension-color'
import Underline from '@tiptap/extension-underline'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableCell from '@tiptap/extension-table-cell'
import TableHeader from '@tiptap/extension-table-header'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'
import Placeholder from '@tiptap/extension-placeholder'
import CharacterCount from '@tiptap/extension-character-count'
import Focus from '@tiptap/extension-focus'

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
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3, 4, 5, 6]
      }
      // horizontalRule, dropcursor et gapcursor sont inclus par défaut dans StarterKit
    }),
    // Formatage de texte
    Underline,
    Subscript,
    Superscript,
    Color,
    Highlight.configure({
      multicolor: true
    }),
    // Alignement
    TextAlign.configure({
      types: ['heading', 'paragraph'],
      defaultAlignment: 'left'
    }),
    // Liens
    Link.configure({
      openOnClick: false,
      linkOnPaste: true,
      HTMLAttributes: {
        class: 'text-primary underline'
      }
    }),
    // Images
    Image.configure({
      inline: true,
      allowBase64: true,
      HTMLAttributes: {
        class: 'max-w-full h-auto rounded'
      }
    }),
    // Tableaux
    Table.configure({
      resizable: true,
      HTMLAttributes: {
        class: 'border-collapse border border-base-300'
      }
    }),
    TableRow,
    TableHeader,
    TableCell,
    // Listes de tâches
    TaskList,
    TaskItem.configure({
      nested: true
    }),
    // Ligne horizontale, dropcursor et gapcursor sont inclus dans StarterKit
    // Placeholder
    Placeholder.configure({
      placeholder: props.placeholder
    }),
    // Compteur de caractères
    CharacterCount.configure({
      limit: props.maxCharacters || undefined
    }),
    // Focus
    Focus.configure({
      className: 'has-focus',
      mode: 'all'
    })
    // Dropcursor et Gapcursor sont inclus dans StarterKit
  ],
  onUpdate: ({ editor }) => {
    const { html, toc } = buildAnchoredHtmlAndToc(editor.getHTML())
    tocItems.value = toc
    lastEmittedHtml.value = html
    emit('update:modelValue', html)
    syncSlashMenu()
  },
  onSelectionUpdate: () => {
    syncSlashMenu()
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
  tocItems.value = buildAnchoredHtmlAndToc(String(props.modelValue || '')).toc
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

  showSlashMenu.value = true
  slashQuery.value = String(match[1] || '').toLowerCase()
  slashCommandRange.value = {
    from: selection.from - match[0].length,
    to: selection.from,
  }
}

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
  return DOMPurify.sanitize(editor.value.getHTML() || '')
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
          class="mb-2 rounded-lg border border-base-300 bg-base-100 p-2 shadow"
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

        <div
          v-if="showTocPanel && tocItems.length"
          class="mb-2 rounded-lg border border-base-300 bg-base-100/80 p-2"
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
            class="section-rich-editor w-full rounded-lg border border-base-300 bg-base-100 px-3 py-2 prose prose-sm max-w-none focus-within:border-primary transition-colors"
            :class="[
              height,
              isFullscreen ? 'min-h-[calc(100vh-8rem)]' : '',
              isFocusMode ? 'w-full' : '',
            ]"
          >
            <EditorContent :editor="editor" />
          </div>
          <div
            v-if="isPreviewSplit"
            class="section-rich-editor-preview w-full rounded-lg border border-base-300 bg-base-100 px-3 py-2"
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

<style scoped lang="scss">
:deep(.ProseMirror) {
  outline: none;
  min-height: 200px;
  width: 100%;

  h1, h2, h3, h4, h5, h6 {
    font-weight: 700;
    line-height: 1.2;
    margin: 0.75rem 0 0.4rem;
  }

  h1 { font-size: 1.6rem; }
  h2 { font-size: 1.4rem; }
  h3 { font-size: 1.25rem; }
  h4 { font-size: 1.1rem; }
  h5 { font-size: 1rem; }
  h6 { font-size: 0.95rem; }

  p {
    margin: 0.25rem 0;
  }

  ul,
  ol {
    padding-left: 1.5rem;
  }

  // Tableaux
  table {
    border-collapse: collapse;
    margin: 0.5rem 0;
    table-layout: fixed;
    width: 100%;
    border: 1px solid hsl(var(--bc) / 0.35);

    td,
    th {
      min-width: 1em;
      min-height: 2rem;
      border: 1px solid hsl(var(--bc) / 0.35);
      padding: 0.4rem 0.5rem;
      vertical-align: top;
      box-sizing: border-box;
      position: relative;
      background: hsl(var(--b1));

      > * {
        margin-bottom: 0;
      }
    }

    th {
      font-weight: bold;
      text-align: left;
      background-color: hsl(var(--b2) / 0.9);
    }

    .selectedCell:after {
      z-index: 2;
      position: absolute;
      content: "";
      left: 0; right: 0; top: 0; bottom: 0;
      background: hsl(var(--p) / 0.1);
      pointer-events: none;
    }

    .column-resize-handle {
      position: absolute;
      right: -2px;
      top: 0;
      bottom: -2px;
      width: 4px;
      background-color: hsl(var(--p));
      pointer-events: none;
    }
  }

  // Listes de tâches
  ul[data-type="taskList"] {
    list-style: none;
    padding: 0;

    li {
      display: flex;
      align-items: flex-start;

      > label {
        flex: 0 0 auto;
        margin-right: 0.5rem;
        user-select: none;
      }

      > div {
        flex: 1 1 auto;
      }
    }
  }

  // Code blocks
  pre {
    background: hsl(var(--b2));
    border-radius: 0.5rem;
    color: hsl(var(--bc));
    font-family: 'JetBrainsMono', monospace;
    padding: 0.75rem 1rem;
    margin: 0.5rem 0;

    code {
      background: none;
      color: inherit;
      font-size: 0.8rem;
      padding: 0;
    }
  }

  code {
    background-color: hsl(var(--b2));
    border-radius: 0.25rem;
    color: hsl(var(--p));
    font-size: 0.9em;
    padding: 0.2em 0.4em;
  }

  // Blockquote
  blockquote {
    border-left: 3px solid hsl(var(--p));
    padding-left: 1rem;
    margin: 0.5rem 0;
    font-style: italic;
  }

  // Horizontal rule
  hr {
    border: none;
    border-top: 2px solid hsl(var(--bc) / 0.2);
    margin: 1rem 0;
  }

  // Images
  img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
  }

  // Focus
  &.has-focus {
    outline: 2px solid hsl(var(--p));
    outline-offset: 2px;
  }
}
</style>


