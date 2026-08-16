@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Petrol Month सेटिङ्स</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <table class="w-full border-collapse border border-gray-200 mb-8">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-left">Month</th>
                <th class="border p-2 text-left">Year</th>
                <th class="border p-2 text-center w-28">Status</th>
                <th class="border p-2 text-center w-40">कार्य</th>
            </tr>
        </thead>
        <tbody>
            @forelse($months as $item)
            <tr>
                <td class="border p-2">{{ $item->month }}</td>
                <td class="border p-2">{{ $item->year }}</td>
                <td class="border p-2 text-center">
                    @if($item->status)
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Enabled</span>
                    @else
                        <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs font-semibold">Disabled</span>
                    @endif
                </td>
                <td class="border p-2 text-center">
                    <div class="flex justify-center gap-2">
                        <form action="{{ route('petrol.months.toggleStatus', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded text-sm text-white {{ $item->status ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                                {{ $item->status ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        <form action="{{ route('petrol.months.destroy', $item->id) }}" method="POST" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center p-4 text-gray-500">कुनै Month थपिएको छैन।</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="bg-gray-100 p-4 rounded">
        <h3 class="font-bold mb-2">नयाँ Month थप्नुहोस्</h3>
        <form action="{{ route('petrol.months.store') }}" method="POST" class="flex gap-2">
            @csrf
            <select name="month" class="border p-2 w-full" required>
                <option value="">-- महिना छान्नुहोस् --</option>
                @foreach($bsMonths as $m)
                    <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
            <select name="year" class="border p-2 w-full" required>
                <option value="">-- वर्ष छान्नुहोस् --</option>
                @foreach($yearOptions as $y)
                    <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 whitespace-nowrap">थप्नुहोस्</button>
        </form>
    </div>
</div>
@endsection