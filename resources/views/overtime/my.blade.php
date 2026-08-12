@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">मेरो OT Records</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('overtime.my') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
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
                <a href="{{ route('overtime.my') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">सि.नं.</th>
                    <th class="p-3 border">कर्मचारी कोड</th>
                    <th class="p-3 border">पद</th>
                    <th class="p-3 border">मिति</th>
                    <th class="p-3 border">कार्यक्रम / कारण</th>
                    <th class="p-3 border">समय</th>
                    <th class="p-3 border">घण्टा</th>
                    <th class="p-3 border">खाजा</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                @php $sn = 1; @endphp
                @forelse($records as $rec)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border text-center">{{ $sn++ }}</td>
                    <td class="p-3 border">{{ auth()->user()->employee->employee_code ?? '-' }}</td>
                    <td class="p-3 border">{{ auth()->user()->employee->position->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $rec->ot_date }}</td>
                    <td class="p-3 border">{{ $rec->event->event_name ?? ($rec->remarks ?: 'सामान्य') }}</td>
                    <td class="p-3 border text-center">{{ $rec->from_time }} - {{ $rec->to_time }}</td>
                    <td class="p-3 border text-center">{{ number_format($rec->total_hours, 2) }}</td>
                    <td class="p-3 border text-center">{{ number_format($rec->tiffin_amount, 2) }}</td>
                    <td class="p-3 border">
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
                    <td class="p-3 border">
                        @if(in_array($rec->status, ['Pending', 'Rejected']))
                            <a href="{{ route('overtime.edit', $rec->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold text-sm">Edit</a>
                            <form action="{{ route('overtime.destroy', $rec->id) }}" method="POST" class="inline" onsubmit="return confirm('के तपाईं पक्का हुनुहुन्छ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm ml-2">Delete</button>
                            </form>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center p-4">कुनै record भेटिएन।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection