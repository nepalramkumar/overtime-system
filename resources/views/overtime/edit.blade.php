@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">
    <!-- Form Title -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-800">Overtime सच्याउनुहोस् (Edit)</h2>
        <p class="text-xs text-slate-500 mt-1">दर्ता भइसकेको ओभरटाइम विवरण परिमार्जन फारम</p>
    </div>

    <!-- Rejection Alert Box -->
    @if($record->status == 'Rejected')
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl mb-5 shadow-sm text-sm">
            <div class="flex items-center gap-2 text-rose-800 font-semibold">
                <i class="fas fa-exclamation-triangle"></i>
                <span>यो Record Reject भएको थियो।</span>
            </div>
            @if($record->rejection_reason)
                <p class="text-rose-700 text-xs mt-1 pl-6"><strong>कारण:</strong> {{ $record->rejection_reason }}</p>
            @endif
            <p class="text-slate-500 text-xs mt-2 pl-6 italic">
                * Note: सच्याएर Submit गरेपछि यो फेरि Swekriti को लागि 'Pending' अवस्थामा जानेछ।
            </p>
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('overtime.update', $record->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Employee Selection -->
            <div>
                <label class="block text-slate-700 font-semibold text-sm mb-1">
                    Employee <span class="text-rose-500">*</span>
                </label>
                @if($canSelectAny)
                    <select name="employee_id" id="employee-select" class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ (old('employee_id', $record->employee_id) == $emp->id) ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->employee_code }})
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full p-2.5 border border-slate-200 rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm flex items-center justify-between">
                        <span>{{ $record->employee->name ?? 'N/A' }} ({{ $record->employee->employee_code ?? '' }})</span>
                        <i class="fas fa-lock text-slate-400"></i>
                    </div>
                    <input type="hidden" name="employee_id" value="{{ $record->employee_id }}">
                @endif
                @error('employee_id')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Selection -->
            <div>
                <label class="block text-slate-700 font-semibold text-sm mb-1">Event / Project</label>
                @if($canSelectAny)
                    <select name="event_id" class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        <option value="">-- सामान्य (General Purpose) --</option>
                        @foreach(\App\Models\Event::where('is_active', true)->orWhere('id', $record->event_id)->orderBy('id', 'desc')->get() as $event)
                            <option value="{{ $event->id }}" {{ (old('event_id', $record->event_id) == $event->id) ? 'selected' : '' }}>
                                {{ $event->event_name }}{{ !$event->is_active ? ' (Disabled)' : '' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full p-2.5 border border-slate-200 rounded-lg bg-slate-100 text-slate-700 font-medium text-sm">
                        {{ $record->event->event_name ?? 'सामान्य (General Purpose)' }}
                    </div>
                    <input type="hidden" name="event_id" value="{{ $record->event_id ?? '' }}">
                @endif
                @error('event_id')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Purpose Selection -->
            <div>
                <label class="block text-slate-700 font-semibold text-sm mb-1">
                    Purpose <span class="text-xs font-normal text-slate-500">(धेरै दिन चल्ने काम भए मात्र)</span>
                </label>
                <select name="purpose_id" class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    <option value="">-- एक दिनको मात्र काम (Purpose चाहिँदैन) --</option>
                    @foreach(\App\Models\Purpose::where('is_active', true)->orWhere('id', $record->purpose_id)->orderBy('id', 'desc')->get() as $purpose)
                        <option value="{{ $purpose->id }}" {{ (old('purpose_id', $record->purpose_id) == $purpose->id) ? 'selected' : '' }}>
                            {{ $purpose->name }}{{ !$purpose->is_active ? ' (Disabled)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('purpose_id')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Input -->
            <div>
                <label class="block text-slate-700 font-semibold text-sm mb-1">
                    Date <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="ot_date" value="{{ old('ot_date', $record->ot_date) }}" 
                       class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                @error('ot_date')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Holiday Checkbox -->
            <div class="flex items-center bg-amber-50 p-3 rounded-lg border border-amber-200/80">
                <input type="checkbox" name="is_holiday" id="is_holiday" value="1" {{ old('is_holiday', $record->is_holiday) ? 'checked' : '' }} 
                       class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                <label for="is_holiday" class="ml-2 text-sm text-slate-700 font-medium select-none cursor-pointer">
                    Is this a Holiday? (सार्वजनिक वा विदाको दिन हो?)
                </label>
            </div>

            <!-- Time Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-700 font-semibold text-sm mb-1">
                        From Time <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="from_time" value="{{ old('from_time', $record->from_time) }}" 
                           class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                    @error('from_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-slate-700 font-semibold text-sm mb-1">
                        To Time <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="to_time" value="{{ old('to_time', $record->to_time) }}" 
                           class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                    @error('to_time')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-slate-700 font-semibold text-sm mb-1">Remarks (कैफियत)</label>
                <textarea name="remarks" class="w-full p-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm" rows="2">{{ old('remarks', $record->remarks) }}</textarea>
                @error('remarks')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ url()->previous() }}" class="w-1/3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold py-3 px-4 rounded-lg shadow-sm transition text-center text-sm">
                    रद्द गर्नुहोस्
                </a>
                <button type="submit" class="w-2/3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-150 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-save"></i>
                    <span>Update गर्नुहोस्</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TomSelect Integration -->
@if($canSelectAny)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('employee-select');
        if (selectEl) {
            new TomSelect("#employee-select", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        }
    });
</script>
@endif
@endsection