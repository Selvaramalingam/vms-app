<x-app-layout>
    <x-slot name="header">Invoice</x-slot>

    <style>
        /* ===== PRINT ===== */
        @media print {
            body * { visibility: hidden !important; }
            #invoice-printable, #invoice-printable * { visibility: visible !important; }
            #invoice-printable { position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; background: #fff; padding: 28px; }
            .no-print { display: none !important; }
            @page { size: A4 landscape; margin: 14mm; }
        }

        /* ===== Invoice Table ===== */
        .inv-table { border-collapse: collapse; width: 100%; font-size: 13px; }
        .inv-table td, .inv-table th { transition: none; }

        /* Checkbox row */
        .inv-table .chk-row th { background: #f8fafc; padding: 7px 14px; border-bottom: 1px solid #e2e8f0; }
        .col-toggle-label {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 5px; cursor: pointer; width: 100%;
            font-size: 10px; color: #64748b; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;
        }
        .col-toggle-label input[type="checkbox"] {
            width: 14px; height: 14px; accent-color: #4f46e5; cursor: pointer; flex-shrink: 0;
        }
        .col-toggle-label:has(input:not(:checked)) { opacity: 0.35; }

        /* Header row */
        .inv-table .hdr-row th {
            background: #1e293b; color: #fff; padding: 10px 14px;
            text-align: left; font-weight: 600; font-size: 11px;
            letter-spacing: 0.06em; text-transform: uppercase;
        }
        .inv-table .hdr-row th.text-right { text-align: right; }
        .inv-table .hdr-row th.text-center { text-align: center; }

        /* Body */
        .inv-table td { padding: 10px 14px; border-bottom: 1px solid #e2e8f0; color: #334155; vertical-align: middle; }
        .inv-table tbody tr:last-child td { border-bottom: none; }
        .inv-table tbody tr:hover { background: #f8fafc; }
        .inv-table tbody tr:nth-child(even) { background: #f8fafc; }
        .inv-table tbody tr:nth-child(even):hover { background: #f1f5f9; }
        .inv-table .text-right { text-align: right; }
        .inv-table .text-center { text-align: center; }

        /* ===== Invoice Summary Table ===== */
        .inv-summary { width: 100%; border-collapse: collapse; font-size: 13px; }
        .inv-summary td { padding: 9px 16px; border-bottom: 1px solid #e2e8f0; }
        .inv-summary tr:last-child td { border-bottom: none; }
        .inv-summary .lbl { color: #64748b; font-weight: 500; }
        .inv-summary .val { color: #1e293b; font-weight: 700; text-align: right; }
        .inv-summary .grand-lbl { color: #1e293b; font-weight: 700; font-size: 14px; }
        .inv-summary .grand-val { color: #4f46e5; font-weight: 800; font-size: 18px; text-align: right; }
    </style>

    {{-- Dynamic print-hide style (updated by JS) --}}
    <style id="print-hide-style"></style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ================ FILTER CARD ================ --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 no-print">
                <form action="{{ route('invoices.index') }}" method="GET"
                      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Vehicle Number</label>
                        <select name="vehicle_id" id="filter_vehicle"
                                class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->vehicle_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Location</label>
                        <input type="text" name="location" id="filter_location"
                               value="{{ request('location') }}" placeholder="e.g. Chennai"
                               class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">From Date</label>
                        <input type="date" name="from_date" id="filter_from_date"
                               value="{{ request('from_date') }}"
                               class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">To Date</label>
                        <input type="date" name="to_date" id="filter_to_date"
                               value="{{ request('to_date') }}"
                               class="block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" id="btn_filter"
                                class="flex-1 bg-indigo-600 text-white font-semibold text-sm py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition">
                            Generate
                        </button>
                        <a href="{{ route('invoices.index') }}"
                           class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-lg shadow-sm hover:bg-slate-50 transition flex items-center justify-center">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            @if($trips->isNotEmpty())

            {{-- ============ ACTION BUTTONS ============ --}}
            <div class="flex gap-3 justify-end no-print">
                <button onclick="printInvoice()" id="btn_print"
                        class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg shadow hover:bg-slate-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
                <button onclick="downloadPDF()" id="btn_pdf"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-rose-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </button>
            </div>

            {{-- ============ INVOICE PRINTABLE ============ --}}
            <div id="invoice-printable" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

                {{-- ── Invoice Header ── --}}
                <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex flex-col gap-1.5">
                    
                    {{-- Vehicle Number --}}
                    <h2 class="text-2xl font-bold tracking-tight">
                        {{ $selectedVehicle ? $selectedVehicle->vehicle_number : 'All Vehicles' }}
                    </h2>

                    {{-- Location --}}
                    @php
                        $invoiceLocation = request('location') ?: ($trips->first()?->location ?? null);
                    @endphp
                    @if($invoiceLocation)
                        <div class="text-lg text-indigo-100 font-medium">
                            {{ $invoiceLocation }}
                        </div>
                    @endif

                    {{-- Date --}}
                    <div class="mt-2">
                        <p class="text-indigo-200 text-sm font-medium">{{ now()->format('d M Y') }}</p>
                    </div>

                </div>

                {{-- ── Data Table ── --}}
                <div class="overflow-x-auto">
                    <table class="inv-table" id="inv-table">
                        <thead>
                            {{-- Checkbox row: controls print/pdf columns only (screen always shows all) --}}
                            <tr class="chk-row no-print" id="chk-row">
                                <th class="text-center" data-col="0">
                                    <label class="col-toggle-label" title="Include S.No in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="0" checked> S.No
                                    </label>
                                </th>
                                <th data-col="1">
                                    <label class="col-toggle-label" title="Include Date in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="1" checked> Date
                                    </label>
                                </th>
                                <th data-col="2">
                                    <label class="col-toggle-label" title="Include Open Hr in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="2" checked> Open Hr
                                    </label>
                                </th>
                                <th data-col="3">
                                    <label class="col-toggle-label" title="Include Close Hr in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="3" checked> Close Hr
                                    </label>
                                </th>
                                <th data-col="4">
                                    <label class="col-toggle-label" title="Include Total Hr in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="4" checked> Total Hr
                                    </label>
                                </th>
                                <th data-col="5">
                                    <label class="col-toggle-label" title="Include Diesel Ltr in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="5" checked> Diesel Ltr
                                    </label>
                                </th>
                                <th data-col="6">
                                    <label class="col-toggle-label" title="Include Diesel Price in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="6" checked> Diesel Price
                                    </label>
                                </th>
                                <th data-col="7">
                                    <label class="col-toggle-label" title="Include Rent Amount in print/PDF">
                                        <input type="checkbox" class="col-toggle" data-col="7" checked> Rent Amt
                                    </label>
                                </th>
                            </tr>

                            {{-- Column header row --}}
                            <tr class="hdr-row">
                                <th class="text-center" data-col="0" style="width:52px;">S.No</th>
                                <th data-col="1">Date</th>
                                <th class="text-right" data-col="2">Open Hr</th>
                                <th class="text-right" data-col="3">Close Hr</th>
                                <th class="text-right" data-col="4">Total Hr</th>
                                <th class="text-right" data-col="5">Diesel (Ltr)</th>
                                <th class="text-right" data-col="6">Diesel Price (₹)</th>
                                <th class="text-right" data-col="7">Rent Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trips as $i => $trip)
                                <tr>
                                    <td class="text-center text-slate-400 font-semibold" data-col="0">{{ $i + 1 }}</td>
                                    <td class="font-semibold text-slate-800" data-col="1">
                                        {{ $trip->date ? $trip->date->format('d M Y') : '—' }}
                                    </td>
                                    <td class="text-right tabular-nums" data-col="2">{{ $trip->open_hour ?? '—' }}</td>
                                    <td class="text-right tabular-nums" data-col="3">{{ $trip->close_hour ?? '—' }}</td>
                                    <td class="text-right tabular-nums font-semibold text-indigo-600" data-col="4">
                                        {{ number_format((float)($trip->total_hour ?? 0), 2) }}
                                    </td>
                                    <td class="text-right tabular-nums" data-col="5">
                                        {{ number_format((float)($trip->fuel_litre ?? 0), 2) }}
                                    </td>
                                    <td class="text-right tabular-nums text-rose-600 font-semibold" data-col="6">
                                        ₹{{ number_format((float)($trip->fuel_cost ?? 0), 2) }}
                                    </td>
                                    <td class="text-right tabular-nums text-emerald-700 font-bold" data-col="7">
                                        ₹{{ number_format((float)($trip->total_amount ?? 0), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            {{-- Spacer row: one line gap before summary --}}
                            <tr>
                                <td colspan="8" style="height:18px; border-bottom:none; background:transparent; padding:0;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ── Invoice Summary ── --}}
                <div class="border-t border-slate-200">
                    <div class="flex justify-end">
                        <div class="w-full lg:w-80 xl:w-96 lg:border-l border-slate-200">
                            <table class="inv-summary" id="inv-summary">
                                <tbody>
                                    {{-- Always shown --}}
                                    <tr id="sum-trips">
                                        <td class="lbl">Total Trips</td>
                                        <td class="val">{{ $trips->count() }}</td>
                                    </tr>
                                    {{-- Only when col 4 (Total Hr) is checked --}}
                                    <tr id="sum-hours" data-summary-col="4">
                                        <td class="lbl">Total Hours</td>
                                        <td class="val">{{ number_format($trips->sum('total_hour'), 2) }} hr</td>
                                    </tr>
                                    {{-- Only when col 5 (Diesel Ltr) is checked --}}
                                    <tr id="sum-diesel-ltr" data-summary-col="5">
                                        <td class="lbl">Diesel Used</td>
                                        <td class="val">{{ number_format($totalDieselLtr, 2) }} ltr</td>
                                    </tr>
                                    {{-- Only when col 6 (Diesel Price) is checked --}}
                                    <tr id="sum-diesel-price" data-summary-col="6">
                                        <td class="lbl">Overall Diesel Price</td>
                                        <td class="val" style="color:#e11d48;">₹{{ number_format($totalOverallDiesel, 2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    {{-- Only when col 7 (Rent Amount) is checked --}}
                                    <tr id="sum-rent" data-summary-col="7" style="background:#f8fafc; border-top: 2px solid #e2e8f0;">
                                        <td class="grand-lbl" style="padding:12px 16px;">Total Rent Amount</td>
                                        <td class="grand-val" style="padding:12px 16px; text-align:right; color:#4f46e5; font-size:20px; font-weight:800;">
                                            ₹{{ number_format($totalRentAmount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- /#invoice-printable -->

            @else

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-slate-700 font-semibold text-base mb-1">No invoice data found</p>
                <p class="text-slate-400 text-sm">Apply filters above and click <strong>Generate</strong> to build your invoice.</p>
            </div>

            @endif

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
    const TOTAL_COLS = 8; // cols 0–7

    // Summary rows tied to column checkboxes
    // col 4 → #sum-hours, col 5 → #sum-diesel-ltr, col 6 → #sum-diesel-price, col 7 → #sum-rent
    const SUMMARY_MAP = { 4: 'sum-hours', 5: 'sum-diesel-ltr', 6: 'sum-diesel-price', 7: 'sum-rent' };

    // Build @media print CSS hiding unchecked table cols AND summary rows
    function updatePrintStyle() {
        const style = document.getElementById('print-hide-style');
        const rules = [];

        for (let i = 0; i < TOTAL_COLS; i++) {
            const chk = document.querySelector(`.col-toggle[data-col="${i}"]`);
            if (chk && !chk.checked) {
                // Hide table column
                rules.push(`#inv-table [data-col="${i}"] { display: none !important; }`);
                // Hide corresponding summary row if mapped
                if (SUMMARY_MAP[i]) {
                    rules.push(`#${SUMMARY_MAP[i]} { display: none !important; }`);
                }
            }
        }

        style.textContent = rules.length
            ? `@media print { ${rules.join(' ')} }`
            : '';
    }

    // Wire checkboxes
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.col-toggle').forEach(chk => {
            chk.addEventListener('change', updatePrintStyle);
        });
        updatePrintStyle();
    });

    // Print
    function printInvoice() {
        updatePrintStyle();
        window.print();
    }

    // Download PDF
    async function downloadPDF() {
        const btn = document.getElementById('btn_pdf');
        const origHTML = btn.innerHTML;
        btn.disabled = true;
        btn.textContent = 'Generating…';

        // Hide checkbox row
        const chkRow = document.getElementById('chk-row');
        chkRow.style.display = 'none';

        // Temporarily hide unchecked columns AND their summary rows for canvas capture
        const hiddenEls = [];
        for (let i = 0; i < TOTAL_COLS; i++) {
            const chk = document.querySelector(`.col-toggle[data-col="${i}"]`);
            if (chk && !chk.checked) {
                // Hide table column cells
                document.querySelectorAll(`#inv-table [data-col="${i}"]`).forEach(el => {
                    el.style.display = 'none';
                    hiddenEls.push(el);
                });
                // Hide corresponding summary row
                if (SUMMARY_MAP[i]) {
                    const row = document.getElementById(SUMMARY_MAP[i]);
                    if (row) { row.style.display = 'none'; hiddenEls.push(row); }
                }
            }
        }

        const element = document.getElementById('invoice-printable');
        const origOverflow = element.style.overflow;
        element.style.overflow = 'visible';

        try {
            const canvas = await html2canvas(element, {
                scale: 2, useCORS: true, logging: false, backgroundColor: '#ffffff',
            });

            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
            const pw = pdf.internal.pageSize.getWidth();
            const ph = pdf.internal.pageSize.getHeight();
            const iw = pw - 20;
            const ih = (canvas.height * iw) / canvas.width;

            let y = 10, rem = ih;
            while (rem > 0) {
                const sh = Math.min(rem, ph - 20);
                const sy = ih - rem;
                pdf.addImage(imgData, 'PNG', 10, y, iw, sh, '', 'FAST', 0,
                    -(sy / ih) * canvas.height * (iw / canvas.width));
                rem -= sh;
                if (rem > 0) { pdf.addPage(); y = 10; }
            }

            const vehicle = '{{ $selectedVehicle ? $selectedVehicle->vehicle_number : "All" }}';
            const from    = '{{ request("from_date") ?? "" }}';
            const to      = '{{ request("to_date") ?? "" }}';
            pdf.save(['Invoice', vehicle, from, to].filter(Boolean).join('_') + '.pdf');

        } catch (err) {
            console.error(err);
            alert('PDF generation failed. Please try printing instead.');
        } finally {
            hiddenEls.forEach(el => el.style.display = '');
            chkRow.style.display = '';
            element.style.overflow = origOverflow;
            btn.disabled = false;
            btn.innerHTML = origHTML;
        }
    }
    </script>
</x-app-layout>
