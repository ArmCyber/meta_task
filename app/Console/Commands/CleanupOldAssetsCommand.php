<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupOldAssetsCommand extends Command
{
    private const CHUNK_SIZE = 100;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old-assets:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old assets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Asset::query()
            ->where('updated_at', '<', now()->subHour())
            ->where('is_completed', false)
            ->chunkById(self::CHUNK_SIZE, function ($assets) {
                foreach ($assets as $asset) {
                    $path = $asset->path;
                    if (File::exists($path)) {
                        $path->delete();
                    }
                    $asset->delete();
                }
            });

        return self::SUCCESS;
    }
}
