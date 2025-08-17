<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Pengajuan/Index', [
            'jenisSuratTersedia' => JenisSurat::all(['id', 'nama_surat', 'syarat']),
            'riwayatPengajuan' => PengajuanSurat::where('user_id', $user->id)
                                    ->with('jenisSurat:id,nama_surat')
                                    ->latest()
                                    ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
        ]);

        // cari nomor terakhir bulan ini
        $lastNumber = PengajuanSurat::whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)
                        ->max('increment_nomor');

        $newNumber = $lastNumber ? $lastNumber + 1 : 1;

        // format nomor surat: 001/SKD/08/2025
        $nomorSurat = str_pad($newNumber, 3, '0', STR_PAD_LEFT)
                    . '/SKD/' . date('m') . '/' . date('Y');

        // Snapshot data pemohon saat ini
        $dataPemohon = [
            'nama'   => $user->name,
            'nik'    => $user->nik,
            'email'  => $user->email,
            'phone'  => $user->phone,
            'address'=> $user->address,
        ];

        PengajuanSurat::create([
            'user_id'         => $user->id,
            'jenis_surat_id'  => $request->jenis_surat_id,
            'data_pemohon'    => $dataPemohon,
            'status'          => 'pending',
            'lampiran'        => null,
            'increment_nomor' => $newNumber,
            'nomor_surat'     => $nomorSurat,
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan surat berhasil dikirim.');
    }

    public function destroy(PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($pengajuanSurat->status !== 'pending') {
            return redirect()->route('pengajuan.index')->with('error', 'Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }

        $pengajuanSurat->delete();

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibatalkan.');
    }
}
