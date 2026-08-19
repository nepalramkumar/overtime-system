@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Active Events / Projects</h2>
            <p class="text-sm text-gray-500 mt-1">सबै कार्यक्रम / Project हरूको OT claim स्थिति यहाँ हेर्नुहोस्।</p>
        </div>
        <a href="{{ route('overtime.create') }}"
           class="bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-800 transition shadow-sm">
            + Log General OT (सामान्य प्रयोजन)
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Card wrapper --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Event Name</th>
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Department</th>
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Date Range</th>
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wide text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition {{ !$event->is_active ? 'bg-gray-50/60' : '' }}">

                            {{-- Event name + OT badges --}}
                            <td class="p-4 align-top">
                                <div class="font-semibold text-gray-800 {{ !$event->is_active ? 'text-gray-400' : '' }}">
                                    {{ $event->event_name }}
                                </div>

                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @forelse($event->status_summary as $status => $count)
                                        @php
                                            $colors = match(strtolower($status)) {
                                                'pending'  => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-700', 'light' => 'bg-yellow-50 border-yellow-100'],
                                                'rejected' => ['bg' => 'bg-red-500',    'text' => 'text-red-700',    'light' => 'bg-red-50 border-red-100'],
                                                'verified' => ['bg' => 'bg-green-500',  'text' => 'text-green-700',  'light' => 'bg-green-50 border-green-100'],
                                                default    => ['bg' => 'bg-blue-500',   'text' => 'text-blue-700',   'light' => 'bg-blue-50 border-blue-100'],
                                            };
                                        @endphp
                                        <div class="flex items-center gap-1.5 {{ $colors['light'] }} border rounded-full pl-2.5 pr-1 py-0.5">
                                            <span class="text-[10px] font-bold {{ $colors['text'] }} uppercase tracking-wide">
                                                {{ $status }}
                                            </span>
                                            <span class="{{ $colors['bg'] }} text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full">
                                                {{ $count }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-[10px] text-gray-400 italic">कुनै claim छैन</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="p-4 align-top text-sm text-gray-600">{{ $event->department }}</td>

                            <td class="p-4 align-top text-sm text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') }}
                                <span class="text-gray-300 mx-1">→</span>
                                {{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') }}
                            </td>

                            <td class="p-4 align-top">
                                @if($event->is_active)
                                    <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-100 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Disabled
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 align-top">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    @if($event->is_active)
                                        <a href="{{ route('overtime.create', ['event_id' => $event->id]) }}"
                                           class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition">
                                            Entry Overtime
                                        </a>
                                    @else
                                        <span class="bg-gray-200 text-gray-400 px-3 py-1.5 rounded-lg text-xs font-bold cursor-not-allowed">
                                            Entry Overtime
                                        </span>
                                    @endif

                                    <a href="{{ route('events.print', $event->id) }}" target="_blank"
                                       class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-purple-700 transition">
                                        Print
                                    </a>
                                    <a href="{{ route('events.edit', $event->id) }}"
   class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-purple-500 transition">
    Edit
</a>

                                    <form action="{{ route('events.toggle', $event->id) }}" method="POST"
                                          onsubmit="return confirm('के तपाईं यो कार्यक्रमको Status बदल्न चाहनुहुन्छ?')">
                                        @csrf
                                        <button type="submit"
                                            class="{{ $event->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            {{ $event->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-400">
                                अहिले कुनै पनि सक्रिय कार्यक्रम छैनन्।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection