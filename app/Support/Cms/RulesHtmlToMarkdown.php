<?php

declare(strict_types=1);

namespace App\Support\Cms;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Convertit le HTML des sections règles CMS (CommonMark / Tiptap) en Markdown.
 *
 * Les spans {@code .kref} redeviennent des shortcodes {@code [[kref:…]]}.
 *
 * @example
 * RulesHtmlToMarkdown::convert('<p><strong>Description</strong> : intro.</p>');
 */
final class RulesHtmlToMarkdown
{
    public static function convert(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $html = KrefSpanToShortcode::apply($html);
        $dom = self::loadFragment($html);
        $bodies = $dom->getElementsByTagName('body');
        $body = $bodies->item(0);
        if (! $body instanceof DOMNode) {
            return '';
        }

        $markdown = self::convertChildren($body);

        return self::normalizeMarkdown($markdown);
    }

    private static function loadFragment(string $html): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>'.$html.'</body></html>';
        @$dom->loadHTML($wrapped, LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);

        return $dom;
    }

    private static function convertChildren(DOMNode $node): string
    {
        $out = '';
        foreach ($node->childNodes as $child) {
            $out .= self::convertNode($child);
        }

        return $out;
    }

    private static function convertNode(DOMNode $node): string
    {
        if ($node instanceof DOMText) {
            return self::unescapeText($node->wholeText);
        }

        if (! $node instanceof DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);

        return match ($tag) {
            'p' => self::block(self::convertChildren($node)),
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6' => self::heading($tag, $node),
            'strong', 'b' => self::wrapInline(self::convertChildren($node), '**'),
            'em', 'i' => self::wrapInline(self::convertChildren($node), '*'),
            'del', 's' => self::wrapInline(self::convertChildren($node), '~~'),
            'code' => self::inlineCode($node),
            'pre' => self::fencedCode($node),
            'a' => self::link($node),
            'img' => self::image($node),
            'br' => "  \n",
            'hr' => "\n---\n\n",
            'blockquote' => self::blockquote($node),
            'ul' => self::listBlock($node, ordered: false),
            'ol' => self::listBlock($node, ordered: true),
            'li' => self::convertChildren($node),
            'table' => self::table($node),
            'thead', 'tbody', 'tfoot', 'tr', 'td', 'th', 'div', 'span', 'section', 'figure', 'figcaption' => self::convertChildren($node),
            default => self::convertChildren($node),
        };
    }

    private static function heading(string $tag, DOMElement $node): string
    {
        $level = (int) $tag[1];
        $text = trim(self::convertChildren($node));
        if ($text === '') {
            return '';
        }

        return str_repeat('#', $level).' '.$text."\n\n";
    }

    private static function block(string $inner): string
    {
        $inner = trim($inner);
        if ($inner === '') {
            return '';
        }

        return $inner."\n\n";
    }

    private static function wrapInline(string $inner, string $marker): string
    {
        $inner = trim($inner);
        if ($inner === '') {
            return '';
        }

        return $marker.$inner.$marker;
    }

    private static function inlineCode(DOMElement $node): string
    {
        if (self::parentTag($node) === 'pre') {
            return $node->textContent ?? '';
        }

        $text = str_replace("\n", ' ', trim($node->textContent ?? ''));
        if ($text === '') {
            return '';
        }

        $ticks = str_contains($text, '`') ? '``' : '`';

        return $ticks.$text.$ticks;
    }

    private static function fencedCode(DOMElement $node): string
    {
        $code = $node->textContent ?? '';
        $code = rtrim($code, "\n");

        return "```\n".$code."\n```\n\n";
    }

    private static function link(DOMElement $node): string
    {
        $label = trim(self::convertChildren($node));
        $href = trim((string) $node->getAttribute('href'));
        if ($label === '') {
            return $href;
        }
        if ($href === '' || $href === '#') {
            return $label;
        }

        return '['.$label.']('.$href.')';
    }

    private static function image(DOMElement $node): string
    {
        $alt = trim((string) $node->getAttribute('alt'));
        $src = trim((string) $node->getAttribute('src'));
        if ($src === '') {
            return $alt;
        }

        return '!['.$alt.']('.$src.')';
    }

    private static function blockquote(DOMElement $node): string
    {
        $inner = trim(self::convertChildren($node));
        if ($inner === '') {
            return '';
        }

        $lines = preg_split("/\r\n|\n|\r/", $inner) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $out[] = $line === '' ? '>' : '> '.$line;
        }

        return implode("\n", $out)."\n\n";
    }

    private static function listBlock(DOMElement $node, bool $ordered): string
    {
        $index = 1;
        $lines = [];
        foreach ($node->childNodes as $child) {
            if (! $child instanceof DOMElement || strtolower($child->tagName) !== 'li') {
                continue;
            }
            $item = trim(self::convertChildren($child));
            $item = preg_replace("/\n{3,}/", "\n\n", $item) ?? $item;
            $itemLines = preg_split("/\r\n|\n|\r/", $item) ?: [''];
            $bullet = $ordered ? $index.'. ' : '- ';
            $first = array_shift($itemLines) ?? '';
            $lines[] = $bullet.$first;
            foreach ($itemLines as $nested) {
                $lines[] = $nested === '' ? '' : '  '.$nested;
            }
            $index++;
        }

        if ($lines === []) {
            return '';
        }

        return implode("\n", $lines)."\n\n";
    }

    private static function table(DOMElement $node): string
    {
        $rows = [];
        foreach ($node->getElementsByTagName('tr') as $tr) {
            if (! $tr instanceof DOMElement) {
                continue;
            }
            $cells = [];
            foreach ($tr->childNodes as $cell) {
                if (! $cell instanceof DOMElement) {
                    continue;
                }
                $cellTag = strtolower($cell->tagName);
                if ($cellTag !== 'td' && $cellTag !== 'th') {
                    continue;
                }
                $text = trim(preg_replace('/\s+/u', ' ', self::convertChildren($cell)) ?? '');
                $text = str_replace('|', '\\|', $text);
                $cells[] = $text;
            }
            if ($cells !== []) {
                $rows[] = $cells;
            }
        }

        if ($rows === []) {
            return '';
        }

        $columnCount = max(array_map('count', $rows));
        $normalized = [];
        foreach ($rows as $row) {
            $normalized[] = array_pad($row, $columnCount, '');
        }

        $header = $normalized[0];
        $body = array_slice($normalized, 1);
        $align = array_fill(0, $columnCount, '---');

        $lines = [];
        $lines[] = '| '.implode(' | ', $header).' |';
        $lines[] = '| '.implode(' | ', $align).' |';
        foreach ($body as $row) {
            $lines[] = '| '.implode(' | ', $row).' |';
        }

        return implode("\n", $lines)."\n\n";
    }

    private static function parentTag(DOMElement $node): string
    {
        $parent = $node->parentNode;

        return $parent instanceof DOMElement ? strtolower($parent->tagName) : '';
    }

    private static function unescapeText(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private static function normalizeMarkdown(string $markdown): string
    {
        $markdown = str_replace("\r\n", "\n", $markdown);
        $markdown = preg_replace("/\n{3,}/", "\n\n", $markdown) ?? $markdown;

        return trim($markdown)."\n";
    }
}
