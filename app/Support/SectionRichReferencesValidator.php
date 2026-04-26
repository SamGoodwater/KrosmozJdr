<?php

namespace App\Support;

use App\Models\Characteristic;
use App\Models\Entity\Campaign;
use App\Models\Entity\Capability;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Scenario;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Valide les spans de références riches (`span.kref`) dans le HTML d’une section texte.
 */
class SectionRichReferencesValidator
{
    private const MAX_KREF_TITLE_LEN = 4096;

    private const MAX_KREF_LEGACY_PAYLOAD_LEN = 2048;

    /**
     * @param  array<int, string>  $allowedEntityTypes
     */
    public function __construct(
        private readonly array $allowedEntityTypes = [
            'campaigns', 'scenarios', 'spells', 'items', 'resources', 'consumables',
            'monsters', 'npcs', 'panoplies', 'capabilities', 'creatures',
        ],
    ) {}

    /**
     * @throws ValidationException
     */
    public function validate(string $html, ?User $user): void
    {
        if ($html === '') {
            return;
        }

        $errors = [];

        $dom = new \DOMDocument;
        $wrapped = '<?xml encoding="UTF-8"><div>'.$html.'</div>';
        $flags = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
        @$dom->loadHTML($wrapped, $flags);
        $xpath = new \DOMXPath($dom);
        /** @var \DOMNodeList $nodes */
        $nodes = $xpath->query('//span[contains(concat(" ", normalize-space(@class), " "), " kref ")]');

        $idx = 0;
        foreach ($nodes as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }
            $idx++;
            $decoded = $this->decodeKrefNode($node);
            if ($decoded === null) {
                $errors[] = "Référence #{$idx} : encodage invalide (attribut title).";

                continue;
            }
            $type = $this->normalizeType($decoded['type']);
            $payload = $decoded['payload'];

            try {
                match ($type) {
                    'characteristic' => $this->assertCharacteristic($payload),
                    'entity' => $this->assertEntity($payload, $user),
                    'page' => $this->assertPage($payload, $user),
                    'pageSection' => $this->assertPageSection($payload, $user),
                    default => throw new \InvalidArgumentException('Type de référence inconnu.'),
                };
            } catch (\InvalidArgumentException $e) {
                $errors[] = "Référence #{$idx} ({$type}) : ".$e->getMessage();
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'data.content' => $errors,
            ]);
        }
    }

    /**
     * @return array{type: string, payload: array<string, mixed>}|null
     */
    private function decodeKrefNode(\DOMElement $node): ?array
    {
        $title = trim((string) $node->getAttribute('title'));
        if ($title !== '') {
            if (mb_strlen($title) > self::MAX_KREF_TITLE_LEN) {
                return null;
            }
            $b64 = strtr($title, '-_', '+/');
            $pad = strlen($b64) % 4;
            if ($pad !== 0) {
                $b64 .= str_repeat('=', 4 - $pad);
            }
            $json = base64_decode($b64, true);
            if ($json === false) {
                return null;
            }
            $data = json_decode($json, true);
            if (! is_array($data) || ! isset($data['t'])) {
                return null;
            }
            $payload = isset($data['p']) && is_array($data['p']) ? $data['p'] : [];

            $type = $this->normalizeType((string) $data['t']);
            if (! $this->isSupportedType($type)) {
                return null;
            }

            return ['type' => $type, 'payload' => $payload];
        }

        $type = (string) $node->getAttribute('data-kref-type');
        $payloadRaw = (string) $node->getAttribute('data-kref-payload');
        if ($type === '') {
            return null;
        }
        if (mb_strlen($payloadRaw) > self::MAX_KREF_LEGACY_PAYLOAD_LEN) {
            return null;
        }
        $payload = json_decode($payloadRaw, true);

        $normalizedType = $this->normalizeType($type);
        if (! $this->isSupportedType($normalizedType)) {
            return null;
        }

        return ['type' => $normalizedType, 'payload' => is_array($payload) ? $payload : []];
    }

    private function normalizeType(string $type): string
    {
        $t = trim($type);

        return $t === 'page_section' ? 'pageSection' : $t;
    }

    private function isSupportedType(string $type): bool
    {
        return in_array($type, ['characteristic', 'entity', 'page', 'pageSection'], true);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertCharacteristic(array $payload): void
    {
        $key = isset($payload['key']) ? trim((string) $payload['key']) : '';
        if ($key === '') {
            throw new \InvalidArgumentException('clé caractéristique manquante.');
        }
        if (in_array($key, ['d', 'level'], true)) {
            return;
        }
        if (! Characteristic::query()->where('key', $key)->exists()) {
            throw new \InvalidArgumentException("caractéristique « {$key} » introuvable.");
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertEntity(array $payload, ?User $user): void
    {
        $entityType = isset($payload['entityType']) ? trim((string) $payload['entityType']) : '';
        $id = $payload['id'] ?? null;
        if ($entityType === '' || $id === null || $id === '') {
            throw new \InvalidArgumentException('entité incomplète (entityType / id).');
        }
        if (! in_array($entityType, $this->allowedEntityTypes, true)) {
            throw new \InvalidArgumentException("type d'entité « {$entityType} » non autorisé.");
        }
        $model = $this->resolveEntityModel($entityType, $id);
        if ($model === null) {
            throw new \InvalidArgumentException('entité introuvable.');
        }
        if (! Gate::forUser($user)->allows('view', $model)) {
            throw new \InvalidArgumentException("accès refusé à l'entité « {$entityType} » #{$id}.");
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertPage(array $payload, ?User $user): void
    {
        $slug = isset($payload['pageSlug']) ? trim((string) $payload['pageSlug']) : '';
        if ($slug === '') {
            throw new \InvalidArgumentException('slug de page manquant.');
        }
        $page = Page::query()->where('slug', $slug)->first();
        if ($page === null) {
            throw new \InvalidArgumentException("page « {$slug} » introuvable.");
        }
        if (! Gate::forUser($user)->allows('view', $page)) {
            throw new \InvalidArgumentException("accès refusé à la page « {$slug} ».");
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function assertPageSection(array $payload, ?User $user): void
    {
        $pageSlug = isset($payload['pageSlug']) ? trim((string) $payload['pageSlug']) : '';
        $sectionId = $payload['sectionId'] ?? null;
        $sectionSlug = isset($payload['sectionSlug']) ? trim((string) $payload['sectionSlug']) : '';

        if ($pageSlug === '') {
            throw new \InvalidArgumentException('section incomplète (pageSlug manquant).');
        }

        if ($sectionSlug !== '') {
            $section = Section::query()
                ->where('slug', $sectionSlug)
                ->whereHas('page', static fn ($q) => $q->where('slug', $pageSlug))
                ->with('page')
                ->first();
        } else {
            if ($sectionId === null || $sectionId === '') {
                throw new \InvalidArgumentException('section incomplète (sectionId ou sectionSlug requis).');
            }
            $section = Section::query()->with('page')->find((int) $sectionId);
        }

        if ($section === null || $section->page === null) {
            throw new \InvalidArgumentException('section introuvable.');
        }
        if ($section->page->slug !== $pageSlug) {
            throw new \InvalidArgumentException('page et section ne correspondent pas.');
        }
        if (! Gate::forUser($user)->allows('view', $section->page) || ! Gate::forUser($user)->allows('view', $section)) {
            throw new \InvalidArgumentException('accès refusé à cette section.');
        }
    }

    private function resolveEntityModel(string $entityType, mixed $id): ?Model
    {
        $id = is_numeric($id) ? (int) $id : $id;

        return match ($entityType) {
            'spells' => Spell::query()->find($id),
            'items' => Item::query()->find($id),
            'resources' => \App\Models\Entity\Resource::query()->find($id),
            'consumables' => Consumable::query()->find($id),
            'monsters' => Monster::query()->find($id),
            'npcs' => Npc::query()->find($id),
            'campaigns' => Campaign::query()->find($id),
            'scenarios' => Scenario::query()->find($id),
            'panoplies' => Panoply::query()->find($id),
            'capabilities' => Capability::query()->find($id),
            'creatures' => Creature::query()->find($id),
            default => null,
        };
    }
}
