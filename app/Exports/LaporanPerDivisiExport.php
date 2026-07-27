<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LaporanPerDivisiExport implements FromArray, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $totalTransaksi;
    protected $totalPendapatan;
    protected $tanggal;

    public function __construct($data, $totalTransaksi, $totalPendapatan, $tanggal)
    {
        $this->data = $data;
        $this->totalTransaksi = $totalTransaksi;
        $this->totalPendapatan = $totalPendapatan;
        $this->tanggal = $tanggal;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 30,  // Nama Divisi
            'C' => 18,  // Jumlah Transaksi
            'D' => 25,  // Total Pendapatan Bersih
            'E' => 15,  // Persentase
        ];
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = []; // Spasi
        $rows[] = ['LAPORAN PENDAPATAN PER DIVISI'];
        $rows[] = ['Periode: ' . Carbon::parse($this->tanggal)->isoFormat('MMMM YYYY')];
        $rows[] = []; // Spasi
        $rows[] = ['No', 'Nama Divisi', 'Jumlah Transaksi', 'Total Pendapatan Bersih', 'Persentase'];

        $no = 1;
        foreach ($this->data as $row) {
            $persentase = $this->totalPendapatan > 0 ? round(($row->total_pendapatan / $this->totalPendapatan) * 100, 1) : 0;
            $rows[] = [
                $no,
                $row->getDivisi->Nama ?? 'Tanpa Divisi',
                $row->jumlah_transaksi,
                'Rp ' . number_format($row->total_pendapatan, 0, ',', '.'),
                $persentase . '%'
            ];
            $no++;
        }

        $rows[] = ['', 'TOTAL', $this->totalTransaksi, 'Rp ' . number_format($this->totalPendapatan, 0, ',', '.'), '100%'];
        $rows[] = []; // Spasi
        $rows[] = ['Dicetak pada: ' . Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss') . ' WIB'];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $dataCount = count($this->data);
        $headerRow = 5;
        $totalRow = $headerRow + 1 + $dataCount;
        $footerRow = $totalRow + 2;

        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);

        return [
            2 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6C757D']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            5 => ['font' => ['bold' => true, 'size' => 11], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            $totalRow => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0D6EFD']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F1FF']]],
            $footerRow => ['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6C757D']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Per Divisi';
    }
}
