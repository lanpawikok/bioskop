@extends('templates.app')

@section('content')
    <div class="container mt-3">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promo.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('staff.promo.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('staff.promo.create') }}" class="btn btn-success">Tambahkan Data</a>
        </div>
        <h5 class="mt-3">Data Promo</h5>
        <table class="table table-bordered" id="promosTable">
            <tr>
                <th>#</th>
                <th>Kode Promo</th>
                <th>Total Potongan</th>
                <th>Aksi</th>
            </tr>
            {{-- $users dapat dari compact --}}
            {{-- foreach karena $users pake ::all() datanya lebih dari satu dan berbentuk array --}}
            @foreach ($promos as $key => $item)
                <tr>
                    {{-- key -> index array dari 0 --}}
                    <td>{{ $key + 1 }}</td>
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
                        {{-- $sistem['id'] akan terkirim ke {id} di routenya --}}
                        <a href="{{ route('staff.promo.edit', $item['id']) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('staff.promo.delete', $item->id) }}" method="POST">
                            {{-- untuk menggunakan route ::delete harus dengan form --}}
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection

@push('script')
    <script>
        $(function () {
            $('#promosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('staff.promo.datatables') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'promo_code', name: 'promo_code' },
                    { data: 'type', name: 'type' },
                    { data: 'discount', name: 'discount' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            })
        })
        </script>
@endpush