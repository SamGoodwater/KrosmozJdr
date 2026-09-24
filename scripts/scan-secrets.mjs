#!/usr/bin/env node
/**
 * Scan anti-secrets (push local + CI).
 *
 * Préfère `gitleaks` si installé (`.gitleaks.toml`), sinon heuristiques Node.
 *
 * @example
 * pnpm run secrets:scan
 * node scripts/scan-secrets.mjs --mode=pre-push   # stdin = lignes pre-push git
 */

import { spawnSync } from 'node:child_process';
import { readFileSync, existsSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import readline from 'node:readline';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const ZERO = '0000000000000000000000000000000000000000';

/** @type {{ id: string, description: string, regex: RegExp, pathRegex?: RegExp }[]} */
const RULES = [
  {
    id: 'mysql-dsn-password',
    description: 'DSN MySQL/MariaDB avec mot de passe',
    regex: /mysql(?:i)?:\/\/[^:\s/'"]+:[^@\s/'"]+@/i,
  },
  {
    id: 'private-key-pem',
    description: 'Clé privée PEM',
    regex: /-----BEGIN (?:RSA |EC |OPENSSH |DSA )?PRIVATE KEY-----/,
  },
  {
    id: 'anthropic-api-key',
    description: 'Clé API Anthropic',
    regex: /sk-ant-api\d{2}-[A-Za-z0-9_-]{20,}/,
  },
  {
    id: 'laravel-app-key',
    description: 'APP_KEY Laravel (base64)',
    regex: /APP_KEY\s*=\s*base64:[A-Za-z0-9+/=]{20,}/i,
  },
  {
    id: 'aws-access-key',
    description: 'AWS Access Key ID',
    regex: /AKIA[0-9A-Z]{16}/,
  },
  {
    id: 'github-pat',
    description: 'GitHub personal access token',
    regex: /gh[pousr]_[A-Za-z0-9_]{20,}/,
  },
];

const PATH_BLOCKLIST = [
  /(^|\/)\.env$/i,
  /(^|\/)\.env\.(?!example(?:$|\.))/i,
  /(^|\/)\.cursor\/mcp\.json$/i,
  /(^|\/)id_rsa$/i,
  /\.pem$/i,
  /\.p12$/i,
];

const PATH_ALLOW = [
  /\.env\.example$/i,
  /pnpm-lock\.yaml$/i,
  /composer\.lock$/i,
  /^private\/archive\//i,
  /^storage\/app\/dev-reports\//i,
  /\.(png|jpe?g|gif|webp|ico|woff2?|ttf)$/i,
];

function hasGitleaks() {
  const r = spawnSync('gitleaks', ['version'], { encoding: 'utf8' });
  return r.status === 0;
}

/**
 * @param {string[]} args
 */
function runGitleaks(args) {
  return spawnSync('gitleaks', args, { cwd: ROOT, encoding: 'utf8', maxBuffer: 16 * 1024 * 1024 });
}

/**
 * @param {string} tipCommit
 * @param {string} filePath
 */
function readAtCommit(tipCommit, filePath) {
  if (!tipCommit || tipCommit === ZERO) return null;
  const r = spawnSync('git', ['show', `${tipCommit}:${filePath}`], {
    cwd: ROOT,
    encoding: 'utf8',
    maxBuffer: 8 * 1024 * 1024,
  });
  return r.status === 0 ? r.stdout : null;
}

/**
 * @param {string} filePath
 */
function readWorktree(filePath) {
  const abs = resolve(ROOT, filePath);
  if (!existsSync(abs)) return null;
  try {
    return readFileSync(abs, 'utf8');
  } catch {
    return null;
  }
}

/**
 * @param {string[]} paths
 * @param {(p: string) => string|null} loader
 */
function scanFallback(paths, loader) {
  /** @type {{ id: string, description: string, path: string, line: number }[]} */
  const findings = [];

  for (const filePath of paths) {
    if (PATH_ALLOW.some((re) => re.test(filePath))) continue;

    for (const blocked of PATH_BLOCKLIST) {
      if (blocked.test(filePath)) {
        findings.push({
          id: 'blocked-path',
          description: `Fichier sensible ne doit pas être versionné`,
          path: filePath,
          line: 1,
        });
        break;
      }
    }

    const content = loader(filePath);
    if (content == null || content === '' || content.includes('\u0000')) continue;

    const lines = content.split(/\r?\n/);
    for (let i = 0; i < lines.length; i++) {
      for (const rule of RULES) {
        if (rule.pathRegex && !rule.pathRegex.test(filePath)) continue;
        // Fixtures de tests de sanitization (volontairement « sales »)
        if (
          rule.id === 'mysql-dsn-password' &&
          /^tests\//.test(filePath)
        ) {
          continue;
        }
        if (rule.regex.test(lines[i])) {
          findings.push({
            id: rule.id,
            description: rule.description,
            path: filePath,
            line: i + 1,
          });
        }
      }
    }
  }

  return findings;
}

/**
 * @returns {Promise<{ range: string, tip: string }|null>}
 */
async function readPrePushRange() {
  const rl = readline.createInterface({ input: process.stdin, crlfDelay: Infinity });
  for await (const line of rl) {
    const parts = line.trim().split(/\s+/);
    if (parts.length < 4) continue;
    const localSha = parts[1];
    const remoteSha = parts[3];
    if (!localSha || localSha === ZERO) continue;
    if (!remoteSha || remoteSha === ZERO) {
      return { range: `${localSha}~30..${localSha}`, tip: localSha };
    }
    return { range: `${remoteSha}..${localSha}`, tip: localSha };
  }
  return null;
}

function reportFindings(findings) {
  console.error('[secrets] Secrets / fichiers sensibles détectés — refus :');
  for (const f of findings.slice(0, 40)) {
    console.error(`  - [${f.id}] ${f.path}:${f.line} — ${f.description}`);
  }
  if (findings.length > 40) {
    console.error(`  … et ${findings.length - 40} autres`);
  }
  console.error(
    '\nRetire les secrets (variables d’env), ne versionne jamais `.env` ni `.cursor/mcp.json`.',
  );
}

async function main() {
  const modeArg = process.argv.find((a) => a.startsWith('--mode='));
  const mode = modeArg ? modeArg.slice('--mode='.length) : 'ci';

  if (mode === 'pre-push') {
    const info = await readPrePushRange();
    if (!info) {
      console.log('[secrets] Aucun commit à pousser — OK');
      process.exit(0);
    }

    if (hasGitleaks()) {
      const gl = runGitleaks([
        'protect',
        '--verbose',
        '--redact',
        '-c',
        '.gitleaks.toml',
        '--log-opts',
        info.range,
      ]);
      if (gl.status === 0) {
        console.log('[secrets] gitleaks protect : OK');
        process.exit(0);
      }
      if (gl.status === 1) {
        console.error('[secrets] gitleaks a détecté des secrets — push refusé.');
        console.error(gl.stdout || gl.stderr);
        process.exit(1);
      }
    }

    const diff = spawnSync(
      'git',
      ['diff', '--name-only', '--diff-filter=ACMR', info.range],
      { cwd: ROOT, encoding: 'utf8' },
    );
    const files = (diff.stdout || '').split('\n').map((s) => s.trim()).filter(Boolean);
    const findings = scanFallback(files, (p) => readAtCommit(info.tip, p));
    if (findings.length) {
      reportFindings(findings);
      process.exit(1);
    }
    console.log(`[secrets] Scan Node (${files.length} fichiers) : OK`);
    process.exit(0);
  }

  // mode ci : arbre tracké
  if (hasGitleaks()) {
    const gl = runGitleaks([
      'detect',
      '--source',
      '.',
      '--verbose',
      '--redact',
      '-c',
      '.gitleaks.toml',
      '--exit-code',
      '1',
    ]);
    if (gl.status === 0) {
      console.log('[secrets] gitleaks detect : OK');
      process.exit(0);
    }
    if (gl.status === 1) {
      console.error('[secrets] gitleaks a détecté des secrets.');
      console.error(gl.stdout || gl.stderr);
      process.exit(1);
    }
  }

  const ls = spawnSync('git', ['ls-files', '-z'], { cwd: ROOT, encoding: 'utf8' });
  if (ls.status !== 0) {
    console.error(ls.stderr || 'git ls-files failed');
    process.exit(2);
  }
  const files = ls.stdout.split('\0').filter(Boolean);
  const findings = scanFallback(files, readWorktree);
  if (findings.length) {
    reportFindings(findings);
    process.exit(1);
  }
  console.log(`[secrets] Scan Node (${files.length} fichiers) : OK`);
  process.exit(0);
}

main().catch((err) => {
  console.error('[secrets] Erreur scanner :', err);
  process.exit(2);
});
