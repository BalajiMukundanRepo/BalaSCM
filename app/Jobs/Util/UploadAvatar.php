<?php

namespace App\Jobs\Util;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadAvatar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;

    protected int $entityId;

    protected string $entityClass;

    public function __construct(string $filePath, int $entityId, string $entityClass)
    {
        $this->filePath = $filePath;
        $this->entityId = $entityId;
        $this->entityClass = $entityClass;
    }

    public function handle(): void
    {
        $disk = config('filesystems.default', 'local');

        if (! file_exists($this->filePath)) {
            Log::error('Avatar file not found', ['path' => $this->filePath]);

            return;
        }

        $filename = 'avatars/'.md5(time().$this->entityId).'.'.pathinfo($this->filePath, PATHINFO_EXTENSION);

        Storage::disk($disk)->put($filename, file_get_contents($this->filePath));

        $url = Storage::disk($disk)->url($filename);

        $entity = $this->entityClass::find($this->entityId);

        if ($entity) {
            $entity->avatar = $url;
            $entity->save();
        }

        if (file_exists($this->filePath) && str_starts_with($this->filePath, storage_path('app/tmp'))) {
            unlink($this->filePath);
        }

        Log::info('Avatar uploaded successfully', ['entity' => $this->entityClass, 'id' => $this->entityId]);
    }
}
