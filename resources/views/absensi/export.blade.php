<table>
    {{-- Baris 1: Spasi --}}
    <tr><td colspan="9" style="height: 10px;"></td></tr>

    {{-- Baris 2: Judul Utama --}}
    <tr>
        <td colspan="9" style="text-align: center; font-size: 14pt; font-weight: bold; background-color: #0D6EFD; color: white; padding: 10px;">
            LAPORAN ABSENSI
        </td>
    </tr>

    {{-- Baris 3: Periode --}}
    <tr>
        <td colspan="9" style="text-align: center; font-style: italic; color: #6C757D; padding: 5px;">
            {{ $filterInfo }}
        </td>
    </tr>

    {{-- Baris 4: Spacer --}}
    <tr><td colspan="9" style="height: 10px;"></td></tr>

    {{-- Baris 5: Header Tabel --}}
    <tr style="background-color: #F8F9FA; font-weight: bold; text-align: center;">
        <th style="padding: 8px; border: 1px solid #DEE2E6;">No</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Nama</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Divisi</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Tanggal</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Status</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Jam Hadir</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Jam Pulang</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Lembur</th>
        <th style="padding: 8px; border: 1px solid #DEE2E6;">Durasi Lembur</th>
    </tr>

    {{-- Baris 6 dst: Data --}}
    @php $no = 1; @endphp
    @foreach($data as $row)
        @php
            $statusLabel = [
                'H' => 'Hadir',
                'I' => 'Izin',
                'S' => 'Sakit',
                'TK' => 'Tanpa Keterangan'
            ][$row->Status] ?? $row->Status;

            $lemburLabel = $row->Lembur === 'Y' ? 'Ya' : 'Tidak';

            $durasiLembur = '-';
            if ($row->Lembur === 'Y' && ($row->MulaiLembur || $row->SelesaiLembur)) {
                $durasiLembur = ($row->MulaiLembur ?: '-') . ' s/d ' . ($row->SelesaiLembur ?: '-');
            }
        @endphp
        <tr>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $no++ }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->getUser->name ?? '-' }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $row->Divisi }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">
                {{ \Carbon\Carbon::parse($row->Tanggal)->isoFormat('D MMMM YYYY') }}
            </td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $statusLabel }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">
                {{ $row->JamHadir ?: '-' }}
            </td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">
                {{ $row->JamPulang ?: '-' }}
            </td>
            <td style="padding: 6px; border: 1px solid #DEE2E6; text-align: center;">{{ $lemburLabel }}</td>
            <td style="padding: 6px; border: 1px solid #DEE2E6;">{{ $durasiLembur }}</td>
        </tr>
    @endforeach

    {{-- Spacer & Footer --}}
    <tr><td colspan="9" style="height: 10px;"></td></tr>
    <tr>
        <td colspan="9" style="text-align: right; font-style: italic; color: #6C757D; font-size: 9pt; padding: 5px;">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss') }} WIB
        </td>
    </tr>
</table>
