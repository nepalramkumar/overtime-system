

<?php $__env->startSection('content'); ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">मास्टर सेटिङ्स (Master Settings)</h1>

        <h2 class="text-xl font-semibold mb-4 text-gray-700">खाजा खर्च दर (Snack Allowance)</h2>
        <table class="w-full border-collapse border border-gray-200 mb-8">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">अवधि (घण्टा)</th>
                    <th class="border p-2">रकम (रु.)</th>
                    <th class="border p-2">कार्य</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $allowances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="border p-2 text-center"><?php echo e($item->min_hours); ?> - <?php echo e($item->max_hours); ?> घण्टा</td>
                    <td class="border p-2 text-center">
                        <form action="<?php echo e(route('settings.updateAllowance', $item->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="number" name="amount" value="<?php echo e($item->amount); ?>" class="border p-1 w-24 text-center">
                    </td>
                    <td class="border p-2 text-center">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
                        </form>
                    </td>
                    <td class="border p-2 text-center">
    <form action="<?php echo e(route('settings.destroyAllowance', $item->id)); ?>" method="POST" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
            Delete
        </button>
    </form>
</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <!-- नयाँ दर थप्ने फर्म -->
<div class="bg-gray-100 p-4 rounded mb-6">
    <h3 class="font-bold mb-2">नयाँ खाजा खर्च दर थप्नुहोस्</h3>
    <form action="<?php echo e(route('settings.storeAllowance')); ?>" method="POST" class="flex gap-2">
        <?php echo csrf_field(); ?>
        <input type="number" name="min_hours" placeholder="न्यूनतम घण्टा" step="any" class="border p-2 w-full" required>
        <input type="number" name="max_hours" placeholder="अधिकतम घण्टा"  class="border p-2 w-full" required>
        <input type="number" name="amount" placeholder="रकम (रु.)" class="border p-2 w-full" required>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">थप्नुहोस्</button>
    </form>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/settings/snack.blade.php ENDPATH**/ ?>