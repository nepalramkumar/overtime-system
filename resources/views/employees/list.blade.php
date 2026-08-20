
@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">कर्मचारी व्यवस्थापन</h2>
    
    <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        + नयाँ कर्मचारी थप्नुहोस्
    </a>
<script src="https://cdn.tailwindcss.com"></script>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 border">नाम</th>
                    <th scope="col" class="px-6 py-3 border">पद (Designation)</th>
                    <th scope="col" class="px-6 py-3 border">विभाग (Department)</th>
                    <!-- <th scope="col" class="px-6 py-3 border">OT रेट</th> -->
                    <th scope="col" class="px-6 py-3 border">कार्य (Action)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 border font-medium text-gray-900">
    {{ $emp->name ?? ($emp->user->name ?? 'N/A') }}
</td>
                    <td class="px-6 py-4 border">{{ $emp->position->name ?? '—' }}</td>
                    <td class="px-6 py-4 border">{{ $emp->department }}</td>
                    <!-- <td class="px-6 py-4 border">रू. {{ number_format($emp->position->ot_rate ?? 0, 2) }} -->
                    <td class="px-6 py-4 border flex gap-2">
                        <a href="{{ route('employees.edit', $emp->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('के तपाईं पक्का हुनुहुन्छ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection