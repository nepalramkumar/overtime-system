@extends('layouts.app')

@section('content')

<!-- Filter Section -->
<form action="{{ route('reports.index') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">रिपोर्ट प्रकार</label>
            <select name="group_by" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="employee" {{ request('group_by') == 'employee' ? 'selected' : '' }}>कर्मचारी अनुसार</option>
                <option value="event" {{ request('group_by') == 'event' ? 'selected' : '' }}>कार्यक्रम अनुसार</option>
            </select>
        </div>
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
            <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
        </div>
    </div>
</form>

<!-- View Toggle -->
<div class="mb-4 flex gap-2">
    <button type="button" id="btn-normal-view" onclick="showView('normal')"
        class="bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold">📄 सामान्य View</button>
    <button type="button" id="btn-pivot-view" onclick="showView('pivot')"
        class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm font-semibold">📊 Pivot View</button>
</div>

<!-- ============================== -->
<!-- सामान्य (Normal) Table View -->
<!-- ============================== -->
<div id="normal-view">
    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-blue-700 text-white text-sm">
                <th class="p-3 border">सि.नं.</th>
                <th class="p-3 border">मिति</th>
                <th class="p-3 border">कर्मचारी कोड</th>
                <th class="p-3 border">कर्मचारी</th>
                <th class="p-3 border">पद</th>
                <th class="p-3 border">कार्यक्रम / कारण</th>
                <th class="p-3 border">समय (From-To)</th>
                <th class="p-3 border">घण्टा (HH:MM)</th>
                <th class="p-3 border">घण्टा (Decimal)</th>
                <th class="p-3 border">खाजा</th>
            </tr>
            </thead>
            <tbody>
            @php $sn = 1; @endphp
            @forelse($groupedData as $empGroup)
                @foreach($empGroup['events'] as $eventGroup)
                    @foreach($eventGroup['records'] as $rec)
                    <tr>
                        <td class="p-3 border text-center">{{ $sn++ }}</td>
                        <td class="p-3 border">{{ $rec->ot_date }}</td>
                        <td class="p-3 border">{{ $empGroup['employee']->employee_code ?? '-' }}</td>
                        <td class="p-3 border">{{ $empGroup['employee']->name ?? 'N/A' }}</td>
                        <td class="p-3 border">{{ $empGroup['employee']->position->name ?? 'N/A' }}</td>
                        <td class="p-3 border">
                            {{ $rec->event->event_name ?? ($rec->remarks ?: 'सामान्य (General)') }}
                            @if($rec->event)
                                <br><span class="text-xs text-gray-500">({{ adToBs($rec->event->start_date) }} - {{ adToBs($rec->event->end_date) }})</span>
                            @endif
                        </td>
                        <td class="p-3 border text-center">{{ $rec->from_time }} - {{ $rec->to_time }}</td>
                        <td class="p-3 border text-center">{{ hoursToHm($rec->total_hours) }}</td>
                        <td class="p-3 border text-center">{{ number_format($rec->total_hours, 2) }}</td>
                        <td class="p-3 border text-center">{{ number_format($rec->tiffin_amount, 2) }}</td>
                    </tr>
                    @endforeach
                @endforeach
            @empty
                <tr><td colspan="10" class="text-center p-4">कुनै डेटा भेटिएन।</td></tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="bg-gray-800 text-white font-bold">
                <td colspan="8" class="p-3 border text-right">कुल जम्मा (Grand Total)</td>
                <td class="p-3 border text-center">{{ number_format($totalHoursDecimalSum, 2) }}</td>
                <td class="p-3 border text-center">रु {{ number_format($totalAmountSum, 2) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="mt-4">
        <a href="{{ route('reports.excel', request()->all()) }}" class="bg-green-600 text-white px-3 py-1 rounded">
            Excel डाउनलोड
        </a>
    </div>
</div>

<!-- ============================== -->
<!-- Pivot Table View -->
<!-- ============================== -->
<div id="pivot-view" style="display:none;">

    @if(count($pivotColumns) == 0)
        <div class="bg-white rounded-lg shadow-sm p-6 text-center text-gray-500">
            कुनै डेटा भेटिएन।
        </div>
    @else
        <h3 class="text-lg font-bold text-gray-700 mb-2">कार्यक्रम अनुसार घण्टा (Programme-wise Hours)</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow-sm mb-6">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-blue-700 text-white">
                        <th class="p-2 border">सि.नं.</th>
                        <th class="p-2 border">मिति</th>
                        <th class="p-2 border">कर्मचारी कोड</th>
                        <th class="p-2 border">कर्मचारी</th>
                        <th class="p-2 border">पद</th>
                        @foreach($pivotColumns as $col)
                            <th class="p-2 border">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $sn = 1; @endphp
                    @forelse($groupedData as $empId => $empGroup)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border text-center">{{ $sn++ }}</td>
                        <td class="p-2 border">-</td>
                        <td class="p-2 border">{{ $empGroup['employee']->employee_code ?? '-' }}</td>
                        <td class="p-2 border">{{ $empGroup['employee']->name ?? 'N/A' }}</td>
                        <td class="p-2 border">{{ $empGroup['employee']->position->name ?? 'N/A' }}</td>
                        @foreach($pivotColumns as $col)
                            <td class="p-2 border text-center">
                                {{ isset($pivotHours[$empId][$col]) ? hoursToHm($pivotHours[$empId][$col]) : '' }}
                            </td>
                        @endforeach
                    </tr>
                    @empty
                        <tr><td colspan="{{ 5 + count($pivotColumns) }}" class="text-center p-4">कुनै डेटा भेटिएन।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="text-lg font-bold text-gray-700 mb-2">कार्यक्रम अनुसार खाजा रकम (Programme-wise Lunch Amount)</h3>
        <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-blue-700 text-white">
                        <th class="p-2 border">सि.नं.</th>
                        <th class="p-2 border">कर्मचारी कोड</th>
                        <th class="p-2 border">कर्मचारी</th>
                        <th class="p-2 border">पद</th>
                        @foreach($pivotColumns as $col)
                            <th class="p-2 border">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $sn = 1; @endphp
                    @forelse($groupedData as $empId => $empGroup)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border text-center">{{ $sn++ }}</td>
                        <td class="p-2 border">{{ $empGroup['employee']->employee_code ?? '-' }}</td>
                        <td class="p-2 border">{{ $empGroup['employee']->name ?? 'N/A' }}</td>
                        <td class="p-2 border">{{ $empGroup['employee']->position->name ?? 'N/A' }}</td>
                        @foreach($pivotColumns as $col)
                            <td class="p-2 border text-center">
                                {{ isset($pivotLunch[$empId][$col]) ? number_format($pivotLunch[$empId][$col], 2) : '' }}
                            </td>
                        @endforeach
                    </tr>
                    @empty
                        <tr><td colspan="{{ 4 + count($pivotColumns) }}" class="text-center p-4">कुनै डेटा भेटिएन।</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
  <div class="mt-4">
            <a href="{{ route('reports.exportPivot', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow">
                Excel डाउनलोड (Pivot)
            </a>
        </div>
    @endif
</div>

<script>
function showView(view) {
    const normalDiv = document.getElementById('normal-view');
    const pivotDiv = document.getElementById('pivot-view');
    const btnNormal = document.getElementById('btn-normal-view');
    const btnPivot = document.getElementById('btn-pivot-view');

    if (view === 'pivot') {
        normalDiv.style.display = 'none';
        pivotDiv.style.display = 'block';
        btnPivot.classList.remove('bg-gray-300', 'text-gray-700');
        btnPivot.classList.add('bg-blue-700', 'text-white');
        btnNormal.classList.remove('bg-blue-700', 'text-white');
        btnNormal.classList.add('bg-gray-300', 'text-gray-700');
    } else {
        pivotDiv.style.display = 'none';
        normalDiv.style.display = 'block';
        btnNormal.classList.remove('bg-gray-300', 'text-gray-700');
        btnNormal.classList.add('bg-blue-700', 'text-white');
        btnPivot.classList.remove('bg-blue-700', 'text-white');
        btnPivot.classList.add('bg-gray-300', 'text-gray-700');
    }
}
</script>

@endsection