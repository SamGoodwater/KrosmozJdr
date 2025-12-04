/**
 * useFileUpload — Composable pour la gestion complète des uploads de fichiers
 * 
 * Centralise toute la logique d'upload :
 * - Détection du type de fichier (image, vidéo, audio, document)
 * - Validation (taille, type MIME)
 * - Création des previews
 * - Gestion du remplacement automatique
 * - Gestion de currentPath/defaultPath/canDelete
 * 
 * @param {Object} options
 * @param {File|FileList|Array|null} options.modelValue - Nouveau fichier sélectionné
 * @param {String|null} options.currentPath - Chemin du fichier existant (optionnel)
 * @param {String|null} options.defaultPath - Chemin du fichier par défaut (optionnel, non supprimable)
 * @param {Boolean} options.canDelete - Si on peut supprimer le fichier (défaut: true)
 * @param {Number|null} options.maxSize - Taille maximale en octets
 * @param {Function} options.onError - Callback pour les erreurs
 * @param {Function} options.onUpdateCurrentFile - Callback quand un nouveau fichier remplace l'ancien
 * @returns {Object} API du composable
 */

import { ref, computed, watch, onUnmounted } from 'vue'

export default function useFileUpload({
  modelValue,
  currentPath = null,
  defaultPath = null,
  canDelete = true,
  maxSize = null,
  onError = null,
  onUpdateCurrentFile = null
}) {
  // ============================================
  // 📁 ÉTAT INTERNE
  // ============================================
  const previewUrls = ref([])
  const displayedFile = ref(null) // Fichier actuellement affiché (File object ou URL string)

  // ============================================
  // 🔧 UTILITAIRES
  // ============================================
  /**
   * Normalise un chemin de fichier pour la comparaison
   * Extrait le pathname d'une URL complète ou retourne le chemin tel quel
   * @param {String} path - Chemin à normaliser
   * @returns {String} Chemin normalisé
   */
  const normalizePath = (path) => {
    if (!path || typeof path !== 'string') return ''
    try {
      if (path.startsWith('http://') || path.startsWith('https://')) {
        const url = new URL(path)
        return url.pathname
      }
      return path
    } catch (e) {
      return path.split('/').pop() || path
    }
  }

  // ============================================
  // 🔍 DÉTECTION DU TYPE DE FICHIER
  // ============================================
  /**
   * Détermine le type de fichier à partir d'un File ou d'une URL
   * @param {File|String} file - File object ou URL string
   * @returns {String} 'image' | 'video' | 'audio' | 'file' | 'unknown'
   */
  const getFileType = (file) => {
    if (!file) return 'unknown'
    
    // Si c'est un File object, utiliser le type MIME
    if (file && typeof file === 'object' && file.type) {
      if (file.type.startsWith('image/')) return 'image'
      if (file.type.startsWith('video/')) return 'video'
      if (file.type.startsWith('audio/')) return 'audio'
      return 'file'
    }
    
    // Si c'est une URL string, utiliser l'extension
    if (typeof file === 'string') {
      const url = file.toLowerCase()
      const imageExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.bmp']
      const videoExts = ['.mp4', '.webm', '.ogg', '.avi', '.mov', '.wmv']
      const audioExts = ['.mp3', '.wav', '.ogg', '.aac', '.flac', '.m4a']
      
      if (imageExts.some(ext => url.includes(ext))) return 'image'
      if (videoExts.some(ext => url.includes(ext))) return 'video'
      if (audioExts.some(ext => url.includes(ext))) return 'audio'
      return 'file'
    }
    
    return 'unknown'
  }

  // ============================================
  // ✅ VALIDATION
  // ============================================
  /**
   * Valide un fichier (taille, type)
   * @param {File} file - File object à valider
   * @returns {Object} { valid: Boolean, error: String|null }
   */
  const validateFile = (file) => {
    if (!file || typeof file !== 'object' || !file.name) {
      return { valid: false, error: 'Le fichier sélectionné n\'est pas valide.' }
    }
    
    // Vérifier la taille
    if (maxSize && file.size > maxSize) {
      const maxSizeMB = (maxSize / 1024 / 1024).toFixed(2)
      return { valid: false, error: `Le fichier est trop volumineux. Taille maximale : ${maxSizeMB}MB` }
    }
    
    return { valid: true, error: null }
  }

  // ============================================
  // 🖼️ GESTION DES PREVIEWS
  // ============================================
  /**
   * Crée une URL d'aperçu pour un File object
   * @param {File} file - File object
   * @returns {String|null} URL d'aperçu ou null
   */
  const createPreviewUrl = (file) => {
    if (!file || typeof file !== 'object' || !file.type) return null
    const type = getFileType(file)
    if (type === 'unknown') return null
    return URL.createObjectURL(file)
  }

  /**
   * Nettoie toutes les URLs d'aperçu
   */
  const cleanupPreviewUrls = () => {
    previewUrls.value.forEach(preview => {
      if (preview.url && preview.url.startsWith('blob:')) {
        URL.revokeObjectURL(preview.url)
      }
    })
    previewUrls.value = []
  }

  /**
   * Extrait les fichiers d'un FileList, Array ou File unique
   * @param {FileList|Array|File|String} input - Input à traiter
   * @returns {Array<File>} Tableau de File objects
   */
  const extractFiles = (input) => {
    if (!input) return []
    
    // Ignorer les strings (comme "C:\fakepath\avatar.png" du navigateur)
    if (typeof input === 'string') {
      return []
    }
    
    // FileList
    if (input && typeof input === 'object' && 'length' in input && input.length > 0) {
      return Array.from(input).filter(f => f && typeof f === 'object' && f.name !== undefined)
    }
    
    // Array
    if (Array.isArray(input)) {
      return input.filter(f => f && typeof f === 'object' && f.name !== undefined)
    }
    
    // File unique
    if (input && typeof input === 'object' && 
        input.name !== undefined && 
        input.size !== undefined && 
        input.type !== undefined) {
      return [input]
    }
    
    return []
  }

  /**
   * Traite un nouveau fichier sélectionné
   * @param {FileList|Array|File} newFile - Nouveau fichier
   */
  const processNewFile = (newFile) => {
    // Nettoyer les anciennes previews AVANT d'ajouter la nouvelle
    cleanupPreviewUrls()
    
    const files = extractFiles(newFile)
    if (files.length === 0) {
      return
    }
    
    // Prendre le premier fichier (pour l'instant, on ne gère qu'un seul fichier)
    const file = files[0]
    
    // Valider
    const validation = validateFile(file)
    if (!validation.valid) {
      if (onError) onError(validation.error)
      return
    }
    
    // Créer la preview
    const previewUrl = createPreviewUrl(file)
    if (previewUrl) {
      previewUrls.value = [{
        url: previewUrl,
        file: file,
        type: getFileType(file),
        name: file.name,
        size: file.size
      }]
    }
    
    // Mettre à jour le fichier affiché
    displayedFile.value = file
  }

  // ============================================
  // 🔧 HELPERS POUR EXTRACTION DE VALEURS
  // ============================================
  /**
   * Extrait la valeur d'un computed, ref, fonction ou valeur directe
   * @param {*} value - Valeur à extraire
   * @returns {*} Valeur extraite
   */
  const extractValue = (value) => {
    if (!value) return null
    if (typeof value === 'function') return value()
    if (typeof value === 'object' && value !== null && 'value' in value) return value.value
    if (typeof value === 'string') return value
    return value
  }

  // ============================================
  // 📊 COMPUTED PROPERTIES
  // ============================================
  /**
   * Valeurs réactives de currentPath et defaultPath
   */
  const currentPathValue = computed(() => extractValue(currentPath))
  const defaultPathValue = computed(() => extractValue(defaultPath))

  /**
   * Détermine quel fichier doit être affiché
   * Priorité : nouveau fichier > currentPath > defaultPath
   */
  const fileToDisplay = computed(() => {
    // Si un nouveau fichier est sélectionné (preview), l'afficher en priorité
    if (previewUrls.value.length > 0) {
      const preview = previewUrls.value[0]
      return {
        source: 'new',
        file: preview.file,
        url: preview.url,
        type: preview.type,
        name: preview.name,
        size: preview.size
      }
    }
    
    // Récupérer les valeurs normalisées
    const current = currentPathValue.value
    const defaultVal = defaultPathValue.value
    
    // Afficher currentPath s'il existe et qu'il n'est pas le defaultPath
    if (current && typeof current === 'string' && current.trim() !== '') {
      const normalizedCurrent = normalizePath(current)
      const normalizedDefault = defaultVal ? normalizePath(defaultVal) : ''
      
      // Vérifier si currentPath correspond à defaultPath (pour éviter d'afficher le default comme current)
      const isDefault = defaultVal && (
        normalizedCurrent === normalizedDefault ||
        normalizedCurrent.endsWith('default_avatar_head.webp') ||
        current.includes('default_avatar_head.webp')
      )
      
      // Si ce n'est pas le fichier par défaut, l'afficher
      if (!isDefault) {
        return {
          source: 'current',
          file: current,
          url: current,
          type: getFileType(current),
          name: current.split('/').pop() || null,
          size: null
        }
      }
      // Si c'est le fichier par défaut, on continue pour afficher defaultPath à la place
    }
    
    // Sinon, afficher defaultPath s'il existe
    if (defaultVal && typeof defaultVal === 'string' && defaultVal.trim() !== '') {
      return {
        source: 'default',
        file: defaultVal,
        url: defaultVal,
        type: getFileType(defaultVal),
        name: defaultVal.split('/').pop() || null,
        size: null
      }
    }
    
    // Aucun fichier à afficher
    return null
  })

  /**
   * Détermine si on peut supprimer le fichier affiché
   */
  const canDeleteFile = computed(() => {
    if (!canDelete) return false
    if (!fileToDisplay.value) return false
    
    // Si c'est le fichier par défaut, on ne peut pas le supprimer
    if (fileToDisplay.value.source === 'default') return false
    
    // Si c'est currentPath, vérifier s'il correspond au defaultPath
    if (fileToDisplay.value.source === 'current') {
      const current = currentPathValue.value
      const defaultVal = defaultPathValue.value
      
      const normalizedCurrent = normalizePath(current || '')
      const normalizedDefault = normalizePath(defaultVal || '')
      
      // Si currentPath correspond à defaultPath, on ne peut pas le supprimer
      if (normalizedCurrent === normalizedDefault || 
          normalizedCurrent.endsWith('default_avatar_head.webp') ||
          (current && current.includes('default_avatar_head.webp'))) {
        return false
      }
    }
    
    // Si c'est un nouveau fichier (preview), on peut le supprimer
    if (fileToDisplay.value.source === 'new') return true
    
    // Sinon, c'est un currentPath qui n'est pas le default, on peut le supprimer
    return true
  })

  /**
   * Vérifie s'il y a un fichier à afficher
   */
  const hasFileToDisplay = computed(() => {
    return fileToDisplay.value !== null && fileToDisplay.value !== undefined
  })

  /**
   * Vérifie s'il y a une preview de nouveau fichier
   */
  const hasPreview = computed(() => {
    return previewUrls.value.length > 0
  })

  // ============================================
  // 🎯 WATCHERS
  // ============================================
  // Fonction helper pour vérifier si une valeur est un File object (sans instanceof)
  const isFileObject = (value) => {
    return value && 
           typeof value === 'object' && 
           value.name !== undefined && 
           value.size !== undefined && 
           value.type !== undefined && 
           value.lastModified !== undefined;
  };

  // Surveiller modelValue pour traiter les nouveaux fichiers
  // modelValue peut être une ref, computed, ou une valeur directe
  watch(modelValue, (newFile) => {
    // Ignorer les strings (comme "C:\fakepath\avatar.png" du navigateur)
    if (typeof newFile === 'string') {
      return
    }
    
    // Vérifier que c'est bien un File, FileList, ou Array
    const isValidFileInput = newFile && (
      isFileObject(newFile) ||
      (newFile && typeof newFile === 'object' && 'length' in newFile && newFile.length > 0 && isFileObject(newFile[0])) ||
      (Array.isArray(newFile) && newFile.length > 0 && isFileObject(newFile[0]))
    )
    
    if (!isValidFileInput && newFile !== null) {
      return
    }
    
    if (newFile) {
      processNewFile(newFile)
    } else {
      // Si modelValue est null, NE PAS nettoyer les previews automatiquement
      // Les previews doivent rester visibles jusqu'à ce que l'utilisateur enregistre
      if (previewUrls.value.length === 0) {
        displayedFile.value = null
      }
    }
  }, { immediate: true })

  // ============================================
  // 🧹 CLEANUP
  // ============================================
  onUnmounted(() => {
    cleanupPreviewUrls()
  })

  // ============================================
  // 🔧 MÉTHODES PUBLIQUES
  // ============================================
  /**
   * Réinitialise le composable (supprime les previews et displayedFile)
   */
  const reset = () => {
    displayedFile.value = null
    cleanupPreviewUrls()
  }

  /**
   * Supprime le fichier actuel (émet un événement delete)
   */
  const deleteFile = () => {
    if (canDeleteFile.value) {
      displayedFile.value = null
      cleanupPreviewUrls()
      // Le parent doit gérer la suppression via l'événement 'delete'
    }
  }

  // ============================================
  // 📤 API PUBLIQUE
  // ============================================
  return {
    // État
    fileToDisplay,
    previewUrls,
    hasFileToDisplay,
    hasPreview,
    canDeleteFile,
    
    // Méthodes
    getFileType,
    validateFile,
    reset,
    deleteFile,
    
    // Utilitaires
    extractFiles
  }
}

