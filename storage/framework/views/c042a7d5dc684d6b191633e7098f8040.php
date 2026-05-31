

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg border">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">कर्मचारीको विवरण थप्नुहोस्</h2>

        <form action="<?php echo e(route('employees.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">कर्मचारीको नाम</label>
                <input type="text" name="name" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="पूरा नाम">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">पद (Designation)</label>
                <input type="text" name="designation" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="जस्तै: Senior Developer">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">विभाग (Department)</label>
                <input type="text" name="department" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="जस्तै: HR Department">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">OT रेट (प्रति घण्टा)</label>
                <input type="number" step="0.01" name="ot_rate" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required placeholder="0.00">
            </div>

            <div class="mt-6 flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 transition">
                    सेभ गर्नुहोस्
                </button>
                <a href="<?php echo e(route('employees.index')); ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-300 transition">
                    रद्द गर्नुहोस्
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/employees/create.blade.php ENDPATH**/ ?>