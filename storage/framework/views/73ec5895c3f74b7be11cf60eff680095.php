

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-4">
    <?php if(session('warning')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow">
            <p class="font-bold">चेतावनी!</p>
            <?php echo e(session('warning')); ?>

            <a href="<?php echo e(route('employees.index')); ?>" class="underline font-semibold">यहाँ क्लिक गर्नुहोस्</a>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('reports.finance')); ?>" method="GET" class="bg-white p-4 mb-4 rounded-lg shadow-sm border">
        </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">Name</th>
                    <th class="p-3 border">कार्यक्रम</th>
                    <th class="p-3 border">Total Hours</th>
                    <th class="p-3 border">OT Rate</th>
                    <th class="p-3 border">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $financeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border"><?php echo e($data->employee->name ?? 'N/A'); ?></td>
                    <td class="p-3 border"><?php echo e($data->event->event_name ?? 'N/A'); ?></td>
                    <td class="p-3 border text-center"><?php echo e($data->total_hours); ?></td>
                    <td class="p-3 border text-center">
                        <?php echo e($data->employee->ot_rate ?? 'N/A'); ?>

                    </td>
                    <td class="p-3 border text-right">
                        रु <?php echo e(number_format($data->total_hours * ($data->employee->ot_rate ?? 0), 2)); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('reports.exportFinanceExcel', request()->all())); ?>" class="bg-green-600 text-white px-4 py-2 rounded shadow">
            Excel डाउनलोड (Finance)
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/reports/index.blade.php ENDPATH**/ ?>