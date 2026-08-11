@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Summary Report</h2>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">कर्मचारी</th>
                    <th class="p-3 border">कार्यक्रम</th>
                    <th class="p-3 border">मिति (देखि - सम्म)</th>
                    <th class="p-3 border">जम्मा घण्टा</th>
                    <th class="p-3 border">जम्मा खाजा</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summaryData as $data)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border">{{ $data->employee->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $data->event->event_name ?? 'सामान्य' }}</td>
                    <td class="p-3 border text-center">{{ $data->date_from }} - {{ $data->date_to }}</td>
                    <td class="p-3 border text-center">{{ number_format($data->total_hours, 2) }}</td>
                    <td class="p-3 border text-right">रु {{ number_format($data->total_lunch, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center p-4">कुनै डेटा भेटिएन।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('reports.exportSummaryExcel', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow">
            Excel डाउनलोड (Summary)
        </a>
    </div>
</div>
@endsection