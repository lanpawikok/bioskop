<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereIn('role', ['staff', 'admin'])->get();
        return view('admin.staff.istaf', compact('users'));
    }

    /**
     * LOGIN FIX (ROUTE: login.auth)
     */
    public function loginAuth(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect sesuai role
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login sebagai Admin');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('success', 'Berhasil login sebagai Staff');
            } else {
                return redirect()->route('home')->with('success', 'Berhasil login');
            }
        }

        return back()->with('error', 'Email atau password salah!');
    }

    public function datatables()
    {
        $users = User::query();
        return datatables()->of($users)->addIndexColumn()->addColumn('action', function ($staff) {
            $btnEdit = '<a href="' . route('admin.users.estaf', $staff->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '<form action="' . route('admin.users.dstaf', $staff->id) . '" method="POST">
                           ' . @csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    /**
     * REGISTER USER BARU
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|min:3',
            'last_name'      => 'required|min:3',
            'email'      => 'required|email:dns|unique:users,email',
            'password'       => 'required|min:8'
        ]);

        $createData = User::create([
            'name'     => $request->first_name . ' ' . $request->last_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user'
        ]);

        return $createData
            ? redirect()->route('login')->with('success', 'Berhasil membuat akun, silahkan login')
            : redirect()->route('signup')->with('failed', 'Gagal memproses data, coba lagi');
    }

    /**
     * OLD AUTH (masih dipakai mungkin oleh route lama)
     */
    public function authentication(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $data = $request->only(['email', 'password']);
        if (Auth::attempt($data)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'berhasil login ');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('success', 'berhasil login ');
            } else {
                return redirect()->route('home')->with('success', 'berhasil login ');
            }
        } else {
            return redirect()->back()->with('error', 'gagal pastikan email dan password benar');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda telah logout');
    }

    public function create()
    {
        return view('admin.staff.cstaf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $createData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);

        return $createData
            ? redirect()->route('admin.staff.istaf')->with('success', 'berhasil menambahkan data')
            : redirect()->back()->with('error', 'gagal menambahkan data');
    }

    public function edit($id)
    {
        $users = User::find($id);
        return view('admin.staff.estaf', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);

        $updateData = User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return $updateData
            ? redirect()->route('admin.staff.istaf')->with('success', 'Berhasil mengubah data')
            : redirect()->back()->with('error', 'Gagal, coba lagi');
    }

    public function destroy($id)
    {
        User::where('id',$id)->delete();
        return redirect()->route('admin.users.istaf')->with('success', 'berhasil menghapus data!');
    }

    public function export()
    {
        $fileName = 'data-petugas.xlsx';
        return Excel::download(new UserExport, $fileName);
    }

    public function trash()
    {
        $userTrash = User::onlyTrashed()->get();
        return view('admin.staff.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->restore();
        return redirect()->route('admin.users.istaf')->with('success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }
}
    