

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">मेरो OT Records</h2>

    <?php if(session('success')): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('overtime.my')); ?>" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
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
                <label class="block text-xs font-medium text-gray-600 mb-1">कार्यक्रम</label>
                <select name="event_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">सबै छान्नुहोस्</option>
                    <?php $__currentLoopData = \App\Models\Event::orderBy('id', 'desc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>><?php echo e($event->event_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">🔍 खोज</button>
                <a href="<?php echo e(route('overtime.my')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">सि.नं.</th>
                    <th class="p-3 border">कर्मचारी कोड</th>
                    <th class="p-3 border">पद</th>
                    <th class="p-3 border">मिति</th>
                    <th class="p-3 border">कार्यक्रम / कारण</th>
                    <th class="p-3 border">समय</th>
                    <th class="p-3 border">घण्टा</th>
                    <th class="p-3 border">खाजा</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                <?php $sn = 1; ?>
               <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 <?php echo e(session('highlight_id') == $rec->id ? 'bg-green-50 ring-2 ring-green-400' : ''); ?>">
                    <td class="p-3 border text-center"><?php echo e($sn++); ?></td>
                    <td class="p-3 border"><?php echo e(auth()->user()->employee->employee_code ?? '-'); ?></td>
                    <td class="p-3 border"><?php echo e(auth()->user()->employee->position->name ?? 'N/A'); ?></td>
                    <td class="p-3 border"><?php echo e($rec->ot_date); ?></td>
                    <td class="p-3 border"><?php echo e($rec->event->event_name ?? ($rec->remarks ?: 'सामान्य')); ?></td>
                    <td class="p-3 border text-center"><?php echo e($rec->from_time); ?> - <?php echo e($rec->to_time); ?></td>
                    <td class="p-3 border text-center"><?php echo e(number_format($rec->total_hours, 2)); ?></td>
                    <td class="p-3 border text-center"><?php echo e(number_format($rec->tiffin_amount, 2)); ?></td>
                    <td class="p-3 border">
                        <?php if($rec->status == 'Verified'): ?>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Verified</span>
                        <?php elseif($rec->status == 'Rejected'): ?>
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Rejected</span>
                            <?php if($rec->rejection_reason): ?>
                                <div class="text-xs text-red-600 mt-1">कारण: <?php echo e($rec->rejection_reason); ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-semibold">Pending</span>
                        <?php endif; ?>
                    </td>
                   <td class="p-3 border">
                        <a href="<?php echo e(route('overtime.print', $rec->id)); ?>" target="_blank" class="text-purple-600 hover:text-purple-900 font-semibold text-sm">Print</a>
                        <?php if(in_array($rec->status, ['Pending', 'Rejected'])): ?>
                            <a href="<?php echo e(route('overtime.edit', $rec->id)); ?>" class="text-blue-600 hover:text-blue-900 font-semibold text-sm ml-2">Edit</a>
                            <form action="<?php echo e(route('overtime.destroy', $rec->id)); ?>" method="POST" class="inline" onsubmit="return confirm('के तपाईं पक्का हुनुहुन्छ?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm ml-2">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="10" class="text-center p-4">कुनै record भेटिएन।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/overtime/my.blade.php ENDPATH**/ ?>