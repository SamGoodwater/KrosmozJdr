<?php

namespace App\Support;

use App\Services\FileService;

/**
 * Validation avancée des valeurs de payload template.
 */
class SectionTemplatePayloadValidator
{
    public static function validateImageSource(?string $src): ?string
    {
        $value = trim((string) $src);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\s*(javascript|data):/i', $value)) {
            return 'La source média utilise un protocole non autorisé.';
        }

        if (!preg_match('/^(https?:\/\/|\/)/i', $value)) {
            return 'La source média doit être une URL HTTP(S) ou un chemin local commençant par /.';
        }

        $escaped = array_map(
            static fn (string $ext): string => preg_quote($ext, '/'),
            FileService::getAllowedExtensions()
        );
        $allowedPattern = implode('|', $escaped);

        if (!preg_match('/\.(' . $allowedPattern . ')(\?.*)?$/i', $value)) {
            return 'La source média doit cibler un type de fichier autorisé.';
        }

        return null;
    }

    public static function validateVideoSource(?string $videoType, ?string $src): ?string
    {
        $type = strtolower(trim((string) $videoType));
        $value = trim((string) $src);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\s*(javascript|data):/i', $value)) {
            return 'La source vidéo utilise un protocole non autorisé.';
        }

        if ($type === 'youtube') {
            $isYoutubeId = (bool) preg_match('/^[A-Za-z0-9_-]{6,20}$/', $value);
            $isYoutubeUrl = (bool) preg_match('#^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[A-Za-z0-9_-]{6,20}#i', $value);
            if (!$isYoutubeId && !$isYoutubeUrl) {
                return 'La source YouTube doit être un ID valide ou une URL YouTube valide.';
            }
            return null;
        }

        if ($type === 'vimeo') {
            $isVimeoId = (bool) preg_match('/^[0-9]{6,12}$/', $value);
            $isVimeoUrl = (bool) preg_match('#^(https?:\/\/)?(www\.)?vimeo\.com\/[0-9]{6,12}#i', $value);
            if (!$isVimeoId && !$isVimeoUrl) {
                return 'La source Vimeo doit être un ID numérique ou une URL Vimeo valide.';
            }
            return null;
        }

        if ($type === 'direct') {
            if (!preg_match('/^(https?:\/\/|\/)/i', $value)) {
                return 'La source vidéo directe doit être une URL HTTP(S) ou un chemin local commençant par /.';
            }

            if (!preg_match('/\.(mp4|webm|ogg|mov|m3u8)(\?.*)?$/i', $value)) {
                return 'La source vidéo directe doit cibler un format vidéo supporté (mp4, webm, ogg, mov, m3u8).';
            }
        }

        return null;
    }

    /**
     * Valide l'URL source d'une section legal_markdown (same-origin uniquement).
     * Accepte uniquement les chemins relatifs pour éviter SSRF et injections.
     *
     * @param string|null $sourceUrl
     * @return string|null Message d'erreur ou null si valide
     */
    public static function validateLegalMarkdownSourceUrl(?string $sourceUrl): ?string
    {
        $value = trim((string) $sourceUrl);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\s*(javascript|data|file):/i', $value)) {
            return 'La source du document utilise un protocole non autorisé.';
        }

        if (!preg_match('/^\/[a-zA-Z0-9\/_.-]+$/', $value)) {
            return 'La source doit être un chemin relatif same-origin (ex: /storage/legal/cgu.md).';
        }

        if (str_contains($value, '..')) {
            return 'Le chemin ne doit pas contenir de séquence "..".';
        }

        return null;
    }
}

