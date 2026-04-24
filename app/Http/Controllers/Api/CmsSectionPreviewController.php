<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

/**
 * Aperçu HTML contrôlé d'une section CMS (popover mentions).
 */
class CmsSectionPreviewController extends Controller
{
    private const MAX_HTML_LENGTH = 4000;

    public function show(Section $section): JsonResponse
    {
        $this->authorize('view', $section);

        $raw = (string) ($section->data['content'] ?? '');
        if ($raw === '') {
            return response()->json([
                'canView' => true,
                'title' => $section->title,
                'html' => '',
            ]);
        }

        $clean = Purifier::clean($raw, 'section_text');
        if (strlen($clean) > self::MAX_HTML_LENGTH) {
            $clean = Str::limit($clean, self::MAX_HTML_LENGTH, '…');
        }

        return response()->json([
            'canView' => true,
            'title' => $section->title,
            'html' => $clean,
        ]);
    }
}
