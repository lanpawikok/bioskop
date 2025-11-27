<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
//proses manipulasi tanggal dan waktu
use Carbon\Carbon;

class MovieExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //menentukan data yang akan di munculkan di excel
        return Movie::orderBy('created_at', 'DESC')->get();
    }

    //untuk menentukan th
    public function headings(): array
    {
        return ["No", 'Judul', 'Durasi', 'Genre', 'Sutradara', 'Usia Minimal', 'Poster', 'Sinopsis', 'Status Aktif'];
            
    }

    //mementukan td
    public function map($movie): array
    {
        return[
            //menambahkan $key diatas dr 1 dst
            ++$this->key,
            $movie->title,
            Carbon::parse($movie->duration)->format("H")." Jam ".Carbon::parse
            ($movie->duration)->format("i")." Menit",
            $movie->genre,
            $movie->director,
            $movie->minimum_age."+",
            //poster berupa url ->asset()
            asset("storage")."/".$movie->poster,
            $movie->description,
            $movie->actived == 1 ? 'Aktif' : 'Non Aktif'
        ];
    }
}
