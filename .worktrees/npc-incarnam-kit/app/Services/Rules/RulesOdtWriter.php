<?php

declare(strict_types=1);

namespace App\Services\Rules;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Écrit un OpenDocument Text minimal à partir d’HTML CommonMark.
 *
 * Couvre titres, paragraphes, listes, tableaux, gras/italique, liens.
 * Suffisant pour relire et rééditer le livre dans LibreOffice.
 *
 * @example
 * (new RulesOdtWriter())->write('<h1>Titre</h1><p>Corps</p>', 'downloads/generated/livre.odt');
 */
class RulesOdtWriter
{
    /**
     * @return string Chemin relatif sur le disque public
     */
    public function write(string $html, string $relativePath): string
    {
        $disk = Storage::disk((string) config('game_downloads.disk', 'public'));
        $absolute = $disk->path($relativePath);
        $directory = dirname($absolute);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($absolute, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Impossible d’écrire le fichier ODT : '.$absolute);
        }

        $zip->addFromString('mimetype', 'application/vnd.oasis.opendocument.text');
        $zip->setCompressionName('mimetype', ZipArchive::CM_STORE);
        $zip->addFromString('META-INF/manifest.xml', $this->manifestXml());
        $zip->addFromString('meta.xml', $this->metaXml());
        $zip->addFromString('styles.xml', $this->stylesXml());
        $zip->addFromString('content.xml', $this->contentXml($html));
        $zip->close();

        return $relativePath;
    }

    private function contentXml(string $html): string
    {
        $body = $this->htmlToOdt($html);

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"'
            .' xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"'
            .' xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"'
            .' xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"'
            .' xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"'
            .' xmlns:xlink="http://www.w3.org/1999/xlink"'
            .' office:version="1.2">'
            .'<office:automatic-styles>'
            .'<style:style style:name="Tbold" style:family="text"><style:text-properties fo:font-weight="bold"/></style:style>'
            .'<style:style style:name="Titalic" style:family="text"><style:text-properties fo:font-style="italic"/></style:style>'
            .'</office:automatic-styles>'
            .'<office:body><office:text>'
            .$body
            .'</office:text></office:body></office:document-content>';
    }

    private function htmlToOdt(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>',
            LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementsByTagName('body')->item(0);
        if (! $root instanceof DOMNode) {
            return '<text:p>'.self::xml('Livre de règles').'</text:p>';
        }

        return $this->childrenToOdt($root);
    }

    private function childrenToOdt(DOMNode $node): string
    {
        $out = '';
        foreach ($node->childNodes as $child) {
            $out .= $this->nodeToOdt($child);
        }

        return $out;
    }

    private function nodeToOdt(DOMNode $node): string
    {
        if ($node instanceof DOMText) {
            $text = trim(preg_replace('/\s+/u', ' ', $node->textContent) ?? '');

            return $text === '' ? '' : '<text:p>'.self::xml($text).'</text:p>';
        }

        if (! $node instanceof DOMElement) {
            return '';
        }

        $tag = strtolower($node->tagName);

        return match ($tag) {
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6' => $this->heading($node, (int) substr($tag, 1)),
            'p' => '<text:p>'.$this->inline($node).'</text:p>',
            'blockquote' => '<text:p>'.$this->inline($node).'</text:p>',
            'pre', 'code' => '<text:p>'.self::xml(trim($node->textContent)).'</text:p>',
            'ul', 'ol' => $this->listToOdt($node, $tag === 'ol'),
            'table' => $this->tableToOdt($node),
            'hr' => '<text:p>―――</text:p>',
            'div', 'section', 'article', 'main', 'span' => $this->childrenToOdt($node),
            'br' => '<text:p></text:p>',
            default => $this->childrenToOdt($node) !== ''
                ? $this->childrenToOdt($node)
                : '<text:p>'.$this->inline($node).'</text:p>',
        };
    }

    private function heading(DOMElement $node, int $level): string
    {
        $level = max(1, min(6, $level));

        return '<text:h text:style-name="Heading_20_'.$level.'" text:outline-level="'.$level.'">'
            .$this->inline($node)
            .'</text:h>';
    }

    private function listToOdt(DOMElement $list, bool $ordered): string
    {
        $xml = '<text:list text:style-name="'.($ordered ? 'L1' : 'L2').'">';
        foreach ($list->childNodes as $item) {
            if (! $item instanceof DOMElement || strtolower($item->tagName) !== 'li') {
                continue;
            }
            $xml .= '<text:list-item><text:p>'.$this->inline($item).'</text:p></text:list-item>';
        }

        return $xml.'</text:list>';
    }

    private function tableToOdt(DOMElement $table): string
    {
        $xml = '<table:table>';
        foreach ($table->getElementsByTagName('tr') as $row) {
            if (! $row instanceof DOMElement) {
                continue;
            }
            $xml .= '<table:table-row>';
            foreach ($row->childNodes as $cell) {
                if (! $cell instanceof DOMElement) {
                    continue;
                }
                $name = strtolower($cell->tagName);
                if (! in_array($name, ['td', 'th'], true)) {
                    continue;
                }
                $xml .= '<table:table-cell office:value-type="string"><text:p>'.$this->inline($cell).'</text:p></table:table-cell>';
            }
            $xml .= '</table:table-row>';
        }

        return $xml.'</table:table>';
    }

    private function inline(DOMNode $node): string
    {
        $out = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMText) {
                $out .= self::xml($child->textContent);

                continue;
            }
            if (! $child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            $inner = $this->inline($child);
            $out .= match ($tag) {
                'strong', 'b' => '<text:span text:style-name="Tbold">'.$inner.'</text:span>',
                'em', 'i' => '<text:span text:style-name="Titalic">'.$inner.'</text:span>',
                'br' => '<text:line-break/>',
                'a' => $inner,
                'code' => $inner,
                'ul', 'ol', 'table' => '',
                default => $inner,
            };
        }

        return $out;
    }

    private function manifestXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">'
            .'<manifest:file-entry manifest:full-path="/" manifest:version="1.2" manifest:media-type="application/vnd.oasis.opendocument.text"/>'
            .'<manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>'
            .'<manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>'
            .'<manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>'
            .'</manifest:manifest>';
    }

    private function metaXml(): string
    {
        $title = self::xml('Krosmoz JDR — Livre de règles');
        $date = now()->toAtomString();

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"'
            .' xmlns:dc="http://purl.org/dc/elements/1.1/"'
            .' xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"'
            .' office:version="1.2">'
            .'<office:meta>'
            .'<dc:title>'.$title.'</dc:title>'
            .'<meta:creation-date>'.$date.'</meta:creation-date>'
            .'<dc:date>'.$date.'</dc:date>'
            .'</office:meta></office:document-meta>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"'
            .' xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"'
            .' xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"'
            .' xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"'
            .' office:version="1.2">'
            .'<office:styles>'
            .'<style:style style:name="Standard" style:family="paragraph">'
            .'<style:text-properties style:font-name="Liberation Serif" fo:font-size="11pt"/>'
            .'</style:style>'
            .'</office:styles>'
            .'</office:document-styles>';
    }

    private static function xml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
