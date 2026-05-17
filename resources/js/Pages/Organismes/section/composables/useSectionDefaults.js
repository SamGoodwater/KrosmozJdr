/**
 * Valeurs par défaut des sections (alias du registry de templates).
 *
 * @description
 * Point d’entrée stable pour les tests et le code qui ne souhaite pas dépendre
 * directement du module `templates`.
 */
import {
  getTemplateDefaults,
  getTemplateDefaultSettings,
  getTemplateDefaultData,
} from "../templates";

/**
 * @returns {{
 *   getDefaults: (templateValue: string) => { settings: Record<string, unknown>, data: Record<string, unknown> },
 *   getDefaultSettings: (templateValue: string) => Record<string, unknown>,
 *   getDefaultData: (templateValue: string) => Record<string, unknown>,
 * }}
 */
export function useSectionDefaults() {
  return {
    getDefaults: getTemplateDefaults,
    getDefaultSettings: getTemplateDefaultSettings,
    getDefaultData: getTemplateDefaultData,
  };
}

export default useSectionDefaults;
