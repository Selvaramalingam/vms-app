<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TripsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::with('vehicle', 'driver');

        // Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $trips = $query->latest()->paginate(20);
        $vehicles = Vehicle::all();
        $drivers = Driver::all();

        // Check if Export Action
        if ($request->has('export')) {
            $exportData = $query->get();
            if ($request->export == 'pdf') {
                $pdf = Pdf::loadView('reports.pdf', compact('exportData'));
                return $pdf->download('vms-report.pdf');
            } elseif ($request->export == 'excel') {
                return Excel::download(new TripsExport($exportData), 'vms-report.xlsx');
            }
        }

        return view('reports.index', compact('trips', 'vehicles', 'drivers'));
    }
}
