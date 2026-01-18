<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('users.index', [
            'users' => $users,
            'title' => 'Users',
            'subtitle' => 'Management',
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'user' => new User(),
            'title' => 'Users',
            'subtitle' => 'Create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'title' => 'Users',
            'subtitle' => 'Edit',
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate($this->updateRules($user));

        if (empty($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted.');
    }

    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    private function updateRules(User $user): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
