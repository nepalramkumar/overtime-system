<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Repair Expense</h2>
        <a href="<?php echo e(route('repair.expenses.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700">
            + नयाँ Repair Expense थप्नुहोस्
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('repair.expenses.index')); ?>" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
        <div class="flex gap-2 items-end">
            <div class="w-64">
                <label class="block text-xs font-medium text-gray-600 mb-1">FY Year</label>
                <select name="fy_year" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                    <option value="">सबै</option>
                    <?php $__currentLoopData = $fyList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($fy); ?>" <?php echo e(request('fy_year') == $fy ? 'selected' : ''); ?>><?php echo e($fy); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-2 rounded">🔍 खोज</button>
            <a href="<?php echo e(route('repair.expenses.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-2 rounded">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
        <table class="w-full border-collapse">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 border">सि.नं.</th>
                    <th class="p-3 border">कर्मचारी</th>
                    <th class="p-3 border">पद</th>
                    <th class="p-3 border">FY Year</th>
                    <th class="p-3 border">जम्मा रकम</th>
                    <th class="p-3 border">Edit अनुमति</th>
                    <th class="p-3 border">कार्य</th>
                </tr>
            </thead>
            <tbody>
                <?php $sn = 1; ?>
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border text-center"><?php echo e($sn++); ?></td>
                    <td class="p-3 border"><?php echo e($expense->employee->name ?? 'N/A'); ?></td>
                    <td class="p-3 border"><?php echo e($expense->employee->position->name ?? 'N/A'); ?></td>
                    <td class="p-3 border"><?php echo e($expense->fy_year); ?></td>
                    <td class="p-3 border text-right">रु <?php echo e(number_format($expense->total_amount, 2)); ?></td>
                    <td class="p-3 border text-center">
                        <?php if($expense->isEdit): ?>
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">खुला</span>
                        <?php else: ?>
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">बन्द</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3 border">
                        <a href="<?php echo e(route('repair.expenses.edit', $expense->id)); ?>"
                           class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-semibold hover:bg-blue-700">
                            Edit
                        </a>
                        <?php if(auth()->user()->role === 'admin' || \App\Models\RolePermission::where('role', auth()->user()->role)->where('permission', 'repair.expenses.manage')->exists()): ?>
                            <form action="<?php echo e(route('repair.expenses.toggleEdit', $expense->id)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="<?php echo e($expense->isEdit ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700'); ?> text-white px-3 py-1 rounded text-sm ml-1">
                                    <?php echo e($expense->isEdit ? 'Edit बन्द गर्नुहोस्' : 'Edit खोल्नुहोस्'); ?>

                                </button>
                            </form>
                            <form action="<?php echo e(route('repair.expenses.destroy', $expense->id)); ?>" method="POST" class="inline" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm ml-1 hover:bg-red-700">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center p-4">कुनै Repair Expense भेटिएन।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($expenses->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/repair/expenses/index.blade.php ENDPATH**/ ?>