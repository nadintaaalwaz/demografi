<?php

namespace App\Console\Commands;

use App\Models\Penduduk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteAllPenduduk extends Command
{
    protected $signature = 'penduduk:delete-all {--force : Skip confirmation}';
    protected $description = 'Hapus semua data penduduk dari database';

    public function handle(): int
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  PERINGATAN: Anda akan menghapus SEMUA data penduduk!');
            $this->line('');

            if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
                $this->info('Pembatalan operasi.');
                return Command::SUCCESS;
            }
        }

        try {
            $count = Penduduk::count();
            Penduduk::truncate();

            $this->info("✅ Berhasil menghapus {$count} data penduduk");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Gagal menghapus data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
