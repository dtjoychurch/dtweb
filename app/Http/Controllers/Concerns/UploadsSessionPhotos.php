<?php

namespace App\Http\Controllers\Concerns;

use App\Models\DiscipleshipSession;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

trait UploadsSessionPhotos
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    protected function storeSessionPhotos(array $files, DiscipleshipSession $session): void
    {
        foreach ($files as $file) {
            $relativePath = $file->store('session-photos/'.$session->id, 'uploads');

            $session->photos()->create([
                'uploaded_by' => Auth::id(),
                'path' => 'uploads/'.$relativePath,
            ]);
        }
    }
}
