<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasRoutePrefix;
use App\Http\Requests\Web\Player\StorePlayerRequest;
use App\Http\Requests\Web\Player\UpdatePlayerRequest;
use App\Models\Positions;
use App\Models\Teams;
use App\Models\User;
use App\Services\PlayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlayerController extends Controller
{
    use HasRoutePrefix;

    public function __construct(protected PlayerService $playerService) {}

    public function index(Request $request)
    {
        $players = $this->playerService->list($request->query(), $request->user());

        return view('players.index', [
            'players' => $players,
            'teams' => $this->getTeamsForUser($request->user()),
            'positions' => Positions::all(),
            'routePrefix' => $this->routePrefix(),
            'filters' => $request->query(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $player = $this->playerService->show($id, $request->user());

        return view('players.show', [
            'player' => $player,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function create(Request $request)
    {
        return view('players.create', [
            'teams' => $this->getTeamsForUser($request->user()),
            'positions' => Positions::all(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function store(StorePlayerRequest $request)
    {
        $player = $this->playerService->create($request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $player->id)
            ->with('success', 'Oyuncu başarıyla oluşturuldu.');
    }

    public function edit(Request $request, int $id)
    {
        $player = $this->playerService->show($id, $request->user());

        return view('players.edit', [
            'player' => $player,
            'teams' => $this->getTeamsForUser($request->user()),
            'positions' => Positions::all(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    public function update(UpdatePlayerRequest $request, int $id)
    {
        $this->playerService->update($id, $request->validated(), $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.show', $id)
            ->with('success', 'Oyuncu başarıyla güncellendi.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->playerService->delete($id, $request->user());

        return redirect()
            ->route($this->routePrefix().'.players.index')
            ->with('success', 'Oyuncu başarıyla silindi.');
    }

    public function createAccount(Request $request, int $playerId)
    {
        abort_unless(
            $request->user()->isSuperAdmin() || $request->user()->isRole(User::ROLE_MANAGER),
            403,
            'Bu işlem için yönetici yetkisi gerekir.'
        );

        $player = $this->playerService->show($playerId, $request->user());

        if ($player->user_id) {
            return redirect()->back()->with('error', 'Bu oyuncunun zaten bir hesabı var.');
        }

        $email = Str::slug($player->first_name, '.').'.'.Str::slug($player->last_name, '.').'.'.$player->id.'@playerpulse.local';

        $user = User::create([
            'name' => $player->first_name,
            'surname' => $player->last_name,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'player',
            'status' => true,
        ]);

        $player->update(['user_id' => $user->id]);

        return redirect()->back()->with('success', 'Oyuncu hesabı başarıyla oluşturuldu. E-posta: '.$email.' / Şifre: password');
    }

    private function getTeamsForUser($user)
    {
        if ($user->isRole('super_admin')) {
            return Teams::all();
        }

        return Teams::whereIn('id', $user->getTeamIds())->get();
    }
}
