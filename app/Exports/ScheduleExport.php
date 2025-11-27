<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Schedule::with(['cinema', 'movie'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Bioskop',
            'Judul Film',
            'Harga',
            'Jadwal Tayang',
        ];
    }

    public function map($schedule): array
    {
        return [
            $schedule->id,
            $schedule->cinema->name ?? '-',
            $schedule->movie->title ?? '-',
            'Rp ' . number_format($schedule->price, 0, ',', '.'),
            implode(', ', $schedule->hours ?? []),
        ];
    }

}