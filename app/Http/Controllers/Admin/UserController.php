<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        // Ambil semua pengguna kecuali admin yang sedang login
        $users = User::withTrashed() // withTrashed() agar pengguna yang dibekukan tetap terlihat
            ->where('id', '!=', auth()->id())
            ->latest()
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'nik' => $user->nik,
                    // 'deleted_at' akan berisi tanggal jika user dibekukan, jika tidak maka null
                    'is_frozen' => $user->trashed(),
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'nik' => 'required|numeric|digits:16|unique:users',
            'phone' => 'nullable|string|max:15',
            'role' => ['required', Rule::in(['admin', 'warga'])],
            'address' => 'nullable|string',
            // Tambahkan validasi untuk field lain jika diperlukan
        ]);

        User::create($validatedData);

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengguna yang ada.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'nik' => ['required', 'numeric', 'digits:16', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:15',
            'role' => ['required', Rule::in(['admin', 'warga'])],
            'address' => 'nullable|string',
        ]);

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = $validatedData['password'];
        }

        // Hapus password dari array agar tidak diupdate jika kosong
        unset($validatedData['password']);

        $user->update($validatedData);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Membekukan pengguna (Soft Delete).
     */
    public function destroy(User $user)
    {
        // Pastikan admin tidak bisa membekukan dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat membekukan akun Anda sendiri.');
        }

        $user->delete();

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil dibekukan.');
    }

    /**
     * Mengaktifkan kembali pengguna yang dibekukan.
     * Kita perlu route tambahan untuk ini.
     */
    public function restore($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();

        return redirect()->back()->with('success', 'Pengguna berhasil diaktifkan kembali.');
    }
}