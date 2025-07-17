#!/usr/bin/env node

/**
 * Script d'indexation de la documentation et de génération du schéma SQL (Mermaid)
 * Usage : node docs/scripts/generate_index_schema_docs.cjs
 *
 * Les credentials SQL sont lus depuis le fichier .env à la racine du projet (non versionné).
 * Fournir un .env.example pour la structure sur GitHub.
 */

const fs = require('fs');
const path = require('path');
const util = require('util');
const readline = require('readline');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.resolve(__dirname, '../../.env') });

// === CONFIGURATION ===
const DOCS_ROOT = path.resolve(__dirname, '../');
const OUTPUT_INDEX = path.join(DOCS_ROOT, 'docs.index.json');
const OUTPUT_SCHEMA = path.join(DOCS_ROOT, '20-Content/SCHEMA.md');

// Config SQL (lues depuis .env)
const SQL_CONFIG = {
  host: process.env.DB_HOST,
  port: process.env.DB_PORT,
  user: process.env.DB_USERNAME,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE,
};

function checkSqlConfig() {
  const missing = [];
  for (const key of ['host', 'user', 'password', 'database']) {
    if (!SQL_CONFIG[key]) missing.push(key);
  }
  if (missing.length) {
    console.error(`Erreur : variable(s) SQL manquante(s) dans .env : ${missing.join(', ')}`);
    process.exit(1);
  }
}

// === 1. INDEXATION DES FICHIERS DOCS ===
async function getMarkdownFiles(dir) {
  let results = [];
  const list = await fs.promises.readdir(dir, { withFileTypes: true });
  for (const file of list) {
    const filePath = path.join(dir, file.name);
    if (file.isDirectory()) {
      results = results.concat(await getMarkdownFiles(filePath));
    } else if (file.name.endsWith('.md')) {
      results.push(filePath);
    }
  }
  return results;
}

async function extractDescription(filePath) {
  const stream = fs.createReadStream(filePath);
  const rl = readline.createInterface({ input: stream, crlfDelay: Infinity });
  let description = '';
  let title = '';
  for await (const line of rl) {
    if (!title && line.trim().startsWith('#')) {
      title = line.replace(/^#+\s*/, '').trim();
    }
    if (line.trim().startsWith('<!--')) {
      description = line.replace(/<!--|-->/g, '').trim();
      break;
    }
    if (line.trim().startsWith('---')) {
      // Front-matter YAML
      let yaml = '';
      let inYaml = true;
      while (inYaml) {
        const yamlLine = await rl[Symbol.asyncIterator]().next();
        if (yamlLine.done) break;
        if (yamlLine.value.trim().startsWith('---')) break;
        yaml += yamlLine.value + '\n';
      }
      const match = yaml.match(/description:\s*(.*)/);
      if (match) description = match[1].trim();
      break;
    }
    if (!description && line.trim() && !line.trim().startsWith('#')) {
      // Première ligne non vide non titre = fallback
      description = line.trim();
      break;
    }
  }
  rl.close();
  stream.close();
  return { title, description };
}

async function buildDocsIndex() {
  const files = await getMarkdownFiles(path.join(DOCS_ROOT));
  const index = [];
  for (const file of files) {
    const relPath = path.relative(DOCS_ROOT, file).replace(/\\/g, '/');
    const { title, description } = await extractDescription(file);
    index.push({ path: relPath, title, description });
  }
  await fs.promises.writeFile(OUTPUT_INDEX, JSON.stringify(index, null, 2), 'utf-8');
  console.log(`Index généré : ${OUTPUT_INDEX}`);
}

// === 2. GÉNÉRATION DU SCHÉMA SQL EN MERMAID ===
async function generateMermaidSchema() {
  checkSqlConfig();
  let connection;
  try {
    connection = await mysql.createConnection(SQL_CONFIG);
  } catch (err) {
    console.error('Erreur de connexion SQL :', err.message);
    process.exit(1);
  }
  const [tables] = await connection.execute('SHOW TABLES');
  const tableNames = tables.map(row => Object.values(row)[0]);
  let mermaid = '# Schéma relationnel global (généré automatiquement)\n\n';
  mermaid += '```mermaid\nerDiagram\n';
  // Récupérer les colonnes et clés étrangères
  for (const table of tableNames) {
    const [columns] = await connection.execute(`SHOW COLUMNS FROM \`${table}\``);
    mermaid += `  ${table.toUpperCase()} {\n`;
    for (const col of columns) {
      mermaid += `    ${col.Field} : ${col.Type}\n`;
    }
    mermaid += '  }\n';
  }
  // Relations (FK)
  for (const table of tableNames) {
    const [fks] = await connection.execute(`SELECT COLUMN_NAME, REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL`, [SQL_CONFIG.database, table]);
    for (const fk of fks) {
      mermaid += `  ${table.toUpperCase()} }o--|| ${fk.REFERENCED_TABLE_NAME.toUpperCase()} : "FK ${fk.COLUMN_NAME}"\n`;
    }
  }
  mermaid += '```\n';
  await connection.end();
  // Créer le dossier cible si besoin
  await fs.promises.mkdir(path.dirname(OUTPUT_SCHEMA), { recursive: true });
  await fs.promises.writeFile(OUTPUT_SCHEMA, mermaid, 'utf-8');
  console.log(`Schéma Mermaid généré : ${OUTPUT_SCHEMA}`);
}

// === MAIN ===
(async () => {
  await buildDocsIndex();
  await generateMermaidSchema();
})();
