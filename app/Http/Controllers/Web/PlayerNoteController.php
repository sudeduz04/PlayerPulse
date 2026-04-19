<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Player\StoreNoteRequest;
use App\Services\PlayerNoteService;
use Illuminate\Http\Request;

class PlayerNoteController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected PlayerNoteService $noteService) {}

    public function store(StoreNoteRequest $request, int $playerId)
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $this->noteService->create($data, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Not başarıyla eklendi.');
    }

    public function destroy(Request $request, int $playerId, int $id)
    {
        $this->noteService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Not başarıyla silindi.');
    }
}
