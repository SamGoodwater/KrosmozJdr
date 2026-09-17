/**
 * Doit être importé **avant** `@cursor/sdk` : le runtime local du SDK exige un binaire ripgrep
 * (chemin absolu dans `CURSOR_RIPGREP_PATH`), sinon erreur « Ripgrep path not configured ».
 *
 * @see https://cursor.com/docs/api/sdk/typescript
 */

import { execSync } from "node:child_process";
import fs from "node:fs";
import path from "node:path";

const existing = (process.env.CURSOR_RIPGREP_PATH ?? "").trim();
if (existing !== "" && path.isAbsolute(existing) && fs.existsSync(existing)) {
    // déjà correct
} else {
    const candidates = [
        "/usr/bin/rg",
        "/bin/rg",
        "/usr/local/bin/rg",
        "/opt/homebrew/bin/rg",
    ];

    let found = "";
    for (const p of candidates) {
        if (fs.existsSync(p)) {
            found = p;
            break;
        }
    }

    if (found === "") {
        try {
            const out = execSync("command -v rg 2>/dev/null", { encoding: "utf8" }).trim();
            if (out !== "" && path.isAbsolute(out) && fs.existsSync(out)) {
                found = out;
            }
        } catch {
            // ignore
        }
    }

    if (found !== "") {
        process.env.CURSOR_RIPGREP_PATH = found;
    }
}

if (
    !(
        process.env.CURSOR_RIPGREP_PATH &&
        path.isAbsolute(process.env.CURSOR_RIPGREP_PATH) &&
        fs.existsSync(process.env.CURSOR_RIPGREP_PATH)
    )
) {
    console.warn(
        "[cursor-dev-review] CURSOR_RIPGREP_PATH non défini : installez ripgrep (`sudo apt install ripgrep` / `brew install ripgrep`) ou exportez CURSOR_RIPGREP_PATH=/chemin/absolu/rg avant le SDK.",
    );
}
