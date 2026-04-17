@extends('be.master')

@php
  $title = 'Profile Settings';
  $breadcrumb = 'Profile';
  $isReadOnly = Auth::user()->permission === 'read';
@endphp

@section('content')
<div class="container-fluid py-4">

    {{-- Banner peringatan untuk user read-only --}}
    @if($isReadOnly)
    <div class="alert d-flex align-items-center gap-2 mb-4"
         style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; color:#92400e; font-size:0.82rem;">
        <i class="fas fa-lock" style="font-size:14px; flex-shrink:0;"></i>
        <span>
            Akun kamu berstatus <strong>read-only</strong>.
            Nama dan username hanya bisa dilihat, tidak bisa diubah.
            Hubungi administrator untuk mengubah data profil atau password.
        </span>
    </div>
    @endif

    <div class="card">
        <div class="card-header pb-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="profile-tab"
                            data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab">
                        Profile Information
                    </button>
                </li>
                @if(!$isReadOnly)
                <li class="nav-item">
                    <button class="nav-link" id="security-tab"
                            data-bs-toggle="tab" data-bs-target="#security"
                            type="button" role="tab">
                        Privacy & Security
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="retention-tab"
                            data-bs-toggle="tab" data-bs-target="#retention"
                            type="button" role="tab">
                        Retention Policy
                    </button>
                </li>
                @else
                {{-- Tab terkunci untuk read-only --}}
                <li class="nav-item">
                    <button class="nav-link disabled" type="button"
                            style="cursor:not-allowed; opacity:0.5;"
                            title="Tidak tersedia untuk akun read-only">
                        <i class="fas fa-lock me-1" style="font-size:10px;"></i>
                        Privacy & Security
                    </button>
                </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content mt-4">

                {{-- TAB INFORMASI PROFIL --}}
                <div class="tab-pane fade show active" id="profile" role="tabpanel">

                    @if($isReadOnly)
                    {{-- READ-ONLY: tampilkan data tanpa form --}}
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle">
                                    <tbody>
                                        <tr>
                                            <th width="30%">Name</th>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{ Auth::user()->name }}"
                                                       disabled readonly
                                                       style="background:#f9fafb; color:#6b7280; cursor:not-allowed;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Username</th>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="{{ Auth::user()->username }}"
                                                       disabled readonly
                                                       style="background:#f9fafb; color:#6b7280; cursor:not-allowed;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Permission</th>
                                            <td>
                                                <span class="badge"
                                                      style="background:#fff7ed; color:#92400e; border:1px solid #fed7aa; font-size:0.75rem; padding:4px 10px;">
                                                    <i class="fas fa-lock me-1"></i> Read Only
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-secondary" disabled
                                        style="cursor:not-allowed; opacity:0.5;">
                                    <i class="fas fa-lock me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                    @else
                    {{-- NORMAL: form bisa diedit --}}
                    <form id="formUpdateProfile" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Name</th>
                                                <td><input type="text" name="name" class="form-control"
                                                           value="{{ Auth::user()->name }}" required></td>
                                            </tr>
                                            <tr>
                                                <th>Username</th>
                                                <td><input type="text" name="username" class="form-control"
                                                           value="{{ Auth::user()->username }}" required></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="button"
                                            onclick="confirmSubmit('formUpdateProfile')"
                                            class="btn btn-info"
                                            style="background: linear-gradient(135deg, #8b0000 0%, #4a0e4e 100%);">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif

                </div>

                {{-- TAB PRIVASI & KEAMANAN (hanya untuk non-read) --}}
                @if(!$isReadOnly)
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <form id="formUpdatePassword" action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Old Password</th>
                                                <td><input type="password" name="old_password"
                                                           class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <th>New Password</th>
                                                <td><input type="password" name="password"
                                                           class="form-control" required></td>
                                            </tr>
                                            <tr>
                                                <th>Confirm New Password</th>
                                                <td><input type="password" name="password_confirmation"
                                                           class="form-control" required></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="button"
                                            onclick="confirmSubmit('formUpdatePassword')"
                                            class="btn btn-info"
                                            style="background: linear-gradient(135deg, #8b0000 0%, #4a0e4e 100%);">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
                
                <!--Start Retention Policy -->
<div class="tab-pane fade" id="retention" role="tabpanel">
                    <div class="row justify-content-center mb-4">
                            <div class="col-md-10">
                                <div class="alert d-flex flex-column gap-1"
                                    style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#166534; font-size:0.85rem; margin-bottom:0;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-database" style="font-size:14px;"></i>
                                        <strong style="font-size:0.95rem;">Active TimescaleDB Status</strong>
                                    </div>
                                    
                                    @if($activePolicy)
                                        <div class="d-flex flex-column gap-1 ps-2" style="border-left: 2px solid #86efac;">
                                            <span><strong>Target Table:</strong> <code style="color:#166534; background:#dcfce7; padding:2px 6px; border-radius:4px;">{{ $activePolicy->hypertable_name }}</code></span>
                                            <span><strong>Action:</strong> Menghapus data lebih tua dari <strong>{{ $activePolicy->drop_after }}</strong> secara otomatis.</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center gap-2 text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>Belum ada retention policy yang berjalan di level database.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    {{-- BLOK @php UNTUK AMBIL $retentionDays DI SINI SUDAH DIHAPUS --}}

                    @if($isReadOnly)

                        <div class="alert d-flex align-items-center gap-2"
                            style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; color:#92400e; font-size:0.82rem;">
                            <i class="fas fa-lock"></i>
                            <span>Retention policy tidak bisa diubah oleh akun read-only.</span>
                        </div>

                        <div class="mt-3">
                            <strong>Current Retention:</strong> {{ $retentionDays }} days
                        </div>

                    @else

                    <form id="formRetention" action="{{ route('retention.update') }}" method="POST">
                        @csrf

                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Retention (Days)</th>
                                                <td>
                                                    <input type="number"
                                                        name="retention_days"
                                                        class="form-control"
                                                        value="{{ $retentionDays }}"
                                                        min="1"
                                                        max="365"
                                                        required>
                                                    <small class="text-muted">
                                                        Data lama akan otomatis dibuang ke tempat sampah.
                                                    </small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="button"
                                            onclick="confirmSubmit('formRetention')"
                                            class="btn btn-info"
                                            style="background: linear-gradient(135deg, #8b0000 0%, #4a0e4e 100%);">
                                        Save Retention Policy
                                    </button>
                                </div>

                            </div>
                        </div>

                    </form>

                    @endif

                </div>
                <!--Finish Retention Policy -->
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT SWEETALERT2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmSubmit(formId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to save the changes?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#8b0000',
            cancelButtonColor: '#8392ab',
            confirmButtonText: 'Yes, Save it!',
            cancelButtonText: 'No, Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ $errors->first() }}",
        });
    @endif
</script>
@endsection