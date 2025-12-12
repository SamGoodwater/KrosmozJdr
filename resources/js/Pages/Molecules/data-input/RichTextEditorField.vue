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
import { computed, onBeforeUnmount, watch, ref } from 'vue'
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
  }
})

const emit = defineEmits(['update:modelValue'])

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
const showColorModal = ref(false)
const linkUrl = ref('')
const linkText = ref('')
const imageUrl = ref('')
const imageAlt = ref('')
const textColor = ref('#000000')
const highlightColor = ref('#ffff00')

// Éditeur TipTap avec toutes les extensions
const editor = useEditor({
  content: props.modelValue || '',
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3, 4]
      },
      // Désactiver blockquote et codeBlock du StarterKit pour les configurer séparément
      blockquote: false,
      codeBlock: false
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
    const html = editor.getHTML()
    emit('update:modelValue', html)
  }
})

// Mettre à jour le contenu si modelValue change de l'extérieur
watch(
  () => props.modelValue,
  (newValue) => {
    if (!editor.value) return
    const currentHtml = editor.value.getHTML()
    if (newValue !== currentHtml) {
      editor.value.commands.setContent(newValue || '', false)
    }
  }
)

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
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
      <div class="w-full">
        <!-- Toolbar complète -->
        <div class="flex flex-wrap gap-1 mb-2 items-center text-sm border-b border-base-300 pb-2">
          <!-- Formatage de texte -->
          <div class="flex gap-1 items-center">
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('bold') }"
              @click="editor?.chain().focus().toggleBold().run()"
              title="Gras"
            >
              <i class="fa-solid fa-bold" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('italic') }"
              @click="editor?.chain().focus().toggleItalic().run()"
              title="Italique"
            >
              <i class="fa-solid fa-italic" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('underline') }"
              @click="editor?.chain().focus().toggleUnderline().run()"
              title="Souligné"
            >
              <i class="fa-solid fa-underline" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              :class="{ 'btn-active': editor?.isActive('strike') }"
              @click="editor?.chain().focus().toggleStrike().run()"
              title="Barré"
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

          <div class="divider divider-horizontal mx-1" />

          <!-- Titres -->
          <div class="flex gap-1 items-center">
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
          </div>

          <div class="divider divider-horizontal mx-1" />

          <!-- Listes -->
          <div class="flex gap-1 items-center">
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

          <div class="divider divider-horizontal mx-1" />

          <!-- Alignement -->
          <div class="flex gap-1 items-center">
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

          <div class="divider divider-horizontal mx-1" />

          <!-- Couleurs et surlignage -->
          <div class="flex gap-1 items-center">
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
              <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow border border-base-300">
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
              <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow border border-base-300">
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

          <div class="divider divider-horizontal mx-1" />

          <!-- Éléments spéciaux -->
          <div class="flex gap-1 items-center">
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

          <div class="divider divider-horizontal mx-1" />

          <!-- Historique -->
          <div class="flex gap-1 items-center">
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="editor?.chain().focus().undo().run()"
              :disabled="!editor?.can().undo()"
              title="Annuler"
            >
              <i class="fa-solid fa-rotate-left" />
            </button>
            <button
              type="button"
              class="btn btn-xs btn-ghost"
              @click="editor?.chain().focus().redo().run()"
              :disabled="!editor?.can().redo()"
              title="Refaire"
            >
              <i class="fa-solid fa-rotate-right" />
            </button>
          </div>

          <!-- Compteur de caractères -->
          <div v-if="showCharacterCount" class="ml-auto text-xs text-base-content/60">
            {{ characterCount }}{{ characterCountLimit ? ` / ${characterCountLimit}` : '' }}
          </div>
        </div>

        <!-- Menu contextuel pour les tableaux -->
        <div v-if="editor?.isActive('table')" class="flex flex-wrap gap-1 mb-2 items-center text-sm border-b border-base-300 pb-2">
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
        </div>

        <!-- Zone éditable -->
        <div
          class="rounded-lg border border-base-300 bg-base-100 px-3 py-2 prose prose-sm prose-invert max-w-none focus-within:border-primary transition-colors"
          :class="height"
        >
          <EditorContent :editor="editor" />
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
    </template>
  </FieldTemplate>
</template>

<style scoped lang="scss">
:deep(.ProseMirror) {
  outline: none;
  min-height: 200px;

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

    td,
    th {
      min-width: 1em;
      border: 1px solid hsl(var(--bc) / 0.2);
      padding: 3px 5px;
      vertical-align: top;
      box-sizing: border-box;
      position: relative;

      > * {
        margin-bottom: 0;
      }
    }

    th {
      font-weight: bold;
      text-align: left;
      background-color: hsl(var(--b2));
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


