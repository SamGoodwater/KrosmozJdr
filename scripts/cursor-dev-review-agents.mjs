#!/usr/bin/env node
/**
 * Enchaîne des runs Cursor SDK (`Agent.prompt`) locaux, un par bloc « Prompts Cursor » du rapport project:review.
 *
 * Prérequis : `pnpm install`, `CURSOR_API_KEY`, exécution depuis la racine du repo (`cwd` = projet).
 *
 * @example node scripts/cursor-dev-review-agents.mjs --report storage/app/dev-reports/review-2026-01-01-120000.md
 * @example pnpm run project:review:cursor-agents -- --report storage/app/dev-reports/review-2026-01-01-120000.md
 */

import "./cursor-ensure-ripgrep-env.mjs";
import { Agent, CursorAgentError } from "@cursor/sdk";
import fs from "node:fs";
import path from "node:path";
import process from "node:process";

function parseArgs(argv) {
    const out = { report: null };
    for (let i = 2; i < argv.length; i++) {
        const a = argv[i];
        if (a.startsWith("--report=")) {
            out.report = a.slice("--report=".length);
        } else if (a === "--report" && argv[i + 1]) {
            out.report = argv[++i];
        }
    }
    return out;
}

/**
 * @param {string} md
 * @returns {{ title: string, body: string }[]}
 */
function extractPromptBlocks(md) {
    const marker = "## Prompts Cursor";
    const idx = md.indexOf(marker);
    if (idx === -1) {
        return [];
    }
    const tail = md.slice(idx);
    const re = /### ([^\n]+)\s*\n\n```text\n([\s\S]*?)```/g;
    const blocks = [];
    let m;
    while ((m = re.exec(tail)) !== null) {
        blocks.push({ title: m[1].trim(), body: m[2].trim() });
    }
    return blocks;
}

async function main() {
    const { report: reportArg } = parseArgs(process.argv);
    if (!reportArg) {
        console.error("Usage: node scripts/cursor-dev-review-agents.mjs --report <chemin-rapport.md>");
        process.exit(1);
    }

    const cwd = process.cwd();
    const reportPath = path.isAbsolute(reportArg) ? reportArg : path.resolve(cwd, reportArg);

    if (!fs.existsSync(reportPath)) {
        console.error("Fichier rapport introuvable :", reportPath);
        process.exit(1);
    }

    const apiKey = (process.env.CURSOR_API_KEY ?? "").trim();
    if (!apiKey) {
        console.error("CURSOR_API_KEY manquant. Définissez-le (voir .env.example).");
        process.exit(1);
    }

    const modelId = (process.env.CURSOR_AGENT_MODEL ?? "composer-2").trim() || "composer-2";

    const md = fs.readFileSync(reportPath, "utf8");
    const blocks = extractPromptBlocks(md);
    if (blocks.length === 0) {
        console.error(
            "Aucun bloc « ### … / ```text » trouvé sous « ## Prompts Cursor ». Lancez d’abord : php artisan project:review …",
        );
        process.exit(1);
    }

    const relReport = path.relative(cwd, reportPath) || reportPath;
    let hadError = false;

    for (const { title, body } of blocks) {
        const prompt = [
            "Tu travailles sur le dépôt Krosmoz-JDR (racine = cwd). Réponds en français.",
            "",
            `Rapport project:review (Markdown) à utiliser comme contexte principal : \`${relReport}\`.`,
            "Ouvre ce fichier, lis les sorties des outils (PHPUnit, PHPStan, etc.) au-dessus de la section « Prompts Cursor ».",
            "",
            `Mission — « ${title} » :`,
            body,
            "",
            "Propose un compte rendu structuré et, si pertinent, des correctifs concrets (fichiers / extraits). N’applique des changements au dépôt que si tu juges que c’est sûr et demandé implicitement par la mission.",
        ].join("\n");

        console.log(`\n── Agent Cursor (local) : ${title} ──\n`);

        try {
            const result = await Agent.prompt(prompt, {
                apiKey,
                model: { id: modelId },
                local: { cwd },
            });

            console.log(`Statut : ${result.status}${result.durationMs != null ? ` (${result.durationMs} ms)` : ""}`);
            if (result.result) {
                console.log("\n--- Réponse (extrait / fin) ---\n");
                const text = result.result;
                const max = 12000;
                console.log(text.length > max ? `${text.slice(0, max)}\n… [tronqué pour le terminal, voir l’historique Cursor]` : text);
            }

            if (result.status !== "finished") {
                hadError = true;
            }
        } catch (e) {
            hadError = true;
            if (e instanceof CursorAgentError) {
                console.error(`Erreur SDK (retryable=${e.isRetryable}) :`, e.message);
            } else {
                console.error(e);
            }
        }
    }

    process.exit(hadError ? 2 : 0);
}

main().catch((e) => {
    console.error(e);
    process.exit(1);
});
