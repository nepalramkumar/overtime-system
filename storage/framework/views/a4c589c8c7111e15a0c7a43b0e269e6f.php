


<?php $__env->startSection('content'); ?>
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md border">
    <h2 class="text-xl font-bold mb-6 text-gray-700">कार्यक्रम (Event) दर्ता गर्नुहोस्</h2>
    
    <form action="<?php echo e(route('events.store')); ?>" method="POST" class="space-y-4">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">कार्यक्रमको नाम</label>
                <input type="text" name="event_name" class="w-full mt-1 p-2 border rounded-md" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">विभाग</label>
                <input type="text" name="department" class="w-full mt-1 p-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">सुरु हुने मिति</label>
                <input type="date" name="start_date" class="w-full mt-1 p-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">सकिने मिति</label>
                <input type="date" name="end_date" class="w-full mt-1 p-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">सुरु हुने समय</label>
                <input type="time" name="start_time" class="w-full mt-1 p-2 border rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">सकिने समय</label>
                <input type="time" name="end_time" class="w-full mt-1 p-2 border rounded-md">
            </div>
        </div>
        <div>
    <label class="block text-gray-700 font-semibold mb-1">सिफारिस गर्ने</label>
    <select name="recommender_employee_id" class="w-full p-2 border rounded">
        <option value="">-- छान्नुहोस् --</option>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($emp->id); ?>">
                <?php echo e($emp->name); ?> — <?php echo e($emp->position->name ?? ''); ?>

            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
        <div>
    <label class="block text-gray-700 font-semibold mb-1">स्वीकृत गर्ने (निर्देशनालय प्रमुख)</label>
    <select name="approver_employee_id" class="w-full p-2 border rounded">
        <option value="">-- छान्नुहोस् --</option>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($emp->id); ?>">
                <?php echo e($emp->name); ?> — <?php echo e($emp->position->name ?? ''); ?> (<?php echo e($emp->department); ?>)
            </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
        
        <div class="flex items-center gap-2 bg-gray-50 p-3 rounded-md">
            <input type="checkbox" name="is_tiffin_eligible" value="1" class="w-5 h-5 text-blue-600">
            <label class="text-sm font-medium text-gray-700">
                यस कार्यक्रमको OT दाबी गर्दा खाजा खर्च गणना गर्ने हो?
                
            </label>
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
            दर्ता गर्नुहोस्
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/events/create.blade.php ENDPATH**/ ?>