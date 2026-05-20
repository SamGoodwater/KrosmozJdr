<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Redirection post-création (Q9) : index par défaut, edit si `redirect_after_create=edit`
 * ou si `$defaultRedirect = 'edit'`.
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
    ): RedirectResponse {
        $target = $request->input('redirect_after_create', $defaultRedirect);
        if ($target === 'edit') {
            return redirect()->route($editRouteName, $model)
                ->with('success', $successMessage);
        }

        return redirect()->route($indexRouteName)
            ->with('success', $successMessage);
    }
}
