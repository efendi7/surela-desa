<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Pengajuan;   // Pastikan Anda memiliki model ini
use App\Models\ProfilDesa;  // Pastikan Anda memiliki model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Menggunakan File facade untuk membaca direktori view
use Inertia\Inertia;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class JenisSuratController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen jenis surat.
     * Juga mengambil daftar file template yang tersedia.
     */
     public function index()
    {
        $templateOptions = [];
        $templatePath = resource_path('views/templates');

        if (File::isDirectory($templatePath)) {
            $files = File::files($templatePath);
            $templateOptions = array_map(function ($file) {
                return str_replace('.blade.php', '.docx', $file->getFilename());
            }, $files);
        }

        // Ambil semua jenis surat dan lampirkan jumlah pengajuan aktif
        $jenisSuratList = JenisSurat::all()->map(function ($jenisSurat) {
            $jenisSurat->active_pengajuan_count = $jenisSurat->pengajuan()
                ->whereIn('status', ['pending', 'diproses'])
                ->count();
            return $jenisSurat;
        });

        return Inertia::render('Admin/JenisSurat/Index', [
            'jenisSurat' => $jenisSuratList,
            'templateOptions' => $templateOptions,
        ]);
    }

    /**
     * Menyimpan jenis surat baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_surat' => 'required|string|max:255|unique:jenis_surats',
            'deskripsi' => 'nullable|string',
            'template_surat' => 'nullable|string|max:255',
            'syarat' => 'nullable|array',
        ]);

        JenisSurat::create($validated);
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    /**
     * Memperbarui data jenis surat yang ada.
     */
   public function update(Request $request, JenisSurat $jenisSurat)
    {
        $validated = $request->validate([
            'nama_surat' => 'required|string|max:255|unique:jenis_surats,nama_surat,' . $jenisSurat->id,
            'deskripsi' => 'nullable|string',
            'template_surat' => 'nullable|string|max:255',
            'syarat' => 'nullable|array',
        ]);

        $jenisSurat->update($validated);
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    /**
     * Menghapus jenis surat dari database.
     */
     public function destroy(JenisSurat $jenisSurat)
    {
        $pengajuanAktifCount = $jenisSurat->pengajuan()
            ->whereIn('status', ['pending', 'diproses'])
            ->count();

        if ($pengajuanAktifCount > 0) {
            return redirect()->route('admin.jenis-surat.index')->with('error', "Gagal menghapus! Masih ada {$pengajuanAktifCount} pengajuan aktif yang menggunakan jenis surat ini.");
        }

        $jenisSurat->delete();
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }
    /**
     * Men-generate dan men-download surat DOCX berdasarkan data pengajuan.
     *
     * @param int $pengajuanId ID dari data pengajuan surat
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function generateSurat($pengajuanId)
    {
        try {
            // 1. Ambil semua data yang diperlukan dari database
            $pengajuan = Pengajuan::with('user', 'jenisSurat')->findOrFail($pengajuanId);
            $profilDesa = ProfilDesa::firstOrFail(); // Gagal jika profil desa tidak ada
            $tanggal = now()->locale('id_ID')->translatedFormat('d F Y');

            // 2. Tentukan nama file template Blade dari data jenis surat
            // Hapus ekstensi .docx untuk mendapatkan nama view-nya
            $templateName = str_replace('.docx', '', $pengajuan->jenisSurat->template_surat);

            // 3. Validasi apakah file template .blade.php benar-benar ada
            if (!$templateName || !view()->exists("templates.{$templateName}")) {
                return back()->with('error', 'Template surat (.blade.php) tidak ditemukan di server.');
            }

            // 4. Render view Blade menjadi string HTML dengan data yang sudah disiapkan
            $html = view("templates.{$templateName}", compact('pengajuan', 'profilDesa', 'tanggal'))->render();

            // 5. Inisialisasi PHPWord dan konversi HTML ke Word
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

            // 6. Siapkan nama file dan path sementara untuk menyimpan file .docx
            // Menggunakan slug untuk nama file yang bersih
            $cleanNamaSurat = \Illuminate\Support\Str::slug($pengajuan->jenisSurat->nama_surat);
            $cleanNamaUser = \Illuminate\Support\Str::slug($pengajuan->user->name);
            $fileName = "{$cleanNamaSurat}-{$cleanNamaUser}-{$pengajuan->id}.docx";
            $tempFilePath = storage_path('app/temp/' . $fileName);

            // Pastikan direktori 'temp' ada di dalam storage/app
            if (!File::isDirectory(storage_path('app/temp'))) {
                File::makeDirectory(storage_path('app/temp'));
            }

            // 7. Simpan file Word ke path sementara
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFilePath);

            // 8. Kirim file sebagai response download dan hapus file sementara setelah terkirim
            return response()->download($tempFilePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Tangani jika terjadi error (misal: data tidak ditemukan, dll)
            // Log error ini untuk debugging
            \Illuminate\Support\Facades\Log::error('Gagal generate surat: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat surat. Silakan coba lagi.');
        }
    }
}
