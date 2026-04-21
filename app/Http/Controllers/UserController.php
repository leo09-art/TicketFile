<?php

namespace App\Http\Controllers;

use App\Models\Counter;
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
            ->with('counters')
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($sort === 'created_asc', fn($q) => $q->orderBy('created_at'))
            ->when($sort !== 'created_asc', fn($q) => $q->orderByDesc('created_at'))
            ->get();

        $counters = Counter::with('agent')->orderBy('name')->get();

        return view('pages.admin.users', [
            'users' => $users,
            'selectedRole' => $role,
            'sort' => $sort,
            'counters' => $counters,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'agent', 'usager'])],
            'counter_id' => ['nullable', 'exists:counters,id'],
        ]);

        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Vous ne pouvez pas retirer votre propre rôle admin.');
        }

        $counterId = $validated['counter_id'] ?? null;
        unset($validated['counter_id']);

        $user->update($validated);

        if ($user->role !== 'agent') {
            Counter::where('agent_user_id', $user->id)->update(['agent_user_id' => null]);
        } else {
            Counter::where('agent_user_id', $user->id)->update(['agent_user_id' => null]);

            if (!empty($counterId)) {
                $counter = Counter::findOrFail($counterId);

                if ($counter->agent_user_id && $counter->agent_user_id !== $user->id) {
                    return back()->with('error', 'Ce guichet est déjà assigné à un autre agent.');
                }

                $counter->update(['agent_user_id' => $user->id]);
            }
        }

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
