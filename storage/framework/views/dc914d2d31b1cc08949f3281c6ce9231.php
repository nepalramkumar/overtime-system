

<?php $__env->startSection('content'); ?>

    <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Overtime & Tiffin Entry</h2>

        <?php if(session('success')): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <form action="<?php echo e(route('overtime.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Select Employee</label>
           <select name="employee_id" id="employee-select" class="w-full p-2 border rounded" required>
    <option value="">-- नाम टाइप गर्नुहोस् --</option>
    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($emp->id); ?>">
            <?php echo e($emp->name); ?> (ID: <?php echo e($emp->id); ?>) - <?php echo e($emp->designation); ?>

        </option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
            </div>

            <?php if(isset($selectedEventId) && $selectedEventId): ?>
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($event->id == $selectedEventId): ?>
                        <div class="bg-blue-50 p-3 rounded border border-blue-200 mb-4">
                            <label class="block text-blue-700 font-bold text-xs uppercase tracking-wide">Selected Event / Project</label>
                            <span class="text-gray-800 font-semibold text-lg"><?php echo e($event->event_name); ?> (<?php echo e($event->department); ?>)</span>
                            <input type="hidden" name="event_id" value="<?php echo e($event->id); ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="bg-gray-50 p-3 rounded border border-gray-200 mb-4">
                    <label class="block text-gray-500 font-bold text-xs uppercase tracking-wide">OT Category</label>
                    <span class="text-gray-700 font-semibold">सामान्य प्रयोजन (General Purpose OT)</span>
                    <input type="hidden" name="event_id" value="">
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Date</label>
                <input type="date" name="ot_date" value="<?php echo e(old('ot_date', date('Y-m-d'))); ?>" class="w-full p-2 border rounded" required>
            </div>

            <div class="flex items-center bg-yellow-50 p-2 rounded border border-yellow-200">
                <input type="checkbox" name="is_holiday" id="is_holiday" value="1" <?php echo e(old('is_holiday') ? 'checked' : ''); ?> class="w-4 h-4 mr-2 text-blue-600 border-gray-300 rounded">
                <label for="is_holiday" class="text-gray-700 font-medium select-none cursor-pointer">Is this a Holiday? (विदाको दिन हो?)</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">From Time (सुरुको समय)</label>
                    <input type="time" name="from_time" value="<?php echo e(old('from_time')); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">To Time (सकिने समय)</label>
                    <input type="time" name="to_time" value="<?php echo e(old('to_time')); ?>" class="w-full p-2 border rounded" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Remarks</label>
                <textarea name="remarks" class="w-full p-2 border rounded" rows="2"><?php echo e(old('remarks')); ?></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">
                Submit Overtime
            </button>
        </form>
    </div>

<script>
    new TomSelect("#employee-select",{
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/overtime/create.blade.php ENDPATH**/ ?>