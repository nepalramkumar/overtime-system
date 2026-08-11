

<?php $__env->startSection('content'); ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Position सेटिङ्स (Designation List & OT Rate)</h1>

        <?php if(session('success')): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <table class="w-full border-collapse border border-gray-200 mb-8">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">पद (Position)</th>
                    <th class="border p-2">OT रेट (प्रति घण्टा)</th>
                    <th class="border p-2">कार्य</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="border p-2 text-center"><?php echo e($item->name); ?></td>
                    <td class="border p-2 text-center">
                        <form action="<?php echo e(route('positions.updateRate', $item->id)); ?>" method="POST" class="flex justify-center gap-2">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="number" step="0.01" name="ot_rate" value="<?php echo e($item->ot_rate); ?>" class="border p-1 w-24 text-center">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update</button>
                        </form>
                    </td>
                    <td class="border p-2 text-center">
                        <form action="<?php echo e(route('positions.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="max-w-4xl mx-auto bg-gray-100 p-4 rounded mb-6">
        <h3 class="font-bold mb-2">नयाँ Position थप्नुहोस्</h3>
        <form action="<?php echo e(route('positions.store')); ?>" method="POST" class="flex gap-2">
            <?php echo csrf_field(); ?>
            <input type="text" name="name" placeholder="Position नाम (जस्तै: Senior Developer)" class="border p-2 w-full" required>
            <input type="number" step="0.01" name="ot_rate" placeholder="OT रेट (रु.)" class="border p-2 w-full">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">थप्नुहोस्</button>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/settings/positions.blade.php ENDPATH**/ ?>