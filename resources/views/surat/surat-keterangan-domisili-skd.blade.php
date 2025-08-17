<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Domisili</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; margin: 0.5in; font-size: 12pt; }
        .kop-surat { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 30px; }
        .kop-surat h2, .kop-surat h3 { margin: 0; }
        .judul-surat { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .nomor-surat { text-align: center; margin-bottom: 30px; }
        .isi-surat { text-align: justify; line-height: 1.5; }
        .isi-surat .indent { text-indent: 0.5in; }
        .data-pemohon { padding-left: 0.5in; margin-top: 15px; margin-bottom: 15px; }
        .penutup { margin-top: 30px; }
        .tanda-tangan { margin-top: 60px; width: 300px; float: right; text-align: center; }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h2>PEMERINTAH KABUPATEN CONTOH</h2>
        <h3>KECAMATAN CONTOH</h3>
        <h3>DESA SUKAMAJU</h3>
        <p style="font-size: 10pt;">Alamat: Jl. Raya Desa No. 1, Kode Pos: 12345, Telp: (021) 123456</p>
    </div>

    <div class="judul-surat">SURAT KETERANGAN DOMISILI</div>
    <div class="nomor-surat">Nomor: 470/{{ $pengajuan->id }}/PEM-DES/{{ date('Y') }}</div>

    <div class="isi-surat">
        <p class="indent">Yang bertanda tangan di bawah ini, Kepala Desa Sukamaju, Kecamatan Contoh, Kabupaten Contoh, dengan ini menerangkan bahwa:</p>

        <table class="data-pemohon">
            <tr>
                <td width="150px">Nama Lengkap</td>
                <td>:</td>
                <td>{{ $pengajuan->data_pemohon['nama'] }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $pengajuan->data_pemohon['nik'] }}</td>
            </tr>
            <tr>
                <td>Tempat/Tanggal Lahir</td>
                <td>:</td>
                <td>- (Data ini perlu ditambahkan di profil user)</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>- (Data ini perlu ditambahkan di profil user)</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $pengajuan->data_pemohon['address'] }}</td>
            </tr>
        </table>

        <p class="indent">Berdasarkan pengamatan kami, nama tersebut di atas adalah benar penduduk yang berdomisili di Desa Sukamaju, Kecamatan Contoh, Kabupaten Contoh.</p>
        <p class="indent">Surat keterangan ini dibuat untuk keperluan {{ $keperluan ?? '...' }}.</p>
    </div>

    <div class="isi-surat penutup">
        <p class="indent">Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="tanda-tangan">
        <p>Sukamaju, {{ date('d F Y') }}</p>
        <p>Kepala Desa Sukamaju</p>
        <br><br><br><br>
        <p style="font-weight: bold; text-decoration: underline;">( NAMA KEPALA DESA )</p>
    </div>

</body>
</html>
