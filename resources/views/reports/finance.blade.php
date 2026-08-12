@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    @if(session('warning'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow">
            <p class="font-bold">चेतावनी!</p>
            {{ session('warning') }}
            <a href="{{ route('employees.index') }}" class="underline font-semibold">यहाँ क्लिक गर्नुहोस्</a>
        </div>
    @endif

    <form action="{{ route('reports.finance') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
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
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">कार्यक्रम</label>
                <select name="event_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">सबै छान्नुहोस्</option>
                    @foreach(\App\Models\Event::all() as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->event_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">🔍 खोज</button>
                <a href="{{ route('reports.finance') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
    <thead class="bg-blue-700 text-white">
        <tr>
            <th class="p-3 border">सि.नं.</th>
            <th class="p-3 border">कर्मचारी कोड</th>
            <th class="p-3 border">Name</th>
            <th class="p-3 border">पद</th>
            <th class="p-3 border">कार्यक्रम</th>
            <th class="p-3 border">Total Hours</th>
            <th class="p-3 border">OT Rate</th>
            <th class="p-3 border">Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $sn = 1; @endphp
        @foreach($financeData as $data)
        <tr class="hover:bg-gray-50">
            <td class="p-3 border text-center">{{ $sn++ }}</td>
            <td class="p-3 border">{{ $data->employee->employee_code ?? '-' }}</td>
            <td class="p-3 border">{{ $data->employee->name ?? 'N/A' }}</td>
            <td class="p-3 border">{{ $data->employee->position->name ?? 'N/A' }}</td>
            <td class="p-3 border">{{ $data->event->event_name ?? 'N/A' }}</td>
            <td class="p-3 border text-center">{{ $data->total_hours }}</td>
            <td class="p-3 border text-center">{{ $data->employee->position->ot_rate ?? 'N/A' }}</td>
            <td class="p-3 border text-right">
                रु {{ number_format($data->total_hours * ($data->employee->position->ot_rate ?? 0), 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
    </div>

    <div class="mt-4">
        <a href="{{ route('reports.exportFinanceExcel', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow">
            Excel डाउनलोड (Finance)
        </a>
    </div>
</div>
@endsection