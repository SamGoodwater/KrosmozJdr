/**
 * Composable pour gérer les modes lecture/écriture des sections
 * 
 * @description
 * Gère l'état des sections en mode édition (frontend uniquement, pas de persistance).
 * Chaque section peut être indépendamment en mode lecture ou écriture.
 * 
 * **État global :**
 * - Utilise un objet réactif pour stocker les IDs des sections en mode édition
 * - Les clés sont les IDs des sections, les valeurs sont des booléens (true = en édition)
 * - Permet à plusieurs composants d'accéder au même état
 * - L'état est perdu au rechargement de la page (frontend uniquement)
 * 
 * **Fonctionnalités :**
 * - `isEditing` : Computed qui indique si la section est en mode édition
 * - `toggleEditMode()` : Bascule entre lecture et écriture
 * - `setEditMode(value)` : Définit explicitement le mode (true = édition, false = lecture)
 * 
 * @param {Number|String|ComputedRef} sectionId - ID de la section (peut être réactif)
 * @returns {Object} { isEditing, toggleEditMode, setEditMode }
 * 
 * @example
 * // Dans un composant Vue
 * const sectionId = computed(() => props.section.id);
 * const { isEditing, toggleEditMode } = useSectionMode(sectionId);
 * 
 * // Basculement au clic
 * <button @click="toggleEditMode">
 *   {{ isEditing ? 'Voir' : 'Éditer' }}
 * </button>
 */
import { computed, reactive, ref } from 'vue';

// État global des sections en mode édition (objet réactif)
// Structure : { [sectionId]: true } pour les sections en mode édition
const editingSections = reactive({});

// Compteur de version pour forcer la réactivité
const version = ref(0);

/**
 * Composable pour gérer le mode d'édition d'une section
 * 
 * @param {Number|String|ComputedRef} sectionId - ID de la section (peut être réactif)
 * @returns {Object} { isEditing, toggleEditMode, setEditMode }
 */
export function useSectionMode(sectionId) {
  // Computed qui dépend directement de sectionId pour la réactivité
  const isEditing = computed(() => {
    // Lire version.value pour créer une dépendance réactive
    version.value; // Force la réactivité
    
    // Lire directement sectionId.value si c'est un computed/ref pour créer la dépendance
    let id;
    if (sectionId && typeof sectionId === 'object' && 'value' in sectionId) {
      id = sectionId.value; // Vue track cette dépendance
    } else {
      id = sectionId;
    }
    
    if (!id) return false;
    // Convertir l'ID en string pour être sûr que la clé est cohérente
    const idStr = String(id);
    return !!editingSections[idStr];
  });
  
  /**
   * Bascule le mode édition (lecture ↔ écriture)
   */
  const toggleEditMode = () => {
    let id;
    
    // Extraire l'ID de manière plus robuste
    if (sectionId && typeof sectionId === 'object' && 'value' in sectionId) {
      id = sectionId.value;
    } else {
      id = sectionId;
    }
    
    // Vérifier que l'ID est valide (pas undefined, null, ou chaîne vide)
    // Note: 0 est un ID valide, donc on ne le rejette pas
    if (id === undefined || id === null || id === '') {
      console.warn('useSectionMode: toggleEditMode appelé sans sectionId valide', { 
        sectionId, 
        sectionIdType: typeof sectionId,
        hasValue: sectionId && 'value' in sectionId,
        extractedValue: sectionId && 'value' in sectionId ? sectionId.value : 'N/A'
      });
      return;
    }
    
    const idStr = String(id);
    const currentState = !!editingSections[idStr];
    
    // Utiliser Vue.delete pour supprimer proprement une propriété réactive
    if (currentState) {
      delete editingSections[idStr];
    } else {
      editingSections[idStr] = true;
    }
    // Incrémenter le compteur pour forcer la réactivité
    version.value++;
  };
  
  /**
   * Définit explicitement le mode édition
   * 
   * @param {Boolean} value - true pour mode édition, false pour mode lecture
   */
  const setEditMode = (value) => {
    let id;
    
    // Extraire l'ID de manière plus robuste
    if (sectionId && typeof sectionId === 'object' && 'value' in sectionId) {
      id = sectionId.value;
    } else {
      id = sectionId;
    }
    
    // Vérifier que l'ID est valide (pas undefined, null, ou chaîne vide)
    // Note: 0 est un ID valide, donc on ne le rejette pas
    if (id === undefined || id === null || id === '') {
      console.warn('useSectionMode: setEditMode appelé sans sectionId valide', { 
        sectionId, 
        value,
        sectionIdType: typeof sectionId,
        hasValue: sectionId && 'value' in sectionId,
        extractedValue: sectionId && 'value' in sectionId ? sectionId.value : 'N/A'
      });
      return;
    }
    
    const idStr = String(id);
    if (value) {
      editingSections[idStr] = true;
    } else {
      delete editingSections[idStr];
    }
    // Incrémenter le compteur pour forcer la réactivité
    version.value++;
  };
  
  return {
    isEditing,
    toggleEditMode,
    setEditMode,
  };
}

