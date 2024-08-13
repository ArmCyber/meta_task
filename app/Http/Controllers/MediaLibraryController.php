<?php

namespace App\Http\Controllers;

use App\Exceptions\UploadException;
use App\Http\Requests\UploadRequest;
use App\Models\Asset;
use App\Services\UploadService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MediaLibraryController extends Controller
{
    /**
     * The index action.
     *
     * @return View
     */
    public function index(): View
    {
        return view('media.index');
    }

    /**
     * Get Asset Offset.
     *
     * @param $assetId
     * @return JsonResponse
     */
    public function getAssetOffset($assetId): JsonResponse
    {
        $offset = Asset::query()->where('id', $assetId)->valueOrFail('offset');

        return response()->json([
            'offset' => $offset,
        ]);
    }

    /**
     * The chunk upload action.
     *
     * @param UploadRequest $request
     * @param UploadService $uploadService
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function upload(UploadRequest $request, UploadService $uploadService): JsonResponse
    {
        try {
            $asset = $uploadService->uploadChunk($request);
        } catch (UploadException $exception) {
            throw ValidationException::withMessages([
                'general' => $exception->getMessage()
            ]);
        }

        $data = $asset->only('id', 'is_completed');
        return response()->json($data);
    }
}
