<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Player\StorePlayerRequest;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Models\User;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlayerController extends BaseController
{
    public function __construct(protected PlayerService $playerService) {}

    public function index(Request $request): JsonResponse
    {
        $players = $this->playerService->list($request->query(), $request->user());

        return $this->sendResponse($players, 'Players retrieved successfully.');
    }

    public function store(StorePlayerRequest $request): JsonResponse
    {
        $player = $this->playerService->create($request->validated(), $request->user());

        return $this->sendResponse($player, 'Player created successfully.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $player = $this->playerService->show($id, $request->user());

        return $this->sendResponse($player, 'Player retrieved successfully.');
    }

    public function update(UpdatePlayerRequest $request, int $id): JsonResponse
    {
        $player = $this->playerService->update($id, $request->validated(), $request->user());

        return $this->sendResponse($player, 'Player updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->playerService->delete($id, $request->user());

        return $this->sendResponse(null, 'Player deleted successfully.');
    }

    public function createAccount(Request $request, int $id): JsonResponse
    {
        abort_unless(
            $request->user()->isSuperAdmin() || $request->user()->isRole(User::ROLE_MANAGER),
            403,
            'Bu islem icin yonetici yetkisi gerekir.'
        );

        $player = $this->playerService->show($id, $request->user());

        if ($player->user_id) {
            return $this->sendError('This player already has an account.', 422);
        }

        $email = Str::slug($player->first_name, '.').'.'.Str::slug($player->last_name, '.').'.'.$player->id.'@playerpulse.local';

        $user = User::create([
            'name' => $player->first_name,
            'surname' => $player->last_name,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => User::ROLE_PLAYER,
            'status' => true,
        ]);

        $player->update(['user_id' => $user->id]);

        return $this->sendResponse([
            'player' => $player->fresh(['team', 'position', 'user']),
            'user' => $user,
            'temporary_password' => 'password',
        ], 'Player account created successfully.', 201);
    }
}
