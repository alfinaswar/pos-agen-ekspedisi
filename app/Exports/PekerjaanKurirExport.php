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

class PekerjaanKurirExport implements FromView, WithStyles, WithTitle, WithColumnWidths
{
    protected $Data;
    protected $FilterInfo;

    public function __construct($Data, $FilterInfo)
    {
        $this->Data = $Data;
        $this->FilterInfo = $FilterInfo;
    }

    public function view(): View
    {
        return view('pekerjaan-kurir.export', [
            'Data' => $this->Data,
            'FilterInfo' => $this->FilterInfo,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 10,  // Jam
            'D' => 20,  // Kurir / User
            'E' => 18,  // Pekerjaan
            'F' => 25,  // Dari
            'G' => 25,  // Tujuan
            'H' => 12,  // Jml Paket
            'I' => 15,  // Durasi
            'J' => 15,  // Status
            'K' => 30,  // Catatan
        ];
    }

    public function styles(Worksheet $Worksheet)
    {
        $DataCount = count($this->Data);

        // Struktur Baris:
        // 1-2: Spasi, 3: Judul, 4: Info Filter, 5: Spasi, 6: Header Tabel
        // Data mulai baris 7. Footer ada setelah data selesai + 1 baris.
        $HeaderRow = 6;
        $FooterRow = $HeaderRow + $DataCount + 2;

        // Merge cells untuk Judul, Filter, dan Footer (Kolom A sampai K)
        $Worksheet->mergeCells('A3:K3');
        $Worksheet->mergeCells('A4:K4');
        $Worksheet->mergeCells('A' . $FooterRow . ':K' . $FooterRow);

        // Border untuk Header dan seluruh baris Data
        $Worksheet->getStyle('A6:K' . ($FooterRow - 1))->applyFromArray([
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
            // Baris Footer (Italic, Abu-abu, Rata kanan)
            $FooterRow => [
                'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6C757D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pekerjaan Kurir';
    }
}
