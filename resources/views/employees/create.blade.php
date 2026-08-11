@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg border">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">कर्मचारीको विवरण थप्नुहोस्</h2>

        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">कर्मचारीको नाम</label>
                <input type="text" name="name" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="पूरा नाम">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code</label>
                <input type="text" name="employee_code" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="जस्तै: EMP001">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">विभाग (Department)</label>
                <input type="text" name="department" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="जस्तै: HR Department">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">पद (Position)</label>
                <select name="position_id" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Position छान्नुहोस् --</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 transition">
                    सेभ गर्नुहोस्
                </button>
                <a href="{{ route('employees.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-300 transition">
                    रद्द गर्नुहोस्
                </a>
            </div>
        </form>
    </div>
</div>
@endsection