<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Live Class Registrations - {{ $liveClass->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .status-confirmed {
            color: green;
        }

        .status-pending {
            color: orange;
        }

        .status-cancelled {
            color: red;
        }

        .attended-yes {
            color: green;
        }

        .attended-no {
            color: red;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Live Class Registrations</div>
        <div class="subtitle">{{ $liveClass->name }}</div>
        <div class="subtitle">Generated on: {{ now()->format('F j, Y g:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th>Status</th>
                <th>Attended</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registrations as $index => $registration)
                @if ($registration->user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $registration->user->name }}</td>
                        <td>{{ $registration->user->email }}</td>
                        <td>{{ $registration->created_at->format('M j, Y g:i A') }}</td>
                        <td class="status-{{ $registration->status ?? 'pending' }}">
                            {{ ucfirst($registration->status ?? 'pending') }}
                        </td>
                        <td class="attended-{{ $registration->attended ? 'yes' : 'no' }}">
                            {{ $registration->attended ? 'Yes' : 'No' }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="color: red;">Student Deleted</td>
                        <td>N/A</td>
                        <td>{{ $registration->created_at->format('M j, Y g:i A') }}</td>
                        <td class="status-{{ $registration->status ?? 'pending' }}">
                            {{ ucfirst($registration->status ?? 'pending') }}
                        </td>
                        <td class="attended-{{ $registration->attended ? 'yes' : 'no' }}">
                            {{ $registration->attended ? 'Yes' : 'No' }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        No registrations found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 12px;">
        <p>Total Registrations: {{ $registrations->count() }}</p>
        <p>Attended: {{ $registrations->where('attended', true)->count() }}</p>
        <p>Not Attended: {{ $registrations->where('attended', false)->count() }}</p>
    </div>
</body>

</html>
