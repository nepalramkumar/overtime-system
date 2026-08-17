<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg border">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">कर्मचारीको विवरण Edit गर्नुहोस्</h2>

        <?php if($errors->any()): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('employees.update', $employee->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <p class="text-xs text-gray-500 mb-4 bg-gray-50 p-2 rounded">
                ℹ️ नाम, विभाग, र पद भविष्यमा External API बाट स्वतः Sync हुने भएकोले, यहाँबाट सम्पादन गर्न मिल्दैन।
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">कर्मचारीको नाम</label>
                <div class="w-full border p-2 rounded bg-gray-100 text-gray-700 font-semibold"><?php echo e($employee->name); ?></div>
            </div>

           <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code</label>
                <div class="w-full border p-2 rounded bg-gray-100 text-gray-700 font-semibold"><?php echo e($employee->employee_code); ?></div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">विभाग (Department)</label>
                <div class="w-full border p-2 rounded bg-gray-100 text-gray-700 font-semibold"><?php echo e($employee->department); ?></div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">पद (Position)</label>
                <div class="w-full border p-2 rounded bg-gray-100 text-gray-700 font-semibold"><?php echo e($employee->position->name ?? 'N/A'); ?></div>
            </div>

            <hr class="my-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Petrol Bill / Repair Expense सम्बन्धी विवरण</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle No</label>
                <input type="text" name="vehicle_no" value="<?php echo e(old('vehicle_no', $employee->vehicle_no)); ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="जस्तै: बा १२ प ३४५६">
                <?php if(empty($employee->vehicle_no)): ?>
                    <p class="text-xs text-red-600 mt-1">⚠ हाल Vehicle No खाली छ — यो नथपेसम्म यस कर्मचारीको Petrol Bill दर्ता गर्न मिल्दैन।</p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Petrol Quantity Limit (महिनाको, लिटरमा)</label>
                <input type="number" name="petrol_quantity_limit" value="<?php echo e(old('petrol_quantity_limit', $employee->petrol_quantity_limit)); ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" min="0">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Repair Expense Limit (FY Year को, रुपैयाँमा)</label>
                <input type="number" name="repair_expense_limit" value="<?php echo e(old('repair_expense_limit', $employee->repair_expense_limit)); ?>" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" min="0">
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 transition">
                    अपडेट गर्नुहोस्
                </button>
                <a href="<?php echo e(route('employees.index')); ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-300 transition">
                    रद्द गर्नुहोस्
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/employees/edit.blade.php ENDPATH**/ ?>