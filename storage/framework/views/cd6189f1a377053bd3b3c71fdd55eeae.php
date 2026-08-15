<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Petrol Month सेटिङ्स</h1>

    <?php if(session('success')): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <table class="w-full border-collapse border border-gray-200 mb-8">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-left">Month</th>
                <th class="border p-2 text-left">Year</th>
                <th class="border p-2 w-24">कार्य</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="border p-2"><?php echo e($item->month); ?></td>
                <td class="border p-2"><?php echo e($item->year); ?></td>
                <td class="border p-2 text-center">
                    <form action="<?php echo e(route('petrol.months.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" class="text-center p-4 text-gray-500">कुनै Month थपिएको छैन।</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="bg-gray-100 p-4 rounded">
        <h3 class="font-bold mb-2">नयाँ Month थप्नुहोस्</h3>
        <form action="<?php echo e(route('petrol.months.store')); ?>" method="POST" class="flex gap-2">
            <?php echo csrf_field(); ?>
            <select name="month" class="border p-2 w-full" required>
                <option value="">-- महिना छान्नुहोस् --</option>
                <?php $__currentLoopData = ['Baishakh','Jestha','Ashad','Shrawan','Bhadra','Ashwin','Kartik','Mangsir','Poush','Magh','Falgun','Chaitra']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>"><?php echo e($m); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="text" name="year" placeholder="जस्तै: 2083" class="border p-2 w-full" required>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 whitespace-nowrap">थप्नुहोस्</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/petrol/months.blade.php ENDPATH**/ ?>