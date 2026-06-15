<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VmsBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vms:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the VMS database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting VMS database backup...');

        $dbPath = database_path('database.sqlite');
        
        if (!File::exists($dbPath)) {
            $this->error('Database file not found at: ' . $dbPath);
            return 1;
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'vms_backup_' . date('Y-m-d_H-i-s') . '.sqlite';
        $backupPath = $backupDir . '/' . $filename;

        if (File::copy($dbPath, $backupPath)) {
            $this->info('Backup created successfully: ' . $filename);
            
            // Optional: Keep only last 7 backups
            $files = File::files($backupDir);
            if (count($files) > 7) {
                usort($files, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                
                $toDelete = count($files) - 7;
                for ($i = 0; $i < $toDelete; $i++) {
                    File::delete($files[$i]);
                    $this->info('Deleted old backup: ' . $files[$i]->getFilename());
                }
            }

            return 0;
        }

        $this->error('Failed to create backup.');
        return 1;
    }
}
