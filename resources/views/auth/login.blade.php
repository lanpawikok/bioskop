<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TIXID - Login</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h2>TIXID</h2>
            <p class="text-muted">Masuk ke akun Anda</p>
        </div>

        <form action="{{ route('login.auth') }}" method="POST">
            @csrf
            
            @if(session('success'))
                <div class="alert alert-success my-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger my-3">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Email input -->
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" id="form1Example1" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" />
                <label class="form-label" for="form1Example1">Email address</label>
            </div>

            <!-- Password input -->
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="form1Example2" class="form-control @error('password') is-invalid @enderror" name="password">
                <label class="form-label" for="form1Example2">Password</label>
            </div>

            <!-- Submit button -->
            <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block w-100 mb-3">Login</button>
            
            <div class="text-center">
                <p class="mb-2">Belum punya akun? <a href="{{ route('signup') }}">Daftar sekarang</a></p>
                <a href="{{ route('home') }}" class="text-muted">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.1.0/mdb.umd.min.js"></script>
</body>

</html>