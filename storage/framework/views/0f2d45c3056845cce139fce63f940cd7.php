<?php $__env->startSection('content'); ?>
<div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">⛽ Petrol Bill विवरण सूची</h2>
            <p class="text-xs text-slate-500 mt-1">सबै पेट्रोल/डिजेल भौचर तथा प्रविष्टिको सूची</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Excel Download Button -->
            <a href="<?php echo e(route('petrol.bills.index', array_merge(request()->all(), ['export' => 'excel']))); ?>" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <i class="fas fa-file-excel"></i>
                <span>Excel डाउनलोड</span>
            </a>
            
            <!-- Create Button -->
            <a href="<?php echo e(route('petrol.bills.create')); ?>" 
               class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <i class="fas fa-plus"></i>
                <span>+ नयाँ Bill थप्नुहोस्</span>
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-xl text-xs font-medium flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-xl text-xs font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-rose-600"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Searchable Filter Form -->
    <form action="<?php echo e(route('petrol.bills.index')); ?>" method="GET" class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="w-full sm:w-80">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">महिना (Month Filter)</label>
                <select name="petrol_month_id" id="filter_month_id" class="w-full border border-slate-300 rounded-lg text-xs">
                    <option value="">-- सबै महिना --</option>
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>" <?php echo e(request('petrol_month_id') == $m->id ? 'selected' : ''); ?>>
                            <?php echo e($m->month); ?> - <?php echo e($m->year); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs px-4 py-2 rounded-lg transition font-medium flex items-center gap-1">
                    <i class="fas fa-search"></i>
                    <span>खोज्नुहोस्</span>
                </button>
                <a href="<?php echo e(route('petrol.bills.index')); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 text-xs px-4 py-2 rounded-lg transition font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Bills Table -->
    <div class="overflow-x-auto bg-white border border-slate-200 rounded-xl shadow-sm">
        <table class="w-full border-collapse text-left text-xs">
            <thead class="bg-slate-800 text-white uppercase tracking-wider font-semibold">
                <tr>
                    <th class="p-3 border-b border-slate-700 text-center">सि.नं.</th>
                    <th class="p-3 border-b border-slate-700">कर्मचारी कोड</th>
                    <th class="p-3 border-b border-slate-700">कर्मचारी</th>
                    <th class="p-3 border-b border-slate-700">पद</th>
                    <th class="p-3 border-b border-slate-700">Month</th>
                    <th class="p-3 border-b border-slate-700 text-center">जम्मा परिमाण (L)</th>
                    <th class="p-3 border-b border-slate-700 text-right">जम्मा रकम</th>
                    <th class="p-3 border-b border-slate-700 text-center">Edit अनुमति</th>
                    <th class="p-3 border-b border-slate-700 text-center">कार्य</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $sn = 1; ?>
                <?php $__empty_1 = true; $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="p-3 text-center text-slate-500 font-medium"><?php echo e($sn++); ?></td>
                    <td class="p-3 font-mono text-slate-700"><?php echo e($bill->employee->employee_code ?? '-'); ?></td>
                    <td class="p-3 font-semibold text-slate-800"><?php echo e($bill->employee->name ?? 'N/A'); ?></td>
                    <td class="p-3 text-slate-600"><?php echo e($bill->employee->position->name ?? 'N/A'); ?></td>
                    <td class="p-3 text-slate-700"><?php echo e($bill->month->month ?? ''); ?> - <?php echo e($bill->month->year ?? ''); ?></td>
                    <td class="p-3 text-center font-medium text-slate-800"><?php echo e(number_format($bill->total_quantity, 2)); ?></td>
                    <td class="p-3 text-right font-semibold text-slate-900">रु <?php echo e(number_format($bill->total_amount, 2)); ?></td>
                    <td class="p-3 text-center">
                        <?php if($bill->isEdit): ?>
                            <span class="bg-emerald-100 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">खुला</span>
                        <?php else: ?>
                            <span class="bg-rose-100 text-rose-800 border border-rose-200 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">बन्द</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="<?php echo e(route('petrol.bills.print', $bill->id)); ?>" target="_blank"
                               class="bg-purple-600 text-white px-2.5 py-1 rounded text-xs font-medium hover:bg-purple-700 transition" title="Print">
                                Print
                            </a>
                            <?php if(auth()->user()->role === 'admin' || \App\Models\RolePermission::where('role', auth()->user()->role)->where('permission', 'petrol.bills.manage')->exists() || $bill->isEdit): ?>
                                <a href="<?php echo e(route('petrol.bills.edit', $bill->id)); ?>"
                                   class="bg-sky-600 text-white px-2.5 py-1 rounded text-xs font-medium hover:bg-sky-700 transition" title="Edit">
                                    Edit
                                </a>
                            <?php endif; ?>
                            <?php if(auth()->user()->role === 'admin' || (auth()->user()->role && \App\Models\RolePermission::where('role', auth()->user()->role)->where('permission', 'petrol.bills.manage')->exists())): ?>
                                <form action="<?php echo e(route('petrol.bills.toggleEdit', $bill->id)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="<?php echo e($bill->isEdit ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700'); ?> text-white px-2.5 py-1 rounded text-xs transition">
                                        <?php echo e($bill->isEdit ? 'Edit बन्द' : 'Edit खोल्नुहोस्'); ?>

                                    </button>
                                </form>
                                <form action="<?php echo e(route('petrol.bills.destroy', $bill->id)); ?>" method="POST" class="inline" onsubmit="return confirm('के तपाईं पक्का डिलिट गर्न चाहनुहुन्छ?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="bg-rose-600 text-white px-2.5 py-1 rounded text-xs hover:bg-rose-700 transition">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="text-center py-8 text-slate-400 font-medium">कुनै Bill भेटिएन।</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <?php echo e($bills->links()); ?>

    </div>
</div>

<!-- TomSelect Scripts & CSS for Searchable Month Dropdown -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.2.2/css/tom-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.2.2/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthFilter = document.getElementById('filter_month_id');
    if (monthFilter && typeof TomSelect !== 'undefined') {
        new TomSelect('#filter_month_id', {
            create: false,
            placeholder: "-- महिना खोज्नुहोस् / छान्नुहोस् --",
            allowEmptyOption: true,
            maxOptions: null
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/petrol/bills/index.blade.php ENDPATH**/ ?>