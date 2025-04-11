<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Room Maintenance Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        .checkmark {
            font-family: DejaVu Sans, sans-serif;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Room Maintenance Record</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Room Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Room Number:</span> {{ $record->room_number }}
            </div>
            <div class="info-item">
                <span class="label">Resident Name:</span> {{ $record->resident_name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="label">Maintained By:</span> {{ $record->user->name ?? 'Unknown' }}
            </div>
            <div class="info-item">
                <span class="label">Last Updated:</span> {{ $record->updated_at->format('Y-m-d H:i:s') }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Bed Configuration</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Bed Type:</span> {{ $record->bed_type ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="label">Bed Rails:</span>
                <span class="checkmark">{{ $record->has_bed_rails ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Bed Rail Covers:</span>
                <span class="checkmark">{{ $record->has_bed_rail_covers ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Mattress Type:</span> {{ $record->mattress_type }}
            </div>
            @if($record->mattress_type === 'Air Mattress')
            <div class="info-item">
                <span class="label">Air Mattress Machine Type:</span> {{ $record->air_mattress_machine_type ?? 'N/A' }}
            </div>
            @endif
            <div class="info-item">
                <span class="label">Sensor Mat:</span>
                <span class="checkmark">{{ $record->has_sensor_mat ? '✓' : '✗' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Room Features</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Ceiling Light:</span>
                <span class="checkmark">{{ $record->has_ceiling_light ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Ceiling Fan:</span>
                <span class="checkmark">{{ $record->has_ceiling_fan ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Wall Light:</span>
                <span class="checkmark">{{ $record->has_wall_light ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Bathroom Light:</span>
                <span class="checkmark">{{ $record->has_bathroom_light ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Air Conditioning:</span>
                <span class="checkmark">{{ $record->has_ac ? '✓' : '✗' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Security Features</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Door Lock:</span>
                <span class="checkmark">{{ $record->has_door_lock ? '✓' : '✗' }}</span>
            </div>
            @if($record->has_door_lock)
            <div class="info-item">
                <span class="label">Door Lock PIN:</span> {{ $record->door_lock_pin ?? 'N/A' }}
            </div>
            @endif
        </div>
    </div>

    @if($record->has_tv)
    <div class="section">
        <div class="section-title">Entertainment</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Television:</span>
                <span class="checkmark">{{ $record->has_tv ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">TV Model:</span> {{ $record->tv_model ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="label">TV Remote:</span>
                <span class="checkmark">{{ $record->has_tv_remote ? '✓' : '✗' }}</span>
            </div>
            <div class="info-item">
                <span class="label">TV Placement:</span> {{ $record->tv_place ?? 'N/A' }}
            </div>
        </div>
    </div>
    @endif

    @if(!empty($record->window_lock_checked))
    <div class="section">
        <div class="section-title">Window Lock Check History</div>
        <table>
            <thead>
                <tr>
                    <th>Date Checked</th>
                    <th>Checked By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->window_lock_checked as $check)
                <tr>
                    <td>{{ $check['checked_date'] ?? 'N/A' }}</td>
                    <td>{{ $check['checked_by'] ?? 'N/A' }}</td>
                    <td>{{ $check['notes'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($record->fire_door_guard_checked))
    <div class="section">
        <div class="section-title">Fire Door Guard Check History</div>
        <table>
            <thead>
                <tr>
                    <th>Date Checked</th>
                    <th>Checked By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->fire_door_guard_checked as $check)
                <tr>
                    <td>{{ $check['checked_date'] ?? 'N/A' }}</td>
                    <td>{{ $check['checked_by'] ?? 'N/A' }}</td>
                    <td>{{ $check['notes'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($record->fire_door_guard_Battry_checked))
    <div class="section">
        <div class="section-title">Fire Door Guard Battery Replacement History</div>
        <table>
            <thead>
                <tr>
                    <th>Date Replaced</th>
                    <th>Replaced By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->fire_door_guard_Battry_checked as $check)
                <tr>
                    <td>{{ $check['replaced_date'] ?? 'N/A' }}</td>
                    <td>{{ $check['replaced_by'] ?? 'N/A' }}</td>
                    <td>{{ $check['notes'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($record->comments))
    <div class="section">
        <div class="section-title">Maintenance Comments History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Added By</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->comments as $comment)
                <tr>
                    <td>{{ $comment['date'] ?? 'N/A' }}</td>
                    <td>{{ $comment['user'] ?? 'N/A' }}</td>
                    <td>{{ $comment['comment'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>