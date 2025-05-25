<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Window Check Report - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
        }
        .summary h2 {
            margin-bottom: 10px;
        }
        .missing-rooms {
            margin-top: 20px;
        }
        .missing-rooms ul {
            list-style-type: none;
            padding-left: 0;
            column-count: 3;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .issue {
            color: red;
            font-weight: bold;
        }
        .good {
            color: green;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Window Check Monthly Report</h1>
        <h2>{{ $monthName }} {{ $year }}</h2>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Check Date</th>
                <th>Fit for Purpose</th>
                <th>Status</th>
                <th>Comments</th>
                <th>Action Taken</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $record)
            <tr>
                <td>{{ $record->room_number }}</td>
                <td>{{ $record->check_date ? date('Y-m-d', strtotime($record->check_date)) : 'N/A' }}</td>
                <td class="{{ $record->fit_for_purpose ? 'good' : 'issue' }}">
                    {{ $record->fit_for_purpose ? 'YES' : 'NO' }}
                </td>
                <td class="{{ $record->status ? 'good' : 'issue' }}">
                    {{ $record->status ? 'GOOD' : 'POOR' }}
                </td>
                <td>{{ $record->comment ?? 'N/A' }}</td>
                <td>{{ $record->action_taken ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h2>Summary</h2>
        <p>Total Rooms: {{ $totalRooms }}</p>
        <p>Rooms Checked: {{ $data->count() }}</p>
        <p>Fit for Purpose: {{ $data->where('fit_for_purpose', true)->count() }}</p>
        <p>Good Status: {{ $data->where('status', true)->count() }}</p>
        <p>Not Fit for Purpose: {{ $notFitForPurposeCount }}</p>
        <p>Poor Status: {{ $poorStatusCount }}</p>
        <p>Total Issues: {{ $issueCount }}</p>
        <p>Completion Rate: {{ $completionRate }}%</p>
    </div>

    @if(count($missingRooms) > 0)
    <div class="missing-rooms">
        <h2>Rooms Not Checked ({{ count($missingRooms) }})</h2>
        <ul>
            @foreach($missingRooms as $room)
            <li>{{ $room }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        <p>This is an automatically generated report for window condition compliance monitoring.</p>
    </div>
</body>
</html>