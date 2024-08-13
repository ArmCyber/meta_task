<?php

namespace App\Services;

use App\Exceptions\UploadException;
use App\Http\Requests\UploadRequest;
use App\Models\Asset;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UploadService
{
    /**
     * Upload a chunk.
     *
     * @throws FileNotFoundException
     * @throws UploadException
     */
    public function uploadChunk(UploadRequest $request): Asset
    {
        $asset = $this->getAssetInstance($request);
        $offset = $request->input('offset');

        if ($offset != $asset->offset) {
            throw new UploadException('Unable to upload file. The uploaded part is corrupted.');
        }

        $chunk = $request->file('chunk')->get();
        File::append($asset->path, $chunk, true);
        $asset->offset += strlen($chunk);

        if ($asset->offset === $asset->total_size) {
            $this->completeUpload($asset);
        }

        $asset->save();

        return $asset;
    }

    /**
     * Create a new asset instance.
     *
     * @param UploadRequest $request
     * @return Asset
     */
    private function createAssetInstance(UploadRequest $request): Asset
    {
        return new Asset([
            'name' => $request->input('name'),
            'filename' => $this->generateFilename(),
            'total_size' => $request->input('total_size'),
        ]);
    }

    /**
     * Load or create the asset instance.
     *
     * @param UploadRequest $request
     * @return Asset
     */
    private function getAssetInstance(UploadRequest $request): Asset
    {
        if ($request->has('init')) {
            return $this->createAssetInstance($request);
        }

        return Asset::query()->findOrFail($request->input('id'));
    }

    /**
     * Generate a filename.
     *
     * @param string $extension
     * @return string
     */
    private function generateFilename(string $extension = 'tmp'): string
    {
        return Str::random(32) . ".$extension";
    }

    /**
     * Finalize the upload.
     *
     * @param Asset $asset
     * @return void
     */
    private function completeUpload(Asset $asset): void
    {
        $tmpPath = $asset->path;
        $extension = File::guessExtension($tmpPath);
        $asset->filename = $this->generateFilename($extension);
        $asset->is_completed = true;
        File::move($tmpPath, $asset->path);
    }
}
