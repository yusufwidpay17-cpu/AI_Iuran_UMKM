<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::orderBy('created_at', 'desc')->get();
        return view('pengguna.index', compact('pengguna'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,admin,petugas',
        ]);

        $userData = [
            'id' => (string) Str::uuid(),
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];

        // Jika tabel users memiliki kolom email dan email diisi
        if (Schema::hasColumn('users', 'email') && $request->filled('email')) {
            $userData['email'] = $request->email;
        }

        User::create($userData);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan. Pengguna dapat langsung login.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:superadmin,admin,petugas',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if (Schema::hasColumn('users', 'email') && $request->filled('email')) {
            $data['email'] = $request->email;
        }

        $user->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return redirect()->route('pengguna.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}

