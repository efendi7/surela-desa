<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WargaPengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan warga yang sedang login
     */
   /**
 * Menampilkan daftar pengaduan warga yang sedang login
 */
public function index()
{
    $user = Auth::user();
    
    $allPengaduan = Pengaduan::where('user_id', $user->id)
        ->with(['admin:id,name'])
        ->latest()
        ->get();

    return Inertia::render('Pengaduan/Index', [
        'pengaduanAktif' => $allPengaduan->whereIn('status', ['pending', 'diproses'])->values(),
        'riwayatPengaduan' => $allPengaduan->where('status', 'selesai')->values(),
        'isProfileComplete' => $user->is_profile_complete,
    ]);
}

    /**
     * Menyimpan pengaduan baru dari warga
     */
    /**
 * Menyimpan pengaduan baru dari warga
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'judul' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'foto_bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'alamat' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'kategori' => 'nullable|string|max:100',
    ]);

    $pathBukti = $request->hasFile('foto_bukti')
        ? $request->file('foto_bukti')->store('pengaduan/bukti', 'public')
        : null;

    $initialTimeline = [
        [
            'status' => 'pending', // Konsisten dengan database (lowercase)
            'message' => 'Laporan pengaduan dibuat oleh warga.',
            'timestamp' => now()->toISOString(),
        ]
    ];

    Pengaduan::create([
        'user_id'    => Auth::id(),
        'judul'      => $validated['judul'],
        'deskripsi'  => $validated['deskripsi'],
        'foto_bukti' => $pathBukti,
        'alamat'     => $validated['alamat'] ?? null,
        'latitude'   => $validated['latitude'] ?? null,
        'longitude'  => $validated['longitude'] ?? null,
        'kategori'   => $validated['kategori'] ?? null,
        'status'     => 'pending', // Konsisten dengan database (lowercase)
        'timeline'   => $initialTimeline,
    ]);

    return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dikirim.');
}


    /**
     * Menampilkan detail pengaduan milik warga yang sedang login
     */
    /**
 * Menampilkan detail pengaduan milik warga yang sedang login
 */
public function show(Pengaduan $pengaduan)
{
    // Pastikan hanya bisa melihat pengaduan milik sendiri
    if ($pengaduan->user_id !== Auth::id()) {
        abort(403, 'Anda tidak berhak mengakses pengaduan ini.');
    }

    // Load semua relasi yang dibutuhkan
    $pengaduan->load(['user:id,name,nik', 'admin:id,name']);

    // Debug timeline
    \Log::info('Timeline data:', [
        'timeline' => $pengaduan->timeline,
        'timeline_type' => gettype($pengaduan->timeline),
        'is_array' => is_array($pengaduan->timeline)
    ]);

    // Pastikan timeline adalah array dan memiliki format yang benar
    if (!is_array($pengaduan->timeline) || empty($pengaduan->timeline)) {
        // Jika timeline kosong atau bukan array, buat timeline default
        $pengaduan->timeline = [
            [
                'status' => $pengaduan->status,
                'message' => 'Laporan pengaduan dibuat oleh warga.',
                'timestamp' => $pengaduan->created_at->toISOString(),
            ]
        ];
    }

    return response()->json($pengaduan);
}

    /**
     * Menghapus pengaduan milik warga yang sedang login
     */
    public function destroy(Pengaduan $pengaduan)
    {
        if ($pengaduan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus laporan ini.');
        }

        // Hapus foto bukti
        if (Storage::exists($pengaduan->foto_bukti)) {
            Storage::delete($pengaduan->foto_bukti);
        }

        // Hapus juga foto proses jika ada
        if ($pengaduan->foto_proses && Storage::exists($pengaduan->foto_proses)) {
            Storage::delete($pengaduan->foto_proses);
        }

        $pengaduan->delete();

        return redirect()->route('pengaduan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Menampilkan foto bukti atau proses milik warga yang sedang login
     */
    public function viewFoto(Pengaduan $pengaduan, $type)
    {
        // Pastikan hanya bisa melihat foto pengaduan milik sendiri
        if ($pengaduan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses foto ini.');
        }

        $filePath = null;

        if ($type === 'bukti' && $pengaduan->foto_bukti) {
            $filePath = $pengaduan->foto_bukti;
        } elseif ($type === 'proses' && $pengaduan->foto_proses) {
            $filePath = $pengaduan->foto_proses;
        }

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'Foto tidak ditemukan.');
        }

        // Return file langsung tanpa download
        return response()->file(Storage::path($filePath));
    }

    /**
     * Download foto untuk warga
     */
    public function downloadFoto(Pengaduan $pengaduan, $type)
    {
        // Validasi akses - hanya bisa download foto milik sendiri
        if ($pengaduan->user_id !== Auth::id()) {
            abort(403);
        }

        $filePath = null;
        $fileName = '';

        if ($type === 'bukti' && $pengaduan->foto_bukti) {
            $filePath = $pengaduan->foto_bukti;
            $fileName = 'foto_bukti_' . $pengaduan->id . '_' . time() . '.jpg';
        } elseif ($type === 'proses' && $pengaduan->foto_proses) {
            $filePath = $pengaduan->foto_proses;
            $fileName = 'foto_proses_' . $pengaduan->id . '_' . time() . '.jpg';
        }

        if (!$filePath || !Storage::exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::download($filePath, $fileName);
    }

    /**
     * API untuk mendapatkan pengaduan warga
     */
    public function apiIndex()
    {
        $user = Auth::user();
        
        $pengaduan = Pengaduan::where('user_id', $user->id)
            ->with(['admin:id,name'])
            ->latest()
            ->get();

        return response()->json($pengaduan);
    }
}