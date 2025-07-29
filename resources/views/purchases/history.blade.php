@extends('layouts.app')

@section('title', 'Purchase History')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Purchase History</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $purchase->course->name ?? '-' }}</td>
                                <td>${{ number_format($purchase->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $purchase->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td>{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No purchases found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
