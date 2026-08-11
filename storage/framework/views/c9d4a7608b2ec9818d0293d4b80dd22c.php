

<?php $__env->startSection('content'); ?>

<form action="<?php echo e(route('reports.finance')); ?>" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
            <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
            <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">कर्मचारी</label>
            <select name="employee_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="">सबै</option>
                <?php $__currentLoopData = \App\Models\Employee::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php echo e(request('employee_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">कार्यक्रम</label>
            <select name="event_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="">सबै कार्यक्रम</option>
                <?php $__currentLoopData = \App\Models\Event::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ev->id); ?>" <?php echo e(request('event_id') == $ev->id ? 'selected' : ''); ?>><?php echo e($ev->event_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded">🔍 खोज</button>
            <a href="<?php echo e(route('reports.finance')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-2 rounded">Reset</a>
        </div>
    </div>
</form>

<div class="overflow-x-auto bg-white rounded-lg shadow-sm">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-blue-700 text-white text-sm">
                <th class="p-3 border">सि.नं.</th>
                <th class="p-3 border">Name</th>
                <th class="p-3 border">कार्यक्रम</th>
                <th class="p-3 border">Designation</th>
                <th class="p-3 border">Date From</th>
                <th class="p-3 border">Date To</th>
                <th class="p-3 border">Total Hours</th>
                <th class="p-3 border">Rate</th>
                <th class="p-3 border">OT Amount</th>
                <th class="p-3 border">Lunch Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $financeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="p-3 border text-center"><?php echo e($loop->iteration); ?></td>
                <td class="p-3 border"><?php echo e($data->employee->name ?? 'N/A'); ?></td>
                <td class="p-3 border"><?php echo e($data->event->event_name ?? 'N/A'); ?></td>
                <td class="p-3 border"><?php echo e($data->employee->designation ?? 'N/A'); ?></td>
                <td class="p-3 border text-center"><?php echo e($data->date_from); ?></td>
                <td class="p-3 border text-center"><?php echo e($data->date_to); ?></td>
                <td class="p-3 border text-center"><?php echo e(number_format($data->total_hours, 2)); ?></td>
                
                <td class="p-3 border text-center"><?php echo e(number_format($data->employee->ot_rate ?? 0, 2)); ?></td>
                
                <td class="p-3 border text-right font-bold">
                    रु <?php echo e(number_format($data->total_hours * ($data->employee->ot_rate ?? 0), 2)); ?>

                </td>
                <td class="p-3 border text-right">रु <?php echo e(number_format($data->total_lunch, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="p-4 text-center text-gray-500">कुनै डेटा भेटिएन।</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <a href="<?php echo e(route('reports.exportFinanceExcel', request()->all())); ?>" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">
        Excel डाउनलोड (Finance)
    </a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/reports/finance.blade.php ENDPATH**/ ?>