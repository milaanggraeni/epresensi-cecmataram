<?php

namespace App\Exports;

use App\Models\Tutor;
use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanKehadiranTutorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $bulan;
    protected $tahun;
    protected $rowNumber = 0;
    protected $namaBulan;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY');
    }

    public function collection()
    {
        return Tutor::orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Tutor',
            'Hadir',
            'Izin',
            'Sakit',
            'Alpa',
            'Persentase Kehadiran (%)',
        ];
    }

    public function map($tutor): array
    {
        $this->rowNumber++;

        // Hitung hari efektif
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = ($this->bulan == date('m') && $this->tahun == date('Y'))
            ? Carbon::now()
            : Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();

        $hariEfektif = 0;
        $tanggalList = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                $hariEfektif++;
                $tanggalList[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }

        // Get absensi data
        $absensiData = Absensi::where('tutor_id', $tutor->id)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->get()
            ->keyBy('tanggal');

        $hadir = $absensiData->whereIn('status', ['Hadir', 'hadir'])->count();
        $izin = $absensiData->whereIn('status', ['Izin', 'izin'])->count();
        $sakit = $absensiData->whereIn('status', ['Sakit', 'sakit'])->count();
        $alfaDb = $absensiData->whereIn('status', ['Alfa', 'alfa'])->count();

        $totalTercatat = $hadir + $izin + $sakit + $alfaDb;
        $alpa = $alfaDb + max(0, $hariEfektif - $totalTercatat);

        $persentase = $hariEfektif > 0 ? round(($hadir / $hariEfektif) * 100, 2) : 0;

        return [
            $this->rowNumber,
            $tutor->nama,
            $hadir,
            $izin,
            $sakit,
            $alpa,
            $persentase,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setAutoFilter('A1:G1');

        // Header row styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data row styling
        $dataStyle = [
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD0D0D0'],
                ],
            ],
        ];

        $sheet->getStyle('A2:G' . ($this->rowNumber + 1))->applyFromArray($dataStyle);

        // Alternate row colors
        for ($i = 2; $i <= $this->rowNumber + 1; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':G' . $i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF5F5F5');
            }
        }

        return $headerStyle;
    }

    public function title(): string
    {
        return 'Laporan Kehadiran';
    }
}
