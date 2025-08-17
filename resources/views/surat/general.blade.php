<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Domisili - {{ $pengajuan->user->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 2cm 1.5cm 2cm; /* Margin diperkecil agar lebih muat */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            font-size: 11pt; /* Font diperkecil dari 12pt */
            line-height: 1.3; /* Line height diperkecil dari 1.5 */
        }

        .kop-surat-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px; /* Spacing dikurangi */
        }

        .logo-cell {
            width: 90px; /* Diperkecil lagi */
            padding-right: 15px; /* Padding dikurangi */
            vertical-align: middle;
        }
        .logo {
            width: 80px; /* Logo diperkecil lagi */
            height: auto;
        }

        .kop-teks {
            text-align: center;
            line-height: 1.2; /* Line height diperkecil */
            vertical-align: middle;
            padding-right: 90px; /* Disesuaikan dengan logo cell baru */
        }
        .kop-teks h2, .kop-teks h3 {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }
        .kop-teks h2 {
            font-size: 16pt; /* Diperkecil dari 18pt */
            white-space: nowrap;
            margin-bottom: 2px;
        }
        .kop-teks h3 {
            font-size: 14pt; /* Diperkecil dari 16pt */
            margin-bottom: 1px;
        }
        .kop-teks p {
            margin: 0;
            font-size: 10pt; /* Diperkecil dari 12pt */
        }

        .garis-kop {
            border-bottom: 2px solid black; /* Diperkecil dari 3px */
            margin: 6px 0 8px 0; /* Margin dikurangi */
        }
        .judul-surat {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 13pt; /* Diperkecil dari 14pt */
            margin: 10px 0 5px 0; /* Margin dikurangi */
        }
        .nomor-surat {
            text-align: center;
            margin-bottom: 12px; /* Margin dikurangi */
        }
        .data-pemohon {
            margin: 8px 0 8px 0.5in; /* Margin dikurangi */
            border-collapse: collapse;
        }
        .data-pemohon td {
            vertical-align: top;
            padding: 1px 0; /* Padding dikecilkan */
        }
        .data-pemohon td:first-child {
            width: 170px; /* Lebar tetap untuk alignment */
        }
        .data-pemohon td:nth-child(2) {
            width: 10px;
        }
        
        .isi-surat {
            line-height: 1.3; /* Konsisten dengan body */
        }
        .isi-surat p {
            margin-bottom: 8px; /* Margin antar paragraf dikurangi */
        }
        .isi-surat .indent {
            text-indent: 0.5in;
        }
        .penutup {
            margin-top: 10px; /* Margin dikurangi */
        }
        .tanda-tangan {
            margin-top: 15px; /* Margin dikurangi */
            width: 250px; /* Width dikurangi */
            float: right;
            text-align: center;
        }
        .tanda-tangan .jabatan {
            margin-bottom: 50px; /* Ruang TTD dikurangi */
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

    <div class="judul-surat">SURAT KETERANGAN DOMISILI</div>
    <div class="nomor-surat">Nomor : {{ $pengajuan->nomor_surat }}</div>
    
    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini Kepala Desa {{ $profilDesa->nama_desa }}, Kecamatan {{ $profilDesa->nama_kecamatan }}, Kabupaten {{ $profilDesa->nama_kabupaten }}, menerangkan dengan sebenarnya bahwa:</p>
        
        <table class="data-pemohon">
            <tr>
                <td>Nama</td>
                <td>:</td>
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