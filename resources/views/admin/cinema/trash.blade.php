@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.cinemas.index')}}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Jadwal Bioskop</h3>
        @if (Session::get('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Lokasi Bioskop</th>
                <th>Aksi</th>
            </tr>

            {{-- $cinemas : dari compact, karena pakai all jd array dimensi --}}
            @foreach ($cinemaTrash as $index => $item)
            <tr>
                {{-- $index dari 0, biar muncul dr 1 -> +1 --}}
                <th>{{  $index+1 }}</th>
                {{-- name, location dari fillable model Cinema --}}
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['location'] }}</td>
                <td class="d-flex">
                     <form action="{{ route('admin.cinemas.restore', $item->id) }}" method="post">
                        @csrf
                        @method('PATCH')
                    <button class="btn btn-success ms-2">Kembalikan</button>
                    </form>
                    <form action="{{ route('admin.cinemas.delete_permanent',$item->id)}}" method="post">
                        @csrf
                        @method('DELETE')
                    <button class="btn btn-danger ms-2">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
@endsection