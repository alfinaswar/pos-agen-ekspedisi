<table>
    {{-- Baris 1: Spasi --}}
    <tr><td colspan="8" style="height: 10px;"></td></tr>

    {{-- Baris 2: Judul Utama --}}
    <tr>
        <td colspan="8" style="text-align: center; font-size: 14pt; font-weight: bold; background-color: #0D6EFD; color: white; padding: 10px;">
            LAPORAN REIMBURSEMENT
        </td>
    </tr>

    {{-- Baris 3: Info Filter --}}
    <tr>
        <td colspan="8" style="text-align: center; font-style: italic; color: #6C757D; padding: 5px;">
            {{ $filterInfo }}
        </td>
    </tr>

    {{-- Baris 4: Spacer --}}
    <tr><td colspan="8" style="height: 10px;"></td></tr>

    {{-- Baris 5: Header Tabel --}}
    <tr style="background-color: #F8F9FA; font-weight: bold; text-align: center;">
        <th style="padding: 8px;">No</th>
        <th style="padding: 8px;">Tanggal</th>
        <th style="padding: 8px;">Nama</th>
        <th style="padding: 8px;">Item</th>
        <th style="padding: 8px;">Nominal</th>
        <th style="padding: 8px;">Status</th>
        <th style="padding: 8px;">Owner Update</th>
        <th style="padding: 8px;">User Create</th>
    </tr>

    {{-- Baris 6 dst: Data --}}
    @foreach($data as $index => $row)
        <tr>
            <td style="padding: 6px; text-align: center;">{{ $index + 1 }}</td>
            <td style="padding: 6px; text-align: center;">
                {{ \Carbon\Carbon::parse($row->Tanggal)->isoFormat('D MMM YYYY') }}
            </td>
            <td style="padding: 6px;">{{ $row->Nama }}</td>
            <td style="padding: 6px;">{{ $row->Item }}</td>
            <td style="padding: 6px; text-align: right; font-weight: 600;">
                Rp {{ number_format($row->Nominal, 0, ',', '.') }}
            </td>
            <td style="padding: 6px; text-align: center;">{{ $row->Status }}</td>
            <td style="padding: 6px;">{{ $row->OwnerUpdate ?: '-' }}</td>
            <td style="padding: 6px;">{{ $row->UserCreate ?: '-' }}</td>
        </tr>
    @endforeach

    {{-- Spacer & Footer --}}
    <tr><td colspan="8" style="height: 10px;"></td></tr>
    <tr>
        <td colspan="8" style="text-align: right; font-style: italic; color: #6C757D; font-size: 9pt; padding: 5px;">
            Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss') }} WIB
        </td>
    </tr>
</table>
