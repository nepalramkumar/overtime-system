@extends('layouts.app')

@section('content')
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Active Events / Projects</h2>
        
        <div class="mb-4 text-right">
            <a href="{{ route('overtime.create') }}" class="bg-gray-600 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">
                Log General OT (सामान्य प्रयोजन)
            </a>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="p-3">Event Name</th>
                    <th class="p-3">Department</th>
                    <th class="p-3">Date Range</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($events as $event)
                    <tr>
                        <td class="p-3 font-semibold">{{ $event->event_name }}</td>
                        <td class="p-3">{{ $event->department }}</td>
                        <td class="p-3 text-sm">{{ $event->start_date }} to {{ $event->end_date }}</td>
                        <td class="p-3">
                            <a href="{{ route('overtime.create', ['event_id' => $event->id]) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-bold hover:bg-blue-700">
                                Entry Overtime
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-3 text-center text-gray-500">अहिले कुनै पनि सक्रिय कार्यक्रम छैनन्।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection