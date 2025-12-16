<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sertifikat Diklat</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        @font-face {
            font-family: 'ScriptMTBold';
            src: url('data:{{ $fonts['ScriptMTBold']['mime'] }};base64,{{ $fonts['ScriptMTBold']['base64'] }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'ARIALBOLDMT';
            src: url('data:{{ $fonts['ARIALBOLDMT']['mime'] }};base64,{{ $fonts['ARIALBOLDMT']['base64'] }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'ARIALBOLDMT';
            src: url('data:{{ $fonts['ARIALBOLDMT']['mime'] }};base64,{{ $fonts['ARIALBOLDMT']['base64'] }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'ARIALBOLDMT';
            src: url('data:{{ $fonts['ARIALBOLDMT']['mime'] }};base64,{{ $fonts['ARIALBOLDMT']['base64'] }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'ARIALBOLDMT';
            src: url('data:{{ $fonts['ARIALBOLDMT']['mime'] }};base64,{{ $fonts['ARIALBOLDMT']['base64'] }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'ArialLight';
            src: url(data:{{ $fonts['ARIALBOLDMT']['mime'] }};base64,{{ $fonts['ARIALBOLDMT']['base64'] }}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* PAGE 1 */
        .page1 {
            position: fixed;
            width: 1122px;
            height: 794px;
            background: url('{{ public_path('diklat_template/sertifikat/bg1.png') }}') no-repeat center center;
            background-size: cover;
            margin: 0;
            page-break-after: avoid;
            overflow: hidden;
        }

        /* PAGE 2 */
        .page2 {
            position: relative;
            width: 1122px;
            height: 794px;
            background: url('{{ public_path('diklat_template/sertifikat/bg2.png') }}') no-repeat center center;
            background-size: cover;
            margin: 0;
            page-break-before: always;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Your other styles like .logo, .text-sertifikat, etc.
       same as before but add .page1 CSS block */

        /* Positioning inside .page1 */
        .logo {
            position: absolute;
            top: 22%;
            left: 9.4%;
        }

        .logo img {
            width: 90px;
        }

        .text-sertifikat {
            font-family: 'ScriptMTBold';
            font-size: 130px;
            position: absolute;
            top: 2%;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            margin: 0;
            white-space: nowrap;
        }

        .p2 {
            font-family: 'ArialBold';
            font-size: 20px;
            position: absolute;
            top: 36.8%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .text-nama {
            font-family: 'ScriptMTBold';
            font-size: 55px;
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translateX(-50%);
            color: #000;
            white-space: nowrap;
        }

        .p3 {
            font-family: 'ArialMtBold';
            font-weight: bold;
            font-size: 16px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .nama_diklat {
            font-family: 'ArialMtExtraBold';
            font-weight: bolder;
            font-size: 25px;
            position: absolute;
            top: 57%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .tanggal {
            font-family: 'ArialMtBold';
            font-weight: bold;
            font-size: 18px;
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .p4 {
            font-family: 'ArialMtBold';
            font-weight: bold;
            font-size: 16px;
            position: absolute;
            top: 71%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .ttd {
            position: absolute;
            top: 80%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .ttd img {
            width: 120px;
        }

        .p5 {
            font-family: 'ArialMtExtraBold';
            font-weight: bold;
            font-size: 16px;
            position: absolute;
            top: 88%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        /* Page 2 header */
        .nama_diklat2 {
            font-family: 'ArialMtBold';
            font-weight: bold;
            font-size: 25px;
            position: absolute;
            top: 6%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            width: 100%;
            text-align: center;
        }

        /* Table styling */
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 13% auto 0;
            /* margin-top adjusted for header */
            text-align: left;
            font-size: 16px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            font-family: 'ArialMtBold';
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }

        td {
            font-family: 'ArialLight';
            font-weight: normal;
            font-size: 16px;
        }
    </style>
</head>

<body>

    {{-- PAGE 1 --}}
    <div class="page1">
        <div class="logo">
            <img src="{{ public_path('diklat_template/sertifikat/logo1.png') }}" alt="Logo" />
        </div>

        <div class="text-sertifikat">Sertifikat</div>

        <div class="p2">
            <p>Diberikan kepada:</p>
        </div>

        <div class="text-nama">{{ $nama }}</div>

        <div class="p3">
            <p>Sebagai Peserta pada Diklat:</p>
        </div>

        <div class="nama_diklat">{{ $nama_diklat }}</div>

        <div class="tanggal">
            <p>Pada Tanggal {{ $tanggal }}</p>
        </div>

        <div class="p4">
            <p>Direktur RS Hermina Daan Mogot</p>
        </div>

        <div class="ttd">
            <img src="{{ public_path('diklat_template/sertifikat/ttd.png') }}" alt="Tanda Tangan" />
        </div>

        <div class="p5">
            <p>{{ $direktur }}</p>
        </div>
    </div>

    {{-- PAGE 2+ (Split table into chunks) --}}
    @php
        $rowsPerPage = 12; // adjust as needed
        $materiChunks = array_chunk($materi, $rowsPerPage);
    @endphp

    @foreach ($materiChunks as $chunk)
        <div class="page2">
            <p class="nama_diklat2">{{ $nama_diklat }}</p>
            <table>
                <thead>
                    <tr>
                        <th style="width:5%;">NO</th>
                        <th>MATERI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chunk as $m)
                        <tr>
                            <td style="text-align:center;">{{ $m['no'] }}</td>
                            <td>{{ $m['materi'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>

</html>
