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

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">Name</th>
                    <th class="p-3 border">कार्यक्रम</th>
                    <th class="p-3 border">Total Hours</th>
                    <th class="p-3 border">OT Rate</th>
                    <th class="p-3 border">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($financeData as $data)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border">{{ $data->employee->name ?? 'N/A' }}</td>
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