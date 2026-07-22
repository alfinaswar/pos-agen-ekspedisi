<table>
    {{-- Baris 1: Spasi --}}
    <tr><td colspan="12" style="height: 10px;"></td></tr>

    {{-- Baris 2: Spasi --}}
    <tr><td colspan="12" style="height: 7px;"></td></tr>

    {{-- Baris 3: Judul Utama --}}
    <tr>
        <td colspan="12" style="text-align: center; font-size: 14pt; font-weight: bold; background-color: #0D6EFD; color: white; padding: 10px;">
            LAPORAN TRANSAKSI
        </td>
    </tr>

    {{-- Baris 4: Info Filter --}}
    <tr>
        <td colspan="12" style="text-align: center; font-style: italic; color: #6C757D; padding: 5px;">
            {{ $filterInfo }}
        </td>
    </tr>

    {{-- Baris 4.1: User Input --}}
    <tr>
        <td colspan="12" style="text-align: left; color: #444; padding: 5px; font-style:italic;">
            Dicetak oleh: {{ $userCreate->name ?? '-' }}
        </td>
    </tr>

    {{-- Baris 5: Spacer --}}
    <tr><td colspan="12" style="height: 10px;"></td></tr>

    {{-- Baris 6: Header Tabel --}}
    <tr style="background-color: #F8F9FA; font-weight: bold; text-align: center;">
        <th style="padding: 8px; border: 1px solid #DEE2E6;">No</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Kode Transaksi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Kode Bayar</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Tanggal</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Ekspedisi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">No. Resi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Metode</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Pendapatan</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Diskon</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Pendapatan Bersih</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Keterangan</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">User Input</th>
    </tr>

    {{-- Baris 7 dst: Data --}}
    @php
        $no = 1;
        $totalDiskon = 0;
        $totalPendapatanBersih = 0;
    @endphp
    @foreach($data as $row)
        @php
            $totalDiskon += $row->Diskon;
            $totalPendapatanBersih += $row->PendapatanBersih;
        @endphp
        <tr>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $no++ }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->KodeTransaksi }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->KodeBayar ?? '-' }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">
                {{ \Carbon\Carbon::parse($row->Tanggal)->isoFormat('D MMM YYYY') }}
            </td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->ekspedisi->NamaEkspedisi }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->NoResi ?: '-' }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $row->Metode }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: right;">Rp {{ number_format($row->Pendapatan, 0, ',', '.') }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: right;">Rp {{ number_format($row->Diskon, 0, ',', '.') }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: right;">Rp {{ number_format($row->PendapatanBersih, 0, ',', '.') }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->Keterangan ?? '-' }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $row->userCreate->name ?? '-' }}</td>
        </tr>
    @endforeach

    {{-- Baris: Total --}}
    <tr style="background-color: #E7F1FF; font-weight: bold; color: #0D6EFD;">
        <td colspan="7" style="padding: 8px; border: 1px solid #DEE2E6; text-align: right;">Total Pendapatan / Diskon / Bersih</td>
        <td style="padding: 8px; border: 1px solid #DEE2E6; text-align: right;">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </td>
        <td style="padding: 8px; border: 1px solid #DEE2E6; text-align: right;">
            Rp {{ number_format($totalDiskon, 0, ',', '.') }}
        </td>
        <td style="padding: 8px; border: 1px solid #DEE2E6; text-align: right;">
            Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}
        </td>
        <td style="padding: 8px; border: 1px solid #DEE2E6;"></td>
        <td style="padding: 8px; border: 1px solid #DEE2E6;"></td>
    </tr>

    {{-- Spacer & Footer --}}
    <tr><td colspan="12" style="height: 10px;"></td></tr>
    <tr>
        <td colspan="12" style="text-align: right; font-style: italic; color: #6C757D; font-size: 9pt; padding: 5px;">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss') }} WIB
        </td>
    </tr>
</table>
