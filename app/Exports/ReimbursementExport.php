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

class ReimbursementExport implements FromView, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $filterInfo;

    public function __construct($data, $filterInfo)
    {
        $this->data = $data;
        $this->filterInfo = $filterInfo;
    }

    public function view(): View
    {
        return view('reimbursement.export', [
            'data' => $this->data,
            'filterInfo' => $this->filterInfo,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 25,  // Nama
            'D' => 35,  // Item
            'E' => 20,  // Nominal
            'F' => 15,  // Status
            'G' => 25,  // Owner Update
            'H' => 25,  // User Create
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->data);

        // Hitung posisi baris (sama dengan AbsensiExport)
        $headerRow = 5; // Header tabel ada di baris 5
        $footerRow = $headerRow + $dataCount + 2;

        // Merge cells (header, filter, footer)
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        $sheet->mergeCells('A' . $footerRow . ':H' . $footerRow);

        // Border untuk header dan data
        $sheet->getStyle('A5:H' . ($footerRow - 2))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']],
            ],
        ]);

        return [
            // Baris 2: Judul (Bold, Biru, Putih)
            2 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Baris 3: Filter Info (Italic, Abu-abu)
            3 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Baris 5: Header Tabel (Bold, Abu muda)
            5 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Footer
            $footerRow => [
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Reimbursement';
    }
}
