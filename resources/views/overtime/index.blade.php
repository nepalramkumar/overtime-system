@extends('layouts.app')
@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">📋 ओभरटाइम रेकर्डहरू</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 border">मिति</th>
                    <th class="p-3 border">कर्मचारी</th>
                    <th class="p-3 border">समय</th>
                    <th class="p-3 border">घण्टा</th>
                    <th class="p-3 border">टिफिन</th>
                    <th class="p-3 border">एक्सन</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $rec)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 border">{{ $rec->ot_date }}</td>
                    <td class="p-3 border">{{ $rec->employee->name ?? 'N/A' }}</td>
                    <td class="p-3 border">{{ $rec->from_time }} - {{ $rec->to_time }}</td>
                    <td class="p-3 border">{{ number_format($rec->total_hours, 2) }}</td>
                    <td class="p-3 border">रु. {{ number_format($rec->tiffin_amount, 0) }}</td>
                    <td class="p-3 border">
                        <!-- <a href="{{ route('overtime.edit', $rec->id) }}" class="text-blue-600 mr-2">Edit</a> -->
                        <form action="{{ route('overtime.destroy', $rec->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('के तपाईं पक्का हटाउन चाहनुहुन्छ?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection