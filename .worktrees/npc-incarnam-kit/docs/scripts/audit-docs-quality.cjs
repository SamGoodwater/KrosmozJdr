#!/usr/bin/env node
/**
 * Audit qualité de la documentation /docs (release 1.3.2).
 * Usage: node docs/scripts/audit-docs-quality.cjs [--max-lines=400]
 */
const fs = require('fs');
const path = require('path');

const DOCS_ROOT = path.join(__dirname, '..');
const INDEX_PATH = path.join(DOCS_ROOT, 'docs.index.json');
const maxLines = Number(process.argv.find((a) => a.startsWith('--max-lines='))?.split('=')[1] ?? 400);
const historyPatterns = [
    /anciennement/i,
    /refactoring/i,
    /\bévolution\b/i,
    /historique des/i,
    /avant\s+1\.\d/i,
];

function walk(dir, acc = []) {
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            if (entry.name.startsWith('.')) continue;
            walk(full, acc);
        } else if (entry.name.endsWith('.md')) {
            acc.push(full);
        }
    }
    return acc;
}

function loadIndexPaths() {
    if (!fs.existsSync(INDEX_PATH)) return new Set();
    const raw = JSON.parse(fs.readFileSync(INDEX_PATH, 'utf8'));
    const paths = new Set();
    const items = Array.isArray(raw) ? raw : raw.entries ?? raw.files ?? [];
    for (const item of items) {
        const p = item.path ?? item.file ?? item.href;
        if (typeof p === 'string') paths.add(p.replace(/^\//, ''));
    }
    return paths;
}

const indexed = loadIndexPaths();
const files = walk(DOCS_ROOT);
const longFiles = [];
const historyHits = [];
const missingIndex = [];

for (const file of files) {
    const rel = path.relative(DOCS_ROOT, file).replace(/\\/g, '/');
    const content = fs.readFileSync(file, 'utf8');
    const lines = content.split('\n').length;
    if (lines > maxLines) {
        longFiles.push({ rel, lines });
    }
    for (const re of historyPatterns) {
        if (re.test(content)) {
            historyHits.push({ rel, pattern: re.source });
            break;
        }
    }
    if (rel !== 'README.md' && !indexed.has(rel) && !rel.startsWith('110-')) {
        missingIndex.push(rel);
    }
}

longFiles.sort((a, b) => b.lines - a.lines);
console.log(`# Audit documentation (${files.length} fichiers .md)\n`);
console.log(`## Fichiers > ${maxLines} lignes (${longFiles.length})`);
for (const f of longFiles.slice(0, 30)) {
    console.log(`- ${f.rel} (${f.lines})`);
}
console.log(`\n## Motifs historique / évolution (${historyHits.length})`);
for (const h of historyHits.slice(0, 25)) {
    console.log(`- ${h.rel}`);
}
console.log(`\n## Hors docs.index.json (${missingIndex.length}, hors 110-To Do)`);
for (const m of missingIndex.slice(0, 25)) {
    console.log(`- ${m}`);
}
if (missingIndex.length > 25) {
    console.log(`… et ${missingIndex.length - 25} autres`);
}
