<script setup>
/**
 * FileField Molecule (DaisyUI, Atomic Design)
 * 
 * @description
 * Molecule pour upload de fichiers complet, utilisant le système unifié useInputField et useFileUpload.
 * Gère automatiquement l'affichage, la validation, les previews et le remplacement de fichiers.
 * 
 * @example
 * // Label simple
 * <FileField label="Fichier" v-model="file" accept=".pdf,.doc" />
 * 
 * // Avec fichier existant et fichier par défaut
 * <FileField 
 *   label="Avatar" 
 *   v-model="newFile"
 *   :currentPath="user.avatar"
 *   defaultPath="default_avatar_head.webp"
 *   :canDelete="true"
 *   accept="image/*"
 *   :maxSize="5242880"
 *   @delete="handleDelete"
 * />
 * 
 * // Avec template personnalisé
 * <FileField 
 *   label="Image" 
 *   v-model="image"
 *   :currentPath="existingImage"
 *   accept="image/*"
 * >
 *   <template #default="{ file, type, url, fileName, canDelete }">
 *     <CustomFileDisplay :file="file" :type="type" :url="url" />
 *   </template>
 * </FileField>
 */
import { useSlots, useAttrs, computed, ref, unref, nextTick } from 'vue'
import FileCore from '@/Pages/Atoms/data-input/FileCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import FilePreview from '@/Pages/Atoms/data-display/FilePreview.vue'
import Image from '@/Pages/Atoms/data-display/Image.vue'
import useInputField from '@/Composables/form/useInputField'
import useFileUpload from '@/Composables/form/useFileUpload'
import useDragAndDrop from '@/Composables/form/useDragAndDrop'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps({
    ...getInputPropsDefinition('file', 'field'),
    /**
     * Chemin du fichier existant à afficher (URL string)
     * Si fourni, ce fichier sera affiché en priorité
     */
    currentPath: {
        type: String,
        default: null,
    },
    /**
     * Chemin du fichier par défaut (URL string)
     * Si fourni et qu'aucun currentPath n'existe, ce fichier sera affiché
     * Les fichiers par défaut ne peuvent pas être supprimés
     */
    defaultPath: {
        type: String,
        default: null,
    },
    /**
     * Si on peut supprimer le fichier affiché (défaut: true)
     * Les fichiers par défaut ne peuvent jamais être supprimés, même si canDelete=true
     */
    canDelete: {
        type: Boolean,
        default: true,
    },
    /**
     * Taille maximale du fichier en octets
     */
    maxSize: {
        type: Number,
        default: null,
    },
    /**
     * `imageHero` : zone compacte image (aperçu, glisser-déposer, clic) sans gros file-input natif.
     * Uniquement si `accept` cible des images.
     * @type {'default'|'imageHero'}
     */
    presentation: {
        type: String,
        default: 'default',
        validator: (v) => ['default', 'imageHero'].includes(v),
    },
})

const emit = defineEmits(['update:modelValue', 'delete', 'error', 'update:currentFile'])

/** Évite v-model sur la prop `modelValue` (règle eslint vue/no-mutating-props). */
function emitModelValue(value) {
  emit('update:modelValue', value)
}
const $attrs = useAttrs()
const $slots = useSlots()

// ------------------------------------------
// 🎯 Utilisation du composable unifié useInputField
// ------------------------------------------
const {
  actionsToDisplay,
  inputRef,
  focus,
  isReadonly,
  inputAttrs,
  listeners,
  labelConfig,
  validationState,
  validationMessage,
  validate,
  setInteracted,
  resetValidation,
  enableValidation,
  disableValidation,
  styleProperties,
  containerClasses,
} = useInputField({
  modelValue: props.modelValue,
  type: 'file',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})

// ------------------------------------------
// 📁 Utilisation du composable useFileUpload
// ------------------------------------------
// Créer une computed pour modelValue pour la réactivité
const modelValueRef = computed(() => props.modelValue)

const {
  fileToDisplay,
  previewUrls,
  hasPreview,
  canDeleteFile,
  reset: resetFileUpload,
  deleteFile: deleteFileUpload,
} = useFileUpload({
  modelValue: modelValueRef,
  currentPath: computed(() => props.currentPath),
  defaultPath: computed(() => props.defaultPath),
  canDelete: props.canDelete,
  maxSize: props.maxSize,
  onError: (error) => {
    emit('error', error)
  },
  onUpdateCurrentFile: (file) => {
    emit('update:currentFile', file)
  }
})

// ------------------------------------------
// 🗑️ Gestion de la suppression
// ------------------------------------------
const handleDelete = () => {
  if (canDeleteFile.value) {
    deleteFileUpload()
    emit('delete')
  }
}

// ------------------------------------------
// 🎯 Gestion du drag & drop
// ------------------------------------------
const { isDragging, dragHandlers } = useDragAndDrop({
  onFilesDropped: (file) => {
    emit('update:modelValue', file)
    setInteracted()
  },
  accept: props.accept
})

/** Le parent choisit `imageHero` ; ne pas exiger `accept` non vide (défaut FileField = ''). */
const useImageHeroLayout = computed(() => props.presentation === 'imageHero')

const heroInputRef = ref(null)

const heroDisplayUrl = computed(() => {
  if (previewUrls.value?.length && previewUrls.value[0]?.url) {
    return previewUrls.value[0].url
  }
  if (fileToDisplay.value?.url) {
    return fileToDisplay.value.url
  }
  return ''
})

const heroDisplayName = computed(() => {
  if (previewUrls.value?.length && previewUrls.value[0]?.name) {
    return previewUrls.value[0].name
  }
  if (fileToDisplay.value?.name) {
    return fileToDisplay.value.name
  }
  return ''
})

/** URL directe (blob, http, /…) vs chemin logique pour {@link Image}. */
const heroImageUseSrc = computed(() => {
  const u = heroDisplayUrl.value || ''
  return /^(blob:|https?:|data:|\/)/i.test(u)
})

/**
 * @param {number|null|undefined} size
 */
function formatHeroFileSize(size) {
  if (size == null || size === '') return null
  const n = Number(size)
  if (!Number.isFinite(n)) return null
  if (n < 1024) return `${n} o`
  if (n < 1024 * 1024) return `${(n / 1024).toFixed(1)} Ko`
  return `${(n / (1024 * 1024)).toFixed(2)} Mo`
}

function onHeroNativeInput(e) {
  const files = e.target.files
  const next = props.multiple ? Array.from(files || []) : files?.length ? files[0] : null
  emit('update:modelValue', next)
  setInteracted()
}

const heroInputBind = computed(() => ({
  ...unref(inputAttrs),
  class: 'sr-only',
}))

function openHeroFilePicker() {
  if (unref(isReadonly)) return
  heroInputRef.value?.click()
}

function onHeroKeydown(e) {
  if (unref(isReadonly)) return
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault()
    openHeroFilePicker()
  }
}

function onHeroZoneClick() {
  openHeroFilePicker()
}

/**
 * Supprime l’image puis ouvre l’explorateur (demande utilisateur).
 */
async function onHeroDeleteThenBrowse() {
  if (canDeleteFile.value) {
    handleDelete()
    await nextTick()
  }
  openHeroFilePicker()
}

/** Texte d’aide sous le bloc : limites + texte métier. */
const effectiveHelper = computed(() => {
  if (props.presentation !== 'imageHero') {
    return props.helper
  }
  const parts = []
  const base = props.helper && String(props.helper).trim()
  if (base) {
    parts.push(base)
  }
  const acc = String(props.accept || '').trim()
  if (!acc || acc.includes('image')) {
    parts.push('Formats acceptés : images (JPEG, PNG, WebP, GIF, etc.).')
  } else {
    parts.push(`Formats acceptés : ${acc}.`)
  }
  if (props.maxSize) {
    parts.push(`Taille maximale : ${formatHeroFileSize(props.maxSize)}.`)
  }
  return parts.join(' ')
})

// ------------------------------------------
// 📤 Exposer les méthodes pour contrôle externe
// ------------------------------------------
defineExpose({
  enableValidation,
  disableValidation,
  resetValidation,
  focus,
  validate,
  handleDelete,
  resetFileUpload,
  inputRef
})
</script>

<template>
  <!-- Mode image : aperçu compact, DnD, survol = infos, icône stylo au repos -->
  <div
    v-if="useImageHeroLayout"
    class="file-field-wrapper file-field-wrapper--hero relative"
    :class="{ dragging: isDragging }"
    @dragenter="dragHandlers.onDragEnter"
    @dragover="dragHandlers.onDragOver"
    @dragleave="dragHandlers.onDragLeave"
    @drop="dragHandlers.onDrop"
  >
    <Transition name="drag-overlay">
      <div
        v-if="isDragging"
        class="drag-overlay absolute inset-0 z-100 flex items-center justify-center rounded-box"
        @dragenter.prevent
        @dragover.prevent
        @drop.prevent
      >
        <div class="drag-overlay-content rounded-box p-8 text-center">
          <div class="mb-4">
            <i class="fa-solid fa-cloud-arrow-up animate-bounce text-6xl text-primary" aria-hidden="true"></i>
          </div>
          <p class="text-lg font-semibold text-primary">Déposez l’image ici</p>
          <p class="mt-2 text-sm text-base-content/70">Relâchez pour remplacer</p>
        </div>
      </div>
    </Transition>

    <FieldTemplate
      :container-classes="containerClasses"
      :label-config="labelConfig"
      :input-attrs="inputAttrs"
      :listeners="listeners"
      :input-ref="heroInputRef"
      :actions-to-display="actionsToDisplay"
      :style-properties="styleProperties"
      :validation-state="validationState"
      :validation-message="validationMessage"
      :helper="effectiveHelper"
      input-type="textarea"
    >
      <template #core>
        <div class="flex w-full max-w-md flex-col gap-2">
          <div
            class="group/hero rounded-xl border-2 border-base-300/50 bg-base-200/15 p-3 transition-[border-color,box-shadow] duration-200 hover:border-primary/60 focus-within:border-primary/50"
            :class="{
              'pointer-events-none opacity-60': isReadonly,
              'cursor-pointer': !isReadonly,
            }"
            role="button"
            tabindex="0"
            @click="!isReadonly && onHeroZoneClick()"
            @keydown="onHeroKeydown"
          >
            <input
              ref="heroInputRef"
              type="file"
              v-bind="heroInputBind"
              @change="onHeroNativeInput"
            />

            <div
              class="relative aspect-4/3 max-h-52 w-full overflow-hidden rounded-lg bg-base-300/20"
            >
              <template v-if="heroDisplayUrl">
                <div
                  class="hero-image-frame h-full w-full [&_img]:max-h-52 [&_img]:object-contain [&_img]:transition-[filter] [&_img]:duration-200 group-hover/hero:[&_img]:grayscale"
                >
                  <Image
                    v-if="heroImageUseSrc"
                    :src="heroDisplayUrl"
                    :alt="heroDisplayName || 'Aperçu image'"
                    fit="contain"
                    rounded="none"
                    class="h-full w-full"
                  />
                  <Image
                    v-else
                    :source="heroDisplayUrl"
                    :alt="heroDisplayName || 'Aperçu image'"
                    fit="contain"
                    rounded="none"
                    class="h-full w-full"
                  />
                </div>
              </template>
              <div
                v-else
                class="flex h-full min-h-32 flex-col items-center justify-center gap-2 px-4 text-center text-base-content/45"
              >
                <i class="fa-solid fa-image text-3xl opacity-60" aria-hidden="true"></i>
                <span class="text-sm">Aucune image</span>
              </div>

              <div
                class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0 transition-colors duration-200 group-hover/hero:bg-black/50"
              >
                <p
                  class="max-w-[90%] px-2 text-center text-sm font-semibold leading-snug text-white opacity-0 drop-shadow-md transition-opacity duration-200 group-hover/hero:opacity-100"
                >
                  Cliquer ou déposer la nouvelle image
                </p>
              </div>
            </div>

            <p
              v-if="heroDisplayName"
              class="mt-2.5 truncate text-sm font-medium text-base-content"
            >
              {{ heroDisplayName }}
            </p>
            <p v-else class="mt-2.5 text-sm italic text-base-content/50">Aucun fichier sélectionné</p>

            <div
              v-if="!isReadonly && heroDisplayUrl"
              class="mt-2 flex flex-wrap items-center gap-2"
            >
              <button
                type="button"
                class="btn btn-outline btn-error btn-xs gap-1"
                title="Retire l’image actuelle puis ouvre l’explorateur pour en choisir une autre"
                @click.stop="onHeroDeleteThenBrowse"
              >
                <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                Supprimer et remplacer
              </button>
            </div>
          </div>
        </div>
      </template>
    </FieldTemplate>
  </div>

  <div
    v-else
    class="file-field-wrapper relative"
    :class="{ dragging: isDragging }"
    @dragenter="dragHandlers.onDragEnter"
    @dragover="dragHandlers.onDragOver"
    @dragleave="dragHandlers.onDragLeave"
    @drop="dragHandlers.onDrop"
  >
    <!-- Overlay de drag & drop -->
    <Transition name="drag-overlay">
      <div
        v-if="isDragging"
        class="drag-overlay absolute inset-0 z-100 flex items-center justify-center rounded-box"
        @dragenter.prevent
        @dragover.prevent
        @drop.prevent
      >
        <div class="drag-overlay-content rounded-box p-8 text-center">
          <div class="mb-4">
            <i class="fa-solid fa-cloud-arrow-up animate-bounce text-6xl text-primary" aria-hidden="true"></i>
          </div>
          <p class="text-lg font-semibold text-primary">Dépose ton fichier ici</p>
          <p class="mt-2 text-sm text-base-content/70">Relâchez pour téléverser</p>
        </div>
      </div>
    </Transition>

    <!-- Affichage du fichier existant ou par défaut (si pas de preview) -->
    <template v-if="fileToDisplay && !hasPreview">
      <div class="current-file-display mb-4" :key="`file-display-${fileToDisplay?.url || 'none'}`">
        <!-- Slot personnalisé si fourni -->
        <slot
          name="default"
          :file="fileToDisplay.file"
          :type="fileToDisplay.type"
          :url="fileToDisplay.url"
          :file-name="fileToDisplay.name"
          :size="fileToDisplay.size"
          :source="fileToDisplay.source"
          :can-delete="canDeleteFile"
        >
          <!-- Affichage automatique si pas de slot -->
          <FilePreview
            :file="fileToDisplay.file"
            :url="fileToDisplay.url"
            :type="fileToDisplay.type"
            :name="fileToDisplay.name"
            :size="fileToDisplay.size"
            :canDelete="canDeleteFile"
            @delete="handleDelete"
          />
        </slot>
      </div>
    </template>
    
    <!-- Preview des nouveaux fichiers sélectionnés -->
    <div v-if="hasPreview" class="file-preview-container mb-4 space-y-2">
      <div
        v-for="(preview, index) in previewUrls"
        :key="`preview-${index}-${preview?.url || 'no-url'}`"
      >
        <!-- Slot personnalisé si fourni -->
        <slot
          v-if="$slots.default"
          name="default"
          :file="preview.file"
          :type="preview.type"
          :url="preview.url"
          :file-name="preview.name"
          :size="preview.size"
          source="new"
          :can-delete="true"
          @delete="() => emit('update:modelValue', null)"
        />
        <!-- Affichage automatique sinon -->
        <FilePreview
          v-else
          :file="preview.file"
          :url="preview.url"
          :type="preview.type"
          :name="preview.name"
          :size="preview.size"
          :canDelete="true"
          @delete="() => emit('update:modelValue', null)"
        />
      </div>
    </div>
    
    <!-- Input file -->
    <FieldTemplate
      :container-classes="containerClasses"
      :label-config="labelConfig"
      :input-attrs="inputAttrs"
      :listeners="listeners"
      :input-ref="inputRef || null"
      :actions-to-display="actionsToDisplay"
      :style-properties="styleProperties"
      :validation-state="validationState"
      :validation-message="validationMessage"
      :helper="effectiveHelper"
    >
      <!-- Slot core spécifique pour FileCore -->
      <template #core="{ inputAttrs: coreInputAttrs, inputRef: slotInputRef }">
        <FileCore
          v-bind="coreInputAttrs"
          :model-value="props.modelValue"
          @update:model-value="emitModelValue"
          :ref="(el) => {
            if (el) {
              if (inputRef) {
                if (typeof inputRef === 'function') {
                  inputRef(el)
                } else if (inputRef.value !== undefined) {
                  inputRef.value = el
                }
              }
              if (slotInputRef && typeof slotInputRef === 'function') {
                slotInputRef(el)
              }
            }
          }"
        />
      </template>
    </FieldTemplate>
  </div>
</template>

<style scoped lang="scss">
// Styles spécifiques pour FileField
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

.file-field-wrapper {
  transition: all 0.2s ease-in-out;
  min-height: 100px;

  &--hero {
    min-height: 0;
  }

  &.dragging {
    transform: scale(1.01);
  }
  
  // Animation pour les fichiers sélectionnés
  .file-preview-container {
    animation: slideIn 0.3s ease-out;
  }
}

.drag-overlay {
  pointer-events: none;
  background: rgba(96, 165, 250, 0.15); /* primary/15 */
  backdrop-filter: blur(12px);
  border: 3px dashed rgba(96, 165, 250, 0.5); /* primary/50 */
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.3),
    0 10px 10px -5px rgba(0, 0, 0, 0.2),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.drag-overlay-content {
  background: rgba(31, 41, 55, 0.85); /* base-100/85 */
  backdrop-filter: blur(16px);
  border: 1px solid rgba(96, 165, 250, 0.3); /* primary/30 */
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.drag-overlay-enter-active,
.drag-overlay-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.drag-overlay-enter-from,
.drag-overlay-leave-to {
  opacity: 0;
  transform: scale(0.95);
  backdrop-filter: blur(0px);
}

.drag-overlay-enter-to,
.drag-overlay-leave-from {
  opacity: 1;
  transform: scale(1);
  backdrop-filter: blur(12px);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}
</style>
