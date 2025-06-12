const fs = require("fs");
const path = require("path");
const glob = require("glob");
const { parse } = require("comment-parser");

// Dossiers à indexer
const atomicDirs = [
    {
        name: "atoms",
        dir: path.join(__dirname, "../../resources/js/Pages/Atoms"),
        output: path.join(
            __dirname,
            "../../resources/js/Pages/Atoms/atoms.index.json",
        ),
    },
    {
        name: "molecules",
        dir: path.join(__dirname, "../../resources/js/Pages/Molecules"),
        output: path.join(
            __dirname,
            "../../resources/js/Pages/Molecules/molecules.index.json",
        ),
    },
    {
        name: "organisms",
        dir: path.join(__dirname, "../../resources/js/Pages/Organismes"),
        output: path.join(
            __dirname,
            "../../resources/js/Pages/Organismes/organisms.index.json",
        ),
    },
];

function extractDocBlock(content) {
    // Cherche le premier docBlock /** ... */
    const match = content.match(/\/\*\*([\s\S]*?)\*\//);
    return match ? "/**" + match[1] + "*/" : null;
}

function parseDocBlock(docBlock) {
    const parsed = parse(docBlock)[0];
    if (!parsed) return null;
    const item = {
        name: "",
        description: parsed.description.trim(),
        props: [],
        slots: [],
    };
    parsed.tags.forEach((tag) => {
        if (tag.tag === "props" || tag.tag === "prop") {
            item.props.push({
                name: tag.name,
                type: tag.type,
                desc: tag.description,
            });
        }
        if (tag.tag === "slot") {
            item.slots.push({
                name: tag.name,
                desc: tag.description,
            });
        }
    });
    return item;
}

function generateIndex({ name, dir, output }) {
    console.log(`\n==== ${name.toUpperCase()} ====\n`);
    if (!fs.existsSync(dir)) {
        console.error(`❌ Dossier introuvable : ${dir}`);
        return;
    }
    const files = glob.sync(dir.replace(/\\/g, "/") + "/**/*.vue");
    if (files.length === 0) {
        console.warn(
            `⚠️  Aucun fichier .vue trouvé dans ${dir} (y compris sous-dossiers).`,
        );
    } else {
        console.log(
            `Fichiers trouvés :\n` + files.map((f) => " - " + f).join("\n"),
        );
    }
    const items = [];
    files.forEach((file) => {
        const content = fs.readFileSync(file, "utf8");
        const docBlock = extractDocBlock(content);
        if (!docBlock) return;
        const item = parseDocBlock(docBlock);
        if (!item) return;
        item.name = path.basename(file, ".vue");
        item.file = path.relative(dir, file).replace(/\\/g, "/");

        // Contrôle qualité : champs manquants
        const warnings = [];
        if (!item.description || !item.description.trim()) {
            warnings.push("description manquante ou vide");
        }
        if (!Array.isArray(item.props) || item.props.length === 0) {
            warnings.push("aucune prop documentée");
        }
        if (!Array.isArray(item.slots) || item.slots.length === 0) {
            warnings.push("aucun slot documenté");
        }
        if (warnings.length > 0) {
            console.warn(
                `\u26A0\uFE0F  [${name}] ${item.name} (${item.file}) : ${warnings.join(", ")}`,
            );
        }
        items.push(item);
    });
    fs.writeFileSync(output, JSON.stringify(items, null, 2), "utf8");
    console.log(`✅ ${output} généré avec ${items.length} ${name}.`);
}

function main() {
    atomicDirs.forEach(generateIndex);
}

main();
