

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Summary Report</h2>
<form action="<?php echo e(route('reports.summary')); ?>" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
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
                <option value="">सबै छान्नुहोस्</option>
                <?php $__currentLoopData = \App\Models\Event::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>><?php echo e($event->event_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">🔍 खोज</button>
            <a href="<?php echo e(route('reports.summary')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
        </div>
    </div>
</form>
    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
       <table class="w-full border-collapse">
    <thead class="bg-blue-700 text-white">
        <tr>
            <th class="p-3 border">सि.नं.</th>
            <th class="p-3 border">कर्मचारी कोड</th>
            <th class="p-3 border">कर्मचारी</th>
            <th class="p-3 border">पद</th>
            <th class="p-3 border">कार्यक्रम</th>
            <th class="p-3 border">मिति (देखि - सम्म)</th>
            <th class="p-3 border">जम्मा घण्टा</th>
            <th class="p-3 border">जम्मा खाजा</th>
        </tr>
    </thead>
    <tbody>
        <?php $sn = 1; ?>
        <?php $__empty_1 = true; $__currentLoopData = $summaryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-gray-50">
            <td class="p-3 border text-center"><?php echo e($sn++); ?></td>
            <td class="p-3 border"><?php echo e($data->employee->employee_code ?? '-'); ?></td>
            <td class="p-3 border"><?php echo e($data->employee->name ?? 'N/A'); ?></td>
            <td class="p-3 border"><?php echo e($data->employee->position->name ?? 'N/A'); ?></td>
            <td class="p-3 border"><?php echo e($data->event->event_name ?? 'सामान्य'); ?></td>
            <td class="p-3 border text-center"><?php echo e($data->date_from); ?> - <?php echo e($data->date_to); ?></td>
            <td class="p-3 border text-center"><?php echo e(number_format($data->total_hours, 2)); ?></td>
            <td class="p-3 border text-right">रु <?php echo e(number_format($data->total_lunch, 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="text-center p-4">कुनै डेटा भेटिएन।</td></tr>
        <?php endif; ?>
    </tbody>
</table>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('reports.exportSummaryExcel', request()->all())); ?>" class="bg-green-600 text-white px-4 py-2 rounded shadow">
            Excel डाउनलोड (Summary)
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/reports/summary.blade.php ENDPATH**/ ?>