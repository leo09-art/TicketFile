<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role');
        $sort = $request->get('sort', 'created_desc');

        $users = User::query()
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($sort === 'created_asc', fn($q) => $q->orderBy('created_at'))
            ->when($sort !== 'created_asc', fn($q) => $q->orderByDesc('created_at'))
            ->get();

        return view('pages.admin.users', [
            'users' => $users,
            'selectedRole' => $role,
            'sort' => $sort,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'agent', 'usager'])],
        ]);

        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle admin.');
        }

        $user->update($validated);

        return back()->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}
