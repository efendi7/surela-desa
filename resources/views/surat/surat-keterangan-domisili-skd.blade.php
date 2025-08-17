<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Domisili - {{ $pengajuan->user->name }}</title>
    <style>
        /* Mengatur agar tidak ada margin & padding bawaan dari browser */
        @page {
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 2cm 2cm 2cm 2.5cm; /* atas, kanan, bawah, kiri */
            font-size: 12pt;
            line-height: 1.5;
        }

        .kop-surat-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* === PERUBAHAN 1: LOGO DIKECILKAN === */
        .logo-cell {
            width: 110px; /* Lebar wadah logo dikecilkan */
            padding-right: 20px;
            vertical-align: middle;
        }
        .logo {
            width: 90px; /* Ukuran logo dikecilkan */
            height: auto;
        }

        /* === PERUBAHAN 2: TEKS DIGESER KE KIRI === */
        .kop-teks {
            text-align: center;
            line-height: 1.4;
            vertical-align: middle;
            /* Padding kanan dibuat lebih besar dari lebar logo-cell untuk menggeser teks ke kiri */
            padding-right: 200px;
        }
        .kop-teks h2, .kop-teks h3 {
            margin: 0;
            font-weight: bold;
        }
        .kop-teks h2 {
            font-size: 18pt;
            white-space: nowrap;
        }
        .kop-teks h3 {
            font-size: 16pt;
        }
        .kop-teks p {
            margin: 0;
            font-size: 12pt;
        }
        /* === AKHIR PERUBAHAN === */

        .garis-kop {
            border-bottom: 3px solid black;
            margin-top: 8px;
            margin-bottom: 2px;
        }
        .judul-surat {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin-top: 15px;
        }
        .nomor-surat {
            text-align: center;
            margin-bottom: 20px;
        }
        .data-pemohon {
            margin-top: 5px;
            margin-bottom: 5px;
            border-collapse: collapse;
        }
        .data-pemohon td {
            vertical-align: top;
            padding: 1px 0;
        }
        .isi-surat .indent {
            text-indent: 0.5in;
        }
        .penutup {
            margin-top: 15px;
        }
        .tanda-tangan {
            margin-top: 30px;
            width: 280px;
            float: right;
            text-align: center;
        }
        .tanda-tangan .jabatan {
            margin-bottom: 70px; /* Ruang untuk TTD & stempel */
        }
    </style>
</head>
<body>

    <table class="kop-surat-table">
        <tr>
            <td class="logo-cell">
                {{-- Logo desa --}}
                @if($profilDesa->logo)
                    <img src="{{ public_path('storage/' . $profilDesa->logo) }}" alt="Logo Kabupaten" class="logo">
                @endif
            </td>
            <td class="kop-teks">
                <h2>PEMERINTAH KABUPATEN {{ strtoupper($profilDesa->nama_kabupaten) }}</h2>
                <h3>KECAMATAN {{ strtoupper($profilDesa->nama_kecamatan) }}</h3>
                <h3>DESA {{ strtoupper($profilDesa->nama_desa) }}</h3>
                <p>Alamat : {{ $profilDesa->alamat }} | Telp. {{ $profilDesa->telepon ?? '-' }}</p>
            </td>
        </tr>
    </table>
    <div class="garis-kop"></div>

    {{-- Sisa konten surat tidak berubah --}}
    <div class="judul-surat">SURAT KETERANGAN DOMISILI</div>
    <div class="nomor-surat">Nomor : {{ $pengajuan->nomor_surat }}</div>
    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini Kepala Desa {{ $profilDesa->nama_desa }}, Kecamatan {{ $profilDesa->nama_kecamatan }}, Kabupaten {{ $profilDesa->nama_kabupaten }}, menerangkan dengan sebenarnya bahwa:</p>
        <table class="data-pemohon" style="margin-left: 0.5in;">
            <tr>
                <td width="180px">Nama</td>
                <td width="10px">:</td>
                <td><b>{{ $pengajuan->user->name }}</b></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $pengajuan->user->tempat_lahir }}, {{ \Carbon\Carbon::parse($pengajuan->user->tanggal_lahir)->locale('id_ID')->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $pengajuan->user->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td>{{ $pengajuan->user->pekerjaan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Agama</td>
                <td>:</td>
                <td>{{ $pengajuan->user->agama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status Perkawinan</td>
                <td>:</td>
                <td>{{ $pengajuan->user->status_perkawinan ?? '-' }}</td>
            </tr>
             <tr>
                <td>Kewarganegaraan</td>
                <td>:</td>
                <td>{{ $pengajuan->user->kewarganegaraan ?? 'Indonesia' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pengajuan->user->address }}</td>
            </tr>
        </table>
        <p class="indent">Bahwa nama tersebut di atas adalah benar penduduk Desa kami dan saat ini yang bersangkutan benar <b>berdomisili</b> di alamat tersebut.</p>
        <p class="indent">Surat Keterangan ini dibuat untuk keperluan <b>{{ $pengajuan->keperluan ?? 'Administrasi' }}</b>. Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
    </div>
    <div class="tanda-tangan">
        <p>{{ $profilDesa->nama_desa }}, {{ $tanggal }}</p>
        <p class="jabatan">Kepala Desa {{ $profilDesa->nama_desa }}</p>
        <p style="font-weight: bold; text-decoration: underline;">{{ $profilDesa->nama_kepala_desa }}</p>
    </div>

</body>
</html>