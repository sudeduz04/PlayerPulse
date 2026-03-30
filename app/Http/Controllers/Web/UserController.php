<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\User\StoreUserRequest;
use App\Http\Requests\Web\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
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

        $users = $query->latest()->paginate(15);

        return view('users.index', [
            'users' => $users,
            'filters' => $request->query(),
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return redirect()
            ->route('super_admin.users.show', $user->id)
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    public function show(int $id)
    {
        $user = User::findOrFail($id);

        $linkedPlayer = null;
        $assignedTeams = collect();

        if ($user->isRole('player')) {
            $linkedPlayer = $user->player?->load(['team', 'position']);
        }

        if ($user->isRole('coach') || $user->isRole('manager')) {
            $assignedTeams = $user->teams()->withCount('players')->get();
        }

        return view('users.show', compact('user', 'linkedPlayer', 'assignedTeams'));
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('super_admin.users.show', $user->id)
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('super_admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
