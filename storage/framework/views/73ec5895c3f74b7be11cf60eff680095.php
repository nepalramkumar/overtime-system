

<?php $__env->startSection('content'); ?>

<!-- Filter Section -->
<form action="<?php echo e(route('reports.index')); ?>" method="GET" class="bg-white border border-gray-200 rounded-lg p-3 mb-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-2 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">रिपोर्ट प्रकार</label>
            <select name="group_by" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="employee" <?php echo e(request('group_by') == 'employee' ? 'selected' : ''); ?>>कर्मचारी अनुसार</option>
                <option value="event" <?php echo e(request('group_by') == 'event' ? 'selected' : ''); ?>>कार्यक्रम अनुसार</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
            <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
            <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">कर्मचारी</label>
            <select name="employee_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="">सबै</option>
                <?php $__currentLoopData = \App\Models\Employee::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php echo e(request('employee_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">कार्यक्रम</label>
            <select name="event_id" class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm">
                <option value="">सबै छान्नुहोस्</option>
                <?php $__currentLoopData = \App\Models\Event::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>><?php echo e($event->event_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex gap-2 items-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded">🔍 खोज</button>
            <a href="<?php echo e(route('reports.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white text-xs px-3 py-1 rounded">Reset</a>
        </div>
    </div>
</form>

<!-- Table -->
<div class="overflow-x-auto bg-white rounded-lg shadow-sm">
    <table class="w-full border-collapse">
        <thead>
        <tr class="bg-blue-700 text-white text-sm">
            <th class="p-3 border">सि.नं.</th>
            <th class="p-3 border">मिति</th>
            <th class="p-3 border">कर्मचारी</th> 
            <th class="p-3 border">कार्यक्रम</th>
            <th class="p-3 border">समय (From-To)</th>
            <th class="p-3 border">घण्टा</th>
             <th class="p-3 border">खाजा</th>
            <th class="p-3 border">जम्मा घण्टा</th>
            <th class="p-3 border">कुल खाजा</th>
        </tr>
    </thead>
   <tbody>
        <?php $__currentLoopData = $groupedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $records): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $totalGroupHours = $records->sum('total_hours');
                $totalGroupAmount = $records->sum('tiffin_amount');
            ?>

            <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="p-3 border text-center"><?php echo e($loop->parent->iteration); ?></td>
                    <td class="p-3 border"><?php echo e($rec->ot_date); ?></td>
                   <td class="p-3 border"><?php echo e($rec->employee->name ?? 'N/A'); ?></td>
                    <td class="p-3 border"><?php echo e($rec->event->event_name ?? 'N/A'); ?></td>
                    <td class="p-3 border text-center"><?php echo e($rec->from_time); ?> - <?php echo e($rec->to_time); ?></td>
                    <td class="p-3 border text-center"><?php echo e(number_format($rec->total_hours, 2)); ?></td>
                    <td class="p-3 border text-center"><?php echo e(number_format($rec->tiffin_amount, 2)); ?></td>
                    
                    <?php if($loop->first): ?>
                        <td rowspan="<?php echo e($records->count()); ?>" class="p-3 border text-center font-bold bg-blue-50 align-middle">
                            <?php echo e(number_format($totalGroupHours, 2)); ?>

                        </td>
                        <td rowspan="<?php echo e($records->count()); ?>" class="p-3 border text-right font-bold bg-blue-50 align-middle">
                            रु <?php echo e(number_format($totalGroupAmount, 2)); ?>

                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
       <tfoot>
    <tr class="bg-gray-800 text-white font-bold">
       
        <td colspan="6" class="p-3 border text-right">कुल जम्मा (Grand Total)</td>
        <td class="p-3 border text-center">-</td> 
        <td class="p-3 border text-center"><?php echo e(number_format($totalHoursDecimalSum, 2)); ?></td>
        <td class="p-3 border text-right">रु <?php echo e(number_format($totalAmountSum, 2)); ?></td>
    </tr>
</tfoot>
    </table>
    
    <div class="mt-4">
        
    </div>
</div>
<a href="<?php echo e(route('reports.excel', request()->all())); ?>" class="bg-green-600 text-white px-3 py-1 rounded">
    Excel डाउनलोड
</a>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/reports/index.blade.php ENDPATH**/ ?>