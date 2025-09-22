<?php

namespace App\Repositories;

use App\Models\PengajuanSurat;
use App\Repositories\Interfaces\ProsesPengajuanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProsesPengajuanRepository implements ProsesPengajuanRepositoryInterface
{
    /**
     * Mengambil pengajuan berdasarkan status dengan pagination
     *
     * @param array $statuses
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginatedByStatus(array $statuses, Request $request): LengthAwarePaginator
    {
        $query = PengajuanSurat::whereIn('status', $statuses)
            ->with(['user:id,name,nik,profile_photo_path', 'jenisSurat:id,nama_surat']);

        $this->applyFilters($query, $request);

        $perPage = $request->get('per_page', 10);
        
        return $query->latest()->paginate($perPage)->withQueryString();
    }

    /**
     * Memperbarui pengajuan surat
     *
     * @param PengajuanSurat $pengajuan
     * @param array $data
     * @return PengajuanSurat
     */
    public function update(PengajuanSurat $pengajuan, array $data): PengajuanSurat
    {
        $pengajuan->fill($data);
        $pengajuan->save();
        
        return $pengajuan->fresh();
    }

    /**
     * Menghapus pengajuan surat
     *
     * @param PengajuanSurat $pengajuan
     * @return bool
     */
    public function delete(PengajuanSurat $pengajuan): bool
    {
        return $pengajuan->delete();
    }

    /**
     * Mencari pengajuan berdasarkan ID dengan relasi
     *
     * @param int $id
     * @return PengajuanSurat|null
     */
    public function findByIdWithRelations(int $id): ?PengajuanSurat
    {
        return PengajuanSurat::with(['user', 'jenisSurat'])->find($id);
    }

    /**
     * Memperbarui timeline pengajuan
     *
     * @param PengajuanSurat $pengajuan
     * @param string $status
     * @param string $message
     * @return PengajuanSurat
     */
   public function updateTimeline(PengajuanSurat $pengajuan, string $status, string $message): PengajuanSurat
{
    $currentTimeline = $pengajuan->timeline ?? [];
    
    $currentTimeline[] = [
        'status' => $status,
        'message' => $message,
        'timestamp' => now()->toISOString(),
        'admin_id' => auth()->id(),
        'admin_name' => auth()->user()->name ?? 'Admin'
    ];

    $pengajuan->update([
        'timeline' => $currentTimeline,
        'updated_at' => now()
    ]);

    return $pengajuan;
}

    /**
     * Memperbarui file final
     *
     * @param PengajuanSurat $pengajuan
     * @param string|null $filePath
     * @return PengajuanSurat
     */
    public function updateFileFinal(PengajuanSurat $pengajuan, ?string $path): PengajuanSurat
{
    $pengajuan->update([
        'file_final' => $path,
        'updated_at' => now()
    ]);

    return $pengajuan;
}

    /**
     * Konfirmasi file final sebagai hasil akhir
     *
     * @param PengajuanSurat $pengajuan
     * @return PengajuanSurat
     */
   /**
 * Konfirmasi file final - pindahkan ke file_hasil dan ubah status
 */
public function confirmFinalFile(PengajuanSurat $pengajuan): PengajuanSurat
{
    // Pindahkan file_final ke file_hasil dan ubah status ke selesai
    $pengajuan->update([
        'file_hasil' => $pengajuan->file_final,
        'file_final' => null,
        'status' => 'selesai',
        'updated_at' => now()
    ]);

    return $pengajuan;
}


    /**
     * Mengambil data lampiran berdasarkan key
     *
     * @param PengajuanSurat $pengajuan
     * @param string $key
     * @return array|null
     */
    public function getLampiranByKey(PengajuanSurat $pengajuan, string $key): ?array
    {
        $fileData = $pengajuan->lampiran[$key] ?? null;

        if (!$fileData || !is_array($fileData) || !isset($fileData['path'])) {
            return null;
        }

        return $fileData;
    }

    /**
     * Menerapkan filter pada query
     *
     * @param mixed $query
     * @param Request $request
     * @return void
     */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('nomor_surat', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_surat')) {
            $query->whereHas('jenisSurat', function ($jenisQuery) use ($request) {
                $jenisQuery->where('nama_surat', $request->jenis_surat);
            });
        }
    }
}