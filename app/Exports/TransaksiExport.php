<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class TransaksiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, WithCustomStartCell, WithEvents
{
    protected $Query;
    protected $TotalPendapatan;
    protected $TotalDiskon;
    protected $TotalPendapatanBersih;
    protected $FilterInfo;

    public function __construct($Query, $TotalPendapatan, $TotalDiskon, $TotalPendapatanBersih, $FilterInfo)
    {
        $this->Query = $Query;
        $this->TotalPendapatan = $TotalPendapatan;
        $this->TotalDiskon = $TotalDiskon;
        $this->TotalPendapatanBersih = $TotalPendapatanBersih;
        $this->FilterInfo = $FilterInfo;
    }

    /**
     * ✅ Mengembalikan Query Builder. Maatwebsite akan otomatis melakukan chunking.
     */
    public function query()
    {
        return $this->Query;
    }

    /**
     * ✅ Mulai tulis data dari baris 7 (Baris 1-6 dikosongkan untuk Header Custom)
     */
    public function startCell(): string
    {
        return 'A7';
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Transaksi',
            'Kode Bayar',
            'Tanggal',
            'Ekspedisi',
            'No. Resi',
            'Metode',
            'Pendapatan',
            'Diskon',
            'Pendapatan Bersih',
            'Keterangan',
            'User Input'
        ];
    }

    /**
     * ✅ Format setiap baris data
     */
    public function map($Row): array
    {
        // Gunakan static variable untuk nomor urut yang akurat
        static $Index = 0;
        $Index++;

        return [
            $Index,
            $Row->KodeTransaksi,
            $Row->KodeBayar,
            $Row->Tanggal ? Carbon::parse($Row->Tanggal)->format('d/m/Y') : '-',
            $Row->ekspedisi ? $Row->ekspedisi->NamaEkspedisi : $Row->Ekspedisi,
            $Row->NoResi,
            $Row->Metode,
            $Row->Pendapatan,
            $Row->Diskon,
            $Row->PendapatanBersih,
            $Row->Keterangan,
            $Row->userCreate ? $Row->userCreate->name : $Row->UserCreate,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
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

    /**
     * ✅ Styling dan Penambahan Baris Total di Bagian Bawah
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $Event) {
                $Sheet = $Event->sheet->getDelegate();

                // 1. Buat Header Custom di Baris 3, 4, 5
                $Sheet->setCellValue('A3', 'LAPORAN DATA TRANSAKSI');
                $Sheet->setCellValue('A4', $this->FilterInfo);
                $Sheet->setCellValue('A5', 'Diekspor pada: ' . Carbon::now()->format('d/m/Y H:i:s'));

                $Sheet->mergeCells('A3:L3');
                $Sheet->mergeCells('A4:L4');
                $Sheet->mergeCells('A5:L5');

                // 2. Cari baris terakhir data untuk menaruh Total
                $HighestRow = $Sheet->getHighestRow();
                $NextRow = $HighestRow + 2; // Beri jarak 1 baris

                // 3. Isi Baris Total
                $Sheet->setCellValue('A' . $NextRow, 'TOTAL KESELURUHAN');
                $Sheet->setCellValue('H' . $NextRow, $this->TotalPendapatan);
                $Sheet->setCellValue('I' . $NextRow, $this->TotalDiskon);
                $Sheet->setCellValue('J' . $NextRow, $this->TotalPendapatanBersih);

                $Sheet->mergeCells('A' . $NextRow . ':G' . $NextRow); // Merge kolom A sampai G untuk label "TOTAL"

                // 4. Terapkan Border untuk Header (Baris 7) sampai Data Terakhir
                $Sheet->getStyle('A7:L' . $HighestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']],
                    ],
                ]);

                // 5. Return Array Styling
                return [
                    // Baris 3: Judul
                    3 => [
                        'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ],
                    // Baris 4: Info Filter
                    4 => [
                        'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6C757D']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                    // Baris 5: User Input
                    5 => [
                        'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '444444']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                    // Baris 7: Header Tabel
                    7 => [
                        'font' => ['bold' => true, 'size' => 11],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ],
                    // Baris Total
                    $NextRow => [
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0D6EFD']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F1FF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ];
            }
        ];
    }

    /**
     * Add implementation of the styles() method for WithStyles concern.
     */
    public function styles(Worksheet $sheet)
    {
        // You may leave this empty if styling is handled via registerEvents()
        return [];
    }

    public function title(): string
    {
        return 'Data Transaksi';
    }
}
