<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return view('admin.movie.index');
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    public function datatables()
    {
        $movies = Movie::query(); // eloquent model
        // DataTable::of($movies) -> mengambil data dari query model movie, keseluruhan field
        // addColumn -> menambahkan column yang bukan bagian dari field movies, biasana digunakan untuk
        // button field yang nilainya akan diolah/manipulasi

        // addIndexColumn -> mengambil index data, mulai dari 1
        return DataTables::of($movies)->addIndexColumn()->addColumn('poster_img', function ($movie) {
            $url = asset('storage/' . $movie->poster);
            return '<img src="' . $url . '" width="70">';
        })
        ->addColumn('actived_badge', function($movie) {
            if ($movie->actived) {
                return '<span class="badge badge-success">Aktif</span>';
            } else {
                return '<span class="badge badge-danger">Non Aktif</span>';
            }
        })
        ->addColumn('action', function ($movie) {
            $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showModal(' . $movie . ')\'>Detail</button>';
            $btnEdit = ' <a href="' . route('admin.movies.edit', $movie->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.movies.delete', $movie->id) . '" method="POST">
                        ' .@csrf_field() . method_field('DELETE') . '<button class="btn btn-danger">Hapus</button>
                        </form>';
                        $btnNonAktif = '';
                        if($movie->actived) {
                            $btnNonAktif = '<form action="' . route('admin.movies.nonaktif', $movie->id) . '" method="POST" class="me-2">
                        ' . csrf_field() . method_field('PATCH') . '<button class="btn btn-danger">Non-aktif</button>
                        </form>';
                        }
                        return '<div class="d-flex justify-content-center align-items-center gap-2">'
                        .$btnDetail . $btnEdit . $btnNonAktif . $btnDelete .
                        '</div>';
        })
        ->rawColumns(['poster_img', 'actived_badge', 'action'])
        ->make(true);
        // rawColumns -> mendaftarkan column yang baru dibuat pada addColumn
    }

    public function chart() {
        $filmActive = Movie::where('actived',1)->count(); // yang di perlukan hanya jumlah, count()
        $filmNonActive = Movie::where('actived', 0)->count();
        $data = [$filmActive, $filmNonActive];
        return response()->json([
            'data' => $data
        ]);
    }

    public function home()
    {
        $movies = Movie::where('actived', 1)->orderBy('created_at', 'Desc')->limit(3)
        ->get();
        return view('home', compact('movies'));
    }


    public function homeMovies(Request $request)
    {
        $query = Movie::where('actived', 1)->orderBy('created_at', 'DESC');

        if ($request->has('search_movie') && !empty($request->search_movie)) {
            $query->where('title', 'like', '%' . $request->search_movie . '%');
        }

        $movies = $query->get();

        return view('movies', compact('movies'));
    }

    public function movieSchedule($movie_id, Request $request)
    {
        $sortPrice = $request->sort_price;

        if($sortPrice){
          $movie = Movie::where('id', $movie_id)->with(['schedules'=>function($q)use($sortPrice){
            $q->orderBy('price', $sortPrice);
          }, 'schedules.cinema'])->first();
        }else{
         $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
        }

        $sortAlfabet = $request->sort_alfabet;
        if($sortAlfabet == 'ASC'){
            $movie->schedules = $movie->schedules->sortBy(function($schedule){
                return $schedule->cinema->name;})->values();
        }elseif($sortAlfabet == 'DESC'){
            $movie->schedules = $movie->schedules->sortByDesc(function($schedule){
                return $schedule->cinema->name;//diurutkan berdasarkan data ini
            })->values();
        }
          

        return view('schedules.detail', compact('movie'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required',
            'poster' => 'required|mimes:jpg,jpeg,png,svg,webp',
            'description' => 'required|min:10'
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'genre film harus diisi',
            'director.required' => 'Sutradara  harus diisi',
            'age_rating.required' => 'Usia minimal harus diisi',
            'poster.required' => 'Poster harus diisi',
            'poster.mimes' => 'Poster harus berupa jpg,jpeg,png,svg,webp',
            'description.required' => 'Sinopsis harus diisi',
            'description.min' => 'Sinopsis harus diisi minimal 10 karakter',
        ]);

        $poster = $request->file('poster');
        $namaFile = Str::random(5,10) . "-poster." . $poster->getClientOriginalExtension();
        $path = $poster->storeAs("poster",$namaFile, "public");
        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path,
            'description' => $request->description,
            'actived' => 1
        ]);
            if ($createData) {
                return redirect()->route('admin.movies.index')->with('success', 'Berhasil menambahkan data!');
            } else {
                return redirect()->back()->with('error', 'Gagal silahkan coba lagi.');
            }

    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie, $id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required',
            'poster' => 'required|mimes:jpg,jpeg,png,svg,webp',
            'description' => 'required|min:10',
        ], [
            'title.required' => 'judul film harus di isi',
            'duration.required' => 'durasil film harus di isi',
            'genre.required' => 'genre film harus di isi',
            'director.required' => 'Sutradara harus di isi',
            'age_rating.required' => 'usia minimal harus di isi',
            'poster.required' => 'poster film harus di isi',
            'poster.mimes' => 'poster harus di isi berupa jpg,jpeg,png,svg,webp',
            'description.required' => 'sinopsis harus di isi',
            'description.min' => 'sinopsis harus di isi minimal 10 ',

        ]);
        //ambil data sebelumnya
        $movie = Movie::find($id);

        //jika input file poster disisi
        if ($request->hasFile('poster')) {
            $filePath = storage_path('app/public/' . $movie->poster);
            //jika file ada di storage path tersebut
            if (file_exists($filePath)) {
                //hapus file lama
                unlink($filePath);
            }
            $file = $request->file('poster');
            //buat nama baru untuk file
            $fileName = 'poster' . Str::random(10) .'.' .
            $file->getClientOriginalExtension();
            $path = $file->storeAs('poster', $fileName, 'public');
        }
        $updateData = $movie->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $request->hasFile('poster') ? $path : $movie->poster,
            'description' => $request->description,
            'actived' => 1
        ]);

        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil memperbarui detail');
        } else {
            return redirect()->back()->with('error', 'Gagal silahkan coba lagi');
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie, $id)

    {
         $schedules = Schedule::where('movie_id',$id)->count();
        if($schedules > 0){
            return redirect()->route('admin.movies.index')->with('error',
            'Gagal menghapus data! karena bioskop masih memiliki jadwal tayang');
        }

        $movie = Movie::findOrFail($id);
        $filePath = storage_path('app/public/' . $movie->poster);
        Movie::where('id', $id)->delete();
        
        return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus film');
    }


    public function nonaktif($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->actived = $movie->actived ? 0 : 1; // ubah jadi non-aktif
        $movie->save();
        return redirect()->route('admin.movies.index')->with('success', 'Berhasil non-aktif data film!');

        // $movie = Movie::findOrFail($id);
        // $movie->actived = 0; // ubah jadi non-aktif
        // $movie->save();

        // return redirect()->route('movies.index')->with('success', 'Berhasil non-aktif data film!');
    }

    public function export()
    {
        //nama file yang akan di dowload
        //ekstensi antara xlsx/csv
        $fileName = "data-film.xlsx";
        //proses download
        return Excel::download(new MovieExport, $fileName);
    }

    public function trash()
    {
        $movieTrash = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movieTrash'));
    }

    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->restore();
         return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');

        if ($movie->poster && file_exists($filePath)) {
            unlink($filePath);
        }
    }
}

