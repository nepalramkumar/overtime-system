

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">मेरो OT Records</h2>

    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-6 py-3 border">मिति</th>
                    <th class="px-6 py-3 border">कार्यक्रम / कारण</th>
                    <th class="px-6 py-3 border">समय</th>
                    <th class="px-6 py-3 border">घण्टा</th>
                    <th class="px-6 py-3 border">खाजा</th>
                    <th class="px-6 py-3 border">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 border"><?php echo e($rec->ot_date); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->event->event_name ?? ($rec->remarks ?: 'सामान्य')); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->from_time); ?> - <?php echo e($rec->to_time); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->total_hours); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($rec->tiffin_amount); ?></td>
                    <td class="px-6 py-4 border">
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
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center p-4">कुनै record छैन।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/overtime/my.blade.php ENDPATH**/ ?>