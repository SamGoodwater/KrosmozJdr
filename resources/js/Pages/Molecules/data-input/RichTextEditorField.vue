<script setup>
/**
 * RichTextEditorField Molecule (TipTap, DaisyUI, Atomic Design)
 *
 * @description
 * Champ de saisie WYSIWYG basé sur TipTap, intégré au design system.
 * - Utilise TipTap (StarterKit + quelques extensions de base)
 * - Fonctionne comme un Field : label, helper, validation externe
 * - v-model = contenu HTML (string)
 *
 * @example
 * <RichTextEditorField
 *   v-model="content"
 *   label="Contenu"
 *   helper="Texte riche de la section"
 *   :validation="{ state: 'error', message: 'Contenu requis' }"
 * />
 */
import { computed, onBeforeUnmount, watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import TextAlign from '@tiptap/extension-text-align'
import Highlight from '@tiptap/extension-highlight'

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

// Éditeur TipTap
const editor = useEditor({
  content: props.modelValue || '',
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3, 4]
      }
    }),
    Link.configure({
      openOnClick: false,
      linkOnPaste: true
    }),
    Image,
    TextAlign.configure({
      types: ['heading', 'paragraph']
    }),
    Highlight
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
        <!-- Toolbar simple -->
        <div class="flex flex-wrap gap-1 mb-2 items-center text-sm">
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('bold') }"
            @click="editor?.chain().focus().toggleBold().run()"
          >
            <i class="fa-solid fa-bold" />
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('italic') }"
            @click="editor?.chain().focus().toggleItalic().run()"
          >
            <i class="fa-solid fa-italic" />
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('strike') }"
            @click="editor?.chain().focus().toggleStrike().run()"
          >
            <i class="fa-solid fa-strikethrough" />
          </button>

          <div class="divider divider-horizontal mx-1" />

          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('heading', { level: 2 }) }"
            @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
          >
            H2
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('heading', { level: 3 }) }"
            @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
          >
            H3
          </button>

          <div class="divider divider-horizontal mx-1" />

          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('bulletList') }"
            @click="editor?.chain().focus().toggleBulletList().run()"
          >
            <i class="fa-solid fa-list-ul" />
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive('orderedList') }"
            @click="editor?.chain().focus().toggleOrderedList().run()"
          >
            <i class="fa-solid fa-list-ol" />
          </button>

          <div class="divider divider-horizontal mx-1" />

          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive({ textAlign: 'left' }) }"
            @click="editor?.chain().focus().setTextAlign('left').run()"
          >
            <i class="fa-solid fa-align-left" />
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive({ textAlign: 'center' }) }"
            @click="editor?.chain().focus().setTextAlign('center').run()"
          >
            <i class="fa-solid fa-align-center" />
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            :class="{ 'btn-active': editor?.isActive({ textAlign: 'right' }) }"
            @click="editor?.chain().focus().setTextAlign('right').run()"
          >
            <i class="fa-solid fa-align-right" />
          </button>

          <div class="divider divider-horizontal mx-1" />

          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="editor?.chain().focus().undo().run()"
          >
            <i class="fa-solid fa-rotate-left" />
          </button>
          <button
            type="button"
            class="btn btn-xs btn-ghost"
            @click="editor?.chain().focus().redo().run()"
          >
            <i class="fa-solid fa-rotate-right" />
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
}
</style>


