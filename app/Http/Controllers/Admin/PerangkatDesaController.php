<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerangkatDesa;
use App\Services\Interfaces\PerangkatDesaServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PerangkatDesaController extends Controller
{
    protected PerangkatDesaServiceInterface $perangkatDesaService;

    public function __construct(PerangkatDesaServiceInterface $perangkatDesaService)
    {
        $this->perangkatDesaService = $perangkatDesaService;
    }

    public function index(Request $request)
    {
        $data = $this->perangkatDesaService->getAllData($request);
        
        return Inertia::render('Admin/PerangkatDesa/Index', $data);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store Request Data: ', $request->all());
            
            $perangkat = $this->perangkatDesaService->createPerangkat($request);
            
            return redirect()->back()
                ->with('success', 'Data perangkat desa berhasil ditambahkan');
                
        } catch (\Exception $e) {
            Log::error('Error creating perangkat: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menambahkan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, PerangkatDesa $perangkatDesa)
    {
        try {
            Log::info('Update Request Data: ', $request->all());
            Log::info('Update Method: ' . $request->method());
            Log::info('Has _method: ' . ($request->has('_method') ? $request->input('_method') : 'none'));
            
            // Karena frontend menggunakan method POST dengan _method=PUT
            // Kita perlu memastikan request diperlakukan sebagai PUT
            if ($request->isMethod('POST') && $request->input('_method') === 'put') {
                $request->setMethod('PUT');
            }
            
            $perangkat = $this->perangkatDesaService->updatePerangkat($request, $perangkatDesa);
            
            return redirect()->back()
                ->with('success', 'Data perangkat desa berhasil diperbarui');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error: ', $e->errors());
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Error updating perangkat: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(PerangkatDesa $perangkatDesa)
    {
        try {
            $this->perangkatDesaService->deletePerangkat($perangkatDesa);
            
            return redirect()->back()
                ->with('success', 'Data perangkat desa berhasil dihapus');
                
        } catch (\Exception $e) {
            Log::error('Error deleting perangkat: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}