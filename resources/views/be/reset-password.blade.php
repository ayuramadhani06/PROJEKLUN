<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>

  <link href="{{ asset('be/css/soft-ui-dashboard.css') }}" rel="stylesheet" />
</head>

<body>
<main class="main-content mt-0">
  <section>
    <div class="page-header min-vh-75">
      <div class="container">
        <div class="row">

          <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto justify-content-center">
            <div class="card mt-8">

              <div class="card-header text-left">
                <h4>Reset Password</h4>
                <p>Masukkan username dan password baru</p>
              </div>

              <div class="card-body">

                @if($errors->any())
                  <div class="alert alert-danger text-white text-sm">
                    {{ $errors->first() }}
                  </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                  @csrf

                  <label>Username</label>
                  <input type="text" name="username" class="form-control mb-3" required>

                  <label>Password Baru</label>
                  <input type="password" name="password" class="form-control mb-3" required>

                  <label>Konfirmasi Password</label>
                  <input type="password" name="password_confirmation" class="form-control mb-3" required>

                  <button type="submit" class="btn bg-gradient-info w-100">
                    Reset Password
                  </button>

                  <div class="text-center mt-3">
                    <a href="{{ route('login') }}">Kembali ke Login</a>
                  </div>

                </form>

              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</main>
</body>
</html>