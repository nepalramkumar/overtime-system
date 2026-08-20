

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Page Header & Create Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">📋 ओभरटाइम रेकर्डहरू</h2>
            <p class="text-xs text-slate-500 mt-1">सिस्टममा प्रविष्टि गरिएका सम्पूर्ण ओभरटाइम तथा खाजा खर्च विवरणहरू</p>
        </div>
        <a href="<?php echo e(route('overtime.create')); ?>" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-lg shadow-sm transition text-sm">
            <i class="fas fa-plus"></i>
            <span>नयाँ OT थप्नुहोस्</span>
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="p-4">मिति (Date)</th>
                        <th class="p-4">कर्मचारी (Employee)</th>
                        <th class="p-4">समय (Time)</th>
                        <th class="p-4 text-center">घण्टा (Hours)</th>
                        <th class="p-4 text-right">टिफिन (Amount)</th>
                        <th class="p-4 text-center">एक्सन (Action)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Date -->
                            <td class="p-4 font-medium text-slate-800 whitespace-nowrap">
                                <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i>
                                <?php echo e($rec->ot_date); ?>

                            </td>

                            <!-- Employee Info -->
                            <td class="p-4">
                                <div class="font-semibold text-slate-800"><?php echo e($rec->employee->name ?? 'N/A'); ?></div>
                                <?php if(isset($rec->employee->employee_code)): ?>
                                    <div class="text-xs text-slate-400">ID: <?php echo e($rec->employee->employee_code); ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- Time Slot -->
                            <td class="p-4 text-slate-600 whitespace-nowrap">
                                <i class="far fa-clock text-slate-400 mr-1"></i>
                                <?php echo e($rec->from_time); ?> - <?php echo e($rec->to_time); ?>

                            </td>

                            <!-- Total Hours Badge -->
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <?php echo e(number_format($rec->total_hours, 2)); ?> hrs
                                </span>
                            </td>

                            <!-- Tiffin Amount -->
                            <td class="p-4 text-right font-semibold text-slate-800 whitespace-nowrap">
                                रु. <?php echo e(number_format($rec->tiffin_amount, 0)); ?>

                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Edit Icon Button -->
                                    <a href="<?php echo e(route('overtime.edit', $rec->id)); ?>" 
                                       class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" 
                                       title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Icon Form -->
                                    <form action="<?php echo e(route('overtime.destroy', $rec->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?> 
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" 
                                                onclick="return confirm('के तपाईं पक्का यो रेकर्ड हटाउन चाहनुहुन्छ?')"
                                                title="Delete Record">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <i class="fas fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                                कुनै पनि ओभरटाइम रेकर्ड भेटिएन।
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links (If Enabled) -->
        <?php if(method_exists($records, 'links')): ?>
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <?php echo e($records->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/overtime/index.blade.php ENDPATH**/ ?>