<?php

/**
 * Icônes de type d'entité : aplats, contour épais, fond transparent.
 *
 * Style aligné sur les icônes de spécialisations (un objet, contour sombre,
 * pas de pastille de fond). Sortie : storage/app/public/images/icons/entities/{clé}.webp
 *
 * Usage : php scripts/build-entity-icons.php
 */

declare(strict_types=1);

$outDir = dirname(__DIR__).'/storage/app/public/images/icons/entities';
if (! is_dir($outDir) && ! mkdir($outDir, 0775, true) && ! is_dir($outDir)) {
    fwrite(STDERR, "Dossier inaccessible : {$outDir}\n");
    exit(1);
}

/**
 * @param  string  $body  Formes SVG (héritent contour #1c1917, 2.6)
 */
function entityIconSvg(string $body): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        .'<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 64 64">'
        .'<g fill="none" stroke="#1c1917" stroke-width="2.6" stroke-linejoin="round" stroke-linecap="round">'
        .$body
        .'</g></svg>';
}

/** @var array<string, string> */
$icons = [
    'breed' => entityIconSvg(
        '<path fill="#2dd4bf" d="M32 6c9 3.2 16 6 20 8v16c0 14-9 22-20 28C21 52 12 44 12 30V14c4-2 11-4.8 20-8z"/>'
        .'<path fill="#0f766e" d="M21 29h22L32 43z"/>'
    ),
    'specialization' => entityIconSvg(
        '<rect x="29" y="8" width="6" height="48" rx="2" fill="#3f6212"/>'
        .'<path fill="#84cc16" d="M35 12h14l8 7-8 7H35z"/>'
        .'<path fill="#bef264" d="M29 30H15l-8 7 8 7h14z"/>'
    ),
    'spell' => entityIconSvg(
        '<path fill="#8b5cf6" d="M32 4l5.2 20.2L58 32l-20.8 7.8L32 60l-5.2-20.2L6 32l20.8-7.8z"/>'
        .'<circle cx="32" cy="32" r="5" fill="#ede9fe"/>'
    ),
    'capability' => entityIconSvg(
        '<path fill="#f472b6" d="M36 4 16 34h12L24 60l24-32H34z"/>'
    ),
    // Bouclier (écu) — silhouette distincte du losange (trait), hexagone (état), étoile (sort).
    'aptitude' => entityIconSvg(
        '<path fill="#e879f9" d="M32 4 52 10v18c0 12-8 22-20 28C20 50 12 40 12 28V10z"/>'
        .'<path fill="#fdf4ff" stroke="none" d="M32 18l2.6 7.2H42l-6 4.6 2.3 7.2L32 32.6l-6.3 4.4 2.3-7.2-6-4.6h7.4z"/>'
    ),
    'monster' => entityIconSvg(
        '<path fill="#9f1239" d="M14 30 4 10l14 8z"/>'
        .'<path fill="#9f1239" d="M50 30 60 10 46 18z"/>'
        .'<ellipse cx="32" cy="38" rx="16" ry="14" fill="#fb7185"/>'
        .'<circle cx="25" cy="36" r="2.4" fill="#1c1917" stroke="none"/>'
        .'<circle cx="39" cy="36" r="2.4" fill="#1c1917" stroke="none"/>'
        .'<path fill="#fff7ed" stroke="none" d="M24 44h5l-2.5 7zM35 44h5l-2.5 7z"/>'
    ),
    'npc' => entityIconSvg(
        '<circle cx="32" cy="20" r="11" fill="#4ade80"/>'
        .'<path fill="#15803d" d="M12 56c1.2-15 8-22 20-22s18.8 7 20 22z"/>'
    ),
    'item' => entityIconSvg(
        '<path fill="#e0e7ff" d="M29 6h6l1.2 26h-8.4z"/>'
        .'<path fill="#6366f1" d="M16 32h32v6H16z"/>'
        .'<path fill="#4338ca" d="M29 38h6v12h-6z"/>'
        .'<circle cx="32" cy="54" r="3.4" fill="#6366f1"/>'
    ),
    'panoply' => entityIconSvg(
        '<rect x="16" y="6" width="32" height="12" rx="3" fill="#e2e8f0"/>'
        .'<rect x="8" y="24" width="48" height="14" rx="3" fill="#94a3b8"/>'
        .'<rect x="14" y="44" width="36" height="12" rx="3" fill="#475569"/>'
    ),
    'consumable' => entityIconSvg(
        '<rect x="26" y="6" width="12" height="8" rx="2" fill="#9a3412"/>'
        .'<path fill="#fb923c" d="M23 17h18l6 10c3 10-1 22-15 27-14-5-18-17-15-27z"/>'
        .'<path d="M30 26c1.2 7 1.2 13 0 18" fill="none" stroke="#fff7ed" stroke-width="2.2"/>'
    ),
    'resource' => entityIconSvg(
        '<path fill="#0ea5e9" d="M32 4 54 22 32 60 10 22z"/>'
        .'<path fill="#e0f2fe" d="M32 4 46 22H18z"/>'
        .'<path d="M18 22h28M32 22 32 48" fill="none" stroke="#0c4a6e" stroke-width="1.8"/>'
    ),
    'condition' => entityIconSvg(
        '<path fill="#ef4444" d="M32 5 56 18.5v19L32 59 8 37.5v-19z"/>'
        .'<rect x="29.2" y="20" width="5.6" height="16" rx="1.6" fill="#fff7ed" stroke="none"/>'
        .'<circle cx="32" cy="44" r="2.7" fill="#fff7ed" stroke="none"/>'
    ),
    'creature-trait' => entityIconSvg(
        '<path fill="#34d399" d="M32 4 56 32 32 60 8 32z"/>'
        .'<path fill="#047857" d="M32 18 44 32 32 46 20 32z"/>'
    ),
    'trait' => entityIconSvg(
        '<path fill="#10b981" d="M32 6 54 32 32 58 10 32z"/>'
    ),
    'language' => entityIconSvg(
        '<rect x="6" y="8" width="52" height="32" rx="8" fill="#22d3ee"/>'
        .'<path fill="#22d3ee" d="M16 40 14 56l16-16z"/>'
        .'<path d="M16 18h30M16 27h20" fill="none" stroke="#164e63" stroke-width="2.6"/>'
    ),
    'job' => entityIconSvg(
        '<rect x="12" y="8" width="40" height="16" rx="3" fill="#facc15"/>'
        .'<rect x="28" y="22" width="8" height="34" rx="2" fill="#a16207"/>'
    ),
    'creature' => entityIconSvg(
        '<circle cx="16" cy="26" r="6" fill="#34d399"/>'
        .'<circle cx="26" cy="16" r="6" fill="#34d399"/>'
        .'<circle cx="40" cy="16" r="6" fill="#34d399"/>'
        .'<circle cx="50" cy="26" r="6" fill="#34d399"/>'
        .'<ellipse cx="32" cy="42" rx="14" ry="12" fill="#6ee7b7"/>'
    ),
    'shop' => entityIconSvg(
        '<path fill="#fbbf24" d="M6 28 32 8l26 20v5H6z"/>'
        .'<rect x="14" y="33" width="36" height="22" rx="2" fill="#d97706"/>'
        .'<rect x="28" y="39" width="8" height="16" fill="#fef3c7"/>'
    ),
    'campaign' => entityIconSvg(
        '<rect x="14" y="6" width="5" height="52" rx="1.5" fill="#57534e"/>'
        .'<path fill="#d6d3d1" d="M19 10h30l-8 12 8 12H19z"/>'
    ),
    'scenario' => entityIconSvg(
        '<rect x="18" y="12" width="28" height="40" rx="2" fill="#f5f5f4"/>'
        .'<rect x="10" y="12" width="10" height="40" rx="5" fill="#a8a29e"/>'
        .'<rect x="44" y="12" width="10" height="40" rx="5" fill="#d6d3d1"/>'
        .'<path d="M24 26h16M24 33h16M24 40h10" fill="none" stroke="#78716c" stroke-width="2.2"/>'
    ),
    'user' => entityIconSvg(
        '<circle cx="32" cy="20" r="11" fill="#60a5fa"/>'
        .'<path fill="#1d4ed8" d="M12 56c1.2-15 8-22 20-22s18.8 7 20 22z"/>'
    ),
    'section' => entityIconSvg(
        '<rect x="8" y="10" width="34" height="22" rx="3" fill="#e4e4e7"/>'
        .'<rect x="22" y="28" width="34" height="22" rx="3" fill="#a1a1aa"/>'
    ),
    'page' => entityIconSvg(
        '<path fill="#f4f4f5" d="M16 8h22l12 12v36H16z"/>'
        .'<path fill="#a1a1aa" d="M38 8v12h12"/>'
        .'<path d="M22 30h18M22 37h18M22 44h11" fill="none" stroke="#52525b" stroke-width="2.2"/>'
    ),
    'capitalize' => entityIconSvg(
        '<ellipse cx="32" cy="48" rx="18" ry="6" fill="#475569"/>'
        .'<rect x="14" y="40" width="36" height="8" fill="#64748b" stroke="none"/>'
        .'<ellipse cx="32" cy="40" rx="18" ry="6" fill="#94a3b8"/>'
        .'<rect x="14" y="30" width="36" height="10" fill="#94a3b8" stroke="none"/>'
        .'<ellipse cx="32" cy="30" rx="18" ry="6" fill="#e2e8f0"/>'
    ),
    'coif' => entityIconSvg(
        '<path fill="#64748b" fill-rule="evenodd" d="M32 4 54 22v28c0 6-8 10-22 10S10 56 10 50V22zM22 30h20v16H22z"/>'
    ),
    'shield' => entityIconSvg(
        '<circle cx="32" cy="32" r="22" fill="#e2e8f0"/>'
        .'<circle cx="32" cy="32" r="8" fill="#6366f1"/>'
        .'<circle cx="32" cy="32" r="3" fill="#eef2ff" stroke="none"/>'
    ),
    'map' => entityIconSvg(
        '<path fill="#f3e2c4" d="M8 16 22 10l14 6 20-8v40L36 56 22 50 8 56z"/>'
        .'<path d="M22 12v38M36 16v38" fill="none" stroke="#92400e" stroke-width="2"/>'
    ),
    'dragodinde' => entityIconSvg(
        '<path fill="#6d28d9" d="M6 40c8-2 12 6 10 10-8-1-12-6-10-10z"/>'
        .'<ellipse cx="26" cy="42" rx="14" ry="10" fill="#7c3aed"/>'
        .'<path fill="#8b5cf6" d="M34 36c4-10 10-18 20-22 1 7-4 12-10 14-3 1-7 3-10 8z"/>'
        .'<ellipse cx="52" cy="16" rx="8" ry="6" fill="#c4b5fd"/>'
        .'<path fill="#fbbf24" d="M58 14h7l-3 5h-6z"/>'
        .'<path fill="#5b21b6" d="M16 34c6 1 12 6 10 12-7 0-14-5-10-12z"/>'
        .'<path d="M20 50v8M32 51v8" fill="none" stroke-width="3"/>'
        .'<circle cx="50" cy="15" r="1.6" fill="#1c1917" stroke="none"/>'
    ),
    'pet' => entityIconSvg(
        '<path fill="#fdba74" d="M20 30 12 14l14 8z"/>'
        .'<path fill="#fdba74" d="M44 30 52 14 38 22z"/>'
        .'<circle cx="32" cy="38" r="15" fill="#fb923c"/>'
        .'<circle cx="26" cy="37" r="2.2" fill="#1c1917" stroke="none"/>'
        .'<circle cx="38" cy="37" r="2.2" fill="#1c1917" stroke="none"/>'
        .'<path d="M28 44c1.6 2 6.4 2 8 0" fill="none"/>'
    ),
    'dice' => entityIconSvg(
        '<rect x="10" y="10" width="44" height="44" rx="8" fill="#e0e7ff"/>'
        .'<circle cx="22" cy="22" r="3.1" fill="#312e81" stroke="none"/>'
        .'<circle cx="42" cy="22" r="3.1" fill="#312e81" stroke="none"/>'
        .'<circle cx="32" cy="32" r="3.1" fill="#312e81" stroke="none"/>'
        .'<circle cx="22" cy="42" r="3.1" fill="#312e81" stroke="none"/>'
        .'<circle cx="42" cy="42" r="3.1" fill="#312e81" stroke="none"/>'
    ),
];

$written = 0;
foreach ($icons as $key => $svg) {
    $image = new Imagick;
    $image->setBackgroundColor(new ImagickPixel('transparent'));
    $image->setResolution(384, 384);
    $image->readImageBlob($svg);
    $image->setImageFormat('webp');
    $image->setOption('webp:lossless', 'true');
    $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
    if ($image->getImageWidth() !== 256 || $image->getImageHeight() !== 256) {
        $image->resizeImage(256, 256, Imagick::FILTER_LANCZOS, 1);
    }
    $path = $outDir.'/'.$key.'.webp';
    $image->writeImage($path);
    $image->clear();
    $written++;
    echo $path."\n";
}

echo "{$written} icônes écrites.\n";
