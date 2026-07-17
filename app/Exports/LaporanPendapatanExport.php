<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths; // <-- Tambahkan ini
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

// Hapus ShouldAutoSize, ganti dengan WithColumnWidths
class LaporanPendapatanExport implements FromArray, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $totalTransaksi;
    protected $totalPendapatan;
    protected $type;
    protected $tanggal;
    protected $expeditionNames;

    public function __construct($data, $totalTransaksi, $totalPendapatan, $type, $tanggal, $expeditionNames)
    {
        $this->data = $data;
        $this->totalTransaksi = $totalTransaksi;
        $this->totalPendapatan = $totalPendapatan;
        $this->type = $type;
        $this->tanggal = $tanggal;
        $this->expeditionNames = $expeditionNames;
    }

    /**
     * ATUR LEBAR KOLOM DI SINI
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,  // Lebar kolom No (dibuat kecil)
            'B' => 25, // Lebar kolom Ekspedisi
            'C' => 18, // Lebar kolom Jumlah Transaksi
            'D' => 22, // Lebar kolom Total Pendapatan
            'E' => 12, // Lebar kolom Persentase
        ];
    }

    public function array(): array
    {
        $rows = [];

        // Baris 1: Judul Utama
        $rows[] = ['LAPORAN PENDAPATAN ' . strtoupper($this->type === 'bulanan' ? 'BULANAN' : 'HARIAN')];

        // Baris 2: Subjudul / Periode
        $rows[] = ['Periode: ' . Carbon::parse($this->tanggal)->isoFormat('D MMMM YYYY')];

        // Baris 3: Spacer (kosong)
        $rows[] = [];

        // Baris 4: Header Tabel
        $rows[] = ['No', 'Ekspedisi', 'Jumlah Transaksi', 'Total Pendapatan', 'Persentase'];

        // Baris 5 dst: Data
        $no = 1;
        foreach ($this->data as $row) {
            $persentase = $this->totalPendapatan > 0
                ? round(($row->total_pendapatan / $this->totalPendapatan) * 100, 1)
                : 0;

            $expeditionName = $this->expeditionNames[$row->Ekspedisi] ?? 'Ekspedisi ' . $row->Ekspedisi;

            $rows[] = [
                $no,
                $expeditionName,
                $row->jumlah_transaksi,
                'Rp ' . number_format($row->total_pendapatan, 0, ',', '.'),
                $persentase . '%'
            ];
            $no++;
        }

        // Baris Total
        $rows[] = [
            '',
            'TOTAL',
            $this->totalTransaksi,
            'Rp ' . number_format($this->totalPendapatan, 0, ',', '.'),
            '100%'
        ];

        // Baris Spacer sebelum footer
        $rows[] = [];

        // Baris Footer
        $rows[] = ['Dicetak pada: ' . Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->data);
        $totalRow = 5 + $dataCount;
        $footerRow = 7 + $dataCount;

        // Merge cells
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            $totalRow => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0D6EFD']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F1FF']],
            ],
            $footerRow => [
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }
}
