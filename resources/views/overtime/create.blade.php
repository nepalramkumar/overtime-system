@extends('layouts.app')

@section('content')

    <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Overtime & Tiffin Entry</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('overtime.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Select Employee</label>
                @if($canSelectAny)
                    <select name="employee_id" id="employee-select" class="w-full p-2 border rounded" required>
                        <option value="">-- नाम टाइप गर्नुहोस् --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->name }} (ID: {{ $emp->id }}) - {{ $emp->designation }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full p-2 border rounded bg-gray-100 text-gray-700 font-semibold">
                        {{ $lockedEmployee->name ?? 'N/A' }} ({{ $lockedEmployee->employee_code ?? '' }})
                    </div>
                    <input type="hidden" name="employee_id" value="{{ $lockedEmployee->id ?? '' }}">
                @endif
            </div>

            @if(isset($selectedEventId) && $selectedEventId)
                @foreach($events as $event)
                    @if($event->id == $selectedEventId)
                        <div class="bg-blue-50 p-3 rounded border border-blue-200 mb-4">
                            <label class="block text-blue-700 font-bold text-xs uppercase tracking-wide">Selected Event / Project</label>
                            <span class="text-gray-800 font-semibold text-lg">{{ $event->event_name }} ({{ $event->department }})</span>
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                        </div>
                    @endif
                @endforeach
            @else
                <div class="bg-gray-50 p-3 rounded border border-gray-200 mb-4">
                    <label class="block text-gray-500 font-bold text-xs uppercase tracking-wide">OT Category</label>
                    <span class="text-gray-700 font-semibold">सामान्य प्रयोजन (General Purpose OT)</span>
                    <input type="hidden" name="event_id" value="">
                </div>
            @endif

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Date</label>
                <input type="date" name="ot_date" value="{{ old('ot_date', date('Y-m-d')) }}" class="w-full p-2 border rounded" required>
            </div>

            <div class="flex items-center bg-yellow-50 p-2 rounded border border-yellow-200">
                <input type="checkbox" name="is_holiday" id="is_holiday" value="1" {{ old('is_holiday') ? 'checked' : '' }} class="w-4 h-4 mr-2 text-blue-600 border-gray-300 rounded">
                <label for="is_holiday" class="text-gray-700 font-medium select-none cursor-pointer">Is this a Holiday? (विदाको दिन हो?)</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">From Time (सुरुको समय)</label>
                    <input type="time" name="from_time" value="{{ old('from_time') }}" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">To Time (सकिने समय)</label>
                    <input type="time" name="to_time" value="{{ old('to_time') }}" class="w-full p-2 border rounded" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Remarks</label>
                <textarea name="remarks" class="w-full p-2 border rounded" rows="2">{{ old('remarks') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">
                Submit Overtime
            </button>
        </form>
    </div>

@if($canSelectAny)
<script>
    new TomSelect("#employee-select",{
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        }
    });
</script>
@endif
@endsection