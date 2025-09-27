<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\User;
use App\Notifications\BeritaBaruNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // ✨ penting
use Inertia\Inertia;

class BeritaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Berita/Index', [
            'beritas' => Berita::with('user:id,name')->latest()->paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        // === VALIDASI ===
        $request->validate([
            'type'        => ['required', Rule::in(['berita', 'pengumuman'])],
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file'        => [
                Rule::requiredIf($request->type === 'pengumuman'),
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120' // 5MB
            ],
            'published_at'=> 'nullable|date',
        ]);

        // === UPLOAD IMAGE ===
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('berita_images', 'public');
        }

        // === UPLOAD FILE PDF ===
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('pengumuman_files', 'public');
        }

        // === SIMPAN DB ===
        $berita = Berita::create([
            'user_id'      => auth()->id(),
            'type'         => $request->type,
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . time(),
            'content'      => $request->content,
            'image'        => $imagePath,
            'file_path'    => $filePath,
            'published_at' => $request->published_at ?? now(),
        ]);

        // === KIRIM NOTIF KE WARGA ===
        $wargas = User::where('role', 'warga')->get();
        Notification::send($wargas, new BeritaBaruNotification($berita));

        return redirect()->route('admin.berita.index')->with('success', 'Postingan berhasil dibuat.');
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'type'        => ['required', Rule::in(['berita', 'pengumuman'])],
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file'        => [
                Rule::requiredIf($request->type === 'pengumuman'),
                'nullable',
                'file',
                'mimes:pdf',
                'max:5120'
            ],
            'published_at'=> 'nullable|date',
        ]);

        // === UPDATE IMAGE ===
        $imagePath = $berita->image;
        if ($request->hasFile('image')) {
            if ($berita->image) {
                Storage::disk('public')->delete($berita->image);
            }
            $imagePath = $request->file('image')->store('berita_images', 'public');
        }

        // === UPDATE FILE PDF ===
        $filePath = $berita->file_path;
        if ($request->hasFile('file')) {
            if ($berita->file_path) {
                Storage::disk('public')->delete($berita->file_path);
            }
            $filePath = $request->file('file')->store('pengumuman_files', 'public');
        }

        $berita->update([
            'type'         => $request->type,
            'title'        => $request->title,
            'slug'         => Str::slug($request->title) . '-' . time(),
            'content'      => $request->content,
            'image'        => $imagePath,
            'file_path'    => $filePath,
            'published_at' => $request->published_at,
        ]);

        return redirect()->route('admin.berita.index')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->image) {
            Storage::disk('public')->delete($berita->image);
        }
        if ($berita->file_path) {
            Storage::disk('public')->delete($berita->file_path);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Postingan berhasil dihapus.');
    }
}
