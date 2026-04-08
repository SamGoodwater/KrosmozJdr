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
import { useSlots, useAttrs, computed, ref, unref } from 'vue'
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
const $attrs = useAttrs()
const $slots = useSlots()

// ------------------------------------------
// 🎯 Utilisation du composable unifié useInputField
// ------------------------------------------
const {
  // V-model et actions
  currentValue,
  actionsToDisplay,
  inputRef,
  focus,
  isModified,
  isReadonly,
  showPassword,
  
  // Attributs et événements
  inputAttrs,
  listeners,
  
  // Labels
  labelConfig,
  
  // Validation
  validationState,
  validationMessage,
  hasInteracted,
  validate,
  setInteracted,
  resetValidation,
  isValid,
  hasError,
  hasWarning,
  hasSuccess,
  
  // Méthodes de contrôle de validation
  enableValidation,
  disableValidation,

  // Style
  styleProperties,
  containerClasses,
  
  // Helpers
  handleAction,
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
  hasFileToDisplay,
  hasPreview,
  canDeleteFile,
  getFileType,
  reset: resetFileUpload,
  deleteFile: deleteFileUpload
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

const isImageAccept = computed(() => {
  const a = String(props.accept || '')
  return a.includes('image')
})

const useImageHeroLayout = computed(
  () => props.presentation === 'imageHero' && isImageAccept.value,
)

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

const heroDisplaySize = computed(() => {
  if (previewUrls.value?.length && previewUrls.value[0]?.size != null) {
    return previewUrls.value[0].size
  }
  if (fileToDisplay.value?.size != null) {
    return fileToDisplay.value.size
  }
  return null
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
        class="drag-overlay absolute inset-0 z-[100] flex items-center justify-center rounded-box"
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
      :helper="props.helper"
      input-type="textarea"
    >
      <template #core>
        <div
          class="group relative w-full max-w-md cursor-pointer overflow-hidden rounded-xl border border-base-300/75 bg-base-200/20 shadow-sm outline-none ring-primary/25 transition-[box-shadow] focus-visible:ring-2"
          :class="{ 'pointer-events-none opacity-60': isReadonly }"
          role="button"
          tabindex="0"
          @click="onHeroZoneClick"
          @keydown="onHeroKeydown"
        >
          <input
            ref="heroInputRef"
            type="file"
            v-bind="heroInputBind"
            @change="onHeroNativeInput"
          />
          <div class="relative aspect-[16/10] max-h-40 w-full bg-base-300/15">
            <Image
              v-if="heroDisplayUrl && heroImageUseSrc"
              :src="heroDisplayUrl"
              :alt="heroDisplayName || 'Aperçu image'"
              fit="contain"
              rounded="none"
              class="h-full w-full [&_img]:max-h-40 [&_img]:object-contain"
            />
            <Image
              v-else-if="heroDisplayUrl"
              :source="heroDisplayUrl"
              :alt="heroDisplayName || 'Aperçu image'"
              fit="contain"
              rounded="none"
              class="h-full w-full [&_img]:max-h-40 [&_img]:object-contain"
            />
            <div
              v-else
              class="flex h-full min-h-[6.5rem] flex-col items-center justify-center gap-1.5 px-4 text-center text-base-content/50"
            >
              <i class="fa-solid fa-cloud-arrow-up text-3xl opacity-75" aria-hidden="true"></i>
              <span class="text-sm font-medium">Glisser-déposer ou cliquer</span>
              <span class="text-xs text-base-content/40">Image (PNG, WebP, JPG…)</span>
            </div>

            <div
              class="pointer-events-none absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100"
            >
              <div class="p-3 text-xs text-white/95">
                <p v-if="heroDisplayName" class="truncate font-medium">{{ heroDisplayName }}</p>
                <p v-if="formatHeroFileSize(heroDisplaySize)" class="mt-0.5 text-white/75">
                  {{ formatHeroFileSize(heroDisplaySize) }}
                </p>
                <p class="mt-1.5 text-[11px] leading-snug text-white/65">
                  Cliquer ou déposer un fichier pour remplacer
                </p>
              </div>
            </div>

            <div class="pointer-events-none absolute bottom-2 right-2 z-10">
              <button
                type="button"
                class="btn btn-circle btn-sm pointer-events-auto border-0 bg-primary text-primary-content shadow-md hover:bg-primary/90"
                title="Choisir une image"
                @click.stop="openHeroFilePicker"
              >
                <i class="fa-solid fa-pen text-sm" aria-hidden="true"></i>
              </button>
            </div>

            <div
              v-if="canDeleteFile && heroDisplayUrl"
              class="pointer-events-none absolute left-2 top-2 z-10 opacity-0 transition-opacity group-hover:opacity-100"
            >
              <button
                type="button"
                class="btn btn-circle btn-ghost btn-sm pointer-events-auto border border-white/20 bg-black/45 text-white hover:bg-error/90"
                title="Supprimer l’image"
                @click.stop="handleDelete"
              >
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
              </button>
            </div>
          </div>
        </div>
      </template>
      <template #helper>
        <slot name="helper" />
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
        class="drag-overlay absolute inset-0 z-[100] flex items-center justify-center rounded-box"
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
      :helper="props.helper"
    >
      <!-- Slot core spécifique pour FileCore -->
      <template #core="{ inputAttrs, inputRef: slotInputRef }">
        <FileCore
          v-bind="inputAttrs"
          v-model="props.modelValue"
          @update:modelValue="(value) => emit('update:modelValue', value)"
          :ref="(el) => { 
            if (el) {
              // Mettre à jour la ref locale si elle existe
              if (inputRef) {
                if (typeof inputRef === 'function') {
                  inputRef(el);
                } else if (inputRef.value !== undefined) {
                  inputRef.value = el;
                }
              }
              // Mettre à jour la ref du slot si elle existe
              if (slotInputRef && typeof slotInputRef === 'function') {
                slotInputRef(el);
              }
            }
          }"
        />
      </template>
      
      <!-- Slots personnalisés -->
      <template #helper>
        <slot name="helper" />
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
