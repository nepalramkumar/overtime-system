@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Petrol Bill</h2>
        <a href="{{ route('petrol.bills.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700">
            + नयाँ Bill थप्नुहोस्
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('petrol.bills.index') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
        <div class="flex gap-2 items-end">
            <div class="w-64">
                <label class="block text-xs font-medium text-gray-600 mb-1">Month</label>
                <select name="petrol_month_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">सबै</option>
                    @foreach($months as $m)
                        <option value="{{ $m->id }}" {{ request('petrol_month_id') == $m->id ? 'selected' : '' }}>{{ $m->month }} - {{ $m->year }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded">🔍 खोज</button>
            <a href="{{ route('petrol.bills.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-2 rounded">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">सि.नं.</th>
                    <th class="p-3 border">कर्मचारी</th>
                    <th class="p-3 border">पद</th>
                    <th class="p-3 border">Month</th>
                    <th class="p-3 border">जम्मा परिमाण (L)</th>
                    <th class="p-3 border">जम्मा रकम</th>
                    <th class="p-3 border">Edit अनुमति</th>
                    <th class="p-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                @php $sn = 1; @endphp
                @forelse($bills as $bill)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border text-center">{{ $sn++ }}</td>
                    <td class="p-3 border">{{ $bill->employee->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $bill->employee->position->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $bill->month->month ?? '' }} - {{ $bill->month->year ?? '' }}</td>
                    <td class="p-3 border text-center">{{ number_format($bill->total_quantity, 2) }}</td>
                    <td class="p-3 border text-right">रु {{ number_format($bill->total_amount, 2) }}</td>
                    <td class="p-3 border text-center">
                        @if($bill->isEdit)
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">खुला</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">बन्द</span>
                        @endif
                    </td>
                    <td class="p-3 border">
                        <a href="{{ route('petrol.bills.print', $bill->id) }}" target="_blank"
                           class="bg-purple-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-purple-700">
                            Print
                        </a>
                        <a href="{{ route('petrol.bills.edit', $bill->id) }}"
                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-blue-700 ml-1">
                            Edit
                        </a>
                        @if(auth()->user()->role === 'admin' || (auth()->user()->role && \App\Models\RolePermission::where('role', auth()->user()->role)->where('permission', 'petrol.bills.manage')->exists()))
                            <form action="{{ route('petrol.bills.toggleEdit', $bill->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="{{ $bill->isEdit ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1 rounded text-sm ml-1">
                                    {{ $bill->isEdit ? 'Edit बन्द गर्नुहोस्' : 'Edit खोल्नुहोस्' }}
                                </button>
                            </form>
                            <form action="{{ route('petrol.bills.destroy', $bill->id) }}" method="POST" class="inline" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm ml-1 hover:bg-red-700">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center p-4">कुनै Bill भेटिएन।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bills->links() }}
    </div>
</div>
@endsection
