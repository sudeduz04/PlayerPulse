<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $this->sendResponse($query->latest()->paginate(15), 'Users retrieved successfully.');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->sendResponse($user, 'User created successfully.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->loadMissing(['player.team', 'player.position', 'teams']);

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $this->sendResponse($user->fresh(), 'User updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        User::findOrFail($id)->delete();

        return $this->sendResponse(null, 'User deleted successfully.');
    }
}
