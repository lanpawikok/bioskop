@extends('templates.app')
@section('content')
    <div class="container my-5">
       <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.index')}}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Jadwal Film</h3>
        @if (Session::get('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Poster</th>
                <th>Judul Film</th>
                <th>Aksi</th>
            </tr>
            @foreach ($movieTrash as $skey => $item)
            <tr>
                <th>{{$skey+1}}</th>
                <th>
                    <img src="{{ asset('storage/'. $item['poster']) }}" width="120">
                </th>
                <th>{{$item['title']}}</th>
               
                <td class="d-flex">
                     <form action="{{ route('admin.movies.restore', $item->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success ms-2">Kembalikan</button>
                    </form>

                    <form action="{{ route('admin.movies.delete_permanent', $item->id) }}" method="POST" onclick="return confirm('Yakin Menghapus Data Ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger ms-2">Hapus</button>
                    </form>

                </td>
            </tr>
             @endforeach
        </table>


    </div>
@endsection

