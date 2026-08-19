@extends('layouts.app')

@section('content')
    @php
        $dayOptions = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    @endphp
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">कार्यालय सिफ्ट सेटिङ्स</h1>

        <!-- Add Shift Form -->
        <form action="{{ route('shifts.store') }}" method="POST" class="flex gap-2 mb-6 bg-blue-50 p-4 rounded">
            @csrf
            <select name="day_name" class="border p-2 w-full" required>
                <option value="">-- दिन छान्नुहोस् --</option>
                @foreach($dayOptions as $day)
                    <option value="{{ $day }}">{{ $day }}</option>
                @endforeach
            </select>
            <input type="time" name="start_time" class="border p-2" required>
            <input type="time" name="end_time" class="border p-2" required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">थप्नुहोस्</button>
        </form>

        <!-- Shifts Table -->
        <table class="w-full border-collapse border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">दिन</th>
                    <th class="border p-2">सुरु</th>
                    <th class="border p-2">अन्त्य</th>
                    <th class="border p-2">कार्य</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $shift)
                <tr>
                    <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                        @csrf @method('PUT')
                        <td class="border p-2">
                            <select name="day_name" class="w-full p-1" required>
                                @foreach($dayOptions as $day)
                                    <option value="{{ $day }}" {{ $shift->day_name === $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border p-2"><input type="time" name="start_time" value="{{ date('H:i', strtotime($shift->start_time)) }}" class="w-full p-1"></td>
                        <td class="border p-2"><input type="time" name="end_time" value="{{ date('H:i', strtotime($shift->end_time)) }}" class="w-full p-1"></td>
                        <td class="border p-2 flex gap-2">
                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded text-sm">Save</button>
                    </form>
                            <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('पक्का हो?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-sm">Delete</button>
                            </form>
                        </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection