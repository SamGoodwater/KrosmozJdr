export function getSizeImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            resolve({ w: img.width, h: img.height, url: url });
        };
        img.onerror = (error) => {
            reject(error);
        };
        img.src = url;
    });
}

/**
 * Vérifie si une image existe à l'URL donnée
 * @param {string} url - URL de l'image
 * @returns {Promise<boolean>}
 */
export const imageExists = async (url) => {
    try {
        const response = await fetch(url, { method: "HEAD" });
        return response.status !== 404;
    } catch (error) {
        return false;
    }
};

export function getFileTypeFromUrl(url) {
    const extension = url.split(".").pop().toLowerCase();
    const imageExtensions = [
        "jpg",
        "jpeg",
        "png",
        "gif",
        "svg",
        "webp",
        "avif",
    ];
    const videoExtensions = ["mp4", "webm", "ogg"];
    const audioExtensions = ["mp3", "wav", "ogg"];

    if (imageExtensions.includes(extension)) return "image";
    if (videoExtensions.includes(extension)) return "video";
    if (audioExtensions.includes(extension)) return "audio";
    return "file";
}

/**
 * Extrait le nom du fichier d'une URL
 * @param {string} url - URL du fichier
 * @returns {string} - Nom du fichier
 */
export function getFileNameFromUrl(url) {
    return url.split("/").pop();
}

/**
 * Convertit une taille en octets en mégaoctets, arrondi à 0,1 Mo près
 * @param {number} bytes - Taille en octets
 * @returns {number} - Taille en mégaoctets arrondie à 0,1 Mo près
 */
export function formatSizeToMB(bytes) {
    const mb = bytes / (1024 * 1024);
    return Math.floor(mb * 10) / 10;
}

/**
 * Convertit un type MIME ou une extension en format lisible
 * @param {string} format - Type MIME ou extension
 * @returns {string} - Format lisible
 */
export function formatFileType(format) {
    format = format.trim();
    if (format.startsWith("image/")) return format.replace("image/", ".");
    if (format.startsWith("video/")) return format.replace("video/", ".");
    if (format.startsWith("audio/")) return format.replace("audio/", ".");
    if (format.startsWith(".")) return format;
    if (format.includes("/*")) return format.replace("/*", "s");
    return format;
}

/**
 * Valide un fichier selon les critères donnés
 * @param {File} file - Fichier à valider
 * @param {Object} options - Options de validation
 * @param {number} [options.maxSize] - Taille maximale en octets
 * @param {string} [options.accept] - Types MIME acceptés, séparés par des virgules
 * @returns {Object} - Résultat de la validation { isValid: boolean, error: string|null }
 */
export function validateFile(file, { maxSize, accept } = {}) {
    if (maxSize && file.size > maxSize) {
        return {
            isValid: false,
            error: `Le fichier ${file.name} dépasse la taille maximale autorisée`,
        };
    }

    if (accept) {
        const acceptedTypes = accept.split(",").map((type) => type.trim());
        const fileType = file.type;
        const fileExtension = `.${file.name.split(".").pop()}`;

        if (
            !acceptedTypes.some(
                (type) =>
                    type === fileType ||
                    type === fileExtension ||
                    (type.includes("/*") &&
                        fileType.startsWith(type.replace("/*", ""))),
            )
        ) {
            return {
                isValid: false,
                error: `Le format du fichier ${file.name} n'est pas accepté`,
            };
        }
    }

    return { isValid: true, error: null };
}

/**
 * Vérifie si un fichier est une image
 * @param {File} file - Fichier à vérifier
 * @returns {boolean} - True si le fichier est une image, false sinon
 */
export function isImage(file) {
    return file.type.startsWith("image/");
}

/**
 * Vérifie si un fichier est une vidéo
 * @param {File} file - Fichier à vérifier
 * @returns {boolean} - True si le fichier est une vidéo, false sinon
 */
export function isVideo(file) {
    return file.type.startsWith("video/");
}

/**
 * Vérifie si un fichier est une audio
 * @param {File} file - Fichier à vérifier
 * @returns {boolean} - True si le fichier est une audio, false sinon
 */
export function isAudio(file) {
    return file.type.startsWith("audio/");
}

/**
 * Vérifie si un fichier est un fichier
 * @param {File} file - Fichier à vérifier
 * @returns {boolean} - True si le fichier est un fichier, false sinon
 */
export function isFile(file) {
    return !isImage(file) && !isVideo(file) && !isAudio(file);
}
