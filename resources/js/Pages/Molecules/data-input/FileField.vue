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
 *   <template #default="{ file, type, url }">
 *     <CustomFileDisplay :file="file" :type="type" :url="url" />
 *   </template>
 * </FileField>
 */
import { useSlots, useAttrs, computed } from 'vue'
import FileCore from '@/Pages/Atoms/data-input/FileCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import FilePreview from '@/Pages/Atoms/data-display/FilePreview.vue'
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
  handleAction
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
  },
  accept: props.accept
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
  <div 
    class="file-field-wrapper relative"
    :class="{ 'dragging': isDragging }"
    @dragenter="dragHandlers.onDragEnter"
    @dragover="dragHandlers.onDragOver"
    @dragleave="dragHandlers.onDragLeave"
    @drop="dragHandlers.onDrop"
  >
    <!-- Overlay de drag & drop -->
    <Transition name="drag-overlay">
      <div 
        v-if="isDragging" 
        class="drag-overlay absolute inset-0 z-[100] flex items-center justify-center bg-primary/30 backdrop-blur-md rounded-lg border-4 border-dashed border-primary shadow-2xl"
        @dragenter.prevent
        @dragover.prevent
        @drop.prevent
      >
        <div class="text-center p-8 bg-base-100/90 rounded-xl shadow-xl">
          <div class="mb-4">
            <i class="fa-solid fa-cloud-arrow-up text-6xl text-primary animate-bounce"></i>
          </div>
          <p class="text-lg font-semibold text-primary">Déposez votre fichier ici</p>
          <p class="text-sm text-content-600 mt-2">Relâchez pour téléverser</p>
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
          :name="fileToDisplay.name"
          :size="fileToDisplay.size"
          :source="fileToDisplay.source"
          :canDelete="canDeleteFile"
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
          :name="preview.name"
          :size="preview.size"
          :source="'new'"
          :canDelete="true"
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
      <template #core="{ inputAttrs, listeners, inputRef: slotInputRef }">
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
}

.drag-overlay-enter-active,
.drag-overlay-leave-active {
  transition: all 0.3s ease;
}

.drag-overlay-enter-from,
.drag-overlay-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.drag-overlay-enter-to,
.drag-overlay-leave-from {
  opacity: 1;
  transform: scale(1);
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
