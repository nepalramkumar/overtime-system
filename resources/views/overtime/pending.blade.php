@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Verify बाँकी रहेका OT Records</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('overtime.pending') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
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
                    @foreach(\App\Models\Event::orderBy('id', 'desc')->get() as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->event_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">🔍 खोज</button>
                <a href="{{ route('overtime.pending') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-6 py-3 border">सि.नं.</th>
                    <th class="px-6 py-3 border">कर्मचारी कोड</th>
                    <th class="px-6 py-3 border">कर्मचारी</th>
                    <th class="px-6 py-3 border">पद</th>
                    <th class="px-6 py-3 border">मिति</th>
                    <th class="px-6 py-3 border">समय</th>
                    <th class="px-6 py-3 border">घण्टा</th>
                    <th class="px-6 py-3 border">कार्यक्रम / कारण</th>
                    <th class="px-6 py-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                @php $sn = 1; @endphp
                @forelse($records as $rec)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 border text-center">{{ $sn++ }}</td>
                    <td class="px-6 py-4 border">{{ $rec->employee->employee_code ?? '-' }}</td>
                    <td class="px-6 py-4 border">{{ $rec->employee->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 border">{{ $rec->employee->position->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 border">{{ $rec->ot_date }}</td>
                    <td class="px-6 py-4 border">{{ $rec->from_time }} - {{ $rec->to_time }}</td>
                    <td class="px-6 py-4 border">{{ $rec->total_hours }}</td>
                    <td class="px-6 py-4 border">{{ $rec->event->event_name ?? ($rec->remarks ?: 'सामान्य') }}</td>
                    <td class="px-6 py-4 border whitespace-nowrap">
                        <form action="{{ route('overtime.verify', $rec->id) }}" method="POST" class="inline" onsubmit="return confirm('के तपाईं यो रेकर्ड verify गर्न चाहनुहुन्छ?')">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">Verify</button>
                        </form>

                        <button type="button" onclick="document.getElementById('reject-modal-{{ $rec->id }}').style.display='flex'"
                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm ml-1">Reject</button>

                        <div id="reject-modal-{{ $rec->id }}" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:50;">
                            <div style="background:#fff; padding:14px; border-radius:8px; width:260px; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                                <h3 style="font-size:14px; font-weight:600; margin-bottom:8px;">Reject गर्ने कारण</h3>
                                <form action="{{ route('overtime.reject', $rec->id) }}" method="POST">
                                    @csrf
                                    <textarea name="reason" rows="2" style="width:100%; border:1px solid #ccc; border-radius:4px; padding:6px; font-size:13px; margin-bottom:8px;" placeholder="कारण लेख्नुहोस्..." required></textarea>
                                    <div style="display:flex; justify-content:flex-end; gap:6px;">
                                        <button type="button" onclick="document.getElementById('reject-modal-{{ $rec->id }}').style.display='none'"
                                                style="background:#e5e7eb; padding:4px 10px; border-radius:4px; font-size:12px; border:none;">रद्द</button>
                                        <button type="submit" style="background:#dc2626; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; border:none;">Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center p-4">कुनै pending record छैन।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection