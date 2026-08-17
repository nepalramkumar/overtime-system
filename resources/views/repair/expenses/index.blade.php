@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Repair Expense</h2>
        <a href="{{ route('repair.expenses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700">
            + नयाँ Repair Expense थप्नुहोस्
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('repair.expenses.index') }}" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
        <div class="flex gap-2 items-end">
            <div class="w-56">
                <label class="block text-xs font-medium text-gray-600 mb-1">FY Year</label>
                <select name="fy_year" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">सबै</option>
                    @foreach($fyList as $fy)
                        <option value="{{ $fy }}" {{ request('fy_year') == $fy ? 'selected' : '' }}>{{ $fy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-56">
                <label class="block text-xs font-medium text-gray-600 mb-1">Month</label>
                <select name="bs_month" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">सबै</option>
                    @foreach($monthList as $m)
                        <option value="{{ $m }}" {{ request('bs_month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded">🔍 खोज</button>
            <a href="{{ route('repair.expenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-2 rounded">Reset</a>
        </div>
    </form>

    <a href="{{ route('repair.expenses.index', array_merge(request()->all(), ['export' => 'excel'])) }}" class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 inline-block mb-3">
        Excel डाउनलोड
    </a>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">सि.नं.</th>
                    <th class="p-3 border">कर्मचारी</th>
                    <th class="p-3 border">पद</th>
                    <th class="p-3 border">FY Year</th>
                    <th class="p-3 border">Month</th>
                    <th class="p-3 border">मिति (BS)</th>
                    <th class="p-3 border">विवरण</th>
                    <th class="p-3 border">रकम</th>
                    <th class="p-3 border">Edit अनुमति</th>
                    <th class="p-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                @php $sn = 1; @endphp
                @forelse($rows as $row)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border text-center">{{ $sn++ }}</td>
                    <td class="p-3 border">{{ $row['employee']->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $row['employee']->position->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $row['fy_year'] }}</td>
                    <td class="p-3 border">{{ $row['bs_month'] }}</td>
                    <td class="p-3 border">{{ $row['bs_date'] }}</td>
                    <td class="p-3 border">{{ $row['description'] }}</td>
                    <td class="p-3 border text-right">रु {{ number_format($row['amount'], 2) }}</td>
                    <td class="p-3 border text-center">
                        @if($row['isEdit'])
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">खुला</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">बन्द</span>
                        @endif
                    </td>
                    <td class="p-3 border">
                        @if(auth()->user()->role === 'admin' || \App\Models\RolePermission::where('role', auth()->user()->role)->where('permission', 'repair.expenses.manage')->exists() || $row['isEdit'])
                            <a href="{{ route('repair.expenses.edit', $row['expense_id']) }}"
                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-blue-700 ml-1">
                                Edit
                            </a>
                        @endif
                        @if(auth()->user()->role === 'admin' || \App\Models\RolePermission::where('role', auth()->user()->role)->where('permission', 'repair.expenses.manage')->exists())
                            <form action="{{ route('repair.expenses.toggleEdit', $row['expense_id']) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="{{ $row['isEdit'] ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1 rounded text-sm ml-1">
                                    {{ $row['isEdit'] ? 'बन्द' : 'खुला' }}
                                </button>
                            </form>
                            <form action="{{ route('repair.expenses.destroy', $row['expense_id']) }}" method="POST" class="inline" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ? (यसले यो पूरा entry - सबै मिति सहित - हटाउँछ)')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm ml-1 hover:bg-red-700">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="text-center p-4">कुनै Repair Expense भेटिएन।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection