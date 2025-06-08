<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Room Maintenance Record - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }

        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            color: #333;
        }

        h2 {
            font-size: 18px;
            margin: 0 0 10px 0;
            color: #666;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 8px 12px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
            color: #333;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px dotted #ccc;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }

        .info-value {
            color: #333;
            flex: 1;
        }

        .yes { color: #28a745; font-weight: bold; }
        .no { color: #dc3545; font-weight: bold; }

        .history-section {
            margin-top: 30px;
        }

        .history-item {
            margin-bottom: 12px;
            padding: 10px;
            border-left: 3px solid #007bff;
            background-color: #f8f9fa;
        }

        .history-date {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 4px;
        }

        .history-content {
            color: #333;
        }

        .no-data {
            color: #666;
            font-style: italic;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px dashed #ddd;
        }

        .print-controls {
            margin-bottom: 20px;
            text-align: center;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }

        .print-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="print-button" onclick="window.print()">🖨️ Print This Page</button>
        <button class="print-button" onclick="window.close()">❌ Close</button>
    </div>

    <div class="header">
        <h1>Room Maintenance Record</h1>
        <h2>Year {{ $filter_year }}</h2>
        <p><strong>Room:</strong> {{ $record['room_number'] }} | <strong>Generated:</strong> {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <div class="section-title">Room Information</div>
            <div class="info-item">
                <div class="info-label">Room Number</div>
                <div class="info-value">{{ $record['room_number'] }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value">{{ $record['updated_at'] }}</div>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Bed Configuration</div>
            <div class="info-item">
                <div class="info-label">Bed Type</div>
                <div class="info-value">{{ $record['bed_type'] ?: 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Bed Rails</div>
                <div class="info-value {{ $record['has_bed_rails'] ? 'yes' : 'no' }}">
                    {{ $record['has_bed_rails'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Bed Rail Covers</div>
                <div class="info-value {{ $record['has_bed_rail_covers'] ? 'yes' : 'no' }}">
                    {{ $record['has_bed_rail_covers'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Mattress Type</div>
                <div class="info-value">{{ $record['mattress_type'] ?: 'N/A' }}</div>
            </div>
            @if ($record['mattress_type'] === 'Air Mattress')
                <div class="info-item">
                    <div class="info-label">Air Mattress Machine</div>
                    <div class="info-value">{{ $record['air_mattress_machine_type'] ?: 'N/A' }}</div>
                </div>
            @endif
            <div class="info-item">
                <div class="info-label">Sensor Mat</div>
                <div class="info-value {{ $record['has_sensor_mat'] ? 'yes' : 'no' }}">
                    {{ $record['has_sensor_mat'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <div class="section-title">Room Features</div>
            <div class="info-item">
                <div class="info-label">Ceiling Light</div>
                <div class="info-value {{ $record['has_ceiling_light'] ? 'yes' : 'no' }}">
                    {{ $record['has_ceiling_light'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Ceiling Fan</div>
                <div class="info-value {{ $record['has_ceiling_fan'] ? 'yes' : 'no' }}">
                    {{ $record['has_ceiling_fan'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Wall Light</div>
                <div class="info-value {{ $record['has_wall_light'] ? 'yes' : 'no' }}">
                    {{ $record['has_wall_light'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Bathroom Light</div>
                <div class="info-value {{ $record['has_bathroom_light'] ? 'yes' : 'no' }}">
                    {{ $record['has_bathroom_light'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Air Conditioning</div>
                <div class="info-value {{ $record['has_ac'] ? 'yes' : 'no' }}">
                    {{ $record['has_ac'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="section-title">Security & Entertainment</div>
            <div class="info-item">
                <div class="info-label">Door Lock</div>
                <div class="info-value {{ $record['has_door_lock'] ? 'yes' : 'no' }}">
                    {{ $record['has_door_lock'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            @if ($record['has_door_lock'])
                <div class="info-item">
                    <div class="info-label">Door Lock PIN</div>
                    <div class="info-value">{{ $record['door_lock_pin'] ?: 'N/A' }}</div>
                </div>
            @endif
            <div class="info-item">
                <div class="info-label">Television</div>
                <div class="info-value {{ $record['has_tv'] ? 'yes' : 'no' }}">
                    {{ $record['has_tv'] ? '✓ Yes' : '✗ No' }}
                </div>
            </div>
            @if ($record['has_tv'])
                <div class="info-item">
                    <div class="info-label">TV Model</div>
                    <div class="info-value">{{ $record['tv_model'] ?: 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">TV Remote</div>
                    <div class="info-value {{ $record['has_tv_remote'] ? 'yes' : 'no' }}">
                        {{ $record['has_tv_remote'] ? '✓ Yes' : '✗ No' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">TV Placement</div>
                    <div class="info-value">{{ $record['tv_place'] ?: 'N/A' }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Historical Data Section -->
    <div class="history-section">
        <!-- Comments Section -->
        <div class="info-section">
            <div class="section-title">Comments ({{ $filter_year }})</div>
            @if (!empty($filtered_comments))
                @foreach ($filtered_comments as $comment)
                    <div class="history-item">
                        <div class="history-date">{{ $comment['date'] }}</div>
                        <div class="history-content">{{ $comment['comment'] }}</div>
                    </div>
                @endforeach
            @else
                <div class="no-data">No comments found for {{ $filter_year }}</div>
            @endif
        </div>

        <!-- Fire Door Guard Checks Section -->
        <div class="info-section">
            <div class="section-title">Fire Door Guard Checks ({{ $filter_year }})</div>
            @if (!empty($filtered_guard_checks))
                @foreach ($filtered_guard_checks as $check)
                    <div class="history-item">
                        <div class="history-date">{{ $check['checked_date'] }}</div>
                        <div class="history-content">{{ $check['notes'] ?: 'No notes' }}</div>
                    </div>
                @endforeach
            @else
                <div class="no-data">No fire door guard checks found for {{ $filter_year }}</div>
            @endif
        </div>

        <!-- Battery Replacements Section -->
        <div class="info-section">
            <div class="section-title">Battery Replacements ({{ $filter_year }})</div>
            @if (!empty($filtered_battery_replacements))
                @foreach ($filtered_battery_replacements as $replacement)
                    <div class="history-item">
                        <div class="history-date">{{ $replacement['replaced_date'] }}</div>
                        <div class="history-content">{{ $replacement['notes'] ?: 'No notes' }}</div>
                    </div>
                @endforeach
            @else
                <div class="no-data">No battery replacements found for {{ $filter_year }}</div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>This maintenance record was generated automatically on {{ date('Y-m-d H:i:s') }}</p>
        <p>Room {{ $record['room_number'] }} Maintenance Record - Year {{ $filter_year }}</p>
    </div>

    <script>
        // Auto-focus for better print experience
        window.onload = function() {
            // Optional: Auto-print when page loads (uncomment if desired)
            // window.print();
        };
    </script>
</body>
</html>