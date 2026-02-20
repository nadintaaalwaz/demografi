<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Kasi Pemerintahan
        // Akun Kasun akan dibuat oleh Kasi melalui sistem
        User::create([
            'username' => 'kasi',
            'password' => Hash::make('kasisebalor726'),
            'nama' => 'Kepala Seksi Pemerintahan',
            'role' => 'kasi',
            'id_dusun' => null, // Kasi tidak terikat dengan dusun tertentu
        ]);
    }
}
