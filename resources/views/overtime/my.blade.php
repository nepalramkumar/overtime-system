@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">📋 मेरो OT Records</h2>
            <p class="text-xs text-slate-500 mt-1">तपाईंले प्रविष्टि गर्नुभएका ओभरटाइम विवरण तथा स्वीकृतिको अवस्था</p>
        </div>
        <a href="{{ route('overtime.create') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition text-sm">
            <i class="fas fa-plus"></i>
            <span>नयाँ OT Entry गर्नुहोस्</span>
        </a>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-emerald-800">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            @if(session('last_event_id'))
                <a href="{{ route('overtime.create', ['event_id' => session('last_event_id')]) }}"
                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition whitespace-nowrap">
                    <i class="fas fa-redo"></i>
                    <span>यही कार्यक्रममा अर्को Entry थप्नुहोस्</span>
                </a>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm text-sm flex items-center gap-2 text-rose-800">
            <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search & Filter Form -->
    <form action="{{ route('overtime.my') }}" method="GET" class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">कार्यक्रम (Event)</label>
                <select name="event_id" id="event-select" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- सबै छान्नुहोस् --</option>
                    @foreach(\App\Models\Event::orderBy('id', 'desc')->get() as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->event_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium text-xs px-4 py-2.5 rounded-lg shadow-sm transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-search"></i>
                    <span>खोज्नुहोस्</span>
                </button>
                <a href="{{ route('overtime.my') }}" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs px-4 py-2.5 rounded-lg border border-slate-300 transition text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="p-3.5 text-center">सि.नं.</th>
                        <th class="p-3.5">कोड</th>
                        <th class="p-3.5">पद</th>
                        <th class="p-3.5">मिति</th>
                        <th class="p-3.5">कार्यक्रम / कारण</th>
                        <th class="p-3.5 text-center">समय</th>
                        <th class="p-3.5 text-center">घण्टा</th>
                        <th class="p-3.5 text-right">खाजा खर्च</th>
                        <th class="p-3.5 text-center">Status</th>
                        <th class="p-3.5 text-center">कार्य</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $sn = 1; @endphp
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/80 transition {{ session('highlight_id') == $rec->id ? 'bg-emerald-50/80 ring-2 ring-emerald-400' : '' }}">
                            <!-- Serial No -->
                            <td class="p-3.5 text-center font-medium text-slate-500">{{ $sn++ }}</td>

                            <!-- Employee Code -->
                            <td class="p-3.5 font-mono text-xs text-slate-600">
                                {{ auth()->user()->employee->employee_code ?? '-' }}
                            </td>

                            <!-- Position -->
                            <td class="p-3.5 text-slate-600">
                                {{ auth()->user()->employee->position->name ?? 'N/A' }}
                            </td>

                            <!-- Date -->
                            <td class="p-3.5 font-medium text-slate-800 whitespace-nowrap">
                                {{ $rec->ot_date }}
                            </td>

                            <!-- Event / Remarks -->
                            <td class="p-3.5 text-slate-800">
                                {{ $rec->event->event_name ?? ($rec->remarks ?: 'सामान्य') }}
                            </td>

                            <!-- Time Slot -->
                            <td class="p-3.5 text-center text-slate-600 whitespace-nowrap text-xs">
                                {{ $rec->from_time }} - {{ $rec->to_time }}
                            </td>

                            <!-- Total Hours -->
                            <td class="p-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ number_format($rec->total_hours, 2) }} hrs
                                </span>
                            </td>

                            <!-- Tiffin Amount -->
                            <td class="p-3.5 text-right font-semibold text-slate-800 whitespace-nowrap">
                                रु. {{ number_format($rec->tiffin_amount, 2) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($rec->status == 'Verified')
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check-circle text-xs"></i> Verified
                                    </span>
                                @elseif($rec->status == 'Rejected')
                                    <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-times-circle text-xs"></i> Rejected
                                    </span>
                                    @if($rec->rejection_reason)
                                        <div class="text-[11px] text-rose-600 mt-1 max-w-[150px] truncate" title="{{ $rec->rejection_reason }}">
                                            कारण: {{ $rec->rejection_reason }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-clock text-xs"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Print -->
                                    <a href="{{ route('overtime.print', $rec->id) }}" target="_blank" 
                                       class="p-1.5 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition" 
                                       title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    @if(in_array($rec->status, ['Pending', 'Rejected']))
                                        <!-- Edit -->
                                        <a href="{{ route('overtime.edit', $rec->id) }}" 
                                           class="p-1.5 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-lg transition" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('overtime.destroy', $rec->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition" 
                                                    onclick="return confirm('के तपाईं पक्का यो रेकर्ड हटाउन चाहनुहुन्छ?')" 
                                                    title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400">
                                <i class="fas fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                                कुनै पनि OT रेकर्ड भेटिएन।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (If Enabled) -->
        @if(method_exists($records, 'links'))
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $records->links() }}
            </div>
        @endif
    </div>

</div>

<!-- TomSelect Scripts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eventSelect = document.getElementById('event-select');
        if (eventSelect) {
            new TomSelect("#event-select", {
                create: false,
                placeholder: "-- सबै छान्नुहोस् --",
                allowEmptyOption: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
    });
</script>
@endsection