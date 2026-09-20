<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Redirection post-création (Q9) : index par défaut, edit si `redirect_after_create=edit`
 * ou si `$defaultRedirect = 'edit'`. JSON (`Accept: application/json`) : `{ id, edit_url }`.
 */
trait RedirectsAfterEntityCreate
{
    protected function redirectAfterEntityStore(
        Request $request,
        Model $model,
        string $editRouteName,
        string $indexRouteName,
        string $successMessage,
        string $defaultRedirect = 'index',
    ): RedirectResponse|JsonResponse {
        $editUrl = route($editRouteName, $model);
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'id' => (int) $model->getKey(),
                'edit_url' => $editUrl,
                'message' => $successMessage,
            ], 201);
        }

        $target = $request->input('redirect_after_create', $defaultRedirect);
        if ($target === 'edit') {
            return redirect()->to($editUrl)
                ->with('success', $successMessage);
        }

        return redirect()->route($indexRouteName)
            ->with('success', $successMessage);
    }
}
