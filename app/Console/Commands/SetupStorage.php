<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupStorage extends Command
{
    protected $signature = 'storage:setup';
    protected $description = 'Setup storage directories for the application';

    public function handle()
    {
        $this->info('Setting up storage directories...');
        
        // Buat direktori yang diperlukan
        $directories = [
            'public/pengaduan/bukti',
            'public/pengaduan/proses',
        ];
        
        foreach ($directories as $dir) {
            if (!Storage::exists($dir)) {
                Storage::makeDirectory($dir);
                $this->info("Created directory: storage/app/{$dir}");
            } else {
                $this->info("Directory already exists: storage/app/{$dir}");
            }
        }
        
        // Buat storage link jika belum ada
        if (!file_exists(public_path('storage'))) {
            $this->call('storage:link');
        } else {
            $this->info('Storage link already exists');
        }
        
        // Set permissions (untuk Unix/Linux systems)
        if (PHP_OS_FAMILY !== 'Windows') {
            chmod(storage_path('app/public'), 0755);
            foreach ($directories as $dir) {
                $fullPath = storage_path('app/' . $dir);
                if (is_dir($fullPath)) {
                    chmod($fullPath, 0755);
                }
            }
            $this->info('Set directory permissions to 755');
        }
        
        $this->info('Storage setup completed successfully!');
        
        return 0;
    }
}