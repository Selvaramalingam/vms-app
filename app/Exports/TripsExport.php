<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TripsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Location',
            'Vehicle',
            'Driver',
            'Total KM',
            'Rent Amount',
            'Fuel Cost',
            'Profit'
        ];
    }

    public function map($trip): array
    {
        return [
            $trip->id,
            $trip->date->format('Y-m-d'),
            $trip->location,
            $trip->vehicle->vehicle_number ?? 'N/A',
            $trip->driver->driver_name ?? 'N/A',
            $trip->total_km,
            $trip->rent_amount,
            $trip->fuel_cost,
            $trip->profit,
        ];
    }
}
