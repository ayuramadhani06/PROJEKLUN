@extends('be.master')
@php
  $title = 'Users Management';
  $breadcrumb = 'Users';
@endphp
@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">User Management</h4>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            + Tambah User
        </button>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Permission</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>

                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->username }}</td>
                        <td>{{ $u->email ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $u->permission == 'manage' ? 'success' : 'secondary' }}">
                                {{ $u->permission == 'manage' ? 'Full Access' : 'Read Only' }}
                            </span>
                        </td>

                        <td class="text-center">

                            <!-- EDIT -->
                            <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $u->id }}">
                                Edit
                            </button>

                            <!-- DELETE -->
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-delete">
                                    Hapus
                                </button>
                            </form>

                        </td>
                    </tr>

                    <!-- MODAL EDIT -->
                    <div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('users.update', $u->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="text" name="name" class="form-control mb-2"
                                            value="{{ $u->name }}" required>

                                        <input type="text" name="username" class="form-control mb-2"
                                            value="{{ $u->username }}" required>

                                        <input type="email" name="email" class="form-control mb-2"
                                            value="{{ $u->email }}" placeholder="Email (optional)">

                                        <input type="password" name="password" class="form-control mb-2"
                                            placeholder="Password baru (opsional)">

                                        <select name="permission" class="form-control">
                                            <option value="read" {{ $u->permission == 'read' ? 'selected' : '' }}>
                                                Read Only
                                            </option>
                                            <option value="manage" {{ $u->permission == 'manage' ? 'selected' : '' }}>
                                                Full Access
                                            </option>
                                        </select>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Update</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>

</div>

<!-- MODAL CREATE -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="text" name="name" class="form-control mb-2"
                        placeholder="Nama" required>

                    <input type="text" name="username" class="form-control mb-2"
                        placeholder="Username" required>

                    <input type="email" name="email" class="form-control mb-2"
                        placeholder="Email (optional)">

                    <input type="password" name="password" class="form-control mb-2"
                        placeholder="Password" required>

                    <select name="permission" class="form-control">
                        <option value="read">Read Only</option>
                        <option value="manage">Full Access</option>
                    </select>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Simpan</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // DELETE CONFIRMATION
    // =========================
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            let form = this.closest('.delete-form');

            Swal.fire({
                title: 'Yakin?',
                text: "User akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // =========================
    // SUCCESS NOTIFICATION
    // =========================
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

});
</script>

@endsection