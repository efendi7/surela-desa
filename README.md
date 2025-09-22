# Sistem Pelayanan Desa Digital

Proyek ini adalah sebuah aplikasi web berbasis **Laravel** dan **Vue.js** (menggunakan Inertia.js) yang dirancang untuk menjadi **Sistem Informasi dan Pelayanan Desa Digital**. Tujuannya adalah untuk memodernisasi dan menyederhanakan layanan administrasi serta komunikasi antara pemerintah desa dengan warganya.

---

## 🎯 Tujuan Aplikasi

Aplikasi ini bertujuan untuk:
1.  **Meningkatkan Efisiensi:** Mengurangi proses manual dalam pengajuan surat dan layanan administrasi lainnya.
2.  **Meningkatkan Transparansi:** Memberikan warga kemampuan untuk melacak status pengajuan surat dan pengaduan mereka secara online.
3.  **Memudahkan Akses Informasi:** Menyediakan platform terpusat untuk informasi penting desa seperti profil, sejarah, visi-misi, dan struktur organisasi.
4.  **Mempercepat Respons:** Memfasilitasi kanal pengaduan warga yang terstruktur agar dapat ditangani lebih cepat oleh perangkat desa.

---

## ✨ Fitur Utama

Aplikasi ini memiliki dua peran utama: **Warga** dan **Admin** (Pemerintah Desa), dengan fitur yang disesuaikan untuk masing-masing peran.

### Untuk Publik & Warga

* **🌐 Halaman Profil Desa:** Pengunjung dapat melihat informasi umum desa seperti sejarah, visi & misi, dan struktur organisasi.
* **👤 Manajemen Akun:** Warga dapat mendaftar, login, dan mengelola profil pribadi mereka.
* **✉️ Pengajuan Surat Online:** Warga dapat mengajukan berbagai jenis surat (misal: Surat Keterangan Tidak Mampu, Surat Pengantar, dll) secara online, mengunggah lampiran yang diperlukan, dan melacak status pengajuannya.
* **📥 Unduh Surat:** Setelah pengajuan disetujui dan surat selesai dibuat oleh admin, warga dapat mengunduh surat final langsung dari akun mereka.
* **📢 Sistem Pengaduan:** Warga bisa membuat laporan atau pengaduan mengenai fasilitas atau layanan desa, lengkap dengan bukti foto, dan memantau progres penanganannya melalui timeline.

### Untuk Admin (Pemerintah Desa)

* **📊 Dashboard Analitik:** Admin memiliki dashboard utama yang menampilkan statistik penting seperti jumlah pengajuan surat, jumlah pengaduan, dan aktivitas terkini.
* **🗂️ Manajemen Pengajuan Surat:** Admin dapat melihat, memverifikasi, memproses, dan menyelesaikan semua pengajuan surat dari warga. Admin juga bisa mengunggah surat yang sudah jadi untuk diunduh oleh warga.
* **🗣️ Manajemen Pengaduan:** Admin dapat meninjau, mengubah status (misalnya: "diterima", "diproses", "selesai"), dan menanggapi pengaduan dari warga, termasuk mengunggah foto sebagai bukti tindak lanjut.
* **📝 Manajemen Konten Desa:** Admin dapat mengelola konten halaman profil desa, informasi perangkat desa, dan jenis-jenis surat yang tersedia untuk diajukan.
* **👥 Manajemen Pengguna:** Admin memiliki akses untuk mengelola data pengguna yang terdaftar di sistem.

---

## 🛠️ Cara Instalasi

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan pengembangan lokal.

### Prasyarat

* PHP 8.2 atau lebih tinggi
* Composer 2
* Node.js & NPM
* Database (misalnya: MySQL, PostgreSQL)

### Langkah-langkah

1.  **Clone *repository* ini:**
    ```bash
    git clone [URL_REPOSITORY_ANDA]
    cd [NAMA_FOLDER_PROYEK]
    ```

2.  **Salin file *environment*:**
    ```bash
    cp .env.example .env
    ```

3.  **Instal dependensi PHP menggunakan Composer:**
    ```bash
    composer install
    ```

4.  **Instal dependensi JavaScript menggunakan NPM:**
    ```bash
    npm install
    ```

5.  ***Generate* kunci aplikasi Laravel:**
    ```bash
    php artisan key:generate
    ```

6.  **Konfigurasi Database:**
    Buka file `.env` dan sesuaikan pengaturan database Anda (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

7.  **Jalankan Migrasi Database:**
    Perintah ini akan membuat semua tabel yang dibutuhkan dalam database Anda.
    ```bash
    php artisan migrate
    ```
    *Opsional: Jika proyek memiliki data awal, jalankan seeder:*
    ```bash
    php artisan db:seed
    ```

8.  **Jalankan *Vite development server*:**
    Buka terminal baru dan jalankan perintah ini untuk meng-*compile* aset *front-end*.
    ```bash
    npm run dev
    ```

9.  **Jalankan *server* Laravel:**
    Di terminal utama Anda, jalankan *server* pengembangan Laravel.
    ```bash
    php artisan serve
    ```

Sekarang, aplikasi sudah dapat diakses melalui `http://127.0.0.1:8000`.