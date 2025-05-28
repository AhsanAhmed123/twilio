@extends('Admin.layout.layout')
@section('content')
<div class="container">
    <h3 class="mb-4">Activity Report</h3>

    <form method="GET" action="{{ route('report.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>Client Name</label>
            <select name="name" class="form-select">
                <option value="">All</option>
                @foreach ($business as $busines)
                    <option value="{{ $busines->business_name }}"> {{  $busines->business_name }}</option>
                @endforeach
              
            </select>
        </div>
        <div class="col-md-3">
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Search</button>
            <a href="{{ route('report.index') }}" class="btn btn-danger">Clear</a>
        </div>
    </form>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>Business Name</th>
                <th>Date</th>
                <th>Month</th>
                <th>Year</th>
                <th>Voice Mail Pulse</th>
                <th>Callback Pulse</th>
                <th>SMS Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ str_pad($row['month'], 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $row['year'] }}</td>
                <td>{{ $row['voice'] }}</td>
                <td>0</td>
                <td>0</td>
            </tr>
            @empty
            <tr><td colspan="7">No data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <footer class="text-center mt-4">
        <small>Copyright © 2021 OCAS Platform</small>
    </footer>
</div>
@endsection
