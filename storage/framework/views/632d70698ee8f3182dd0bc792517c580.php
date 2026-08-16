<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">
        <?php echo e($bill ? 'Petrol Bill Edit गर्नुहोस्' : 'नयाँ Petrol Bill'); ?>

    </h2>

    <?php if(session('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e($bill ? route('petrol.bills.update', $bill->id) : route('petrol.bills.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php if($bill): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">कर्मचारी</label>
                <?php if($bill): ?>
                    <div class="w-full p-2 border rounded bg-gray-100 text-gray-700 font-semibold">
                        <?php echo e($bill->employee->name ?? 'N/A'); ?>

                    </div>
                <?php else: ?>
                    <select name="employee_id" class="w-full p-2 border rounded" required>
                        <option value="">-- छान्नुहोस् --</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?> (<?php echo e($emp->employee_code); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Month</label>
                <?php if($bill): ?>
                    <div class="w-full p-2 border rounded bg-gray-100 text-gray-700 font-semibold">
                        <?php echo e($bill->month->month ?? ''); ?> - <?php echo e($bill->month->year ?? ''); ?>

                    </div>
                <?php else: ?>
                    <select name="petrol_month_id" class="w-full p-2 border rounded" required>
                        <option value="">-- छान्नुहोस् --</option>
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->month); ?> - <?php echo e($m->year); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            </div>
        </div>

        <h3 class="font-semibold text-gray-700 mb-2">Petrol भरेको विवरण</h3>
        <table class="w-full border-collapse mb-2" id="rows-table">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-sm">मिति</th>
                    <th class="border p-2 text-sm">परिमाण (Litre)</th>
                    <th class="border p-2 text-sm">दर</th>
                    <th class="border p-2 text-sm">रकम</th>
                    <th class="border p-2 text-sm w-12"></th>
                </tr>
            </thead>
            <tbody id="rows-body">
                <?php
                    $existingDates = $bill ? $bill->date : [now()->format('Y-m-d')];
                    $existingQty   = $bill ? $bill->quantity : [''];
                    $existingRate  = $bill ? $bill->rate : [''];
                    $existingAmt   = $bill ? $bill->amount : [''];
                ?>
                <?php $__currentLoopData = $existingDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="border p-1"><input type="date" name="date[]" value="<?php echo e($d); ?>" class="w-full p-1 border rounded row-date" required></td>
                    <td class="border p-1"><input type="number" step="0.01" name="quantity[]" value="<?php echo e($existingQty[$i] ?? ''); ?>" class="w-full p-1 border rounded row-qty" required></td>
                    <td class="border p-1"><input type="number" step="0.01" name="rate[]" value="<?php echo e($existingRate[$i] ?? ''); ?>" class="w-full p-1 border rounded row-rate" required></td>
                    <td class="border p-1"><input type="number" step="0.01" name="amount[]" value="<?php echo e($existingAmt[$i] ?? ''); ?>" class="w-full p-1 border rounded row-amount" required></td>
                    <td class="border p-1 text-center"><button type="button" class="text-red-600 font-bold remove-row">✕</button></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <button type="button" id="add-row" class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-300 mb-4">
            + थप पंक्ति थप्नुहोस्
        </button>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-1">कैफियत</label>
            <textarea name="remarks" class="w-full p-2 border rounded" rows="2"><?php echo e($bill->remarks ?? ''); ?></textarea>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">
            <?php echo e($bill ? 'Update गर्नुहोस्' : 'Submit गर्नुहोस्'); ?>

        </button>
    </form>
</div>

<script>
document.getElementById('add-row').addEventListener('click', function () {
    const tbody = document.getElementById('rows-body');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="border p-1"><input type="date" name="date[]" class="w-full p-1 border rounded row-date" required></td>
        <td class="border p-1"><input type="number" step="0.01" name="quantity[]" class="w-full p-1 border rounded row-qty" required></td>
        <td class="border p-1"><input type="number" step="0.01" name="rate[]" class="w-full p-1 border rounded row-rate" required></td>
        <td class="border p-1"><input type="number" step="0.01" name="amount[]" class="w-full p-1 border rounded row-amount" required></td>
        <td class="border p-1 text-center"><button type="button" class="text-red-600 font-bold remove-row">✕</button></td>
    `;
    tbody.appendChild(row);
});

document.getElementById('rows-body').addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        if (document.querySelectorAll('#rows-body tr').length > 1) {
            e.target.closest('tr').remove();
        }
    }
});

// परिमाण x दर = रकम auto-calculate
document.getElementById('rows-body').addEventListener('input', function (e) {
    if (e.target.classList.contains('row-qty') || e.target.classList.contains('row-rate')) {
        const row = e.target.closest('tr');
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        row.querySelector('.row-amount').value = (qty * rate).toFixed(2);
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/petrol/bills/form.blade.php ENDPATH**/ ?>