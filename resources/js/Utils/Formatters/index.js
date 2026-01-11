/**
 * Formatters — Export centralisé de tous les formatters
 *
 * @description
 * Point d'entrée unique pour importer tous les formatters et le registre.
 * Permet l'enregistrement automatique de tous les formatters.
 */

// Base et registre
export { BaseFormatter } from './BaseFormatter.js';
export { registerFormatter, getFormatter, hasFormatter, getAllFormatters, clearRegistry } from './FormatterRegistry.js';

// Formatters Priorité 1
export { RarityFormatter } from './RarityFormatter.js';
export { LevelFormatter } from './LevelFormatter.js';
export { VisibilityFormatter } from './VisibilityFormatter.js';
export { UsableFormatter } from './UsableFormatter.js';
export { PriceFormatter } from './PriceFormatter.js';
export { DofusVersionFormatter } from './DofusVersionFormatter.js';
export { AutoUpdateFormatter } from './AutoUpdateFormatter.js';
export { DofusdbIdFormatter } from './DofusdbIdFormatter.js';

// Formatters Priorité 2
export { WeightFormatter } from './WeightFormatter.js';
export { ImageFormatter } from './ImageFormatter.js';
export { OfficialIdFormatter } from './OfficialIdFormatter.js';
export { DateFormatter } from './DateFormatter.js';
export { BooleanFormatter } from './BooleanFormatter.js';

// Formatters Priorité 3
export { HostilityFormatter } from './HostilityFormatter.js';
export { ElementFormatter } from './ElementFormatter.js';
export { CategoryFormatter } from './CategoryFormatter.js';

// Enregistrement automatique de tous les formatters
import { registerFormatter } from './FormatterRegistry.js';
import { RarityFormatter } from './RarityFormatter.js';
import { LevelFormatter } from './LevelFormatter.js';
import { VisibilityFormatter } from './VisibilityFormatter.js';
import { UsableFormatter } from './UsableFormatter.js';
import { PriceFormatter } from './PriceFormatter.js';
import { DofusVersionFormatter } from './DofusVersionFormatter.js';
import { AutoUpdateFormatter } from './AutoUpdateFormatter.js';
import { DofusdbIdFormatter } from './DofusdbIdFormatter.js';
import { WeightFormatter } from './WeightFormatter.js';
import { ImageFormatter } from './ImageFormatter.js';
import { OfficialIdFormatter } from './OfficialIdFormatter.js';
import { DateFormatter } from './DateFormatter.js';
import { HostilityFormatter } from './HostilityFormatter.js';
import { ElementFormatter } from './ElementFormatter.js';
import { CategoryFormatter } from './CategoryFormatter.js';

// Enregistrer tous les formatters Priorité 1
registerFormatter(RarityFormatter);
registerFormatter(LevelFormatter);
registerFormatter(VisibilityFormatter);
registerFormatter(UsableFormatter);
registerFormatter(PriceFormatter);
registerFormatter(DofusVersionFormatter);
registerFormatter(AutoUpdateFormatter);
registerFormatter(DofusdbIdFormatter);

// Enregistrer tous les formatters Priorité 2
registerFormatter(WeightFormatter);
registerFormatter(ImageFormatter);
registerFormatter(OfficialIdFormatter);
registerFormatter(DateFormatter);

// Enregistrer tous les formatters Priorité 3
registerFormatter(HostilityFormatter);
registerFormatter(ElementFormatter);
registerFormatter(CategoryFormatter);
