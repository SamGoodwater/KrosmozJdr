<script setup>
/**
 * Document Atom (DaisyUI, Atomic Design)
 * 
 * @description
 * Atom pour afficher un aperçu de document générique.
 * Utilisé par FilePreview pour l'affichage automatique des fichiers non-média.
 * 
 * @example
 * <Document 
 *   :name="fileName"
 *   :size="fileSize"
 *   @delete="handleDelete"
 *   :canDelete="true"
 * />
 */
import { computed } from 'vue'

const props = defineProps({
  name: {
    type: String,
    default: null,
  },
  size: {
    type: Number,
    default: null,
  },
  canDelete: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['delete'])

const fileSizeFormatted = computed(() => {
  if (!props.size) return null
  if (props.size < 1024) return `${props.size} B`
  if (props.size < 1024 * 1024) return `${(props.size / 1024).toFixed(2)} KB`
  return `${(props.size / (1024 * 1024)).toFixed(2)} MB`
})

const handleDelete = () => {
  if (props.canDelete) {
    emit('delete')
  }
}
</script>

<template>
  <div class="file-preview-item relative group">
    <div class="p-4 bg-base-200 rounded-lg border border-base-300 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <i class="fa-solid fa-file text-2xl text-content-400"></i>
        <div>
          <div v-if="name" class="text-sm font-medium">{{ name }}</div>
          <div v-if="fileSizeFormatted" class="text-xs text-content-600">{{ fileSizeFormatted }}</div>
        </div>
      </div>
      <button
        v-if="canDelete"
        type="button"
        @click="handleDelete"
        class="btn btn-sm btn-error btn-circle opacity-0 group-hover:opacity-100 transition-opacity"
        aria-label="Supprimer le fichier"
      >
        <i class="fa-solid fa-times"></i>
      </button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.file-preview-item {
  animation: slideIn 0.3s ease-out;
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
</style>

