<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\UploadsSessionPhotos;
use App\Http\Requests\StoreDiscipleshipSessionPhotoRequest;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\DiscipleshipSessionPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DiscipleshipSessionPhotoController extends Controller
{
    use UploadsSessionPhotos;

    public function store(StoreDiscipleshipSessionPhotoRequest $request, DiscipleshipRelationship $relationship, DiscipleshipSession $session): RedirectResponse
    {
        abort_unless($session->relationship_id === $relationship->id, 404);

        $this->authorize('create', [DiscipleshipSessionPhoto::class, $session]);

        $this->storeSessionPhotos($request->file('photos', []), $session);

        return redirect()
            ->route('discipleship.sessions.show', [$relationship, $session])
            ->with('status', '已新增照片。');
    }

    public function destroy(DiscipleshipRelationship $relationship, DiscipleshipSession $session, DiscipleshipSessionPhoto $photo): RedirectResponse
    {
        abort_unless($photo->session_id === $session->id && $session->relationship_id === $relationship->id, 404);

        $this->authorize('delete', $photo);

        Storage::disk('uploads')->delete(str_replace('uploads/', '', $photo->path));
        $photo->delete();

        return redirect()
            ->route('discipleship.sessions.show', [$relationship, $session])
            ->with('status', '已刪除照片。');
    }
}
