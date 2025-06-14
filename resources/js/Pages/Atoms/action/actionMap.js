// actionMap.js
// Ce fichier ne contient QUE les listes et mappings spécifiques à la famille action (Atomic Design).
// Les listes transverses (colorList, variantList, sizeList, etc.) sont désormais centralisées dans atomMap.js.
// À importer ici UNIQUEMENT ce qui est propre à action (ex: typeList, placementList, targetList, methodList, etc.)

export const typeList = ["button", "submit", "reset", "radio", "checkbox"];
export const placementList = [
    "start",
    "end",
    "top",
    "bottom",
    "left",
    "right",
    "center",
    "bottom-end",
    "top-end",
    "left-end",
    "right-end",
];
export const targetList = ["_blank", "_self", "_parent", "_top"];
export const methodList = [
    "",
    "get",
    "post",
    "put",
    "delete",
    "patch",
    "options",
    "head",
    "trace",
    "connect",
    "link",
    "unlink",
];
