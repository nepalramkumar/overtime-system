

<?php $__env->startSection('content'); ?>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Active Events / Projects</h2>
        
        <div class="mb-4 text-right">
            <a href="<?php echo e(route('overtime.create')); ?>" class="bg-gray-600 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">
                Log General OT (सामान्य प्रयोजन)
            </a>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
               <tr class="bg-blue-600 text-white">
                    <th class="p-3">Event Name</th>
                    <th class="p-3">Department</th>
                    <th class="p-3">Date Range</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                     <tr class="<?php echo e(!$event->is_active ? 'bg-gray-100 opacity-60' : ''); ?>">
                        <td class="p-3 font-semibold"><?php echo e($event->event_name); ?></td>
                        <td class="p-3"><?php echo e($event->department); ?></td>
                        <td class="p-3 text-sm"><?php echo e($event->start_date); ?> to <?php echo e($event->end_date); ?></td>
                       <td class="p-3">
                            <?php if($event->is_active): ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Active</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <?php if($event->is_active): ?>
                                <a href="<?php echo e(route('overtime.create', ['event_id' => $event->id])); ?>" 
                                   class="bg-blue-600 text-white px-3 py-1 rounded text-sm font-bold hover:bg-blue-700">
                                    Entry Overtime
                                </a>
                            <?php else: ?>
                                <span class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-sm font-bold cursor-not-allowed">
                                    Entry Overtime
                                </span>
                            <?php endif; ?>
                            <a href="<?php echo e(route('events.print', $event->id)); ?>" target="_blank"
                               class="bg-purple-600 text-white px-3 py-1 rounded text-sm font-bold hover:bg-purple-700 ml-1">
                                Print
                            </a>
                            <form action="<?php echo e(route('events.toggle', $event->id)); ?>" method="POST" class="inline" onsubmit="return confirm('के तपाईं यो कार्यक्रमको Status बदल्न चाहनुहुन्छ?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="<?php echo e($event->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700'); ?> text-white px-3 py-1 rounded text-sm font-bold ml-1">
                                    <?php echo e($event->is_active ? 'Disable' : 'Enable'); ?>

                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-500">अहिले कुनै पनि सक्रिय कार्यक्रम छैनन्।</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/events/index.blade.php ENDPATH**/ ?>