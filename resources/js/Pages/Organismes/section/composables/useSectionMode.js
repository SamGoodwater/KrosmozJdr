/**
 * Composable pour gérer les modes lecture/écriture des sections
 * 
 * @description
 * Gère l'état des sections en mode édition (frontend uniquement).
 * Chaque section peut être indépendamment en mode lecture ou écriture.
 * 
 * @example
 * const { isEditing, toggleEditMode, setEditMode } = useSectionMode(sectionId);
 */
import { computed } from 'vue';

// État global des sections en mode édition (Set de section IDs)
const editingSections = new Set();

/**
 * Composable pour gérer le mode d'édition d'une section
 * 
 * @param {Number|String|ComputedRef} sectionId - ID de la section (peut être réactif)
 * @returns {Object} { isEditing, toggleEditMode, setEditMode }
 */
export function useSectionMode(sectionId) {
  // Extraire la valeur réactive si c'est un computed/ref
  const getSectionId = () => {
    if (typeof sectionId === 'object' && 'value' in sectionId) {
      return sectionId.value;
    }
    return sectionId;
  };
  
  const isEditing = computed(() => {
    const id = getSectionId();
    return id ? editingSections.has(id) : false;
  });
  
  /**
   * Bascule le mode édition (lecture ↔ écriture)
   */
  const toggleEditMode = () => {
    const id = getSectionId();
    if (!id) return;
    
    if (editingSections.has(id)) {
      editingSections.delete(id);
    } else {
      editingSections.add(id);
    }
  };
  
  /**
   * Définit explicitement le mode édition
   * 
   * @param {Boolean} value - true pour mode édition, false pour mode lecture
   */
  const setEditMode = (value) => {
    const id = getSectionId();
    if (!id) return;
    
    if (value) {
      editingSections.add(id);
    } else {
      editingSections.delete(id);
    }
  };
  
  return {
    isEditing,
    toggleEditMode,
    setEditMode,
  };
}

