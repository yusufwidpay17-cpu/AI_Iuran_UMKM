@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="page-header">
    <div>
        <h2 class="page-title">Manajemen Pengguna</h2>
        <p class="page-subtitle">Kelola admin dan petugas yang dapat mengakses sistem</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('modalTambah').style.display='flex'">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pengguna
        </button>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    <div class="table-responsive">
        <table class="table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
            <thead style="background-color: var(--surface-container-lowest); border-bottom: 1px solid var(--outline-variant);">
                <tr>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">NAMA PENGGUNA</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">USERNAME</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">EMAIL</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">PERAN (ROLE)</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">DITAMBAHKAN PADA</th>
                    <th style="padding: 16px; text-align: center; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px; width: 150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengguna as $user)
                <tr style="border-bottom: 1px solid var(--outline-variant); transition: background-color 0.2s;">
                    <td style="padding: 16px; vertical-align: middle;">
                        <strong style="color: var(--on-surface); font-weight: 600;">{{ $user->nama }}</strong>
                    </td>
                    <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant);">
                        {{ $user->username }}
                    </td>
                    <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant);">
                        {{ $user->email }}
                    </td>
                    <td style="padding: 16px; vertical-align: middle;">
                        @if($user->role == 'superadmin')
                            <span style="background-color: #e6f4ea; color: #1e8e3e; border-radius: 999px; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Super Admin</span>
                        @elseif($user->role == 'admin')
                            <span style="background-color: #e8f0fe; color: #1967d2; border-radius: 999px; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Admin</span>
                        @else
                            <span style="background-color: #fef7e0; color: #b06000; border-radius: 999px; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Petugas</span>
                        @endif
                    </td>
                    <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant);">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td style="padding: 16px; vertical-align: middle; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <form action="{{ route('pengguna.reset-password', $user->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="padding: 6px 12px; min-height: 32px; background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" title="Kirim Link Reset Password" onclick="return confirm('Kirim link reset password ke email pengguna ini?')">Reset Pass</button>
                            </form>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="editUser('{{ $user->id }}', '{{ $user->nama }}', '{{ $user->username }}', '{{ $user->email }}', '{{ $user->role }}')" style="padding: 6px 12px; min-height: 32px;">Edit</button>
                            @if(auth()->id() != $user->id)
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user->id }}')" style="padding: 6px 12px; min-height: 32px; background-color: #d32f2f; color: white;">Hapus</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($pengguna->isEmpty())
                <tr>
                    <td colspan="6" class="text-center" style="padding: 40px; color: var(--outline);">
                        Belum ada data pengguna.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="modal-content card" style="width: 100%; max-width: 500px; padding: 24px; position: relative;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Tambah Pengguna</h3>
        <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="position: absolute; right: 24px; top: 24px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        
        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">Peran (Role)</label>
                <select name="role" class="form-select" required>
                    <option value="petugas">Petugas Lapangan</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="modal-content card" style="width: 100%; max-width: 500px; padding: 24px; position: relative;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Edit Pengguna</h3>
        <button type="button" onclick="document.getElementById('modalEdit').style.display='none'" style="position: absolute; right: 24px; top: 24px; background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">Peran (Role)</label>
                <select name="role" id="edit_role" class="form-select" required>
                    <option value="petugas">Petugas Lapangan</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalEdit').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Hapus -->
<form id="formDelete" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function editUser(id, nama, username, email, role) {
        document.getElementById('formEdit').action = "{{ url('pengguna') }}/" + id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            const form = document.getElementById('formDelete');
            form.action = "{{ url('pengguna') }}/" + id;
            form.submit();
        }
    }
</script>
@endsection
