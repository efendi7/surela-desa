<?php

namespace App\Policies;

use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengajuanSuratPolicy
{
    use HandlesAuthorization;

    /**
     * Cek apakah user boleh menghapus atau membatalkan pengajuan.
     */
   // app/Policies/PengajuanSuratPolicy.php
public function view(User $user, PengajuanSurat $pengajuanSurat)
{
    return $user->id === $pengajuanSurat->user_id;
}

public function delete(User $user, PengajuanSurat $pengajuanSurat)
{
    return $user->id === $pengajuanSurat->user_id;
}

    /**
     * Cek apakah user boleh mengunduh file hasil dari pengajuan.
     */
    public function download(User $user, PengajuanSurat $pengajuan): bool
    {
        // User hanya bisa mengunduh file dari pengajuan miliknya sendiri.
        return $user->id === $pengajuan->user_id;
    }
}