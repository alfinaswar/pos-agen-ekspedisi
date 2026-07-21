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
use Carbon\Carbon;

class AbsensiExport implements FromView, WithStyles, WithTitle, WithColumnWidths
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
        return view('absensi.export', [
            'data' => $this->data,
            'filterInfo' => $this->filterInfo,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Nama
            'C' => 20,  // Divisi
            'D' => 15,  // Tanggal
            'E' => 12,  // Status
            'F' => 15,  // Jam Hadir
            'G' => 15,  // Jam Pulang
            'H' => 10,  // Lembur
            'I' => 25,  // Durasi Lembur
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->data);

        // Hitung posisi baris (sama dengan array)
        $headerRow = 5; // Header tabel ada di baris 5
        $totalRow = $headerRow + 1 + $dataCount;
        $footerRow = $totalRow + 2;

        // Merge cells
        $sheet->mergeCells('A2:I2');  // Judul
        $sheet->mergeCells('A3:I3');  // Periode
        $sheet->mergeCells('A' . $footerRow . ':I' . $footerRow); // Footer

        // Border untuk header dan data
        $sheet->getStyle('A5:I' . ($totalRow - 1))->applyFromArray([
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
            // Baris 3: Periode (Italic, Abu-abu)
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
        return 'Laporan Absensi';
    }
}
