<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Organisasi / HIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-body p-5">
                        <h3 class="text-center fw-bold mb-4 text-primary">Daftar Akun HIMA / Panitia</h3>
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tenant.register.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nama Organisasi / HIMA</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: HMIF AMIKOM" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Resmi Organisasi</label>
                                <input type="email" name="email" class="form-control" placeholder="Contoh: hmif@amikom.ac.id" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Singkat</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan profil singkat organisasi Anda...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Daftarkan Organisasi</button>
                        </form>

                        <hr class="my-4">
                        <p class="text-center mb-0">Sudah punya akun? <a href="{{ route('tenant.login') }}">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>