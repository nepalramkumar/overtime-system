
<?php $__env->startSection('content'); ?>
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">📋 ओभरटाइम रेकर्डहरू</h2>

        <?php if(session('success')): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-3 border">मिति</th>
                    <th class="p-3 border">कर्मचारी</th>
                    <th class="p-3 border">समय</th>
                    <th class="p-3 border">घण्टा</th>
                    <th class="p-3 border">टिफिन</th>
                    <th class="p-3 border">एक्सन</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 border"><?php echo e($rec->ot_date); ?></td>
                    <td class="p-3 border"><?php echo e($rec->employee->name ?? 'N/A'); ?></td>
                    <td class="p-3 border"><?php echo e($rec->from_time); ?> - <?php echo e($rec->to_time); ?></td>
                    <td class="p-3 border"><?php echo e(number_format($rec->total_hours, 2)); ?></td>
                    <td class="p-3 border">रु. <?php echo e(number_format($rec->tiffin_amount, 0)); ?></td>
                    <td class="p-3 border">
                        <!-- <a href="<?php echo e(route('overtime.edit', $rec->id)); ?>" class="text-blue-600 mr-2">Edit</a> -->
                        <form action="<?php echo e(route('overtime.destroy', $rec->id)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-600" onclick="return confirm('के तपाईं पक्का हटाउन चाहनुहुन्छ?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/overtime/index.blade.php ENDPATH**/ ?>