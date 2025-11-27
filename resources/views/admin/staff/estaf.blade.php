@extends('templates.app')
@section('content')
    <div class="w-75 d-block mx-auto my-5 p-4">
        <h5 class="text-center my-3">Mengubah Data Petugas</h5>
        <form method="POST" action="{{route('admin.users.istaf', ['id' => $users['id']])}}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{$users['name']}}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Email</label>
                <input type="email"  id="email"name="email" class="form-control @error('email') is-invalid @enderror" value="{{$users['email']}}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Password</label>
                <input type="password"  id="password"name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button class="btn btn-primary" type="submit">Tambah Data</button>
        </form>
    </div>
@endsection
