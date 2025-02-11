
const colorNames = [
    "red", "orange", "amber", "yellow", "lime", "green", "emerald", "teal", "cyan",
    "sky", "blue", "indigo", "violet", "purple", "fuchsia", "pink", "rose", "slate",
    "gray", "zinc", "neutral", "stone", "black", "white"
];

// adjustment: number of intensity levels to adjust (1 to 9)
// direction: "auto" (default), "decrease" or "augmentation"
export function adjustIntensityColor(
    color,
    adjustment = 2,
    direction = "auto"
) {
    const intensities = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
    const colorParts = color.split("-");
    let baseColor, intensity;

    if (colorParts.length === 3) {
        baseColor = `${colorParts[0]}-${colorParts[1]}`;
        intensity = parseInt(colorParts[2]);
    } else if (colorParts.length === 2) {
        baseColor = colorParts[0];
        intensity = parseInt(colorParts[1]);
    } else {
        return color;
    }

    const currentIndex = intensities.indexOf(intensity);
    if (currentIndex === -1) {
        console.error("Invalid intensity value");
        return color;
    }

    let newIndex;
    if (direction === "auto") {
        if (intensity >= 500) {
            newIndex = currentIndex - adjustment;
        } else {
            newIndex = currentIndex + adjustment;
        }
    } else if (direction === "decrease") {
        newIndex = currentIndex - adjustment;
    } else if (direction === "augmentation") {
        newIndex = currentIndex + adjustment;
    } else {
        console.error("Invalid direction value");
        return color;
    }

    newIndex = Math.max(0, Math.min(intensities.length - 1, newIndex));

    return `${baseColor}-${intensities[newIndex]}`;
}

export function getColorFromString(input, number = 500) {
    if (!input || typeof input !== 'string') {
        console.error("Invalid input value");
        return null;
    }

    if (!colorNames || colorNames.length === 0) {
        console.error("Color names array is not defined or empty");
        return null;
    }

    const letters = input.slice(0, 2).toLowerCase();
    const firstCharCode = letters.charCodeAt(0) - 97;
    const secondCharCode = letters.length > 1 ? letters.charCodeAt(1) - 97 : 0;

    if (firstCharCode < 0 || firstCharCode > 25 || secondCharCode < 0 || secondCharCode > 25) {
        console.error("Input contains non-alphabetical characters");
        return null;
    }

    const index = firstCharCode + secondCharCode;
    const colorIndex = index % colorNames.length;
    const color = colorNames[colorIndex];

    return number ? `${color}-${number}` : color;
}

/**
 * Adjusts the intensity of a base color to increase the contrast with a target color.
 *
 * @param {string} baseColor - The base color in the format "color-intensity".
 * @param {string} targetColor - The target color in the format "color-intensity".
 * @returns {string|null} - The adjusted base color with increased contrast, or null if input is invalid.
 */
export function adjustColorForContrast(baseColor, targetColor) {
    if (!baseColor || !targetColor || typeof baseColor !== 'string' || typeof targetColor !== 'string') {
        console.error("Invalid input value");
        return null;
    }

    const baseColorParts = baseColor.split("-");
    const targetColorParts = targetColor.split("-");
    if (baseColorParts.length !== 2) {
        baseColorParts[1] = '500';
    }
    if (targetColorParts.length !== 2) {
        targetColorParts[1] = '500';
    }

    const baseColorIndex = colorNames.indexOf(baseColorParts[0]);
    const targetColorIndex = colorNames.indexOf(targetColorParts[0]);

    if (baseColorIndex === -1 || targetColorIndex === -1) {
        console.error("Invalid color name");
        return null;
    }

    if (baseColorParts[0] === 'black') {
        return targetColorIndex < colorNames.indexOf('gray') ? 'white' : 'black';
    }

    if (baseColorParts[0] === 'white') {
        return targetColorIndex > colorNames.indexOf('gray') ? 'black' : 'white';
    }

    const contrastDifference = Math.abs(baseColorIndex - targetColorIndex);
    const adjustment = Math.ceil(contrastDifference / 2);

    return adjustIntensityColor(baseColor, adjustment, baseColorIndex > targetColorIndex ? "decrease" : "augmentation");
}
