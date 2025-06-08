<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maintenance Record - Room {{ $record->room_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .section {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .section-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            font-size: 14px;
            color: #495057;
        }
        
        .section-content {
            padding: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        
        .info-value {
            color: #333;
        }
        
        .status-yes {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-no {
            color: #dc3545;
            font-weight: bold;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .history-table th,
        .history-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .history-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        .history-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .no-data {
            color: #6c757d;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        @media print {
            body { margin: 0; }
            .section { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Maintenance Record</h1>
        <p>Room {{ $record->room_number }} - {{ $filter_year }}</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Room Information -->
    <div class="section">
        <div class="section-header">Room Information</div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Room Number:</span>
                    <span class="info-value">{{ $record->room_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Maintained By:</span>
                    <span class="info-value">{{ $record->user->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bed Configuration -->
    <div class="section">
        <div class="section-header">Bed Configuration</div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Bed Type:</span>
                    <span class="info-value">{{ e($record->bed_type ?: 'Not specified') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bed Rails:</span>
                    <span class="info-value {{ $record->has_bed_rails ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_bed_rails ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bed Rail Covers:</span>
                    <span class="info-value {{ $record->has_bed_rail_covers ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_bed_rail_covers ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mattress Type:</span>
                    <span class="info-value">{{ $record->mattress_type ?: 'Normal Mattress' }}</span>
                </div>
                @if($record->mattress_type === 'Air Mattress' && $record->air_mattress_machine_type)
                <div class="info-item">
                    <span class="info-label">Air Mattress Machine:</span>
                    <span class="info-value">{{ e($record->air_mattress_machine_type) }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Sensor Mat:</span>
                    <span class="info-value {{ $record->has_sensor_mat ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_sensor_mat ? 'Yes' : 'No' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Features -->
    <div class="section">
        <div class="section-header">Room Features</div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Ceiling Light:</span>
                    <span class="info-value {{ $record->has_ceiling_light ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_ceiling_light ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ceiling Fan:</span>
                    <span class="info-value {{ $record->has_ceiling_fan ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_ceiling_fan ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Wall Light:</span>
                    <span class="info-value {{ $record->has_wall_light ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_wall_light ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bathroom Light:</span>
                    <span class="info-value {{ $record->has_bathroom_light ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_bathroom_light ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Air Conditioning:</span>
                    <span class="info-value {{ $record->has_ac ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_ac ? 'Yes' : 'No' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Features -->
    <div class="section">
        <div class="section-header">Security Features</div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Door Lock:</span>
                    <span class="info-value {{ $record->has_door_lock ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_door_lock ? 'Yes' : 'No' }}
                    </span>
                </div>
                @if($record->has_door_lock && $record->door_lock_pin)
                <div class="info-item">
                    <span class="info-label">Door Lock PIN:</span>
                    <span class="info-value">{{ e($record->door_lock_pin) }}</span>
                </div>
                @endif
            </div>

            <!-- Fire Door Guard Checks -->
            <h4>Fire Door Guard Check History ({{ $filter_year }})</h4>
            @if($filtered_guard_checks->count() > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date Checked</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filtered_guard_checks as $check)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($check->checked_date)->format('M j, Y') }}</td>
                            <td>{{ e($check->notes ?: 'No notes') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">No fire door guard checks recorded for {{ $filter_year }}</div>
            @endif

            <!-- Battery Replacements -->
            <h4>Fire Door Guard Battery Replacement History ({{ $filter_year }})</h4>
            @if($filtered_battery_replacements->count() > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date Replaced</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filtered_battery_replacements as $replacement)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($replacement->replaced_date)->format('M j, Y') }}</td>
                            <td>{{ e($replacement->notes ?: 'No notes') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">No battery replacements recorded for {{ $filter_year }}</div>
            @endif
        </div>
    </div>

    <!-- Entertainment -->
    <div class="section">
        <div class="section-header">Entertainment</div>
        <div class="section-content">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Television:</span>
                    <span class="info-value {{ $record->has_tv ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_tv ? 'Yes' : 'No' }}
                    </span>
                </div>
                @if($record->has_tv)
                <div class="info-item">
                    <span class="info-label">TV Model:</span>
                    <span class="info-value">{{ e($record->tv_model ?: 'Not specified') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">TV Remote:</span>
                    <span class="info-value {{ $record->has_tv_remote ? 'status-yes' : 'status-no' }}">
                        {{ $record->has_tv_remote ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">TV Placement:</span>
                    <span class="info-value">{{ e($record->tv_place ?: 'Not specified') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Maintenance Comments -->
    <div class="section">
        <div class="section-header">Maintenance Comments ({{ $filter_year }})</div>
        <div class="section-content">
            @if($filtered_comments->count() > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Date</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filtered_comments as $comment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($comment->date)->format('M j, Y') }}</td>
                            <td>{{ e($comment->comment) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">No maintenance comments recorded for {{ $filter_year }}</div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>This maintenance record was generated automatically from the maintenance management system.</p>
        <p>For questions or updates, please contact the maintenance department.</p>
    </div>
</body>
</html>