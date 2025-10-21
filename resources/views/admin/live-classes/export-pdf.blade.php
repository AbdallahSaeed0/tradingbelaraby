<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Live Class Registrations - {{ $liveClass->name }}</title></head>

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
                        <td class="text-danger">Student Deleted</td>
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
                    <td colspan="6" class="pdf-empty-state">
                        No registrations found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pdf-footer">
        <p>Total Registrations: {{ $registrations->count() }}</p>
        <p>Attended: {{ $registrations->where('attended', true)->count() }}</p>
        <p>Not Attended: {{ $registrations->where('attended', false)->count() }}</p>
    </div>
</body>

</html>

