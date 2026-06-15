<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VMS Report PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Vehicle Management System - Reports</h2>
    <p>Generated on: {{ now()->format('d M Y h:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Vehicle</th>
                <th>Driver</th>
                <th class="text-right">Rent</th>
                <th class="text-right">Fuel</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @php $tRent=0; $tFuel=0; $tProfit=0; @endphp
            @foreach($exportData as $row)
                @php $tRent+=$row->rent_amount; $tFuel+=$row->fuel_cost; $tProfit+=$row->profit; @endphp
                <tr>
                    <td>{{ $row->date->format('d/m/Y') }}</td>
                    <td>{{ $row->location }}</td>
                    <td>{{ $row->vehicle->vehicle_number ?? '-' }}</td>
                    <td>{{ $row->driver->driver_name ?? '-' }}</td>
                    <td class="text-right">{{ $row->rent_amount }}</td>
                    <td class="text-right">{{ $row->fuel_cost }}</td>
                    <td class="text-right">{{ $row->profit }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th class="text-right">{{ $tRent }}</th>
                <th class="text-right">{{ $tFuel }}</th>
                <th class="text-right">{{ $tProfit }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
