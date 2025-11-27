@extends('templates.app')
@section('content')
    <div class="container mt-3">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary ms-2">Export (.xlsx)</a>
            <a href="{{ route('admin.users.trash') }}" class="btn btn-secondary ms-2">Data Sampah</a>
            <a href="{{ route('admin.users.cstaf') }}" class="btn btn-success ms-2">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Pengguna (Admin & Staff)</h5>
        <table class="table table-bordered" id="usersTable">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            @foreach ($users as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['email'] }}</td>
                    <td class="text-center">
                        @if ($item['role'] == 'admin')
                            <span class="badge badge-primary">Admin</span>
                        @elseif ($item['role'] == 'staff')
                            <span class="badge badge-success">Staff</span>
                        @endif
                    </td>
                    <td class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.users.estaf', $item['id']) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('admin.users.dstaf', $item['id']) }}" method="POST">
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
            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users.datatables') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                     { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            })
        })
        </script>
@endpush
