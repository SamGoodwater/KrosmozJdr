#!/usr/bin/env node

/**
 * Génère l'index de /docs et, si la configuration DB est disponible,
 * un schéma Mermaid dans docs/backend/database/SCHEMA.md.
 */
const fs = require('fs');
const path = require('path');
const readline = require('readline');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.resolve(__dirname, '../../.env') });

const DOCS_ROOT = path.resolve(__dirname, '../');
const OUTPUT_INDEX = path.join(DOCS_ROOT, 'docs.index.json');
const OUTPUT_SCHEMA = path.join(DOCS_ROOT, 'backend/database/SCHEMA.md');

const SQL_CONFIG = {
  host: process.env.DB_HOST,
  port: process.env.DB_PORT,
  user: process.env.DB_USERNAME,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE,
};

async function getMarkdownFiles(dir) {
  let results = [];
  const entries = await fs.promises.readdir(dir, { withFileTypes: true });
  for (const entry of entries) {
    const filePath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      if (entry.name === 'node_modules') continue;
      results = results.concat(await getMarkdownFiles(filePath));
    } else if (entry.name.endsWith('.md')) {
      results.push(filePath);
    }
  }
  return results;
}

async function extractDescription(filePath) {
  const stream = fs.createReadStream(filePath);
  const rl = readline.createInterface({ input: stream, crlfDelay: Infinity });
  let title = '';
  let description = '';
  for await (const line of rl) {
    const trimmed = line.trim();
    if (!title && trimmed.startsWith('#')) title = trimmed.replace(/^#+\s*/, '').trim();
    if (trimmed && !trimmed.startsWith('#') && !trimmed.startsWith('---') && !trimmed.startsWith('>')) {
      description = trimmed.replace(/^[-*]\s*/, '').trim();
      break;
    }
  }
  rl.close();
  stream.close();
  return { title, description };
}

async function buildDocsIndex() {
  const files = await getMarkdownFiles(DOCS_ROOT);
  const index = [];
  for (const file of files) {
    const relPath = path.relative(DOCS_ROOT, file).replace(/\\/g, '/');
    if (relPath === 'docs.index.json') continue;
    const { title, description } = await extractDescription(file);
    index.push({ path: relPath, title, description });
  }
  index.sort((a, b) => a.path.localeCompare(b.path));
  await fs.promises.writeFile(OUTPUT_INDEX, JSON.stringify(index, null, 2), 'utf-8');
  console.log(`Index généré : ${OUTPUT_INDEX} (${index.length} entrées)`);
}

function hasSqlConfig() {
  return ['host', 'user', 'password', 'database'].every((key) => Boolean(SQL_CONFIG[key]));
}

async function generateMermaidSchemaIfPossible() {
  if (!hasSqlConfig()) {
    console.warn('Schéma SQL ignoré : configuration DB incomplète dans .env.');
    return;
  }
  let connection;
  try {
    connection = await mysql.createConnection(SQL_CONFIG);
    const [tables] = await connection.execute('SHOW TABLES');
    const tableNames = tables.map((row) => Object.values(row)[0]);
    let mermaid = '# Schéma relationnel global\n\n```mermaid\nerDiagram\n';
    for (const table of tableNames) {
      const [columns] = await connection.execute(`SHOW COLUMNS FROM \`${table}\``);
      mermaid += `  ${table.toUpperCase()} {\n`;
      for (const col of columns) mermaid += `    ${col.Field} : ${col.Type}\n`;
      mermaid += '  }\n';
    }
    for (const table of tableNames) {
      const [fks] = await connection.execute(
        'SELECT COLUMN_NAME, REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
        [SQL_CONFIG.database, table],
      );
      for (const fk of fks) {
        mermaid += `  ${table.toUpperCase()} }o--|| ${fk.REFERENCED_TABLE_NAME.toUpperCase()} : "FK ${fk.COLUMN_NAME}"\n`;
      }
    }
    mermaid += '```\n';
    await fs.promises.mkdir(path.dirname(OUTPUT_SCHEMA), { recursive: true });
    await fs.promises.writeFile(OUTPUT_SCHEMA, mermaid, 'utf-8');
    console.log(`Schéma Mermaid généré : ${OUTPUT_SCHEMA}`);
  } catch (error) {
    console.warn(`Schéma SQL ignoré : ${error.message}`);
  } finally {
    if (connection) await connection.end();
  }
}

(async () => {
  await buildDocsIndex();
  await generateMermaidSchemaIfPossible();
})();
