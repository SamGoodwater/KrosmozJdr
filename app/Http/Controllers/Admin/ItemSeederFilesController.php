<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Seeder\Item\ItemSeederExporter;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use App\Services\Seeder\Item\ItemSeederImporter;
use Illuminate\Http\RedirectResponse;

/**
 * Aller-retour entre les équipements en base et les fichiers JSON versionnés du seeder.
 *
 * Opérations courtes (quelques dizaines d'items) : exécution synchrone, pas de file d'attente.
 * L'export écrit dans le dépôt, il est donc réservé au développement.
 */
class ItemSeederFilesController extends Controller
{
    public function export(ItemSeederExporter $exporter): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        if (! app()->environment(['local', 'testing'])) {
            return $this->back('error', 'L’export vers le dépôt n’est disponible qu’en développement.');
        }

        $result = $exporter->export(states: ['auto'], prune: true);

        $message = sprintf(
            '%d objet(s) auto écrit(s) dans %s.',
            count($result['written']),
            ItemSeederFileRepository::RELATIVE_ROOT
        );
        if ($result['removed'] !== []) {
            $message .= sprintf(' %d fichier(s) obsolète(s) supprimé(s).', count($result['removed']));
        }
        if ($result['skipped'] !== []) {
            $message .= sprintf(' %d objet(s) ignoré(s) (clé ou type manquant).', count($result['skipped']));
        }

        return $this->back('success', $message);
    }

    public function import(ItemSeederImporter $importer): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        if (app()->environment('production')) {
            return $this->back('error', 'Le rejeu des fichiers en base n’est pas disponible en production.');
        }

        $result = $importer->import();

        $message = sprintf(
            '%d objet(s) créé(s), %d mis à jour depuis les fichiers du dépôt.',
            count($result['created']),
            count($result['updated'])
        );
        if ($result['skipped'] !== []) {
            $message .= sprintf(' %d fichier(s) ignoré(s).', count($result['skipped']));
        }

        return $this->back('success', $message);
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(request()->user()?->isInteractiveSuperAdmin() === true, 403);
    }

    private function back(string $key, string $message): RedirectResponse
    {
        return redirect()
            ->route('admin.content.ia-generation.edit')
            ->with($key, $message);
    }
}
