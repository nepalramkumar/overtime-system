@extends('layouts.app')

@section('content')

<form action="{{ route('reports.summary') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">कर्मचारी</label>
            <select name="employee_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="">सबै</option>
                @foreach(\App\Models\Employee::all() as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                @endforeach
            </select>
            <div>
    <label class="block text-xs font-medium text-gray-600 mb-1">कार्यक्रम</label>
    <select name="event_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        <option value="">सबै कार्यक्रम</option>
        @foreach(\App\Models\Event::all() as $ev)
            <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>
                {{ $ev->event_name }}
            </option>
        @endforeach
    </select>
</div>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded">🔍 खोज</button>
            <a href="{{ route('reports.summary') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-2 rounded">Reset</a>
        </div>
    </div>
</form>

<div class="overflow-x-auto bg-white rounded-lg shadow-sm">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-blue-700 text-white">
                <th class="p-3 border">सि.नं.</th>
                <th class="p-3 border">Name</th>
                <th class="p-3 border">Designation</th> 
                <th class="p-3 border">कार्यक्रम</th> 
                <th class="p-3 border">Date from</th>
                <th class="p-3 border">Date to</th>
                <th class="p-3 border">Total Hours</th>
                <th class="p-3 border">Lunch total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summaryData as $data)
            <tr>
                <td class="p-3 border text-center">{{ $loop->iteration }}</td>
                <td class="p-3 border">{{ $data->employee->name ?? 'N/A' }}</td>
                <td class="p-3 border">{{ $data->employee->designation ?? 'N/A' }}</td> <td class="p-3 border">{{ $data->event->event_name ?? 'N/A' }}</td> 
                <td class="p-3 border text-center">{{ $data->date_from }}</td>
                <td class="p-3 border text-center">{{ $data->date_to }}</td>
                <td class="p-3 border text-center">{{ number_format($data->total_hours, 2) }}</td>
                <td class="p-3 border text-right">रु {{ number_format($data->total_lunch, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-4 text-center text-gray-500">कुनै डेटा भेटिएन।</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="{{ route('reports.exportSummaryExcel', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">
        Excel डाउनलोड (Summary)
    </a>
</div>
@endsection