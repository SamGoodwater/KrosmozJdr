<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateUserDataExportJob;
use App\Models\DataSubjectRequest;
use App\Models\PrivacyAuditLog;
use App\Models\PrivacyExport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPrivacyController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $requests = DataSubjectRequest::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(25)
            ->get()
            ->map(fn (DataSubjectRequest $row) => [
                'id' => $row->id,
                'type' => $row->type,
                'status' => $row->status,
                'requested_at' => $row->requested_at?->toIso8601String(),
                'processed_at' => $row->processed_at?->toIso8601String(),
                'expires_at' => $row->expires_at?->toIso8601String(),
                'meta' => $row->meta ?? [],
            ])
            ->values();

        $exports = PrivacyExport::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(25)
            ->get()
            ->map(function (PrivacyExport $row): array {
                $isReady = $row->status === PrivacyExport::STATUS_READY;
                $notExpired = $row->expires_at === null || $row->expires_at->isFuture();
                $downloadUrl = null;
                if ($isReady && $notExpired) {
                    $downloadUrl = URL::temporarySignedRoute(
                        'user.privacy.exports.download',
                        now()->addHours(24),
                        ['privacyExport' => $row->id]
                    );
                }

                return [
                    'id' => $row->id,
                    'status' => $row->status,
                    'created_at' => $row->created_at?->toIso8601String(),
                    'expires_at' => $row->expires_at?->toIso8601String(),
                    'download_url' => $downloadUrl,
                    'meta' => $row->meta ?? [],
                ];
            })
            ->values();

        return Inertia::render('Pages/user/Privacy', [
            'requests' => $requests,
            'exports' => $exports,
        ]);
    }

    public function requestExport(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $pendingExport = PrivacyExport::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [PrivacyExport::STATUS_PENDING, PrivacyExport::STATUS_PROCESSING])
            ->latest('id')
            ->first();

        if ($pendingExport !== null && ! config('privacy.export_sync')) {
            return back()->withErrors([
                'privacy' => 'Un export est déjà en cours de génération.',
            ]);
        }

        if ($pendingExport !== null && config('privacy.export_sync')) {
            $dsr = DataSubjectRequest::query()->find($pendingExport->data_subject_request_id);
            GenerateUserDataExportJob::dispatchSync($pendingExport->id, $dsr?->id);

            return back()->with('success', 'Ton export est prêt. Tu peux le télécharger ci-dessous.');
        }

        $dataSubjectRequest = DataSubjectRequest::query()->create([
            'user_id' => $user->id,
            'type' => DataSubjectRequest::TYPE_EXPORT,
            'status' => DataSubjectRequest::STATUS_PENDING,
            'requested_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
        ]);

        $privacyExport = PrivacyExport::query()->create([
            'user_id' => $user->id,
            'data_subject_request_id' => $dataSubjectRequest->id,
            'status' => PrivacyExport::STATUS_PENDING,
            'path' => sprintf('privacy-exports/user-%d/export-%s.zip', $user->id, Str::uuid()->toString()),
        ]);

        if (config('privacy.export_sync')) {
            GenerateUserDataExportJob::dispatchSync($privacyExport->id, $dataSubjectRequest->id);

            return back()->with('success', 'Ton export est prêt. Tu peux le télécharger ci-dessous.');
        }

        GenerateUserDataExportJob::dispatch($privacyExport->id, $dataSubjectRequest->id);

        return back()->with('success', 'Ta demande d’export a été prise en compte. Le fichier sera bientôt disponible.');
    }

    public function downloadExport(Request $request, PrivacyExport $privacyExport): StreamedResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $user = $request->user();
        if ((int) $privacyExport->user_id !== (int) $user->id) {
            abort(403);
        }
        if ($privacyExport->status !== PrivacyExport::STATUS_READY) {
            abort(404);
        }
        if ($privacyExport->expires_at !== null && $privacyExport->expires_at->isPast()) {
            abort(410);
        }
        if (! Storage::disk('local')->exists($privacyExport->path)) {
            abort(404);
        }

        $privacyExport->update([
            'downloaded_at' => now(),
        ]);

        PrivacyAuditLog::log(
            PrivacyAuditLog::ACTION_EXPORT_DOWNLOADED,
            $privacyExport->user_id,
            $user->id,
            ['export_id' => $privacyExport->id],
            $request->ip(),
            Str::limit((string) $request->userAgent(), 500)
        );

        return Storage::disk('local')->download(
            $privacyExport->path,
            sprintf('krosmozjdr-export-rgpd-%d.zip', $privacyExport->id)
        );
    }

    public function requestDeletion(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe est incorrect.',
            ]);
        }

        if ($user->is_system ?? false) {
            return back()->withErrors([
                'privacy' => 'Ce compte ne peut pas être supprimé.',
            ]);
        }

        if ($user->role === User::ROLE_SUPER_ADMIN) {
            $superAdminCount = User::query()->where('role', User::ROLE_SUPER_ADMIN)->count();
            if ($superAdminCount <= 1) {
                return back()->withErrors([
                    'privacy' => 'Impossible de supprimer le dernier super administrateur.',
                ]);
            }
        }

        $withdrawalDays = config('privacy.erasure_withdrawal_days', 7);
        $expiresAt = now()->addDays($withdrawalDays);

        $dsr = DataSubjectRequest::query()->create([
            'user_id' => $user->id,
            'type' => DataSubjectRequest::TYPE_ERASURE,
            'status' => DataSubjectRequest::STATUS_PENDING,
            'requested_at' => now(),
            'expires_at' => $expiresAt,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
        ]);

        PrivacyAuditLog::log(
            PrivacyAuditLog::ACTION_ERASURE_REQUESTED,
            $user->id,
            $user->id,
            ['request_id' => $dsr->id],
            $request->ip(),
            Str::limit((string) $request->userAgent(), 500)
        );

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = $withdrawalDays > 0
            ? "Ta demande de suppression a été enregistrée. Tu disposes de {$withdrawalDays} jours pour changer d'avis. Si tu te reconnectes avant le " . $expiresAt->translatedFormat('d/m/Y') . ", tu pourras annuler la demande et récupérer ton compte. Passé ce délai, un administrateur pourra effectuer la suppression définitive."
            : 'Ta demande de suppression a été enregistrée. Un administrateur pourra effectuer la suppression définitive.';

        return redirect()->route('home')->with('success', $message);
    }

    /**
     * Annule une demande de suppression en cours (récupération du compte).
     */
    public function cancelDeletionRequest(Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);

        $dsr = DataSubjectRequest::query()
            ->where('user_id', $user->id)
            ->where('type', DataSubjectRequest::TYPE_ERASURE)
            ->whereIn('status', [DataSubjectRequest::STATUS_PENDING, DataSubjectRequest::STATUS_PROCESSING])
            ->latest('id')
            ->first();

        if (! $dsr) {
            return back()->with('info', 'Aucune demande de suppression en cours.');
        }

        $dsr->update(['status' => DataSubjectRequest::STATUS_CANCELLED]);

        PrivacyAuditLog::log(
            PrivacyAuditLog::ACTION_ERASURE_CANCELLED,
            $user->id,
            $user->id,
            ['request_id' => $dsr->id],
            $request->ip(),
            Str::limit((string) $request->userAgent(), 500)
        );

        return back()->with('success', 'Ta demande de suppression a été annulée. Ton compte est conservé.');
    }
}

