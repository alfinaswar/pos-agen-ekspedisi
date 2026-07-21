<table>
    {{-- Baris 1: Spasi --}}
    <tr><td colspan="7" style="height: 10px;"></td></tr>

    {{-- Baris 2: Spasi --}}
    <tr><td colspan="7" style="height: 7px;"></td></tr>

    {{-- Baris 3: Judul Utama --}}
    <tr>
        <td colspan="7" style="text-align: center; font-size: 14pt; font-weight: bold; background-color: #0D6EFD; color: white; padding: 10px;">
            LAPORAN TRANSAKSI
        </td>
    </tr>

    {{-- Baris 4: Info Filter --}}
    <tr>
        <td colspan="7" style="text-align: center; font-style: italic; color: #6C757D; padding: 5px;">
            {{ $filterInfo }}
        </td>
    </tr>

    {{-- Baris 5: Spacer --}}
    <tr><td colspan="7" style="height: 10px;"></td></tr>

    {{-- Baris 6: Header Tabel --}}
    <tr style="background-color: #F8F9FA; font-weight: bold; text-align: center;">
        <th style="padding: 8px; border: 1px solid #DEE2E6;">No</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Kode Transaksi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Tanggal</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Ekspedisi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">No. Resi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Metode</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Pendapatan</th>
    </tr>

    {{-- Baris 7 dst: Data --}}
    @php $no = 1; @endphp
    @foreach($data as $row)
        <tr>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $no++ }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->KodeTransaksi }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">
                {{ \Carbon\Carbon::parse($row->Tanggal)->isoFormat('D MMM YYYY') }}
            </td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->ekspedisi->NamaEkspedisi }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->NoResi ?: '-' }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $row->Metode }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: right;">Rp {{ number_format($row->Pendapatan, 0, ',', '.') }}</td>
        </tr>
    @endforeach

    {{-- Baris: Total --}}
    <tr style="background-color: #E7F1FF; font-weight: bold; color: #0D6EFD;">
        <td colspan="6" style="padding: 8px; border: 1px solid #DEE2E6; text-align: right;">Total Pendapatan</td>
        <td style="padding: 8px; border: 1px solid #DEE2E6; text-align: right;">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </td>
    </tr>

    {{-- Spacer & Footer --}}
    <tr><td colspan="7" style="height: 10px;"></td></tr>
    <tr>
        <td colspan="7" style="text-align: right; font-style: italic; color: #6C757D; font-size: 9pt; padding: 5px;">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss') }} WIB
        </td>
    </tr>
</table>
