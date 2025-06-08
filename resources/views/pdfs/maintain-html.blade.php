<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Room Maintenance Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                font-size: 11px;
            }

            .page-break {
                page-break-before: always;
            }

            .section {
                break-inside: avoid;
            }
        }

        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            color: #333;
        }

        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .left-column,
        .right-column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
        }

        .right-column {
            padding-right: 0;
            padding-left: 2%;
        }

        .section {
            margin-bottom: 15px;
            break-inside: avoid;
        }

        .section-title {
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 6px 8px;
            margin-bottom: 8px;
            border: 1px solid #ddd;
            font-size: 12px;
            color: #333;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-item {
            display: table-cell;
            padding: 3px 5px;
            border-bottom: 1px dotted #ccc;
            vertical-align: top;
        }

        .info-item:first-child {
            width: 40%;
            font-weight: bold;
            background-color: #fafafa;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .checkmark {
            font-family: DejaVu Sans, sans-serif;
            font-weight: bold;
            color: #333;
        }

        .yes {
            color: #28a745;
        }

        .no {
            color: #dc3545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }

        .full-width {
            width: 100%;
            clear: both;
            margin-top: 20px;
        }

        .value {
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button class="btn" onclick="window.print()">🖨️ Print</button>
        <button class="btn" onclick="downloadPDF()">📄 Save as PDF</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="header">
        <h1>Room Maintenance Record</h1>
        <p><strong>Room:</strong> {{ $record->room_number }} | <strong>Generated:</strong> {{ date('Y-m-d H:i:s') }} |
            <strong>Maintained by:</strong> {{ $record->user->name ?? 'Unknown' }}
        </p>
    </div>

    <div class="two-column">
        <!-- LEFT COLUMN -->
        <div class="left-column">
            <div class="section">
                <div class="section-title">Room Information</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-item">Room Number</div>
                        <div class="info-item value">{{ $record->room_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Maintained By</div>
                        <div class="info-item value">{{ $record->user->name ?? 'Unknown' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Last Updated</div>
                        <div class="info-item value">{{ $record->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Room Features</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-item">Ceiling Light</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_ceiling_light ? 'yes' : 'no' }}">
                                {{ $record->has_ceiling_light ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Ceiling Fan</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_ceiling_fan ? 'yes' : 'no' }}">
                                {{ $record->has_ceiling_fan ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Wall Light</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_wall_light ? 'yes' : 'no' }}">
                                {{ $record->has_wall_light ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Bathroom Light</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_bathroom_light ? 'yes' : 'no' }}">
                                {{ $record->has_bathroom_light ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Air Conditioning</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_ac ? 'yes' : 'no' }}">
                                {{ $record->has_ac ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($record->has_tv)
                <div class="section">
                    <div class="section-title">Entertainment</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-item">Television</div>
                            <div class="info-item">
                                <span class="checkmark yes">✓ Yes</span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">TV Model</div>
                            <div class="info-item value">{{ $record->tv_model ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">TV Remote</div>
                            <div class="info-item">
                                <span class="checkmark {{ $record->has_tv_remote ? 'yes' : 'no' }}">
                                    {{ $record->has_tv_remote ? '✓ Yes' : '✗ No' }}
                                </span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">TV Placement</div>
                            <div class="info-item value">{{ $record->tv_place ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-column">
            <div class="section">
                <div class="section-title">Bed Configuration</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-item">Bed Type</div>
                        <div class="info-item value">{{ $record->bed_type ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Bed Rails</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_bed_rails ? 'yes' : 'no' }}">
                                {{ $record->has_bed_rails ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Bed Rail Covers</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_bed_rail_covers ? 'yes' : 'no' }}">
                                {{ $record->has_bed_rail_covers ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-item">Mattress Type</div>
                        <div class="info-item value">{{ $record->mattress_type ?? 'N/A' }}</div>
                    </div>
                    @if ($record->mattress_type === 'Air Mattress')
                        <div class="info-row">
                            <div class="info-item">Air Mattress Machine</div>
                            <div class="info-item value">{{ $record->air_mattress_machine_type ?? 'N/A' }}</div>
                        </div>
                    @endif
                    <div class="info-row">
                        <div class="info-item">Sensor Mat</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_sensor_mat ? 'yes' : 'no' }}">
                                {{ $record->has_sensor_mat ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Security Features</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-item">Door Lock</div>
                        <div class="info-item">
                            <span class="checkmark {{ $record->has_door_lock ? 'yes' : 'no' }}">
                                {{ $record->has_door_lock ? '✓ Yes' : '✗ No' }}
                            </span>
                        </div>
                    </div>
                    @if ($record->has_door_lock)
                        <div class="info-row">
                            <div class="info-item">Door Lock PIN</div>
                            <div class="info-item value">{{ $record->door_lock_pin ?? 'N/A' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- FULL WIDTH SECTIONS (History Tables) -->
    @php
        // Get guard checks for the specified year using your model's relationship
        $guardChecks = $record->fireDoorGuardChecks->filter(function ($check) use ($year) {
            return \Carbon\Carbon::parse($check->checked_date)->year == $year;
        });
    @endphp
    @if ($guardChecks->count() > 0)
        <div class="full-width section">
            <div class="section-title">Fire Door Guard Check History ({{ $year }})</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Date Checked</th>
                        <th style="width: 80%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guardChecks as $check)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($check->checked_date)->format('M j, Y') }}</td>
                            <td>{{ $check->notes ?: 'No notes' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @php
        // Get battery replacements for the specified year using your model's relationship
        $batteryReplacements = $record->fireDoorGuardBatteryReplacements->filter(function ($replacement) use ($year) {
            return \Carbon\Carbon::parse($replacement->replaced_date)->year == $year;
        });
    @endphp
    @if ($batteryReplacements->count() > 0)
        <div class="full-width section">
            <div class="section-title">Fire Door Guard Battery Replacement History ({{ $year }})</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Date Replaced</th>
                        <th style="width: 80%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($batteryReplacements as $replacement)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($replacement->replaced_date)->format('M j, Y') }}</td>
                            <td>{{ $replacement->notes ?: 'No notes' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @php
        // Get comments for the specified year using your model's relationship
        $comments = $record->comments->filter(function ($comment) use ($year) {
            return \Carbon\Carbon::parse($comment->date)->year == $year;
        });
    @endphp
    @if ($comments->count() > 0)
        <div class="full-width section">
            <div class="section-title">Maintenance Comments History ({{ $year }})</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 85%;">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comments as $comment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($comment->date)->format('M j, Y') }}</td>
                            <td>{{ $comment->comment }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Footer -->
    <div
        style="margin-top: 30px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
        <p>This maintenance record was generated automatically on {{ date('Y-m-d H:i:s') }}</p>
    </div>

    {{-- <script>
        function downloadPDF() {
            // Modern browsers support print to PDF
            window.print();
        }

        // Auto-focus print when opened in new tab
        window.addEventListener('load', function() {
            if (window.location.search.includes('autoprint=1')) {
                setTimeout(() => window.print(), 500);
            }
        });
    </script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const roomNumber = "{{ $record->room_number }}";
            const year = new Date().getFullYear();
            const fileName = `Room-${roomNumber}-${year}.pdf`;

            const element = document.body;
            html2pdf().from(element).save(fileName);
        }
    </script>

    <script>
        window.addEventListener('open-new-window', event => {
            window.open(event.detail.url, '_blank');
        });
    </script>

</body>

</html>
