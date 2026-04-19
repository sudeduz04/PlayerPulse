<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Player\StoreInjuryRequest;
use App\Services\InjuryService;
use Illuminate\Http\Request;

class InjuryController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected InjuryService $injuryService) {}

    public function store(StoreInjuryRequest $request, int $playerId)
    {
        $data = $request->validated();
        $data['player_id'] = $playerId;

        $this->injuryService->create($data, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Sakatlık kaydı başarıyla eklendi.');
    }

    public function update(StoreInjuryRequest $request, int $playerId, int $id)
    {
        $this->injuryService->update($id, $request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Sakatlık kaydı başarıyla güncellendi.');
    }

    public function destroy(Request $request, int $playerId, int $id)
    {
        $this->injuryService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $playerId)
            ->with('success', 'Sakatlık kaydı başarıyla silindi.');
    }
}
