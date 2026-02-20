<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== LOGIN DEBUG TEST ===\n\n";

$user = User::first();

if ($user) {
    echo "User found:\n";
    echo "- ID: " . $user->id . "\n";
    echo "- Username: " . $user->username . "\n";
    echo "- Nama: " . $user->nama . "\n";
    echo "- Role: " . $user->role . "\n\n";
    
    echo "Password Test:\n";
    $password = 'kasisebalor726';
    $result = Hash::check($password, $user->password);
    echo "- Testing password: '$password'\n";
    echo "- Result: " . ($result ? "✅ MATCH" : "❌ NOT MATCH") . "\n\n";
    
    if (!$result) {
        echo "⚠️ Password tidak match! Silakan reset password.\n";
    } else {
        echo "✅ Password benar, login seharusnya berhasil.\n";
    }
} else {
    echo "❌ User tidak ditemukan!\n";
}

echo "\n=== END TEST ===\n";
