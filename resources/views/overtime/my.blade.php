@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">मेरो OT Records</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-6 py-3 border">मिति</th>
                    <th class="px-6 py-3 border">कार्यक्रम / कारण</th>
                    <th class="px-6 py-3 border">समय</th>
                    <th class="px-6 py-3 border">घण्टा</th>
                    <th class="px-6 py-3 border">खाजा</th>
                    <th class="px-6 py-3 border">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 border">{{ $rec->ot_date }}</td>
                    <td class="px-6 py-4 border">{{ $rec->event->event_name ?? ($rec->remarks ?: 'सामान्य') }}</td>
                    <td class="px-6 py-4 border">{{ $rec->from_time }} - {{ $rec->to_time }}</td>
                    <td class="px-6 py-4 border">{{ $rec->total_hours }}</td>
                    <td class="px-6 py-4 border">{{ $rec->tiffin_amount }}</td>
                    <td class="px-6 py-4 border">
                        @if($rec->status == 'Verified')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Verified</span>
                        @elseif($rec->status == 'Rejected')
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Rejected</span>
                            @if($rec->rejection_reason)
                                <div class="text-xs text-red-600 mt-1">कारण: {{ $rec->rejection_reason }}</div>
                            @endif
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-semibold">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center p-4">कुनै record छैन।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection