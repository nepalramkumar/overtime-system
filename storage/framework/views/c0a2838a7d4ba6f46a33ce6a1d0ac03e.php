

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Verified OT Records</h2>

    <?php if(session('success')): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('overtime.verified')); ?>" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
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
                <a href="<?php echo e(route('overtime.verified')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-6 py-3 border">सि.नं.</th>
                    <th class="px-6 py-3 border">कर्मचारी कोड</th>
                    <th class="px-6 py-3 border">कर्मचारी</th>
                    <th class="px-6 py-3 border">पद</th>
                    <th class="px-6 py-3 border">मिति</th>
                    <th class="px-6 py-3 border">समय</th>
                    <th class="px-6 py-3 border">घण्टा</th>
                    <th class="px-6 py-3 border">कार्यक्रम / कारण</th>
                    <th class="px-6 py-3 border">Verify गर्ने</th>
                    <th class="px-6 py-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                <?php $sn = 1; ?>
                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 border text-center"><?php echo e($sn++); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->employee->employee_code ?? '-'); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->employee->name ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->employee->position->name ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->ot_date); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->from_time); ?> - <?php echo e($rec->to_time); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->total_hours); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->event->event_name ?? ($rec->remarks ?: 'सामान्य')); ?></td>
                    <td class="px-6 py-4 border text-xs"><?php echo e($rec->verifier->name ?? 'N/A'); ?></td>
                    <td class="px-6 py-4 border">
                        <form action="<?php echo e(route('overtime.unverify', $rec->id)); ?>" method="POST" onsubmit="return confirm('के तपाईं यो रेकर्ड Unverify गर्न चाहनुहुन्छ? यो फेरि Pending मा जान्छ।')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="bg-orange-500 text-white px-3 py-1 rounded hover:bg-orange-600 text-sm">Unverify</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="10" class="text-center p-4">कुनै Verified record छैन।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/overtime/verified.blade.php ENDPATH**/ ?>