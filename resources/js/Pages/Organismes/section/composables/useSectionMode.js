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
  // Extraire la valeur réactive si c'est un computed/ref
  const getSectionId = () => {
    if (typeof sectionId === 'object' && 'value' in sectionId) {
      return sectionId.value;
    }
    return sectionId;
  };
  
  const isEditing = computed(() => {
    // Lire version.value pour créer une dépendance réactive
    version.value; // Force la réactivité
    const id = getSectionId();
    if (!id) return false;
    // Convertir l'ID en string pour être sûr que la clé est cohérente
    return !!editingSections[String(id)];
  });
  
  /**
   * Bascule le mode édition (lecture ↔ écriture)
   */
  const toggleEditMode = () => {
    const id = getSectionId();
    if (!id) return;
    
    const idStr = String(id);
    // Utiliser Vue.delete pour supprimer proprement une propriété réactive
    if (editingSections[idStr]) {
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
    const id = getSectionId();
    if (!id) return;
    
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

