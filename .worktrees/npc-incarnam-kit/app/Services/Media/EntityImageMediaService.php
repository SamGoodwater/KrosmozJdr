<?php

namespace App\Services\Media;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Centralise l’attachement de fichiers image via Spatie Media Library et les URLs des conversions.
 */
class EntityImageMediaService
{
    /**
     * Règles de validation communes pour un champ fichier image (upload multipart).
     *
     * @param  int|null  $maxKb  Taille max en Ko ; défaut config `krosmoz_images.max_upload_kb`.
     * @return array<string, mixed>
     */
    public function imageUploadRules(?int $maxKb = null): array
    {
        $maxKb = $maxKb ?? (int) config('krosmoz_images.max_upload_kb', 5120);

        return [
            'required',
            'file',
            'max:'.$maxKb,
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $value instanceof UploadedFile) {
                    $fail(__('validation.image', ['attribute' => $attribute]));

                    return;
                }
                $mime = strtolower((string) $value->getMimeType());
                $ext = strtolower((string) $value->getClientOriginalExtension());
                $vectorOk = in_array($mime, ['image/svg+xml'], true) || $ext === 'svg';
                $rasterMimes = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'image/bmp', 'image/tiff', 'image/x-ms-bmp',
                    'image/avif', 'image/heic', 'image/heif',
                    'image/x-icon', 'image/vnd.microsoft.icon',
                ];
                if ($vectorOk || in_array($mime, $rasterMimes, true)) {
                    return;
                }
                // Fallback : extension connue comme image
                if (FileService::isImagePath('x.'.$ext)) {
                    return;
                }
                $fail(__('validation.mimetypes', ['attribute' => $attribute, 'values' => 'image/*']));
            },
        ];
    }

    /**
     * Attache un média depuis la requête, avec nommage {@see HasMediaCustomNaming} si défini sur le modèle.
     *
     * @param  string  $collection  Nom de collection Spatie (ex. images, icons, avatars).
     * @param  string|null  $syncColumn  Colonne à mettre à jour avec l’URL du média principal (ex. image, icon).
     */
    public function attachFromRequest(
        HasMedia $model,
        Request $request,
        string $fieldName,
        string $collection,
        ?string $syncColumn = null,
        ?int $maxKb = null,
    ): Media {
        $rules = [$fieldName => $this->imageUploadRules($maxKb)];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        /** @var UploadedFile $upload */
        $upload = $request->file($fieldName);
        $ext = $upload->getClientOriginalExtension() ?: 'png';

        $adder = $model->addMediaFromRequest($fieldName);
        if (method_exists($model, 'getMediaFileNameForCollection')) {
            $customName = $model->getMediaFileNameForCollection($collection, $ext);
            if ($customName !== null && $customName !== '') {
                $adder->usingFileName($customName);
            }
        }

        $media = $adder->toMediaCollection($collection);

        if ($syncColumn !== null && $syncColumn !== '') {
            $model->update([$syncColumn => $media->getUrl()]);
        }

        return $media;
    }

    /**
     * Charge utile JSON pour le frontend : URL principale + conversions thumb / webp si générées.
     *
     * @return array{url: string, thumb_url: string|null, webp_url: string|null, path: string, media_id: int}
     */
    public function mediaPayload(Media $media): array
    {
        return [
            'url' => $media->getUrl(),
            'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
            'webp_url' => $media->hasGeneratedConversion('webp') ? $media->getUrl('webp') : null,
            'path' => $media->getPath(),
            'media_id' => (int) $media->getKey(),
        ];
    }
}
