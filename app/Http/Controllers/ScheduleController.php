<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScheduleExport;
use App\Models\Ticket;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();

        // return view('staff.schedule.index', compact('cinemas', 'movies'));

        // with () : memanggil detail relasi, tidak hanya angka id nya
        // isi with() dari function relasi di model
        $schedules = Schedule::with(['cinema', 'movie'])->get();

        return view('staff.schedule.index', compact('cinemas', 'movies', 'schedules'));
    }

    public function datatables()
    {
        $schedules = Schedule::with(['cinema', 'movie']);
        return datatables()->of($schedules)->addIndexColumn()
        ->addColumn('price_format', function ($schedule) {
            return 'Rp. ' . number_format($schedule->price, 0, ',', '.');
        })->addColumn('hours_list', function ($schedule) {
            return implode(', ', $schedule->hours);
        })->addColumn('action', function ($schedule) {
            $btnEdit = '<a href="' . route('staff.schedules.edit', $schedule->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('staff.schedules.delete', $schedule->id) . '" method="POST">
                           ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            // karena hours array, yng dicalidasi isi itemnya menggunakan (.*)
            //date format : bentuk item arraynya berupa formar waktu H:i
            'hours.*' => 'required|date_format:H:i',
        ], [
            'cinema_id.required' => 'Bioskop harus dipilih',
            'movie_id.required' => 'Film harus dipilih',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang diisi minimal 1 data',
            'hours.*.date_format' => 'Jam tayang diisi dengan format jam:menit'
        ]);

        // cek aapakah data bioskop dan film yang dipilih sudah ada, al; ada ambil jamnua
        $hours = Schedule::where('cinema_id', $request->cinema_id)
            ->where('movie_id', $request->movie_id)
            ->value('hours');

        // value('hours) : dari schedule cmn ambil bagian hpurus
        // jika blm ada data bioskop dan film, hours akan NULL ubah menjadi []
        $hoursBefore = $hours ?? [];

        // gabungkan hours sebelyuumya dengan hours yang baru akan dirtambahkan
        $mergeHours = array_merge($hoursBefore, $request->hours);
        //jika ada jam duplikat, ambil salha satu
        $newHours = array_unique($mergeHours);
        //updateOrCreate : mengecek berdasarkan array 1, jika ada maka update array 2, jikatidak ada tambahkan data dari awrray 1 dan 2
        $createData = Schedule::updateOrCreate(
            [
                'cinema_id' => $request->cinema_id,
                'movie_id' => $request->movie_id,
            ],
            [
                'price' => $request->price,
                // jam penggabungan sblm dan baru di proses diatas
                'hours' => $newHours
            ]
        );
        if ($createData) {
            return redirect()->route('staff.schedules.index')->with(
                'success',
                'Berhasil menambahkan data!'
            );
        } else {
            return redirect()->back() - with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule,$id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule,$id)
    {
        $request->validate([
            'price' => 'required|numeric',
            // karena hours array, yng dicalidasi isi itemnya menggunakan (.*)
            //date format : bentuk item arraynya berupa formar waktu H:i
            'hours.*' => 'required|date_format:H:i',
        ], [
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang harus diisi',
            'hours.*.date_format' => 'Jam tayang diisi dengan format jam:menit',
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => $request->hours,
        ]);

        if ($updateData) {
            return redirect()->route('staff.schedules.index')->with(
                'success',
                'Berhasil mengubah data!'
            );
        } else {
            return redirect()->back() - with('error', 'Gagal! silahkan coba lagi');
        }
    }



     public function export()
     {
        $fileName = 'data-jadwal-tayang.xlsx';
        // memproses donwload
        return Excel::download(new ScheduleExport, $fileName);
     }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule,$id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menghapus data!');
    }

    public function trash()
    {
        $scheduleTrash = Schedule::with(['cinema', 'movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }

    public function restore($id)
    {
       $schedule = Schedule::onlyTrashed()->find($id);
       $schedule->restore();
         return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }

        public function showSeats($scheduleId, $hourId)
        {
            $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
            $hour = $schedule['hours'][$hourId];
            
            // ambil data kursi dengan kriteria
            // 1. sudah dibayar (ada paid_date di ticket payment)
            // 2. tiket dibeli di tgl dan jam yang sesuai diklik

            $seats = Ticket::where('schedule_id', $scheduleId)
                ->whereHas('ticketPayment', function ($q) {
                    // ambil tanggal sekarang
                    $date = now()->format('Y-m-d');
                    // whereDate : mencari berdasarkan tanggal
                    $q->whereDate('paid_date', $date);
                })
                ->whereTime('hour', $hour)
                ->pluck('rows_of_seats');
            
            // pluck() : mengambil data hanya satu column
            
            // Inisialisasi array kosong untuk menampung hasil
            $seatsFormat = [];
            
            // Loop through setiap seat yang di-pluck
            foreach ($seats as $seat) {
                // Jika seat adalah string (contoh: "A1,A2,A3"), convert ke array
                if (is_string($seat)) {
                    $seatArray = explode(',', $seat);
                    // Trim whitespace dari setiap elemen
                    $seatArray = array_map('trim', $seatArray);
                    $seatsFormat = array_merge($seatsFormat, $seatArray);
                } 
                // Jika seat sudah berupa array
                elseif (is_array($seat)) {
                    $seatsFormat = array_merge($seatsFormat, $seat);
                }
            }
            
            // Hapus duplikat kursi jika ada
            $seatsFormat = array_unique($seatsFormat);
            
            return view('schedules.show-seats', compact('schedule', 'hour', 'seatsFormat'));
        }
}