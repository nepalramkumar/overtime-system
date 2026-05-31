


<?php $__env->startSection('content'); ?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">कर्मचारी व्यवस्थापन</h2>
    
    <a href="<?php echo e(route('employees.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        + नयाँ कर्मचारी थप्नुहोस्
    </a>
<script src="https://cdn.tailwindcss.com"></script>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 border">नाम</th>
                    <th scope="col" class="px-6 py-3 border">पद (Designation)</th>
                    <th scope="col" class="px-6 py-3 border">विभाग (Department)</th>
                    <th scope="col" class="px-6 py-3 border">OT रेट</th>
                    <th scope="col" class="px-6 py-3 border">कार्य (Action)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 border font-medium text-gray-900">
    <?php echo e($emp->name ?? ($emp->user->name ?? 'N/A')); ?>

</td>
                    <td class="px-6 py-4 border"><?php echo e($emp->designation); ?></td>
                    <td class="px-6 py-4 border"><?php echo e($emp->department); ?></td>
                    <td class="px-6 py-4 border">रू. <?php echo e(number_format($emp->ot_rate, 2)); ?></td>
                    <td class="px-6 py-4 border flex gap-2">
                        <a href="<?php echo e(route('employees.edit', $emp->id)); ?>" class="text-blue-600 hover:text-blue-900 font-semibold">Edit</a>
                        <form action="<?php echo e(route('employees.destroy', $emp->id)); ?>" method="POST" onsubmit="return confirm('के तपाईं पक्का हुनुहुन्छ?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/employees/list.blade.php ENDPATH**/ ?>