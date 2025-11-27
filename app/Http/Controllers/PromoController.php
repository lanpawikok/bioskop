<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromoExport;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

     public function datatables()
    {
        $promos = Promo::query();
        return datatables()->of($promos)->addIndexColumn()->addColumn('action', function ($promo) {
            $btnEdit = '<a href="' . route('staff.promo.edit', $promo->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('staff.promo.delete', $promo->id) . '" method="POST">
                           ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    public function create()    
    {
        return view('staff.promo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|unique:promos,promo_code',
            'type'       => 'required|in:percent,rupiah',
            'discount'   => 'required|numeric|min:1',
        ]);

        // Validasi tambahan
        if ($request->type === 'percent' && $request->discount > 100) {
            return back()->withErrors(['discount' => 'Diskon dalam persen tidak boleh lebih dari 100'])->withInput();
        }

        if ($request->type === 'rupiah' && $request->discount < 1000) {
            return back()->withErrors(['discount' => 'Diskon dalam rupiah minimal Rp 1.000'])->withInput();
        }

        Promo::create([
            'promo_code' => $request->promo_code,
            'type'       => $request->type,
            'discount'   => $request->discount,
            'actived'    => 1
        ]);

        return redirect()->route('staff.promo.index')->with('success', 'Promo berhasil ditambahkan');
    }

    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('staff.promo.edit', compact('promo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'promo_code' => 'required|unique:promos,promo_code,' . $id,
            'discount'   => 'required|numeric|min:1',
            'type'       => 'required|in:percent,rupiah',
        ]);

        // Validasi tambahan sama kaya di store
        if ($request->type === 'percent' && $request->discount > 100) {
            return back()->withErrors(['discount' => 'Diskon dalam persen tidak boleh lebih dari 100'])->withInput();
        }

        if ($request->type === 'rupiah' && $request->discount < 500) {
            return back()->withErrors(['discount' => 'Diskon dalam rupiah minimal Rp 500'])->withInput();
        }

        $promo = Promo::findOrFail($id);
        $promo->update([
            'promo_code' => $request->promo_code,
            'discount'   => $request->discount,
            'type'       => $request->type,
        ]);

        return redirect()->route('staff.promo.index')->with('success', 'Promo berhasil diperbarui');
    }


    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();
        return redirect()->route('staff.promo.index')->with('success', 'Promo berhasil dihapus');
    }

    public function export()
     {
        $fileName = 'data-promo.xlsx';
        // memproses donwload
        return Excel::download(new PromoExport, $fileName);
     }


     public function trash()
    {
        $promoTrash = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promoTrash'));
    }

    public function restore($id)
    {
       $promo = Promo::onlyTrashed()->find($id);
        if ($promo) {
            $promo->restore();
            return redirect()->route('staff.promo.index')->with('success', 'Promo berhasil dikembalikan!');
        }
        return redirect()->back()->with('error', 'Promo tidak ditemukan.');
    }

    public function deletePermanent($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }

}