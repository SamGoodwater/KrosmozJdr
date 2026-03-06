<script setup>
/**
 * Paramètres dédiés du template gallery.
 *
 * @description
 * Permet de configurer les settings (colonnes, gap) et les données (images).
 * Utilisé dans CreateSectionModal et SectionParamsModal.
 */
import { ref, watch, computed } from "vue";
import axios from "axios";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    section: { type: Object, default: null },
    settings: {
        type: Object,
        default: () => ({ columns: 3, gap: "md" }),
    },
    data: {
        type: Object,
        default: () => ({ images: [] }),
    },
    mode: { type: String, default: "edit" },
});

const emit = defineEmits(["update:settings", "update:data"]);

const columnsOptions = [
    { value: 2, label: "2 colonnes" },
    { value: 3, label: "3 colonnes" },
    { value: 4, label: "4 colonnes" },
];

const gapOptions = [
    { value: "sm", label: "Petit" },
    { value: "md", label: "Moyen" },
    { value: "lg", label: "Grand" },
];

const localSettings = ref({
    columns: Number(props.settings?.columns) || 3,
    gap: String(props.settings?.gap || "md"),
});
const syncFromProps = ref(false);
const lastSettingsSignature = ref("");
const lastDataSignature = ref("");
const isUploading = ref(false);
const uploadError = ref("");

const localImages = ref(
    Array.isArray(props.data?.images)
        ? props.data.images.map((img) => ({
            src: String(img?.src || ""),
            alt: String(img?.alt || ""),
            caption: String(img?.caption || ""),
        }))
        : []
);

const sectionId = computed(() => {
    const raw = props.section?.id;
    const n = Number(raw);
    return Number.isFinite(n) && n > 0 ? n : null;
});

const sectionFiles = computed(() => {
    const files = Array.isArray(props.section?.files) ? props.section.files : [];
    return files.filter((file) => {
        const url = String(file?.url || file?.file || "").toLowerCase();
        return /\.(png|jpe?g|gif|webp|avif|bmp|svg)(\?|$)/.test(url);
    });
});

watch(
    () => props.settings,
    (next) => {
        syncFromProps.value = true;
        localSettings.value = {
            columns: Number(next?.columns) || 3,
            gap: String(next?.gap || "md"),
        };
        lastSettingsSignature.value = JSON.stringify({
            columns: localSettings.value.columns,
            gap: localSettings.value.gap,
        });
        syncFromProps.value = false;
    },
    { deep: true }
);

watch(
    () => props.data,
    (next) => {
        syncFromProps.value = true;
        localImages.value = Array.isArray(next?.images)
            ? next.images.map((img) => ({
                src: String(img?.src || ""),
                alt: String(img?.alt || ""),
                caption: String(img?.caption || ""),
            }))
            : [];
        lastDataSignature.value = JSON.stringify({
            images: (localImages.value || [])
                .map((img) => ({
                    src: String(img?.src || "").trim(),
                    alt: String(img?.alt || "").trim(),
                    caption: String(img?.caption || "").trim(),
                }))
                .filter((img) => img.src !== ""),
        });
        syncFromProps.value = false;
    },
    { deep: true, immediate: true }
);

const emitSettings = () => {
    const payload = {
        columns: [2, 3, 4].includes(Number(localSettings.value.columns)) ? Number(localSettings.value.columns) : 3,
        gap: ["sm", "md", "lg"].includes(String(localSettings.value.gap)) ? String(localSettings.value.gap) : "md",
    };
    const sig = JSON.stringify(payload);
    if (sig === lastSettingsSignature.value) return;
    lastSettingsSignature.value = sig;
    emit("update:settings", payload);
};

const emitData = () => {
    const images = (localImages.value || [])
        .map((img) => ({
            src: String(img?.src || "").trim(),
            alt: String(img?.alt || "").trim(),
            caption: String(img?.caption || "").trim(),
        }))
        .filter((img) => img.src !== "");
    const payload = { images };
    const sig = JSON.stringify(payload);
    if (sig === lastDataSignature.value) return;
    lastDataSignature.value = sig;
    emit("update:data", payload);
};

watch(
    () => [localSettings.value.columns, localSettings.value.gap],
    () => {
        if (syncFromProps.value) return;
        emitSettings();
    }
);

watch(
    localImages,
    () => {
        if (syncFromProps.value) return;
        emitData();
    },
    { deep: true }
);

const addImage = () => {
    localImages.value = [
        ...localImages.value,
        { src: "", alt: "", caption: "" },
    ];
};

const removeImage = (index) => {
    localImages.value = localImages.value.filter((_, i) => i !== index);
};

const uploadFiles = async (event) => {
    const files = Array.from(event?.target?.files || []);
    if (!files.length || !sectionId.value) return;

    isUploading.value = true;
    uploadError.value = "";
    try {
        const uploadedRows = [];
        for (const file of files) {
            const formData = new FormData();
            formData.append("file", file);
            formData.append("title", file.name);

            const response = await axios.post(
                route("sections.files.store", { section: sectionId.value }),
                formData,
                { headers: { "Content-Type": "multipart/form-data" } }
            );
            const uploaded = response?.data?.file || null;
            if (uploaded?.url) {
                uploadedRows.push({
                    src: String(uploaded.url),
                    alt: String(uploaded.title || file.name || ""),
                    caption: "",
                });
            }
        }
        if (uploadedRows.length) {
            localImages.value = [...localImages.value, ...uploadedRows];
        }
    } catch (error) {
        uploadError.value = error?.response?.data?.message || "Erreur lors de l'upload des images.";
    } finally {
        isUploading.value = false;
        if (event?.target) event.target.value = "";
    }
};

const addSectionFileToGallery = (file) => {
    const src = String(file?.url || file?.file || "").trim();
    if (!src) return;
    const exists = (localImages.value || []).some((img) => String(img?.src || "").trim() === src);
    if (exists) return;
    localImages.value = [
        ...localImages.value,
        {
            src,
            alt: String(file?.title || ""),
            caption: String(file?.description || ""),
        },
    ];
};
</script>

<template>
    <div class="space-y-4">
        <SelectField
            v-model="localSettings.columns"
            label="Colonnes"
            helper="Nombre de colonnes de la galerie"
            :options="columnsOptions"
            @update:model-value="emitSettings"
        />

        <SelectField
            v-model="localSettings.gap"
            label="Espacement"
            helper="Espacement entre les images"
            :options="gapOptions"
            @update:model-value="emitSettings"
        />

        <div class="space-y-3">
            <div v-if="sectionId" class="space-y-2">
                <label class="label">
                    <span class="label-text">Uploader des images</span>
                </label>
                <input
                    type="file"
                    accept="image/*"
                    multiple
                    class="file-input file-input-bordered w-full"
                    :disabled="isUploading"
                    @change="uploadFiles"
                />
                <p class="text-xs text-base-content/60">
                    Les fichiers sont stockés sur la section puis ajoutés à la galerie.
                </p>
                <p v-if="uploadError" class="text-error text-sm">{{ uploadError }}</p>
            </div>

            <div v-if="sectionFiles.length" class="space-y-2">
                <h5 class="font-semibold text-sm">Fichiers déjà uploadés</h5>
                <div class="space-y-2">
                    <div
                        v-for="file in sectionFiles"
                        :key="file.id"
                        class="rounded-lg border border-base-300 p-2 flex items-center justify-between gap-2"
                    >
                        <div class="min-w-0">
                            <p class="text-xs font-medium truncate">{{ file.title || file.url || file.file }}</p>
                            <p class="text-xs text-base-content/60 truncate">{{ file.url || file.file }}</p>
                        </div>
                        <Btn size="xs" variant="ghost" @click="addSectionFileToGallery(file)">
                            Ajouter à la galerie
                        </Btn>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <h5 class="font-semibold text-sm">Images de la galerie</h5>
                <Btn size="xs" variant="outline" @click="addImage">
                    <Icon source="fa-plus" pack="solid" class="mr-1" />
                    Ajouter une image
                </Btn>
            </div>

            <div v-if="!localImages.length" class="text-xs text-base-content/60 italic">
                Aucune image. Ajoute au moins une URL d'image.
            </div>

            <div
                v-for="(image, index) in localImages"
                :key="index"
                class="rounded-lg border border-base-300 p-3 space-y-2"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/70">Image {{ index + 1 }}</span>
                    <Btn size="xs" variant="ghost" color="error" @click="removeImage(index)">
                        Supprimer
                    </Btn>
                </div>

                <InputField
                    v-model="image.src"
                    label="URL"
                    placeholder="https://example.com/image.jpg"
                />
                <InputField
                    v-model="image.alt"
                    label="Texte alternatif"
                    placeholder="Description de l'image"
                />
                <InputField
                    v-model="image.caption"
                    label="Légende (optionnel)"
                    placeholder="Légende affichée sous l'image"
                />
            </div>
        </div>
    </div>
</template>
