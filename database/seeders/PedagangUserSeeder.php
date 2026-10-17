<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pedagang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PedagangUserSeeder extends Seeder
{
    public function run()
    {
        $pedagangs = Pedagang::all();

        foreach ($pedagangs as $pdg) {
            // Check if pedagang already has a user account
            $existingUser = User::where('pedagang_id', $pdg->id)->first();
            if ($existingUser) {
                // Ensure it is active with pedagang role
                $existingUser->update([
                    'role' => 'pedagang',
                    'status_aktivasi' => 'aktif',
                    'password' => Hash::make('password123'),
                ]);
                continue;
            }

            // Generate clean username from name
            $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pdg->nama_pedagang));
            if (empty($cleanName)) {
                $cleanName = 'pedagang' . strtolower(str_replace('-', '', $pdg->no_kios ?? ''));
            }

            // Check if username already exists in users
            $username = $cleanName;
            if (User::where('username', $username)->exists()) {
                $username = $cleanName . 'pedagang';
            }
            if (User::where('username', $username)->exists()) {
                $kiosClean = strtolower(str_replace(['-', ' '], '', $pdg->no_kios ?? ''));
                $username = 'pdg_' . ($kiosClean ?: rand(10, 99));
            }

            $email = $username . '@amanahledger.id';

            User::create([
                'id' => (string) Str::uuid(),
                'nama' => $pdg->nama_pedagang,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'pedagang',
                'pedagang_id' => $pdg->id,
                'status_aktivasi' => 'aktif',
                'activation_token' => null,
                'activation_expires_at' => null,
            ]);
        }
    }
}
