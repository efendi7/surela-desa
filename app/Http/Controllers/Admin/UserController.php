<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index(): Response
    {
        $users = User::withTrashed()
            ->where('id', '!=', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
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
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'nik' => 'required|numeric|digits:16|unique:users',
            'phone' => 'nullable|string|max:15',
            'role' => ['required', Rule::in(['admin', 'warga'])],
            'address' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'pekerjaan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => ['nullable', Rule::in(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])],
            'kewarganegaraan' => 'nullable|string|max:50',
        ]);

        if ($request->file('profile_photo')) {
            $validatedData['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        User::create($validatedData);

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail spesifik pengguna.
     */
    public function show(User $user): Response
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Menampilkan form untuk mengedit pengguna.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
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
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            'nik' => ['required', 'numeric', 'digits:16', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:15',
            'role' => ['required', Rule::in(['admin', 'warga'])],
            'address' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'pekerjaan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => ['nullable', Rule::in(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])],
            'kewarganegaraan' => 'nullable|string|max:50',
        ]);
        
        // Gunakan $request->post() untuk menghindari masalah saat form dikirim dengan method PUT/PATCH
        $userDataToUpdate = $request->except(['password', 'password_confirmation', 'profile_photo']);

        if ($request->file('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $userDataToUpdate['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        if ($request->filled('password')) {
            $userDataToUpdate['password'] = Hash::make($request->input('password'));
        }

        $user->update($userDataToUpdate);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Membekukan pengguna (Soft Delete).
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat membekukan akun Anda sendiri.');
        }

        $user->delete();

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil dibekukan.');
    }

    /**
     * Mengaktifkan kembali pengguna yang dibekukan.
     */
    public function restore($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();

        return redirect()->back()->with('success', 'Pengguna berhasil diaktifkan kembali.');
    }
}