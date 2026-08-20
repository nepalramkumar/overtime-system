

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">

    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Active Events / Projects</h2>
            <p class="text-xs text-slate-500 mt-1">सबै कार्यक्रम / Project हरूको OT claim स्थिति यहाँ हेर्नुहोस्।</p>  
        </div>
        <a href="<?php echo e(route('overtime.create')); ?>"
           class="inline-flex items-center justify-center gap-2 bg-slate-700 text-white px-4 py-2.5 rounded-lg font-medium text-xs hover:bg-slate-800 transition shadow-sm">
            <i class="fas fa-plus-circle"></i>
            <span>Log General OT (सामान्य प्रयोजन)</span>
        </a>
    </div>

    
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
            <span><?php echo e(session('success')); ?></span>  
        </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Event Name</th>  
                        <th class="p-4">Department</th>  
                        <th class="p-4">Date Range</th>  
                        <th class="p-4">Status</th>  
                        <th class="p-4 text-right">Action</th>  
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition <?php echo e(!$event->is_active ? 'bg-slate-50/60' : ''); ?>">  

                            
                            <td class="p-4 align-top">
                                <div class="font-semibold text-slate-800 <?php echo e(!$event->is_active ? 'text-slate-400' : ''); ?>">
                                    <?php echo e($event->event_name); ?>  
                                </div>

                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <?php $__empty_2 = true; $__currentLoopData = $event->status_summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <?php
                                            $colors = match(strtolower($status)) {
                                                'pending'  => ['bg' => 'bg-amber-500', 'text' => 'text-amber-700', 'light' => 'bg-amber-50 border-amber-200'],
                                                'rejected' => ['bg' => 'bg-rose-500',  'text' => 'text-rose-700',  'light' => 'bg-rose-50 border-rose-200'],
                                                'verified' => ['bg' => 'bg-emerald-500','text' => 'text-emerald-700','light' => 'bg-emerald-50 border-emerald-200'],
                                                default    => ['bg' => 'bg-blue-500',  'text' => 'text-blue-700',  'light' => 'bg-blue-50 border-blue-200'],
                                            };
                                        ?>
                                        <div class="flex items-center gap-1.5 <?php echo e($colors['light']); ?> border rounded-full pl-2.5 pr-1 py-0.5 shadow-2xs">
                                            <span class="text-[10px] font-bold <?php echo e($colors['text']); ?> uppercase tracking-wide">
                                                <?php echo e($status); ?>  
                                            </span>
                                            <span class="<?php echo e($colors['bg']); ?> text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">
                                                <?php echo e($count); ?>  
                                            </span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <span class="text-[10px] text-slate-400 italic">कुनै claim छैन</span>  
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="p-4 align-top text-xs text-slate-600"><?php echo e($event->department); ?></td>  

                            <td class="p-4 align-top text-xs text-slate-600 whitespace-nowrap">
                                <?php echo e(\Carbon\Carbon::parse($event->start_date)->format('Y-m-d')); ?>

                                <span class="text-slate-300 mx-1">→</span>
                                <?php echo e(\Carbon\Carbon::parse($event->end_date)->format('Y-m-d')); ?>  
                            </td>

                            <td class="p-4 align-top">
                                <?php if($event->is_active): ?>
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>  
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Disabled
                                    </span>  
                                <?php endif; ?>
                            </td>

                            <td class="p-4 align-top text-right">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    <?php if($event->is_active): ?>
                                        <a href="<?php echo e(route('overtime.create', ['event_id' => $event->id])); ?>"
                                           class="bg-blue-600 text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-700 transition shadow-2xs">
                                            Entry Overtime
                                        </a>  
                                    <?php else: ?>
                                        <span class="bg-slate-100 text-slate-400 px-2.5 py-1.5 rounded-lg text-xs font-semibold cursor-not-allowed border border-slate-200">
                                            Entry Overtime
                                        </span>  
                                    <?php endif; ?>

                                    <a href="<?php echo e(route('events.print', $event->id)); ?>" target="_blank"
                                       class="bg-purple-600 text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-purple-700 transition shadow-2xs">
                                        Print
                                    </a>  
                                    
                                    <a href="<?php echo e(route('events.edit', $event->id)); ?>"
                                       class="bg-purple-600 text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-purple-700 transition shadow-2xs">
                                        Edit
                                    </a>

                                    <form action="<?php echo e(route('events.toggle', $event->id)); ?>" method="POST" class="inline"
                                          onsubmit="return confirm('के तपाईं यो कार्यक्रमको Status बदल्न चाहनुहुन्छ?')">  
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="<?php echo e($event->is_active ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700'); ?> text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold transition shadow-2xs">
                                            <?php echo e($event->is_active ? 'Disable' : 'Enable'); ?>

                                        </button>  
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="p-10 text-center text-slate-400">
                                <i class="fas fa-calendar-times text-2xl mb-2 block text-slate-300"></i>
                                अहिले कुनै पनि सक्रिय कार्यक्रम छैनन्।
                            </td>  
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/events/index.blade.php ENDPATH**/ ?>