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
            'C' => 20,  // Kode Bayar
            'D' => 22,  // Tanggal
            'E' => 20,  // Ekspedisi
            'F' => 20,  // No. Resi
            'G' => 15,  // Metode
            'H' => 22,  // Pendapatan
            'I' => 18,  // Diskon
            'J' => 22,  // Pendapatan Bersih
            'K' => 28,  // Keterangan
            'L' => 22,  // User Input
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->data);

        // Baris index pada blade:
        // 1 - spasi, 2 - spasi, 3 - judul, 4 - filter, 5 - user input, 6 - spasi, 7 - header
        // Data mulai baris 8. Setelah data: total, spacer, footer
        $headerRow = 7;
        $footerRow = $headerRow + $dataCount + 3;

        // Merge cells untuk judul, filter, user, dan footer
        $sheet->mergeCells('A3:L3');
        $sheet->mergeCells('A4:L4');
        $sheet->mergeCells('A5:L5');
        $sheet->mergeCells('A' . $footerRow . ':L' . $footerRow);

        // Border untuk header dan data
        $sheet->getStyle('A7:L' . ($footerRow - 3))->applyFromArray([
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
            // Baris 5: User Input (Italic, left)
            5 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '444444']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            // Baris 7: Header Tabel (Bold, Abu muda)
            7 => [
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
