<?php

namespace App\Http\Requests;

use App\Models\JenisSurat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePengajuanSuratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Hanya user yang sudah login yang bisa membuat pengajuan.
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'lampiran' => 'nullable|array', // Izinkan lampiran kosong jika tidak ada syarat
        ];

        // Temukan jenis surat untuk mendapatkan syaratnya
        $jenisSurat = JenisSurat::find($this->input('jenis_surat_id'));

        // Jika jenis surat ditemukan dan memiliki syarat, buat aturan validasi dinamis
        if ($jenisSurat && !empty($jenisSurat->syarat)) {
            foreach ($jenisSurat->syarat as $syarat) {
                $slug = Str::slug($syarat, '_');
                $rules["lampiran.{$slug}"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
            }
        }

        return $rules;
    }

    /**
     * Pesan error kustom untuk validasi.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'jenis_surat_id.required' => 'Anda harus memilih jenis surat.',
            'lampiran.*.required' => 'Dokumen ini wajib diunggah.',
            'lampiran.*.mimes' => 'File harus berformat PDF, JPG, atau PNG.',
            'lampiran.*.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
        ];
    }
}