@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 class="page-title" style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--on-surface);">Manajemen Pengguna & Hak Akses</h2>
        <p class="page-subtitle" style="margin: 4px 0 0; color: var(--on-surface-variant); font-size: 0.875rem;">
            Kelola hak akses pengelola (Superadmin/Admin/Petugas) dan undang pedagang dengan pembuatan kata sandi privat
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="bukaModalTambah()" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah / Undang Pengguna
        </button>
    </div>
</div>

@if(session('activation_preview_link'))
    <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: var(--radius-md); padding: 16px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div style="font-weight: 700; color: #065f46; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                <span>🔗</span> Tautan Aktivasi Akun Siap Digunakan
            </div>
            <span style="font-size: 0.75rem; background: #10b981; color: white; padding: 2px 8px; border-radius: 12px; font-weight: 600;">Privat & Aman</span>
        </div>
        <p style="font-size: 0.85rem; color: #047857; margin: 0 0 10px;">
            Email undangan telah dikirimkan ke kotak masuk pengguna. Anda juga dapat langsung menyalin tautan di bawah ini untuk dibagikan via WhatsApp/chat kepada pedagang terkait:
        </p>
        <div style="display: flex; gap: 8px; align-items: center;">
            <input type="text" id="copyActivationLink" value="{{ session('activation_preview_link') }}" readonly style="flex: 1; padding: 8px 12px; border-radius: 8px; border: 1px solid #6ee7b7; font-size: 0.8rem; background: white; font-family: monospace; color: #065f46;">
            <button type="button" class="btn btn-primary btn-sm" onclick="salinTautanAktivasi()" style="white-space: nowrap; font-size: 0.8rem;">
                📋 Salin Tautan
            </button>
        </div>
    </div>
@endif

<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid var(--outline-variant); background: var(--surface-container-lowest);">
    <div class="table-responsive">
        <table class="table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
            <thead style="background-color: var(--surface-container-low); border-bottom: 1px solid var(--outline-variant);">
                <tr>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">NAMA & EMAIL</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">USERNAME</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">PERAN (ROLE)</th>
                    <th style="padding: 16px; text-align: center; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">STATUS AKUN</th>
                    <th style="padding: 16px; text-align: left; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px;">TERDAFTAR</th>
                    <th style="padding: 16px; text-align: center; font-size: 0.8125rem; font-weight: 700; color: var(--outline); letter-spacing: 0.5px; width: 220px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengguna as $user)
                <tr style="border-bottom: 1px solid var(--outline-variant); transition: background-color 0.2s;">
                    <td style="padding: 16px; vertical-align: middle;">
                        <strong style="color: var(--on-surface); font-weight: 600; display: block;">{{ $user->nama }}</strong>
                        <span style="font-size: 0.8rem; color: var(--on-surface-variant);">
                            {{ $user->email ?? 'Belum ada email' }}
                        </span>
                        @if($user->role === 'pedagang' && $user->pedagang)
                            <div style="margin-top: 4px; font-size: 0.75rem; color: var(--primary); font-weight: 600;">
                                🏪 Kios {{ $user->pedagang->no_kios }} ({{ $user->pedagang->nama_usaha ?? $user->pedagang->nama_pedagang }})
                            </div>
                        @endif
                    </td>
                    <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant);">
                        <code>{{ $user->username }}</code>
                    </td>
                    <td style="padding: 16px; vertical-align: middle;">
                        @if($user->role == 'superadmin')
                            <span style="background-color: #f3e8ff; color: #7e22ce; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Super Admin</span>
                        @elseif($user->role == 'admin')
                            <span style="background-color: #e0f2fe; color: #0369a1; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Admin</span>
                        @elseif($user->role == 'petugas')
                            <span style="background-color: #fef3c7; color: #b45309; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Petugas Lapangan</span>
                        @else
                            <span style="background-color: #d1fae5; color: #047857; border-radius: 999px; padding: 4px 12px; font-size: 0.75rem; font-weight: 700; display: inline-block;">Pedagang</span>
                        @endif
                    </td>
                    <td style="padding: 16px; vertical-align: middle; text-align: center;">
                        @if(($user->status_aktivasi ?? 'aktif') === 'pending')
                            <span style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; border-radius: 999px; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                ⏳ Menunggu Sandi
                            </span>
                        @else
                            <span style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; border-radius: 999px; padding: 4px 10px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                                ✓ Aktif
                            </span>
                        @endif
                    </td>
                    <td style="padding: 16px; vertical-align: middle; color: var(--on-surface-variant); font-size: 0.85rem;">
                        {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                    </td>
                    <td style="padding: 16px; vertical-align: middle; text-align: center;">
                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                            @if(($user->status_aktivasi ?? 'aktif') === 'pending')
                                <form action="{{ route('pengguna.resend-activation', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" style="padding: 4px 8px; font-size: 0.75rem; background: #f59e0b; color: white;" title="Kirim Ulang Tautan Aktivasi">
                                        📩 Resend
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-secondary btn-sm" onclick="editUser('{{ $user->id }}', '{{ addslashes($user->nama) }}', '{{ $user->username }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->pedagang_id }}', '{{ $user->status_aktivasi ?? 'aktif' }}')" style="padding: 4px 10px; font-size: 0.75rem;">
                                Edit
                            </button>
                            @if(auth()->id() != $user->id)
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user->id }}')" style="padding: 4px 10px; font-size: 0.75rem; background-color: #ef4444; color: white;">
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($pengguna->isEmpty())
                <tr>
                    <td colspan="6" class="text-center" style="padding: 40px; color: var(--outline);">
                        Belum ada data pengguna yang terdaftar.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah / Undang Pengguna -->
<div id="modalTambah" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 540px; padding: 24px; position: relative; border-radius: 14px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--on-surface);">Tambah / Berikan Hak Akses Login</h3>
            <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--outline);">&times;</button>
        </div>
        
        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf
            
            <!-- Pilih Peran (Role) -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Peran (Hak Akses Akun) <span style="color: #ef4444;">*</span></label>
                <select name="role" id="tambah_role" class="form-select" required onchange="handleRoleChange(this.value)">
                    <option value="pedagang">Pedagang (Hanya melihat kios & tagihan miliknya)</option>
                    <option value="petugas">Petugas Lapangan (Mencatat transaksi & pembayaran)</option>
                    <option value="admin">Admin (Kelola semua data master & laporan)</option>
                    <option value="superadmin">Super Admin (Akses penuh tanpa batas)</option>
                </select>
            </div>

            <!-- Bagian Khusus Role Pedagang: Pilih Data Pedagang -->
            <div class="form-group" id="group_pedagang_pilihan" style="margin-bottom: 14px; background: #f0fdf4; padding: 12px; border-radius: 8px; border: 1px solid #bbf7d0;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem; color: #166534;">
                    Hubungkan ke Data Lapak / Pedagang <span style="color: #ef4444;">*</span>
                </label>
                <select name="pedagang_id" id="tambah_pedagang_id" class="form-select" onchange="autoFillPedagang(this)">
                    <option value="">-- Pilih Data Pedagang Yang Terdaftar --</option>
                    @foreach($pedagangList as $pdg)
                        <option value="{{ $pdg->id }}" data-nama="{{ $pdg->nama_pedagang }}" data-telepon="{{ $pdg->no_telepon }}">
                            {{ $pdg->nama_pedagang }} (Kios: {{ $pdg->no_kios ?? '-' }} - {{ $pdg->blok_kios ?? '-' }})
                        </option>
                    @endforeach
                </select>
                <div style="font-size: 0.75rem; color: #15803d; margin-top: 4px;">
                    Akun ini hanya dapat melihat informasi tagihan dan kuitansi kios terpilih.
                </div>
            </div>

            <!-- Nama Lengkap -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Nama Pengguna <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama" id="tambah_nama" class="form-control" required placeholder="Nama pemilik akun">
            </div>

            <!-- Email Terdaftar (Kunci untuk Aktivasi Privat) -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Alamat Email <span style="color: #ef4444;">*</span></label>
                <input type="email" name="email" id="tambah_email" class="form-control" placeholder="contoh: pedagang@gmail.com" required>
                <div style="font-size: 0.75rem; color: var(--on-surface-variant); margin-top: 4px;">
                    Tautan aktivasi privat akan dikirimkan ke alamat email ini.
                </div>
            </div>

            <!-- Username untuk Login -->
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Username Login <span style="color: #ef4444;">*</span></label>
                <input type="text" name="username" id="tambah_username" class="form-control" required placeholder="Username tanpa spasi">
            </div>

            <!-- Pilihan Metode Kata Sandi -->
            <div class="form-group" style="margin-bottom: 16px; background: var(--surface-container-low); padding: 12px; border-radius: 8px;">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; display: block;">
                    Penentuan Kata Sandi (Password):
                </label>
                
                <div style="margin-bottom: 8px;">
                    <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 0.85rem; color: var(--on-surface);">
                        <input type="radio" name="metode_pembuatan" value="aktivasi_email" checked onchange="toggleMetodePassword('aktivasi_email')" style="margin-top: 3px;">
                        <div>
                            <strong>Kirim Tautan Aktivasi ke Email (Direkomendasikan)</strong>
                            <div style="font-size: 0.75rem; color: var(--on-surface-variant);">
                                Pengguna/pedagang akan menerima email resmi dan menentukan kata sandinya sendiri secara mandiri.
                            </div>
                        </div>
                    </label>
                </div>

                <div>
                    <label style="display: flex; align-items: flex-start; gap: 8px; cursor: pointer; font-size: 0.85rem; color: var(--on-surface);">
                        <input type="radio" name="metode_pembuatan" value="manual_password" onchange="toggleMetodePassword('manual_password')" style="margin-top: 3px;">
                        <div>
                            <strong>Tentukan Kata Sandi Manual Sekarang oleh Admin</strong>
                            <div style="font-size: 0.75rem; color: var(--on-surface-variant);">
                                Admin langsung membuatkan password saat ini juga tanpa menunggu aktivasi email.
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Input Manual Password (Hanya Muncul Jika Dipilih Manual) -->
            <div class="form-group" id="group_manual_password" style="display: none; margin-bottom: 18px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Kata Sandi Manual (Min. 6 Karakter)</label>
                <input type="password" name="password" id="input_manual_password" class="form-control" placeholder="Masukkan kata sandi">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTambah').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitTambah">Kirim Undangan Akses</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="modalEdit" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; overflow-y: auto; padding: 20px;">
    <div class="modal-content card" style="width: 100%; max-width: 520px; padding: 24px; position: relative; border-radius: 14px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--outline-variant); padding-bottom: 12px;">
            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--on-surface);">Edit Data Pengguna</h3>
            <button type="button" onclick="document.getElementById('modalEdit').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--outline);">&times;</button>
        </div>
        
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Nama Lengkap</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Alamat Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email pengguna">
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Peran (Role)</label>
                <select name="role" id="edit_role" class="form-select" required onchange="handleEditRoleChange(this.value)">
                    <option value="pedagang">Pedagang</option>
                    <option value="petugas">Petugas Lapangan</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>

            <div class="form-group" id="group_edit_pedagang" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Tautkan ke Kios Pedagang</label>
                <select name="pedagang_id" id="edit_pedagang_id" class="form-select">
                    <option value="">-- Tanpa Tautan Kios --</option>
                    @foreach($semuaPedagang as $pdg)
                        <option value="{{ $pdg->id }}">
                            {{ $pdg->nama_pedagang }} (Kios: {{ $pdg->no_kios ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Status Akun</label>
                <select name="status_aktivasi" id="edit_status_aktivasi" class="form-select" required>
                    <option value="aktif">Aktif (Dapat Login)</option>
                    <option value="pending">Pending (Menunggu Aktivasi Password Mandiri)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label class="form-label" style="font-weight: 600; font-size: 0.85rem;">Reset Kata Sandi Baru <small style="font-weight: normal; color: var(--on-surface-variant);">(Kosongkan jika tidak diubah)</small></label>
                <input type="password" name="password" id="edit_password" class="form-control" minlength="6" placeholder="Ketik kata sandi baru untuk mengganti">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
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
    function bukaModalTambah() {
        document.getElementById('modalTambah').style.display = 'flex';
        handleRoleChange(document.getElementById('tambah_role').value);
    }

    function handleRoleChange(role) {
        const groupPedagang = document.getElementById('group_pedagang_pilihan');
        const selectPedagang = document.getElementById('tambah_pedagang_id');
        if (role === 'pedagang') {
            groupPedagang.style.display = 'block';
            selectPedagang.required = true;
        } else {
            groupPedagang.style.display = 'none';
            selectPedagang.required = false;
        }
    }

    function handleEditRoleChange(role) {
        const groupEditPedagang = document.getElementById('group_edit_pedagang');
        if (role === 'pedagang') {
            groupEditPedagang.style.display = 'block';
        } else {
            groupEditPedagang.style.display = 'none';
        }
    }

    function autoFillPedagang(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const nama = selectedOpt.getAttribute('data-nama');
            const inputNama = document.getElementById('tambah_nama');
            const inputUsername = document.getElementById('tambah_username');
            if (nama && !inputNama.value) {
                inputNama.value = nama;
            }
            if (nama && !inputUsername.value) {
                // Generate username rekomendasi dari nama tanpa spasi
                const cleanUser = nama.toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 15);
                inputUsername.value = cleanUser + Math.floor(100 + Math.random() * 900);
            }
        }
    }

    function toggleMetodePassword(metode) {
        const groupManual = document.getElementById('group_manual_password');
        const inputManual = document.getElementById('input_manual_password');
        const btnSubmit = document.getElementById('btnSubmitTambah');
        const inputEmail = document.getElementById('tambah_email');

        if (metode === 'manual_password') {
            groupManual.style.display = 'block';
            inputManual.required = true;
            inputEmail.required = false;
            btnSubmit.textContent = 'Simpan & Buat Akun';
        } else {
            groupManual.style.display = 'none';
            inputManual.required = false;
            inputEmail.required = true;
            btnSubmit.textContent = 'Kirim Undangan Akses';
        }
    }

    function editUser(id, nama, username, email, role, pedagangId, statusAktivasi) {
        document.getElementById('formEdit').action = "{{ url('pengguna') }}/" + id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_pedagang_id').value = pedagangId || '';
        document.getElementById('edit_status_aktivasi').value = statusAktivasi || 'aktif';
        document.getElementById('edit_password').value = '';
        
        handleEditRoleChange(role);
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.')) {
            const form = document.getElementById('formDelete');
            form.action = "{{ url('pengguna') }}/" + id;
            form.submit();
        }
    }

    function salinTautanAktivasi() {
        const copyText = document.getElementById("copyActivationLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(function() {
            alert("✓ Tautan aktivasi berhasil disalin ke clipboard! Anda dapat mengirimkannya via WhatsApp kepada pedagang.");
        });
    }
</script>
@endsection
