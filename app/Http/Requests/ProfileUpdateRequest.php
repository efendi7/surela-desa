<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            
            // Profile photo validation
            'profile_photo' => ['nullable', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // 2MB max
            
            // Personal information validation
            'nik' => ['nullable', 'string', 'digits:16', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:15'],
            'address' => ['nullable', 'string', 'max:1000'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', Rule::in(['Laki-laki', 'Perempuan'])],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'agama' => ['nullable', 'string', 'max:50'],
            'status_perkawinan' => ['nullable', 'string', Rule::in(['Belum Menikah', 'Menikah', 'Cerai'])],
            'kewarganegaraan' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'profile_photo.mimes' => 'Foto profil harus berupa file gambar (JPG, JPEG, PNG, atau GIF).',
            'profile_photo.max' => 'Ukuran foto profil maksimal 2MB.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar di sistem.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
        ];
    }
}