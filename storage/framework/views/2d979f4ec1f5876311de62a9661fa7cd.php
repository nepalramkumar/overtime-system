<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'OT System')); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0 shadow-xl">
            <!-- Logo / Title -->
            <div class="p-5 border-b border-gray-700">
                <h2 class="text-2xl font-bold text-center">
                    <i class="fas fa-clock mr-2"></i>OT System
                </h2>
            </div>

            <nav class="p-4">

                <!-- Main Menu -->
                <div class="mb-6">
                    <h3 class="text-xs uppercase text-gray-400 font-semibold tracking-wider mb-3">
                        Main Menu
                    </h3>

                    <ul class="space-y-2">

                        <li>
                            <a href="<?php echo e(route('dashboard')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-gauge-high w-5"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- Configuration -->
                <div class="mb-6">
                    <h3 class="text-xs uppercase text-gray-400 font-semibold tracking-wider mb-3">
                        Configuration
                    </h3>

                    <ul class="space-y-2">

                       <li>
                            <a href="<?php echo e(route('users.index')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('shifts.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-business-time w-5"></i>
                                <span>User</span>
                            </a>
                        </li>    
                    <li>
                            <a href="<?php echo e(route('shifts.index')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('shifts.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-business-time w-5"></i>
                                <span>Shift Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('settings.snack')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('shifts.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-business-time w-5"></i>
                                <span>Lunch Settings</span>
                            </a>
                        </li>
                             <li>
                            <a href="<?php echo e(route('employees.create')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('shifts.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-business-time w-5"></i>
                                <span>Add Staff</span>
                            </a>
                        </li>
                           </li>
                             <li>
                            <a href="<?php echo e(route('employees.index')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('shifts.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-business-time w-5"></i>
                                <span>Staff</span>
                            </a>
                        </li>
                        <li>
    <a href="<?php echo e(route('overtime.my')); ?>"
       class="flex items-center gap-3 p-3 rounded-lg transition duration-200
       <?php echo e(request()->routeIs('overtime.my') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
        <i class="fas fa-user-clock w-5"></i>
        <span>मेरो OT Records</span>
    </a>
</li>
                        <li>
        <a href="<?php echo e(route('positions.index')); ?>"
           class="flex items-center gap-3 p-3 rounded-lg transition duration-200
           <?php echo e(request()->routeIs('positions.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
            <i class="fas fa-briefcase w-5"></i>
            <span>Position Settings</span>
        </a>
        <li>
    <a href="<?php echo e(route('overtime.pending')); ?>"
       class="flex items-center gap-3 p-3 rounded-lg transition duration-200
       <?php echo e(request()->routeIs('overtime.pending') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
        <i class="fas fa-clock w-5"></i>
        <span>Pending Overtime</span>
    </a>
</li>
        <?php if(auth()->user()->role === 'admin'): ?>
<li>
        <a href="<?php echo e(route('permissions.index')); ?>"
           class="flex items-center gap-3 p-3 rounded-lg transition duration-200
           <?php echo e(request()->routeIs('permissions.*') ? 'bg-green-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
            <i class="fas fa-lock w-5"></i>
            <span>Role Permissions</span>
        </a>
    </li>
<?php endif; ?>
    </li>

                    </ul>
                </div>

                <!-- Events -->
                <div>
                    <h3 class="text-xs uppercase text-gray-400 font-semibold tracking-wider mb-3">
                        Events
                    </h3>

                    <ul class="space-y-2">

                        <li>
                            <a href="<?php echo e(route('events.create')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('events.*') ? 'bg-purple-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-calendar-days w-5"></i>
                                <span>Events</span>
                            </a>
                        </li>
                          <li>
                            <a href="<?php echo e(route('events.list')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('events.*') ? 'bg-purple-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-calendar-days w-5"></i>
                                <span>Events list</span>
                            </a>
                        </li>

                    </ul>
                </div>
                 <!-- Report -->
                <div>
                    <h3 class="text-xs uppercase text-gray-400 font-semibold tracking-wider mb-3">
                        Report
                    </h3>

                    <ul class="space-y-2">

                        <li>
                            <a href="<?php echo e(route('reports.index')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('events.*') ? 'bg-purple-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-calendar-days w-5"></i>
                                <span>Main Report</span>
                            </a>
                        </li>
                          <li>
                            <a href="<?php echo e(route('reports.summary')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('events.*') ? 'bg-purple-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-calendar-days w-5"></i>
                                <span>Summary</span>
                            </a>
                        </li>
                         <li>
                            <a href="<?php echo e(route('reports.finance')); ?>"
                               class="flex items-center gap-3 p-3 rounded-lg transition duration-200
                               <?php echo e(request()->routeIs('events.*') ? 'bg-purple-600 text-white shadow-md' : 'hover:bg-gray-700'); ?>">
                                <i class="fas fa-calendar-days w-5"></i>
                                <span>Finance</span>
                            </a>
                        </li>

                    </ul>
                </div>

            </nav>
        </aside>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col">

            <header>
                <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </header>

            <main class="p-8">
                <div class="bg-white p-6 rounded shadow">
                    <?php echo e($slot ?? ''); ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>

        </div>

    </div>
</body>
</html><?php /**PATH D:\xampp\htdocs\overtime-system\resources\views/layouts/app.blade.php ENDPATH**/ ?>