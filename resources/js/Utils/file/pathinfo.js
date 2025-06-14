/**
 * Parse un chemin de fichier et retourne ses composants
 * Similaire à PHP pathinfo()
 *
 * @param {string} path - Le chemin du fichier
 * @returns {Object} Un objet contenant dirname, basename, extension et filename
 */
export function pathinfo(path) {
    // Séparer le chemin en segments
    const segments = path.split("/");
    const basename = segments.pop();

    // Séparer le nom de fichier et l'extension
    const lastDotIndex = basename.lastIndexOf(".");
    const filename =
        lastDotIndex !== -1 ? basename.substring(0, lastDotIndex) : basename;
    const ext = lastDotIndex !== -1 ? basename.substring(lastDotIndex + 1) : "";

    return {
        dirname: segments.join("/"),
        basename,
        extension: ext,
        ext,
        filename,
    };
}
