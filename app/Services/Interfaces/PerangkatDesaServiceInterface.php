<?php

// App/Services/Interfaces/PerangkatDesaServiceInterface.php
namespace App\Services\Interfaces;

use App\Models\PerangkatDesa;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface PerangkatDesaServiceInterface
{
    // ========== ADMIN CRUD METHODS ==========
    
    /**
     * Get all data for admin page
     */
    public function getAllData(Request $request);

    /**
     * Create new perangkat desa
     */
    public function createPerangkat(Request $request): PerangkatDesa;

    /**
     * Update existing perangkat desa
     */
    public function updatePerangkat(Request $request, PerangkatDesa $perangkatDesa): PerangkatDesa;

    /**
     * Delete perangkat desa
     */
    public function deletePerangkat(PerangkatDesa $perangkatDesa): void;

    // ========== PUBLIC DISPLAY METHODS ==========

    /**
     * Get active perangkat desa for public display
     */
    public function getActivePerangkatForPublic(): Collection;

    /**
     * Get structured data for public display
     */
    public function getStructuredDataForPublic(): array;

    /**
     * Get perangkat desa by jabatan
     */
    public function getPerangkatByJabatan(string $namaJabatan): ?PerangkatDesa;

    /**
     * Get kepala desa data
     */
    public function getKepalaDesa(): ?PerangkatDesa;
}