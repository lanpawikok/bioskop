@extends('templates.app')

@section('content')
    <div class="container mt-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promo.index')}}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Jadwal Promo</h3>
        @if (Session::get('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Judul Film</th>
                <th>Aksi</th>
            </tr>
            {{-- $users dapat dari compact --}}
            {{-- foreach karena $users pake ::all() datanya lebih dari satu dan berbentuk array --}}
            @foreach ($promoTrash as $index => $item)
                <tr>
                    {{-- key -> index array dari 0 --}}
                    <td>{{ $index + 1 }}</td>
                    {{-- name dan email dari fillable --}}
                    <td>{{ $item->promo_code }}</td>
                    <td>
                        @if ($item['type'] == 'rupiah')
                            <small class="badge badge-primary">Rp {{ number_format($item['discount'], 0, ',', '.') }}</small>
                        @else
                            <small class="badge badge-primary">{{ $item['discount'] }} %</small>
                        @endif
                    </td>
                    <td class="d-flex gap-2">
                         <form action="{{ route('staff.promo.restore', $item->id) }}" method="POST">
                            {{-- untuk menggunakan route ::delete harus dengan form --}}
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success ms-2">Kembalikan</button>
                        </form>
                        {{-- $sistem['id'] akan terkirim ke {id} di routenya --}}
                        <form action="{{ route('staff.promo.delete_permanent', $item->id) }}" method="POST">
                            {{-- untuk menggunakan route ::delete harus dengan form --}}
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