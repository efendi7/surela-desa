{{-- resources/views/templates/surat-keterangan-usaha.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Usaha</title>
    <style>
        /* Anda bisa menambahkan styling dasar di sini */
        body { font-family: 'Times New Roman', serif; font-size: 12pt; }
        .kop-surat { text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px;}
        .judul-surat { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .isi-surat { margin-top: 20px; }
        .penutup { margin-top: 40px; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h2>PEMERINTAH KABUPATEN {{ strtoupper($profilDesa->kabupaten) }}</h2>
        <h3>KECAMATAN {{ strtoupper($profilDesa->kecamatan) }}</h3>
        <h3>KEPALA DESA {{ strtoupper($profilDesa->nama_desa) }}</h3>
        <p>{{ $profilDesa->alamat }}</p>
    </div>

    <div class="judul-surat">
        <p>SURAT KETERANGAN USAHA</p>
        <p>Nomor: {{ $pengajuan->nomor_surat ?? '....... / SKU / ..... / ......' }}</p>
    </div>

    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini Kepala Desa {{ $profilDesa->nama_desa }}, Kecamatan {{ $profilDesa->kecamatan }}, Kabupaten {{ $profilDesa->kabupaten }}, dengan ini menerangkan bahwa:</p>

        <table style="margin-left: 20px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $pengajuan->user->name }}</strong></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $pengajuan->user->nik }}</td>
            </tr>
             <tr>
                <td>Tempat/Tgl. Lahir</td>
                <td>:</td>
                <td>{{ $pengajuan->user->tempat_lahir }}, {{ \Carbon\Carbon::parse($pengajuan->user->tanggal_lahir)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pengajuan->user->alamat }}</td>
            </tr>
        </table>

        <p>Berdasarkan pengamatan kami, yang bersangkutan adalah benar penduduk Desa {{ $profilDesa->nama_desa }} dan memiliki usaha sebagai berikut:</p>

         <table style="margin-left: 20px;">
            <tr>
                <td>Nama Usaha</td>
                <td>:</td>
                {{-- Ganti 'nama_usaha' dengan key yang sesuai dari data_pemohon --}}
                <td><strong>{{ $pengajuan->data_pemohon['nama_usaha'] ?? '[Nama Usaha]' }}</strong></td>
            </tr>
            <tr>
                <td>Jenis Usaha</td>
                <td>:</td>
                <td>{{ $pengajuan->data_pemohon['jenis_usaha'] ?? '[Jenis Usaha]' }}</td>
            </tr>
            <tr>
                <td>Alamat Usaha</td>
                <td>:</td>
                <td>{{ $pengajuan->data_pemohon['alamat_usaha'] ?? '[Alamat Usaha]' }}</td>
            </tr>
        </table>

        <p>Demikian Surat Keterangan Usaha ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="penutup" style="margin-left: 400px; text-align: center;">
        <p>{{ $profilDesa->nama_desa }}, {{ $tanggal }}</p>
        <p>Kepala Desa {{ $profilDesa->nama_desa }}</p>
        <br><br><br>
        <p style="font-weight: bold; text-decoration: underline;">{{ $profilDesa->nama_kepala_desa }}</p>
    </div>

</body>
</html>