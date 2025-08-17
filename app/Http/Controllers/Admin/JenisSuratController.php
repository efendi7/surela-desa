<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JenisSuratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/JenisSurat/Index', [
            'jenisSurat' => JenisSurat::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_surat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'syarat' => 'nullable|array',
        ]);
        JenisSurat::create($validated);
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $validated = $request->validate([
            'nama_surat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'syarat' => 'nullable|array',
        ]);
        $jenisSurat->update($validated);
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisSurat $jenisSurat)
    {
        $jenisSurat->delete();
        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }
}
