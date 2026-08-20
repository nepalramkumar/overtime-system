

<?php $__env->startSection('content'); ?>
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Role Permissions</h1>

        <?php if(session('success')): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo e(session('success')); ?></div>  
        <?php endif; ?>

        <!-- Search Box -->
        <div class="mb-4">
            <input type="text" id="permissionSearch" placeholder="Feature खोज्नुहोस्..." class="border p-2 rounded w-full text-sm" onkeyup="filterPermissions()">
        </div>

        <form action="<?php echo e(route('permissions.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <table id="permissionTable" class="w-full border-collapse border border-gray-200 mb-6">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2 text-left">Feature</th>  
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="border p-2 text-center capitalize"><?php echo e($role); ?></th>  
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="perm-row">
                        <td class="border p-2 feature-name"><?php echo e($label); ?></td>  
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="border p-2 text-center">
                                <input type="checkbox"
                                       name="permissions[<?php echo e($role); ?>][<?php echo e($key); ?>]"
                                       class="w-5 h-5"
                                       <?php echo e(in_array($role . '|' . $key, $existing) ? 'checked' : ''); ?>>  
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Save Permissions
            </button>
        </form>
    </div>

    <script>
    function filterPermissions() {
        let input = document.getElementById('permissionSearch').value.toLowerCase();
        let rows = document.querySelectorAll('.perm-row');
        rows.forEach(row => {
            let text = row.querySelector('.feature-name').innerText.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/settings/permissions.blade.php ENDPATH**/ ?>