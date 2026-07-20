<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard Organisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0">
                    <div class="card-body p-5">
                        <h3 class="text-center fw-bold mb-4 text-primary">Login Dashboard HIMA</h3>
                        
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tenant.login.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email Organisasi</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required autocomplete="current-password">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ingat Saya</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Masuk Dashboard</button>
                        </form>

                        <hr class="my-4">
                        <p class="text-center mb-0">Organisasi belum terdaftar? <a href="{{ route('tenant.register') }}">Daftar Sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>