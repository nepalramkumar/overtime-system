@extends('layouts.app')

@section('content')

    <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Overtime Edit गर्नुहोस्</h2>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        @if($record->status == 'Rejected')
            <div class="bg-red-50 border border-red-200 p-3 rounded mb-4">
                <p class="text-red-700 font-semibold text-sm">यो record Reject भएको थियो।</p>
                @if($record->rejection_reason)
                    <p class="text-red-600 text-sm mt-1">कारण: {{ $record->rejection_reason }}</p>
                @endif
                <p class="text-gray-600 text-xs mt-1">Save गरेपछि, यो फेरि Pending मा जान्छ।</p>
            </div>
        @endif

        <form action="{{ route('overtime.update', $record->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Employee</label>
                @if($canSelectAny)
                    <select name="employee_id" class="w-full p-2 border rounded" required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $record->employee_id == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full p-2 border rounded bg-gray-100 text-gray-700 font-semibold">
                        {{ $record->employee->name ?? 'N/A' }} ({{ $record->employee->employee_code ?? '' }})
                    </div>
                    <input type="hidden" name="employee_id" value="{{ $record->employee_id }}">
                @endif
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Event /General</label>
                @if($canSelectAny)
                    <select name="event_id" class="w-full p-2 border rounded">
                        <option value="">-- सामान्य (General) --</option>
                        @foreach(\App\Models\Event::where('is_active', true)->orWhere('id', $record->event_id)->orderBy('id', 'desc')->get() as $event)
                            <option value="{{ $event->id }}" {{ $record->event_id == $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }}{{ !$event->is_active ? ' (Disabled)' : '' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full p-2 border rounded bg-gray-100 text-gray-700 font-semibold">
                        {{ $record->event->event_name ?? 'सामान्य (General)' }}
                    </div>
                    <input type="hidden" name="event_id" value="{{ $record->event_id ?? '' }}">
                @endif
            </div>

          <div>
    <label class="block text-gray-700 font-semibold mb-1">Purpose (General OT भए, धेरै दिन चल्ने काम भए मात्र छान्नुहोस्)</label>
    <select name="purpose_id" class="w-full p-2 border rounded">
        <option value="">-- एक दिनको मात्र काम (Purpose चाहिँदैन) --</option>
       @foreach(\App\Models\Purpose::where('is_active', true)->orWhere('id', $record->purpose_id)->orderBy('id', 'desc')->get() as $purpose)
            <option value="{{ $purpose->id }}" {{ $record->purpose_id == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}{{ !$purpose->is_active ? ' (Disabled)' : '' }}</option>
        @endforeach
    </select>
</div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Date</label>
                <input type="date" name="ot_date" value="{{ $record->ot_date }}" class="w-full p-2 border rounded" required>
            </div>

            <div class="flex items-center bg-yellow-50 p-2 rounded border border-yellow-200">
                <input type="checkbox" name="is_holiday" id="is_holiday" value="1" {{ $record->is_holiday ? 'checked' : '' }} class="w-4 h-4 mr-2">
                <label for="is_holiday" class="text-gray-700 font-medium select-none cursor-pointer">Is this a Holiday?</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">From Time</label>
                    <input type="time" name="from_time" value="{{ $record->from_time }}" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">To Time</label>
                    <input type="time" name="to_time" value="{{ $record->to_time }}" class="w-full p-2 border rounded" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Remarks</label>
                <textarea name="remarks" class="w-full p-2 border rounded" rows="2">{{ $record->remarks }}</textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">
                Update गर्नुहोस्
            </button>
        </form>
    </div>

@endsection