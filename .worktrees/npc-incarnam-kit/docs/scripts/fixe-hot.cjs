// utile sur windows arm

const fs = require("fs");
const path = require("path");
const hotFile = path.join(__dirname, "..", "..", "public", "hot"); // Chemin vers le fichier hot
const url = "http://localhost:5173";

if (fs.existsSync(hotFile)) {
    fs.writeFileSync(hotFile, url);
    console.log("✔️  [fix-hot] public/hot corrigé →", url);
}
