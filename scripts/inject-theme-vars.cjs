const fs = require('fs');
const path = require('path');

const themeCssPath = path.join(__dirname, '../resources/css/theme.css');
const appCssPath = path.join(__dirname, '../resources/css/app.css');
const appSaveCssPath = path.join(__dirname, '../resources/css/app.save.css');

const APP_THEME_VARS_LIGHT_START = 'INJECTION_THEME_VARS_LIGHT_START';
const APP_THEME_VARS_LIGHT_END = 'INJECTION_THEME_VARS_LIGHT_END';
const APP_THEME_VARS_DARK_START = 'INJECTION_THEME_VARS_DARK_START';
const APP_THEME_VARS_DARK_END = 'INJECTION_THEME_VARS_DARK_END';

const THEME_VARS_LIGHT_START = '/*! THEME_VARS_LIGHT_START */';
const THEME_VARS_LIGHT_END = '/*! THEME_VARS_LIGHT_END */';
const THEME_VARS_DARK_START = '/*! THEME_VARS_DARK_START */';
const THEME_VARS_DARK_END = '/*! THEME_VARS_DARK_END */';

// Si app.css n'existe pas, le créer à partir de app.save.css
if (!fs.existsSync(appCssPath)) {
  if (fs.existsSync(appSaveCssPath)) {
    fs.copyFileSync(appSaveCssPath, appCssPath);
    console.log('Fichier app.css créé à partir de app.save.css');
  } else {
    throw new Error('Ni app.css ni app.save.css trouvés. Impossible de continuer.');
  }
}

if (!fs.existsSync(themeCssPath)) {
  throw new Error('theme.css introuvable. Compile d\'abord le SCSS.');
}

const themeCss = fs.readFileSync(themeCssPath, 'utf8');
let appCss = fs.readFileSync(appCssPath, 'utf8');

function extractVars(themeCss, start, end) {
  const block = themeCss.split(start)[1]?.split(end)[0];
  if (!block) return '';
  const match = block.match(/:root\s*{([\s\S]*?)}/);
  return match ? match[1].trim() : block.trim();
}

const lightVars = extractVars(themeCss, THEME_VARS_LIGHT_START, THEME_VARS_LIGHT_END);
const darkVars = extractVars(themeCss, THEME_VARS_DARK_START, THEME_VARS_DARK_END);

function replaceBetweenMarkers(css, startMarker, endMarker, content) {
  // RegExp robuste pour matcher /* INJECTION_THEME_VARS_LIGHT_START */ ... /* INJECTION_THEME_VARS_LIGHT_END */
  const regex = new RegExp(
    `(\/\\*\\s*${startMarker}\\s*\\*\/)([\\s\\S]*?)(\/\\*\\s*${endMarker}\\s*\\*\/)`,
    'm'
  );
  return css.replace(regex, `$1\n${content}\n$3`);
}

appCss = replaceBetweenMarkers(appCss, APP_THEME_VARS_LIGHT_START, APP_THEME_VARS_LIGHT_END, lightVars);
appCss = replaceBetweenMarkers(appCss, APP_THEME_VARS_DARK_START, APP_THEME_VARS_DARK_END, darkVars);

fs.writeFileSync(appCssPath, appCss, 'utf8');
console.log('Blocs DaisyUI theme injectés avec succès dans app.css !'); 