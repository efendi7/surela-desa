<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Dapatkan akses ke semua shared props
const page = usePage();

// Buat computed property untuk URL logo agar lebih bersih dan reaktif
const logoUrl = computed(() => {
    // Cek apakah profilDesa dan properti logo ada dan tidak kosong
    if (page.props.profilDesa && page.props.profilDesa.logo) {
        // Asumsi: 'logo' berisi path file seperti 'logos/nama-file.png'
        // File yang diupload via Laravel biasanya disimpan di folder 'storage/app/public'
        // dan diakses melalui URL '/storage/...'
        return `/storage/${page.props.profilDesa.logo}`;
    }
    
    // Jika tidak ada logo, gunakan gambar default sebagai fallback
    return '/Navbar/logo-desa.png';
});
</script>

<template>
  <img 
    :src="logoUrl" 
    alt="Logo Aplikasi" 
    class="h-10 w-auto"
  />
</template>