<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Contracts\View\View;

class TransaksiExport implements FromView, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $totalPendapatan;
    protected $filterInfo;

    public function __construct($data, $totalPendapatan, $filterInfo)
    {
        $this->data = $data;
        $this->totalPendapatan = $totalPendapatan;
        $this->filterInfo = $filterInfo;
    }

    public function view(): View
    {
        return view('transaksi.export', [
            'data' => $this->data,
            'totalPendapatan' => $this->totalPendapatan,
            'filterInfo' => $this->filterInfo,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No (kecil)
            'B' => 20,  // Kode Transaksi
            'C' => 22,  // Tanggal
            'D' => 20,  // Ekspedisi
            'E' => 20,  // No. Resi
            'F' => 15,  // Metode
            'G' => 22,  // Pendapatan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->data);

        // Baris-baris sesuai dengan struktur yang sesuai dengan blade (lihat referensi ReimbursementExport)
        $headerRow = 6;
        $footerRow = $headerRow + $dataCount + 3;

        // Merge cells untuk judul, filter, dan footer
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');
        $sheet->mergeCells('A' . $footerRow . ':G' . $footerRow);

        // Border untuk header dan data
        $sheet->getStyle('A6:G' . ($footerRow - 3))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']],
            ],
        ]);

        return [
            // Baris 3: Judul (Bold, Biru, Putih)
            3 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Baris 4: Info Filter (Italic, Abu-abu)
            4 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Baris 6: Header Tabel (Bold, Abu muda)
            6 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Baris Total (Bold, Biru muda)
            ($footerRow - 2) => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0D6EFD']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F1FF']],
            ],
            // Footer (Italic, Abu-abu, Rata kanan)
            $footerRow => [
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Transaksi';
    }
}
