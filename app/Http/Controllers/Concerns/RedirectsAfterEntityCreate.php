<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Redirection post-création (Q9) : index par défaut, edit si `redirect_after_create=edit`.
 */
trait RedirectsAfterEntityCreate
{
  protected function redirectAfterEntityStore(
      Request $request,
      Model $model,
      string $editRouteName,
      string $indexRouteName,
      string $successMessage,
  ): RedirectResponse {
      if ($request->input('redirect_after_create') === 'edit') {
          return redirect()->route($editRouteName, $model)
              ->with('success', $successMessage);
      }

      return redirect()->route($indexRouteName)
          ->with('success', $successMessage);
  }
}
