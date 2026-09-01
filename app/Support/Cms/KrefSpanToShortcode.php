<?php

declare(strict_types=1);

namespace App\Support\Cms;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Convertit les spans HTML {@code .kref} en shortcodes Markdown {@code [[kref:…]]}.
 *
 * Inverse de {@see KrefShortcodeReplacer} / de l’import {@code pages:import-rules-toc}.
 *
 * @example
 * KrefSpanToShortcode::apply('<span class="kref" title="…">PA</span>');
 * // [[kref:characteristic:action_points_creature|PA]]
 */
final class KrefSpanToShortcode
{
    /** @var array<int, string> */
    private const ALLOWED_TYPES = ['characteristic', 'entity', 'page', 'pageSection'];

    public static function apply(string $html): string
    {
        $html = trim($html);
        if ($html === '' || ! str_contains($html, 'kref')) {
            return $html;
        }

        $dom = self::loadFragment($html);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//span[contains(concat(" ", normalize-space(@class), " "), " kref ")]');
        if ($nodes === false) {
            return $html;
        }

        $toReplace = [];
        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }
            $shortcode = self::shortcodeForSpan($node);
            if ($shortcode === null) {
                continue;
            }
            $toReplace[] = [$node, $shortcode];
        }

        foreach ($toReplace as [$node, $shortcode]) {
            $text = $dom->createTextNode($shortcode);
            $node->parentNode?->replaceChild($text, $node);
        }

        return self::innerHtml($dom);
    }

    private static function shortcodeForSpan(DOMElement $span): ?string
    {
        $decoded = self::decodeTitle((string) $span->getAttribute('title'))
            ?? self::decodeLegacyAttributes($span);
        if ($decoded === null) {
            return null;
        }

        $type = $decoded['type'];
        $target = self::payloadToTarget($type, $decoded['payload']);
        if ($target === null || $target === '') {
            return null;
        }

        $label = $decoded['label'] !== ''
            ? $decoded['label']
            : trim($span->textContent ?? '');
        if ($label === '') {
            $label = $target;
        }

        return '[[kref:'.$type.':'.$target.'|'.$label.']]';
    }

    /**
     * @return array{type: string, payload: array<string, mixed>, label: string}|null
     */
    public static function decodeTitle(string $title): ?array
    {
        $title = trim($title);
        if ($title === '' || strlen($title) > 4096) {
            return null;
        }

        $b64 = strtr($title, '-_', '+/');
        $pad = strlen($b64) % 4 === 0 ? '' : str_repeat('=', 4 - (strlen($b64) % 4));
        $json = base64_decode($b64.$pad, true);
        if (! is_string($json) || $json === '') {
            return null;
        }

        $data = json_decode($json, true);
        if (! is_array($data) || ! isset($data['t']) || ! is_string($data['t'])) {
            return null;
        }

        $type = $data['t'] === 'page_section' ? 'pageSection' : $data['t'];
        if (! in_array($type, self::ALLOWED_TYPES, true)) {
            return null;
        }

        $payload = isset($data['p']) && is_array($data['p']) ? $data['p'] : [];
        $label = isset($data['l']) && is_string($data['l']) ? trim($data['l']) : '';

        return ['type' => $type, 'payload' => $payload, 'label' => $label];
    }

    /**
     * @return array{type: string, payload: array<string, mixed>, label: string}|null
     */
    private static function decodeLegacyAttributes(DOMElement $span): ?array
    {
        $rawType = trim((string) $span->getAttribute('data-kref-type'));
        $type = $rawType === 'page_section' ? 'pageSection' : $rawType;
        if (! in_array($type, self::ALLOWED_TYPES, true)) {
            return null;
        }

        $payloadRaw = (string) $span->getAttribute('data-kref-payload');
        $payload = [];
        if ($payloadRaw !== '') {
            $decoded = json_decode($payloadRaw, true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        $label = trim($span->textContent ?? '');

        return ['type' => $type, 'payload' => $payload, 'label' => $label];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function payloadToTarget(string $type, array $payload): ?string
    {
        if ($type === 'characteristic') {
            $key = trim((string) ($payload['key'] ?? ''));

            return $key !== '' ? $key : null;
        }

        if ($type === 'page') {
            $slug = trim((string) ($payload['pageSlug'] ?? ''));

            return $slug !== '' ? $slug : null;
        }

        if ($type === 'pageSection') {
            $pageSlug = trim((string) ($payload['pageSlug'] ?? ''));
            if ($pageSlug === '') {
                return null;
            }
            $sectionSlug = trim((string) ($payload['sectionSlug'] ?? ''));
            if ($sectionSlug !== '') {
                return $pageSlug.'@'.$sectionSlug;
            }
            $sectionId = $payload['sectionId'] ?? '';
            if ($sectionId === '' || $sectionId === null) {
                return null;
            }

            return $pageSlug.':'.(string) $sectionId;
        }

        if ($type === 'entity') {
            $entityType = trim((string) ($payload['entityType'] ?? ''));
            $id = $payload['id'] ?? '';
            if ($entityType === '' || $id === '' || $id === null) {
                return null;
            }

            return $entityType.':'.(string) $id;
        }

        return null;
    }

    private static function loadFragment(string $html): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>'.$html.'</body></html>';
        @$dom->loadHTML($wrapped, LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);

        return $dom;
    }

    private static function innerHtml(DOMDocument $dom): string
    {
        $bodies = $dom->getElementsByTagName('body');
        $body = $bodies->item(0);
        if (! $body instanceof DOMNode) {
            return '';
        }

        $html = '';
        foreach ($body->childNodes as $child) {
            $html .= $dom->saveHTML($child);
        }

        return $html;
    }
}
