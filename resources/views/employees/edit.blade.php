@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg border">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">कर्मचारीको विवरण Edit गर्नुहोस्</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">कर्मचारीको नाम</label>
                <input type="text" name="name" value="{{ old('name', $employee->name) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code</label>
                <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">विभाग (Department)</label>
                <input type="text" name="department" value="{{ old('department', $employee->department) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">पद (Position)</label>
                <select name="position_id" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Position छान्नुहोस् --</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                    @endforeach
                </select>
            </div>

            <hr class="my-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Petrol Bill / Repair Expense सम्बन्धी विवरण</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle No</label>
                <input type="text" name="vehicle_no" value="{{ old('vehicle_no', $employee->vehicle_no) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="जस्तै: बा १२ प ३४५६">
                @if(empty($employee->vehicle_no))
                    <p class="text-xs text-red-600 mt-1">⚠ हाल Vehicle No खाली छ — यो नथपेसम्म यस कर्मचारीको Petrol Bill दर्ता गर्न मिल्दैन।</p>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Petrol Quantity Limit (महिनाको, लिटरमा)</label>
                <input type="number" name="petrol_quantity_limit" value="{{ old('petrol_quantity_limit', $employee->petrol_quantity_limit) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" min="0">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Repair Expense Limit (FY Year को, रुपैयाँमा)</label>
                <input type="number" name="repair_expense_limit" value="{{ old('repair_expense_limit', $employee->repair_expense_limit) }}" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" min="0">
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 transition">
                    अपडेट गर्नुहोस्
                </button>
                <a href="{{ route('employees.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-300 transition">
                    रद्द गर्नुहोस्
                </a>
            </div>
        </form>
    </div>
</div>
@endsection