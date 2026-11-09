<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeOwner();
        $users = User::orderBy('name')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:owner,barista,admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:owner,barista,admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        // Jangan izinkan owner menonaktifkan dirinya sendiri jika hanya satu-satunya
        if ($user->id === auth()->id() && !$validated['is_active']) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri yang sedang aktif.');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        $this->authorizeOwner();

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $name = $user->name;

        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', "Pengguna {$name} berhasil dihapus dari sistem.");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('users.index')->with('error', "Pengguna {$name} tidak dapat dihapus karena telah memiliki riwayat data/transaksi di sistem. Anda dapat menonaktifkan status akunnya melalui menu Edit.");
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', "Gagal menghapus pengguna: " . $e->getMessage());
        }
    }

    private function authorizeOwner(): void
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner / Admin yang berwenang mengelola pengguna.');
        }
    }
}
