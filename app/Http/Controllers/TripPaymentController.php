<?php

namespace App\Http\Controllers;

use App\Models\TripPayment;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripPaymentController extends Controller
{
    public function index(Request $request)
    {
        // Get only the latest payment ID for each trip to prevent multiple grids for the same trip
        $latestPaymentIds = TripPayment::selectRaw('MAX(id) as id')
            ->groupBy('trip_id')
            ->pluck('id');

        $query = TripPayment::whereIn('id', $latestPaymentIds)
            ->with('trip.vehicle', 'trip.driver')->latest();

        if ($request->filled('vehicle_id')) {
            $query->whereHas('trip', function($q) use ($request) {
                $q->where('vehicle_id', $request->vehicle_id);
            });
        }
        if ($request->filled('driver_id')) {
            $query->whereHas('trip', function($q) use ($request) {
                $q->where('driver_id', $request->driver_id);
            });
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(15)->withQueryString();
        $vehicles = \App\Models\Vehicle::orderBy('vehicle_number')->get();
        $drivers = \App\Models\Driver::orderBy('driver_name')->get();
            
        return view('payments.index', compact('payments', 'vehicles', 'drivers'));
    }

    public function create(Request $request)
    {
        $trip_id = $request->get('trip_id');
        // Eager load payments so Alpine.js can use the history
        $trips = Trip::with('vehicle', 'driver', 'payments')
            ->latest()
            ->get();
        return view('payments.create', compact('trips', 'trip_id'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'paid' => 'required|numeric|min:1',
            'date' => 'required|date',
            'method' => 'required|string',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);
        $payments = $trip->payments()->get();
        
        $totalPaid = $payments->sum('paid') + $payments->sum('advance') + $validated['paid'];
        $balance = $trip->rent_amount - $totalPaid;

        // Auto-determine status
        if ($balance <= 0) {
            $status = 'completed';
            $balance = 0;
        } else {
            $status = 'partial';
        }

        TripPayment::create([
            'trip_id' => $trip->id,
            'advance' => 0,
            'paid' => $validated['paid'],
            'date' => $validated['date'],
            'balance' => $balance,
            'status' => $status,
            'method' => $validated['method'],
        ]);

        \App\Models\ActivityLog::log('created', 'Payment', 'Payment recorded for Trip #' . $trip->id . '. Amount: ₹' . $validated['paid']);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    public function edit(TripPayment $payment)
    {
        $payment->load('trip.vehicle', 'trip.driver');
        $paymentHistory = $payment->trip->payments()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        return view('payments.edit', compact('payment', 'paymentHistory'));
    }

    public function update(Request $request, TripPayment $payment)
    {
        $validated = $request->validate([
            'paid' => 'required|numeric|min:1',
            'date' => 'required|date',
            'method' => 'required|string',
        ]);

        $payment->update([
            'paid' => $validated['paid'],
            'date' => $validated['date'],
            'method' => $validated['method'],
            'advance' => 0,
        ]);

        $this->recalculateBalances($payment->trip);

        \App\Models\ActivityLog::log('updated', 'Payment', 'Payment updated for Trip #' . $payment->trip_id);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
    }

    public function destroy(TripPayment $payment)
    {
        $tripId = $payment->trip_id;
        $trip = $payment->trip;
        $payment->delete();

        $this->recalculateBalances($trip);

        \App\Models\ActivityLog::log('deleted', 'Payment', 'Payment deleted for Trip #' . $tripId);

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }

    private function recalculateBalances(Trip $trip)
    {
        $allPayments = $trip->payments()->orderBy('date')->orderBy('id')->get();
        $runningPaid = 0;
        
        foreach($allPayments as $p) {
            $runningPaid += $p->paid + $p->advance;
            $p->balance = $trip->rent_amount - $runningPaid;
            
            if ($p->balance <= 0) {
                $p->status = 'completed';
                $p->balance = 0;
            } else {
                $p->status = 'partial';
            }
            $p->save();
        }
    }

    public function tripPayment(Trip $trip)
    {
        $trip->load('vehicle', 'driver');
        $payments = $trip->payments()->latest()->get();
        
        $totalPaid = $payments->sum('paid') + $payments->sum('advance');
        $balance = $trip->rent_amount - $totalPaid;

        return view('payments.trip_payment', compact('trip', 'payments', 'totalPaid', 'balance'));
    }

    public function storeTripPayment(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string',
        ]);

        $payments = $trip->payments()->get();
        $totalPaid = $payments->sum('paid') + $payments->sum('advance') + $validated['amount'];
        $balance = $trip->rent_amount - $totalPaid;

        // Auto-determine status based on new balance
        if ($balance <= 0) {
            $status = 'completed';
            $balance = 0; // Prevent negative balance if overpaid
        } else {
            $status = 'partial';
        }

        TripPayment::create([
            'trip_id' => $trip->id,
            'advance' => 0,
            'paid' => $validated['amount'],
            'balance' => $balance,
            'status' => $status,
            'method' => $validated['method'],
        ]);

        \App\Models\ActivityLog::log('created', 'Payment', 'Payment recorded for Trip #' . $trip->id . '. Amount: ₹' . $validated['amount']);

        return redirect()->route('trips.payments', $trip)->with('success', 'Payment recorded successfully!');
    }
}
