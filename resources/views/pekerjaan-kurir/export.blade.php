<table>
    <!-- Baris 1 & 2: Spasi -->
    <tr><td></td></tr>
    <tr><td></td></tr>

    <!-- Baris 3: Judul (Akan di-merge oleh Export Class) -->
    <tr>
        <td><h3>LAPORAN PEKERJAAN KURIR</h3></td>
    </tr>

    <!-- Baris 4: Info Filter (Akan di-merge oleh Export Class) -->
    <tr>
        <td>{{ $FilterInfo }}</td>
    </tr>

    <!-- Baris 5: Spasi -->
    <tr><td></td></tr>

    <!-- Baris 6: Header Tabel -->
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Jam</th>
        <th>Nama Kurir</th>
        <th>Pekerjaan</th>
        <th>Dari</th>
        <th>Tujuan</th>
        <th>Jml Paket</th>
        <th>Durasi</th>
        <th>Status</th>
        <th>Catatan</th>
    </tr>

    <!-- Baris 7+: Data -->
    @foreach($Data as $Index => $Row)
    <tr>
        <td>{{ $Index + 1 }}</td>
        <td>{{ \Carbon\Carbon::parse($Row->Tanggal)->format('d/m/Y') }}</td>
        <td>{{ $Row->Jam }}</td>
        <td>{{ $Row->UserCreate ?? '-' }}</td>
        <td>{{ $Row->Pekerjaan }}</td>
        <td>{{ $Row->DariLokasi }}</td>
        <td>{{ $Row->Tujuan }}</td>
        <td style="text-align: center;">{{ $Row->JumlahPaket }}</td>
        <td>{{ $Row->Durasi }}</td>
        <td style="text-align: center;">{{ $Row->Status }}</td>
        <td>{{ strip_tags($Row->Catatan ?? '') }}</td>
    </tr>
    @endforeach

    <!-- Baris Footer: Total Data & Timestamp -->
    <tr>
        <td colspan="11">
            Total Data: {{ count($Data) }} Laporan | Diekspor pada: {{ now()->format('d/m/Y H:i:s') }}
        </td>
    </tr>
</table>
