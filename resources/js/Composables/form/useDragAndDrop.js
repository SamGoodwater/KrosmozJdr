/**
 * useDragAndDrop — Composable pour la gestion du drag & drop de fichiers
 * 
 * @description
 * Gère les événements de drag & drop pour les composants de type file.
 * Fournit un état réactif pour l'affichage d'un overlay pendant le drag.
 * 
 * @param {Function} onFilesDropped - Callback appelé quand des fichiers sont déposés
 * @param {String|Array} accept - Types MIME acceptés (optionnel)
 * @returns {Object} API du composable
 */

import { ref, computed } from 'vue'

export default function useDragAndDrop({ onFilesDropped, accept = null } = {}) {
  const isDragging = ref(false)
  const dragCounter = ref(0) // Compteur pour gérer plusieurs zones de drag

  /**
   * Vérifie si un fichier est accepté selon le type MIME
   * @param {File} file - Fichier à vérifier
   * @returns {Boolean} True si accepté
   */
  const isFileAccepted = (file) => {
    if (!accept) return true
    
    // Gérer le format HTML standard (chaîne séparée par des virgules)
    const acceptString = typeof accept === 'string' ? accept : ''
    const acceptTypes = acceptString
      .split(',')
      .map(type => type.trim())
      .filter(type => type.length > 0)
    
    if (acceptTypes.length === 0) return true
    
    return acceptTypes.some(pattern => {
      // Pattern simple (ex: "image/*")
      if (pattern.endsWith('/*')) {
        const baseType = pattern.slice(0, -2)
        return file.type.startsWith(baseType)
      }
      // Pattern exact (ex: "image/png")
      if (pattern.includes('/')) {
        return file.type === pattern
      }
      // Extension (ex: ".png" ou "png")
      const normalizedPattern = pattern.startsWith('.') ? pattern : `.${pattern}`
      return file.name.toLowerCase().endsWith(normalizedPattern.toLowerCase())
    })
  }

  /**
   * Traite les fichiers déposés
   * @param {FileList|Array<File>} files - Fichiers déposés
   */
  const handleFiles = (files) => {
    if (!files || files.length === 0) return

    const fileArray = Array.from(files)
    const acceptedFiles = fileArray.filter(file => isFileAccepted(file))

    if (acceptedFiles.length > 0 && onFilesDropped) {
      // Pour l'instant, on ne gère qu'un seul fichier
      onFilesDropped(acceptedFiles[0])
    }
  }

  /**
   * Gère l'événement dragenter
   */
  const handleDragEnter = (event) => {
    event.preventDefault()
    event.stopPropagation()
    
    // Vérifier que c'est bien un fichier qui est dragué
    const hasFiles = event.dataTransfer?.types?.includes('Files') || 
                     (event.dataTransfer?.items && event.dataTransfer.items.length > 0)
    
    if (hasFiles) {
      dragCounter.value++
      
      if (dragCounter.value === 1) {
        isDragging.value = true
      }
    }
  }

  /**
   * Gère l'événement dragover
   */
  const handleDragOver = (event) => {
    event.preventDefault()
    event.stopPropagation()
    
    // Ajouter un effet visuel selon le type de fichier
    if (event.dataTransfer) {
      event.dataTransfer.dropEffect = 'copy'
    }
  }

  /**
   * Gère l'événement dragleave
   */
  const handleDragLeave = (event) => {
    event.preventDefault()
    event.stopPropagation()
    
    dragCounter.value--
    
    if (dragCounter.value === 0) {
      isDragging.value = false
    }
  }

  /**
   * Gère l'événement drop
   */
  const handleDrop = (event) => {
    event.preventDefault()
    event.stopPropagation()
    
    isDragging.value = false
    dragCounter.value = 0

    const files = event.dataTransfer?.files
    if (files && files.length > 0) {
      handleFiles(files)
    }
  }

  /**
   * Réinitialise l'état du drag & drop
   */
  const reset = () => {
    isDragging.value = false
    dragCounter.value = 0
  }

  return {
    isDragging: computed(() => isDragging.value),
    dragHandlers: {
      onDragEnter: handleDragEnter,
      onDragOver: handleDragOver,
      onDragLeave: handleDragLeave,
      onDrop: handleDrop,
    },
    reset,
  }
}

