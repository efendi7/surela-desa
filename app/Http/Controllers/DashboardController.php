<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\PengajuanSurat;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $submissions = PengajuanSurat::where('user_id', $user->id)
            ->with('jenisSurat:id,nama_surat')
            ->orderBy('created_at', 'desc')
            ->get();

        // --- PERUBAHAN DI SINI ---
        // Kita pecah dan tambahkan statistik baru
        $statistics = [
            'totalSubmissions' => $submissions->count(),
            'pendingSubmissions'   => $submissions->where('status', 'pending')->count(), // Hanya hitung 'pending'
            'processedSubmissions' => $submissions->where('status', 'diproses')->count(),// BARU: Hitung 'diproses'
            'completedSubmissions' => $submissions->where('status', 'selesai')->count(),
            'rejectedSubmissions'  => $submissions->where('status', 'ditolak')->count(),  // BARU: Hitung 'ditolak'
        ];
        // --- AKHIR PERUBAHAN ---

        return Inertia::render('Dashboard', [
            'userStatistics' => $statistics,
            'userSubmissions' => $submissions,
        ]);
    }
}