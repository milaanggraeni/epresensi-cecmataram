<?php

namespace App\Exports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PesertaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $search;
    protected $kelasId;
    protected $rowNumber = 0;

    public function __construct($search = '', $kelasId = '')
    {
        $this->search = $search;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = Peserta::with(['kelas', 'user']);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('nis', 'LIKE', '%' . $search . '%');
            });
        }

        if ($this->kelasId) {
            $query->where('kelas_id', $this->kelasId);
        }

        return $query->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Peserta',
            'Jenis Kelamin',
            'Kelas',
            'Email',
        ];
    }

    public function map($peserta): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $peserta->nama,
            $peserta->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $peserta->kelas->nama_kelas ?? '-',
            $peserta->user->email ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4472C4'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Peserta';
    }
}
