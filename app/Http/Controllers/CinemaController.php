<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Cinema;
use App\Models\Movie;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CinemaExport;
use Illuminate\Support\Facades\Auth;

class CinemaController extends Controller
{
    public function index()
    {
        // Tidak perlu compact cinemas karena pakai DataTables server-side
        return view('admin.cinema.index');  // <- Ubah ke 'cinemas' (plural)
    }

    public function datatables()
    {
        $cinemas = Cinema::query();
        
        return datatables()->of($cinemas)
            ->addIndexColumn()
            ->addColumn('action', function ($cinema) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $cinema->id) . '" class="btn btn-sm btn-secondary me-2">Edit</a>';
                $btnDelete = '<form action="' . route('admin.cinemas.delete', $cinema->id) . '" method="POST" style="display:inline;">
                               ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus?\')">Hapus</button>
                            </form>';
                return '<div class="d-flex gap-2">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.cinema.create');  // <- Ubah ke 'cinemas' (plural)
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10',
        ], [
            'name.required' => 'Nama Bioskop harus diisi',
            'location.required' => 'Lokasi harus diisi',
            'location.min' => 'Lokasi harus diisi minimal 10 karakter'
        ]);

        $createData = Cinema::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        if ($createData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil membuat data baru!');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat data!');
        }
    }

    public function edit($id)
    {
        $cinema = Cinema::find($id);
        return view('admin.cinema.edit', compact('cinema'));  // <- Ubah ke 'cinemas' (plural)
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10'
        ], [
            'name.required' => 'Nama Bioskop harus diisi',
            'location.required' => 'Lokasi Bioskop harus diisi',
            'location.min' => 'Lokasi Bioskop harus diisi minimal 10 karakter',
        ]);

        $updateData = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        
        if ($updateData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan coba lagi');
        }
    }

    public function destroy($id)
    {
        $schedules = Schedule::where('cinema_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.cinemas.index')->with('error', 'Tidak dapat menghapus data bioskop! Data tertaut dengan jadwal tayang');
        }
        Cinema::where('id', $id)->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        $fileName = 'data-Cinema.xlsx';
        return Excel::download(new CinemaExport, $fileName);
    }

    public function trash()
    {
        $cinemaTrash = Cinema::onlyTrashed()->get();
        return view('admin.cinema.trash', compact('cinemaTrash'));
    }

    public function restore($id)
    {
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema->restore();
        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus seutuhnya!');
    }

    public function cinemaList()
    {
        $cinemas = Cinema::all();
        return view('schedules.cinemas', compact('cinemas'));
    }

    public function cinemaSchedule($cinema_id)
    {
        $schedules = Schedule::where('cinema_id', $cinema_id)
            ->with('movie')
            ->whereHas('movie', function($q) {
                $q->where('actived', 1);
            })
            ->get();
        return view('schedules.cinema-schedules', compact('schedules'));
    }
}