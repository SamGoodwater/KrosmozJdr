const fs = require("fs");
const path = require("path");
const glob = require("glob");
const { parse } = require("comment-parser");

const atomsDir = path.join(__dirname, "../../resources/js/Pages/Atoms");
const outputFile = path.join(atomsDir, "atoms.index.json");

console.log("Chemin absolu utilisé pour atomsDir :", atomsDir);
console.log(
    "Contenu du dossier racine :",
    fs.readdirSync(atomsDir, { withFileTypes: true }).map((f) => f.name),
);

function extractDocBlock(content) {
    // Cherche le premier docBlock /** ... */
    const match = content.match(/\/\*\*([\s\S]*?)\*\//);
    return match ? "/**" + match[1] + "*/" : null;
}

function parseDocBlock(docBlock) {
    const parsed = parse(docBlock)[0];
    if (!parsed) return null;
    const atom = {
        name: "",
        description: parsed.description.trim(),
        props: [],
        slots: [],
        daisyui: null,
    };
    parsed.tags.forEach((tag) => {
        if (tag.tag === "props" || tag.tag === "prop") {
            atom.props.push({
                name: tag.name,
                type: tag.type,
                desc: tag.description,
            });
        }
        if (tag.tag === "slot") {
            atom.slots.push({
                name: tag.name,
                desc: tag.description,
            });
        }
        if (tag.tag === "see" && tag.description.includes("daisyui.com")) {
            atom.daisyui = tag.description;
        }
    });
    return atom;
}

function main() {
    if (!fs.existsSync(atomsDir)) {
        console.error(`❌ Dossier introuvable : ${atomsDir}`);
        process.exit(1);
    }
    const files = glob.sync(atomsDir.replace(/\\/g, "/") + "/**/*.vue");
    if (files.length === 0) {
        console.warn(
            `⚠️  Aucun fichier .vue trouvé dans ${atomsDir} (y compris sous-dossiers).`,
        );
    } else {
        console.log(
            `Fichiers trouvés :\n` + files.map((f) => " - " + f).join("\n"),
        );
    }
    const atoms = [];
    files.forEach((file) => {
        const content = fs.readFileSync(file, "utf8");
        const docBlock = extractDocBlock(content);
        if (!docBlock) return;
        const atom = parseDocBlock(docBlock);
        if (!atom) return;
        atom.name = path.basename(file, ".vue");
        atoms.push(atom);
    });
    fs.writeFileSync(outputFile, JSON.stringify(atoms, null, 2), "utf8");
    console.log(`✅ atoms.index.json généré avec ${atoms.length} atoms.`);
}

main();
