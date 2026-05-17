/**
 * Classes CSS des sections — façade sur {@link SectionStyleService}.
 *
 * @param {import('vue').MaybeRefOrGetter<object>} settings
 */
import { computed, toValue } from "vue";
import { SectionStyleService } from "@/Utils/Services/SectionStyleService";

export function useSectionStyles(settings) {
  const resolved = () => toValue(settings) ?? {};

  return {
    alignClasses: computed(() => SectionStyleService.getAlignClasses(resolved())),
    sizeClasses: computed(() => SectionStyleService.getTextSizeClasses(resolved())),
    containerClasses: computed(() => SectionStyleService.getContainerClasses(resolved())),
    galleryClasses: computed(() => SectionStyleService.getGalleryClasses(resolved())),
  };
}

export default useSectionStyles;
