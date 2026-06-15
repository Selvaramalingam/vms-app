<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024, 2) . ' KB',
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
            
            // Sort by date descending
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        Artisan::call('vms:backup');
        return redirect()->route('backups.index')->with('success', 'Backup triggered successfully!');
    }

    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (File::exists($path)) {
            return response()->download($path);
        }
        return redirect()->route('backups.index')->with('error', 'Backup file not found.');
    }

    public function destroy($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
            return redirect()->route('backups.index')->with('success', 'Backup deleted successfully.');
        }
        return redirect()->route('backups.index')->with('error', 'Backup file not found.');
    }
}
